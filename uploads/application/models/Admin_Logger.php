<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Logger extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function log($log_name, $log_desc=null, $ref_presentation_id=null, $other_ref=null)
    {
        $set = array(
            'admin_id' => $this->session->userdata('user_id'),
            'log_name' => $log_name,
            'log_desc' => $log_desc,
            'ref_presentation_id' => $ref_presentation_id,
            'other_ref' => $other_ref,
            "date_time" => date("Y-m-d H:i:s")
        );
        $this->db->insert("admin_logs", $set);

        return true;
    }

    public function log_no_session($admin_id, $log_name, $ref_presentation_id=null, $other_ref=null)
    {
        $set = array(
            'presenter_id' => $admin_id,
            'log_name' => $log_name,
            'ref_presentation_id' => $ref_presentation_id,
            'other_ref' => $other_ref,
            "date_time" => date("Y-m-d H:i:s")
        );
        $this->db->insert("admin_logs", $set);

        return true;
    }

}
