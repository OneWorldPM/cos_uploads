<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $login_status = $this->session->userdata('admin_login_status');
        if ($login_status != true)
            redirect(base_url('admin'));

        $this->load->model('Admin_Logger');
    }

    public function index()
    {
        $this->load->view('admin/head');

        $this->load->view('admin/dashboard');

        $this->load->view('admin/models/change-password');
        $this->load->view('admin/models/files');
        $this->load->view('admin/models/load-presentations');

        $this->load->view('admin/foot');
    }

    public function getPresentationList()
    {

        $this->db->select("p.*, s.name as session_name, pr.presenter_id, CONCAT(pr.first_name, ' ', pr.last_name) as presenter_name");
        $this->db->from('presentations p');
        $this->db->join('sessions s', 's.id = p.session_id');
        $this->db->join('presenter pr', 'pr.presenter_id = p.presenter_id');
        $this->db->order_by('p.created_on', 'DESC');
        $result = $this->db->get();

        if ($result->num_rows() > 0)
        {
            foreach ($result->result() as $row)
                $row->uploadStatus = $this->checkUploadStatus($row->id);

            echo json_encode(array('status'=>'success', 'data'=>$result->result()));
            return;
        } else {
            echo json_encode(array('status'=>'error', 'msg'=>'Unable to load your presentations data'));
            return;
        }
    }

    public function getUploadedFiles($user_id, $presentation_id)
    {
        $this->db->select('*');
        $this->db->from('uploads');
        $this->db->where('presenter_id', $user_id);
        $this->db->where('presentation_id', $presentation_id);
        $this->db->where('deleted', 0);

        $result = $this->db->get();

        if ($result->num_rows() > 0)
        {
            echo json_encode(array('status'=>'success', 'msg'=>'Files are uploaded', 'files'=>$result->result()));
        }else{
            echo json_encode(array('status'=>'error', 'msg'=>'No files uploaded yet'));
        }

        return;
    }

    private function checkUploadStatus($presentation_id)
    {
        $this->db->select('*');
        $this->db->from('uploads');
        $this->db->where('presentation_id', $presentation_id);
        $this->db->where('deleted', 0);

        $result = $this->db->get();

        if ($result->num_rows() > 0)
            return $result->num_rows();

        return false;
    }

    public function openFile($file_id)
    {
        $login_status = $this->session->userdata('admin_login_status');
        if ($login_status != true)
        {
            echo 'You are not logged in.';
            return;
        }

        $this->db->select('*');
        $this->db->from('uploads');
        $this->db->where('id', $file_id);
        $this->db->where('deleted', 0);

        $result = $this->db->get();

        if ($result->num_rows() > 0)
        {
            $this->Admin_Logger->log("Downloaded", null, null, $file_id);

            $file = FCPATH.$result->row()->file_path;
            $new_filename = $result->row()->name;

            header("Content-Type: {$result->row()->format}");
            header("Content-Length: " . filesize($file));
            header('Content-Disposition: attachment; filename="' . $new_filename . '"');
            readfile($file);

        }else{
            echo 'Either this file does not exist or you are not authorized to open it.';
        }

        return;
    }

    public function activatePresentation($presentation_id)
    {
        $this->db->set('active', 1);
        $this->db->where('id', $presentation_id);
        $this->db->update('presentations');

        if ($this->db->affected_rows() > 0)
        {
            $this->Admin_Logger->log("Activated", null, $presentation_id);

            echo json_encode(array('status'=>'success', 'msg'=>'Presentation activated'));

        }else{
            echo json_encode(array('status'=>'error', 'msg'=>'Database error'));
        }
    }

    public function disablePresentation($presentation_id)
    {
        $this->db->set('active', 0);
        $this->db->where('id', $presentation_id);
        $this->db->update('presentations');

        if ($this->db->affected_rows() > 0)
        {
            $this->Admin_Logger->log("Disabled", null, $presentation_id);

            echo json_encode(array('status'=>'success', 'msg'=>'Presentation disabled'));

        }else{
            echo json_encode(array('status'=>'error', 'msg'=>'Database error'));
        }
    }

    public function loadPresentations()
    {

        $allowed_column_names = array(
            'A'=>'Abstract.ControlNumber',
            'B'=>'Topic.Topic1',
            'C'=>'ePoster.Title',
            'D'=>'Author.FirstName',
            'E'=>'Author.LastName',
            'F'=>'Author.Salutation',
            'G'=>'Author.Email',
            'H'=>'Primary author',
            'I'=>'co-authors',
            'J'=>'AWARD'
        );

        $required_column_names = array(
            'B'=>'Topic.Topic1',
            'C'=>'ePoster.Title',
            'D'=>'Author.FirstName',
            'E'=>'Author.LastName',
            'G'=>'Author.Email'
        );

        $param_column_index = array(
            'email'=>'G',
            'name_prefix'=>'F',
            'first_name'=>'D',
            'last_name'=>'E',
            'session_name'=>'B',
            'presentation_name'=>'C',
            'award'=>'J'
        );

        $admin_id = $_SESSION['user_id'];

        if (!isset($_FILES['file']['name']))
        {
            echo json_encode(array('status'=>'failed', 'msg'=>'File is required'));
            return;
        }

        $file = $_FILES['file'];

        $this->load->library('excel');

        $objPHPExcel = PHPExcel_IOFactory::load($file['tmp_name']);


        /** Save file for logging */
        $unique_file_name = date("Y-m-d_H:i:s").'.'.pathinfo($file["name"])['extension'];
        move_uploaded_file($file["tmp_name"], FCPATH.'upload_system_files/data_load_files/'.$unique_file_name);
        $this->Admin_Logger->log("Data load initiated", $file['name']." ($unique_file_name)");


        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

        /** @var array $cell
         * Get the data from spreadsheet file
         */
        foreach ($cell_collection as $cell)
        {
            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();

            if ($row == 1) {
                $header[$column] = $data_value;
            } else {
                $rows[$row][$column] = $data_value;
            }
        }

        foreach ($allowed_column_names as $columnIndex => $column_name)
        {
            /** @var array $header */
            if ($header[$columnIndex] != $column_name)
            {
                $this->Admin_Logger->log("Data load error", "Column {$columnIndex} is not {$column_name} in the row 1");
                echo json_encode(array('status'=>'failed', 'msg'=>"Column {$columnIndex} is not {$column_name} in the row 1", 'updatedPresentations'=>0, 'createdPresentations'=>0));
                return;
            }
        }

        $this->db->trans_begin();

        $duplicateRows = 0;
        $createdPresentations = 0;
        /** @var array $rows */
        foreach ($rows as $row => $row_columns)
        {
            /** Empty column value catcher */
            foreach ($required_column_names as $columnIndex => $column_name)
            {
                if ($row_columns[$columnIndex] == '')
                {
                    $this->db->trans_rollback();
                    $this->Admin_Logger->log("Data load error", "{$column_name} (Column {$columnIndex}) is empty in the row {$row}");
                    echo json_encode(array('status'=>'failed', 'msg'=>"{$column_name} (Column {$columnIndex}) is empty in the row {$row}", 'updatedPresentations'=>0, 'createdPresentations'=>0));
                    return;
                }
            }

            $name_prefix = (isset($row_columns['F']))?str_replace('\'', "\`", $row_columns[$param_column_index['name_prefix']]):'';
            $first_name = str_replace('\'', "\`", $row_columns[$param_column_index['first_name']]);
            $last_name = str_replace('\'', "\`", $row_columns[$param_column_index['last_name']]);
            $email = str_replace('\'', "\`", $row_columns[$param_column_index['email']]);
            $password = str_replace('\'', "\`", $first_name);
            $session_name = str_replace('\'', "\`", $row_columns[$param_column_index['session_name']]);
            $presentation_name = str_replace('\'', "\`", $row_columns[$param_column_index['presentation_name']]);
            $created_date_time = date("Y-m-d H:i:s");

            $award = 'null';
            if (isset($row_columns[$param_column_index['award']]))
            {
                $award = ucfirst(str_replace('\'', "\`", $row_columns[$param_column_index['award']]));
                $award = ($award == '')?'null':"'{$award}'";
            }

            $exists = $this->checkDuplicate($email, $session_name, $presentation_name);

            if ($exists)
            {
                $desc = json_encode($exists);
                $this->db->query("INSERT INTO `admin_logs`(`admin_id`, `log_name`, `log_desc`, `ref_presentation_id`, `other_ref`, `date_time`) VALUES ( '{$admin_id}', 'Ignored load item', '{$desc}', '{$exists->presentation_id}', '{$exists->presenter_id}', '{$created_date_time}')");
                $duplicateRows = $duplicateRows+1;

            }else{

                try{
                    $emailExists = $this->checkEmailExists($email);
                    if ($emailExists)
                    {
                        $presenter_id = $emailExists;
                    }else{
                        $this->db->query("INSERT INTO `presenter`(`name_prefix`, `first_name`, `last_name`, `email`, `password`, `creation_date`) VALUES ('{$name_prefix}', '{$first_name}','{$last_name}','{$email}','{$password}','{$created_date_time}')");
                        $presenter_id = $this->db->insert_id();
                        $this->db->query("INSERT INTO `admin_logs`(`admin_id`, `log_name`, `log_desc`, `ref_presentation_id`, `other_ref`, `date_time`) VALUES ( '{$admin_id}', 'Created user', null, null, '{$presenter_id}', '{$created_date_time}')");
                    }

                    $sessionExists = $this->checkSessionExists($session_name);
                    if ($sessionExists)
                    {
                        $session_id = $sessionExists;
                    }else{
                        $this->db->query("INSERT INTO `sessions`(`name`) VALUES ('{$session_name}')");
                        $session_id = $this->db->insert_id();
                        $this->db->query("INSERT INTO `admin_logs`(`admin_id`, `log_name`, `log_desc`, `ref_presentation_id`, `other_ref`, `date_time`) VALUES ( '{$admin_id}', 'Created session', null, null, '{$session_id}', '{$created_date_time}')");
                    }

                    $presentationExists = $this->checkPresentationExists($presentation_name, $session_id, $presenter_id);
                    if ($presentationExists)
                    {
                        $desc = json_encode($presentationExists);
                        $this->db->query("INSERT INTO `admin_logs`(`admin_id`, `log_name`, `log_desc`, `ref_presentation_id`, `other_ref`, `date_time`) VALUES ( '{$admin_id}', 'Ignored load item', '{$desc}', '{$presentationExists->id}', '{$presentationExists->presenter_id}', '{$created_date_time}')");
                        $duplicateRows = $duplicateRows+1;
                    }else{
                        $this->db->query("INSERT INTO `presentations`(`name`, `session_id`, `presenter_id`, `created_on`,`award`) VALUES ('{$presentation_name}','{$session_id}','{$presenter_id}','{$created_date_time}',".$award.")");
                        $presentation_id = $this->db->insert_id();
                        $this->db->query("INSERT INTO `admin_logs`(`admin_id`, `log_name`, `log_desc`, `ref_presentation_id`, `other_ref`, `date_time`) VALUES ( '{$admin_id}', 'Created presentation', null, '{$presentation_id}', null, '{$created_date_time}')");
                        $createdPresentations = $createdPresentations+1;
                    }

                }catch (Exception $e)
                {
                    $this->db->trans_rollback();
                    $this->Admin_Logger->log("Data load error", $e->getMessage());
                    echo json_encode(array('status'=>'failed', 'msg'=>'Query Error: '.$e->getMessage(), 'updatedPresentations'=>0, 'createdPresentations'=>0));
                    return;
                }


            }


        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->Admin_Logger->log("Data load error", json_encode($this->db->error()));
            echo json_encode(array('status'=>'failed', 'msg'=>'Query Transaction Error: Unable to load the data', 'updatedPresentations'=>0, 'createdPresentations'=>0));
            return;
        }
        else
        {
            $this->db->trans_commit();
            $this->Admin_Logger->log("Data load success", json_encode(array('updatedPresentations'=>0, 'createdPresentations'=>$createdPresentations, 'duplicatedRows'=>$duplicateRows)));
            echo json_encode(array('status'=>'success', 'msg'=>'Data loaded successfully', 'updatedPresentations'=>0, 'createdPresentations'=>$createdPresentations, 'duplicatedRows'=>$duplicateRows));
            return;
        }

        return;
    }

    private function checkDuplicate($email, $session_name, $presentation_name)
    {
        $this->db->select('p.presenter_id, pr.id as presentation_id, s.id as session_id')
            ->from('presentations pr')
            ->join('presenter p', "p.presenter_id = pr.presenter_id")
            ->join('sessions s', "s.id = pr.session_id")
            ->where('p.email', "$email")
            ->where('s.name', "$session_name")
            ->where('pr.name', "$presentation_name");

        $result = $this->db->get();

        if ($result->num_rows() > 0)
            return $result->row();

        return false;
    }

    private function checkEmailExists($email)
    {
        $this->db->select('presenter_id')
            ->from('presenter')
            ->where('email', "$email");

        $result = $this->db->get();

        if ($result->num_rows() > 0)
            return $result->row()->presenter_id;

        return false;
    }

    private function checkSessionExists($session_name)
    {
        $this->db->select('id')
            ->from('sessions')
            ->where('name', "$session_name");

        $result = $this->db->get();

        if ($result->num_rows() > 0)
            return $result->row()->id;

        return false;
    }

    private function checkPresentationExists($presentation_name, $session_id, $presenter_id)
    {
        $query = $this->db->query("select id, name, session_id, presenter_id from presentations where name='{$presentation_name}' and session_id='{$session_id}' and presenter_id='{$presenter_id}'");

        if ($query->num_rows() > 0)
            return $query->result_object()[0];

        return false;
    }

}
