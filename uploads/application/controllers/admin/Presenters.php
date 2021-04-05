<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Presenters extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $login_status = $this->session->userdata('admin_login_status');
        if ($login_status != true)
            redirect(base_url('admin'));

        $this->load->model('Admin_Logger');
        $this->load->model('admin/Presenter_model','m_presenter');
    }

    public function index()
    {
        $this->load->view('admin/head');
        $this->load->view('admin/models/presenter');
        $this->load->view('admin/presenter');
        $this->load->view('admin/foot');
    }

    public function getPresenters(){
        $result=$this->m_presenter->getPresenters();
        if($result){
            echo json_encode(array('status'=>'success', 'data'=>$result->result()));
        }else{
            echo json_encode(array('status'=>'error', 'msg'=>'Database error'));
        }

    }
    public function update_presenter(){

        $post = $this->input->post();

        $result= $this->m_presenter->updatePresenter($post);
        if($result){
            echo json_encode(array('status'=>'success','msg'=>'Presenter Updated'));
        }else{
            echo json_encode(array('status'=>'error', 'msg'=>'Database error'));
        }
    }

}