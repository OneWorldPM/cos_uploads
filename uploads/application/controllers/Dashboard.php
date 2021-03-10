<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $login_status = $this->session->userdata('uploads_login_status');
        if ($login_status != true)
            redirect(base_url());

        $this->load->model('Presenter_Logger');
    }

    public function index()
    {
        $this->load->view('presenter/head');

        $this->load->view('presenter/dashboard');

        $this->load->view('presenter/models/change-password');
        $this->load->view('presenter/models/upload');

        $this->load->view('presenter/foot');
    }

    public function getPresentationList()
    {
        $user_id = $this->session->userdata('user_id');

        $this->db->select('p.*, s.name as session_name');
        $this->db->from('presentations p');
        $this->db->join('sessions s', 's.id = p.session_id');
        $this->db->where("p.presenter_id ", $user_id);
        $this->db->where("p.active ", 1);
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

    public function uploadFile()
    {
        ini_set('set_time_limit', '3600');
        ini_set('max_execution_time',3600);
        ini_set('max_input_time','500');
        ini_set('session.gc_maxlifetime',84000);
        ini_set('session.cookie_lifetime',84000);
        ini_set('memory_limit','512M');
        ini_set('upload_max_filesize', '3072M');
        ini_set('post_max_size', '3072M');

        //ToDo: Check whether file transaction has been freeze-ed by admin

        /**
         * Authentication
         */
        $login_status = $this->session->userdata('uploads_login_status');
        if ($login_status != true)
        {
            echo json_encode(array('status'=>'error', 'msg'=>'You are not logged in'));
            return;
        }

        $user = $this->input->post()['user_id'];
        $presentation_id = $this->input->post()['presentation_id'];

        if ($user != $_SESSION['user_id'])
        {
            echo json_encode(array('status'=>'error', 'msg'=>'You are not authorized to upload files here'));
            return;
        }


        /**
         * Process file
         */
        if (!isset($_FILES['file']))
        {
            echo json_encode(array('status'=>'error', 'msg'=>'File is required'));
            return;

        }else{  //File present
            //print_r($_FILES); exit;

            $upload_dir = FCPATH.'upload_system_files/doc_upload/'.$user;
            $unique_str = md5(date('Y-m-d H:i:s:u'));

            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $type = $_FILES['file']['type'];
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            $upload_file_name = $unique_str.'.'.$extension;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $upload_dir.'/'.$upload_file_name)) {

                $file_path = 'upload_system_files/doc_upload/'.$user.'/'.$upload_file_name;

                $upload = array(
                    'name' => $name,
                    'format' => $type,
                    'extension' => $extension,
                    'size' => $size,
                    'file_path' => $file_path,
                    'presentation_id' => $presentation_id,
                    'presenter_id' => $user,
                    "uploaded_date_time" => date("Y-m-d H:i:s")
                );
                $this->db->insert("uploads", $upload);

                if ($this->db->affected_rows() > 0)
                {
                    $file_id = $this->db->insert_id();

                    $this->Presenter_Logger->log("Uploaded", $presentation_id, $file_id);

                    echo json_encode(array('status'=>'success', 'fileId'=>$file_id, 'msg'=>'File is uploaded'));
                    return;

                }else{
                    echo json_encode(array('status'=>'error', 'msg'=>'Unable to upload the file'));
                    return;
                }

            } else {
                echo json_encode(array('status'=>'error', 'msg'=>'Unable to upload the file'));
                return;
            }

        }

        return;

    }

    public function deleteFile()
    {
        //ToDo: Check whether file transaction has been freeze-ed by admin

        /**
         * Authentication
         */
        $login_status = $this->session->userdata('uploads_login_status');
        if ($login_status != true)
        {
            echo json_encode(array('status'=>'error', 'msg'=>'You are not logged in'));
            return;
        }

        $logged_in_user = $this->session->userdata('user_id');

        $user = $this->input->post()['user_id'];
        $file_id = $this->input->post()['file_id'];
        $presentation_id = $this->input->post()['presentation_id'];

        if ($user != $logged_in_user)
        {
            echo json_encode(array('status'=>'error', 'msg'=>'You are not authorized to delete this file'));
            return;
        }

        $this->db->set('deleted', 1);
        $this->db->set('deleted_date_time', date("Y-m-d H:i:s"));
        $this->db->where('presenter_id', $user);
        $this->db->where('id', $file_id);
        $this->db->update('uploads');

        if ($this->db->affected_rows() > 0)
        {
            $this->Presenter_Logger->log("Deleted", $presentation_id, $file_id);

            echo json_encode(array('status'=>'success', 'fileId'=>$file_id, 'msg'=>'File deleted'));

        }else{
            echo json_encode(array('status'=>'error', 'fileId'=>$file_id, 'msg'=>'Database error'));
        }

        return;
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
        $login_status = $this->session->userdata('uploads_login_status');
        if ($login_status != true)
        {
            echo 'You are not logged in.';
            return;
        }

        $this->db->select('*');
        $this->db->from('uploads');
        $this->db->where('id', $file_id);
        $this->db->where('presenter_id', $this->session->userdata('user_id'));
        $this->db->where('deleted', 0);

        $result = $this->db->get();

        if ($result->num_rows() > 0)
        {
            $this->Presenter_Logger->log("Downloaded", null, $file_id);

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

    public function sendFileReceiptEmail()
    {
        $post = $this->input->post();

        $this->load->config('email', TRUE);

        if (!$this->config->item('smtp_user', 'email'))
        {
            $response = array(
                'status' => 'failed',
                'msg' => "Send email option is not configured, please contact system administrator."
            );

            echo json_encode($response);

            return;
        }

        $email = $_SESSION['email'];

        $uploadedFiles = $post['uploadedFiles'];
        $deletedFiles = $post['deletedFiles'];

        $config = Array(
            'protocol' => $this->config->item('protocol', 'email'),
            'smtp_host' => $this->config->item('smtp_host', 'email'),
            'smtp_port' => $this->config->item('smtp_port', 'email'),
            'smtp_user' => $this->config->item('smtp_user', 'email'),
            'smtp_pass' => $this->config->item('smtp_pass', 'email'),
            'mailtype'  => $this->config->item('mailtype', 'email'),
            'charset'   => $this->config->item('charset', 'email')
        );
        $this->load->library('email', $config);

        $this->email->from('presentations@yourconference.live', 'COS ePoster Submission');
        $this->email->to($email);
        //$this->email->cc('athullive@gmail.com');
        //$this->email->bcc('them@their-example.com');

        $this->email->subject('Files have been changed');

        $email_body = file_get_contents(base_url()."upload_system_files/email_templates/file_receipt.php?uploadedFiles={$uploadedFiles}&deletedFiles={$deletedFiles}");

        $this->email->message($email_body);

        $result = $this->email->send();

        if ($result)
        {
            $response = array(
                'status' => 'success',
                'msg' => "Email was sent!"
            );
            echo json_encode($response);
        }else{

            $response = array(
                'status' => 'failed',
                'msg' => "Unable to send email!"
            );

            echo json_encode($response);
        }

        return;
    }

}
