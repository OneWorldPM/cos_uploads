<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email extends CI_Controller
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

        $this->load->view('admin/email');

        $this->load->view('admin/models/change-password');
        $this->load->view('admin/models/email-preview');
        $this->load->view('admin/models/email-edit');
        $this->load->view('admin/models/email-add');

        $this->load->view('admin/models/email-to-custom');
        $this->load->view('admin/models/email-to-all');

        $this->load->view('admin/foot');
    }

    public function getAllTemplates()
    {
        $this->db->select("*");
        $this->db->where("active", 1);
        $this->db->from('email_templates');
        $result = $this->db->get();

        if ($result->num_rows() > 0)
        {
            echo json_encode(array('status'=>'success', 'data'=>$result->result()));
            return;
        } else {
            echo json_encode(array('status'=>'error', 'msg'=>'Unable to load email templates'));
            return;
        }
    }

    public function getTemplateById($id)
    {
        $this->db->select("*");
        $this->db->from('email_templates');
        $this->db->where('id', $id);
        $result = $this->db->get();

        if ($result->num_rows() > 0)
        {
            echo json_encode(array('status'=>'success', 'data'=>$result->result()[0]));
            return;
        } else {
            echo json_encode(array('status'=>'error', 'msg'=>'Unable to load email templates'));
            return;
        }
    }

    public function editEmailTemplate()
    {
        $post = $this->input->post();

        $id = $post['id'];
        $subject = $post['subject'];
        $content = $post['content'];

        $data = array(
            'subject' => $subject,
            'content'  => $content,
            'updated_on'  => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('user_id')
        );


        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('email_templates');

        if($this->db->affected_rows() > 0){
            $this->Admin_Logger->log("Edited email template", null, null, $id);
            echo json_encode(array('status'=>'success'));
        }else{
            echo json_encode(array('status'=>'error'));
        }

        return;
    }

    public function addEmailTemplate()
    {
        $post = $this->input->post();

        $subject = $post['subject'];
        $content = $post['content'];

        $data = array(
            'subject' => $subject,
            'content'  => $content,
            'created_on'  => date("Y-m-d H:i:s"),
            'created_by' => $this->session->userdata('user_id')
        );


        $this->db->set($data);
        $this->db->insert('email_templates');

        if($this->db->affected_rows() > 0){
            $id = $this->db->insert_id();
            $this->Admin_Logger->log("Added email template", null, null, $id);
            echo json_encode(array('status'=>'success', 'templateId'=>$id));
        }else{
            echo json_encode(array('status'=>'error'));
        }

        return;
    }

    public function sendToCustomEmail()
    {
        $post = $this->input->post();
        $email = $post['toEmail'];
        $template_id = $post['templateId'];
        $subject = $post['subject'];
        $content = $post['content'];

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

        $this->email->subject($subject);

        /** Get email template */
        $url = base_url()."upload_system_files/email_templates/common_template.php";
        $fields = array(
            'content'=>$content);
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $email_body = curl_exec($ch);

        $this->email->message($email_body);

        $result = $this->email->send();

        if ($result)
        {
            $this->Admin_Logger->log("Sent email", $email, null, $template_id);
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

    public function sendToAll()
    {
        $post = $this->input->post();
        $template_id = $post['templateId'];
        $subject = $post['subject'];
        $content = $post['content'];

        $emailList = $this->getAllPresenterEmails();

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
        $this->email->to('presentations@yourconference.live');
        //$this->email->cc('athullive@gmail.com');
        $this->email->bcc($emailList);

        $this->email->subject($subject);

        /** Get email template */
        $url = base_url()."upload_system_files/email_templates/common_template.php";
        $fields = array(
            'content'=>$content);
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $email_body = curl_exec($ch);

        $this->email->message($email_body);

        $result = $this->email->send();

        if ($result)
        {
            $this->Admin_Logger->log("Sent bulk email", json_encode($emailList), null, $template_id);
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

    public function getAllPresenterEmails($print=false)
    {
        $this->db->select("email");
        $this->db->from('presenter');
        //$this->db->where_in('presenter_id', array(81, 79, 78));
        $result = $this->db->get();

        $emails =array();
        if ($result->num_rows() > 0)
        {
            foreach ($result->result() as $row)
                $emails[] = $row->email;

            if ($print)
            {
                echo json_encode($emails);
                return;
            }else{
                return $emails;
            }
        } else {
            if ($print)
            {
                echo json_encode(array());
                return;
            }else{
                return array();
            }
        }
    }

    public function disableTemplate($id)
    {
        $this->db->set('active', 0);
        $this->db->where('id', $id);
        $this->db->update('email_templates');

        if($this->db->affected_rows() > 0){
            $this->Admin_Logger->log("Removed email template", null, null, $id);
            echo json_encode(array('status'=>'success'));
        }else{
            echo json_encode(array('status'=>'error'));
        }

        return;
    }
}
