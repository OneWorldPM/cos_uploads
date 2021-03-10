<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Presenter_Logger');
    }

    public function index()
    {
        $login_status = $this->session->userdata('uploads_login_status');
        if ($login_status === true)
            redirect(base_url('dashboard'));

        $this->load->view('presenter/head');
        $this->load->view('presenter/login');
        $this->load->view('presenter/foot');
    }

    public function verify()
    {
        $post = $this->input->post();

        $this->db->select('*');
        $this->db->from('presenter');
        $this->db->where("email", $post['email']);
        $this->db->where("password", $post['password']);
        $result = $this->db->get();

        if ($result->num_rows() > 0) {

            $this->session->set_userdata('uploads_login_status', true);
            $this->session->set_userdata('admin_login_status', false);
            $this->session->set_userdata('user_id', $result->row()->presenter_id);
            $this->session->set_userdata('email', $result->row()->email);
            $this->session->set_userdata('name_prefix', $result->row()->name_prefix);
            $this->session->set_userdata('first_name', $result->row()->first_name);
            $this->session->set_userdata('last_name', $result->row()->last_name);
            $this->session->set_userdata('fullname', $result->row()->first_name.' '.$result->row()->last_name);

            $this->Presenter_Logger->log("Login");

            echo json_encode(array('status'=>'success'));
            return;
        } else {
            echo json_encode(array('status'=>'error', 'msg'=>'Incorrect username or password'));
            return;
        }
    }

    public function resetPassword()
    {
        $login_status = $this->session->userdata('uploads_login_status');
        if ($login_status != true)
            echo json_encode(array('status'=>'error', 'msg'=>'You are not logged in'));

        $user_id = $this->session->userdata('user_id');
        $newPass = $this->input->post()['newPass'];

        $this->db->set('password', $newPass);
        $this->db->where('presenter_id', $user_id);
        $this->db->update('presenter');

        if($this->db->affected_rows() > 0){

            $this->Presenter_Logger->log("Password reset");
            $this->Presenter_Logger->log("Logout");

            $this->session->unset_userdata('uploads_login_status');
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('email');
            $this->session->unset_userdata('name_prefix');
            $this->session->unset_userdata('first_name');
            $this->session->unset_userdata('last_name');
            $this->session->unset_userdata('fullname');

            echo json_encode(array('status'=>'success', 'msg'=>'Your password is now reset, please login again'));
        }else{
            echo json_encode(array('status'=>'error', 'msg'=>'Unable to reset your password'));
        }

        return;


    }

    public function logout()
    {
        $this->Presenter_Logger->log("Logout");

        $this->session->unset_userdata('uploads_login_status');
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('name_prefix');
        $this->session->unset_userdata('first_name');
        $this->session->unset_userdata('last_name');
        $this->session->unset_userdata('fullname');
        header('location:' . base_url());
    }
}
