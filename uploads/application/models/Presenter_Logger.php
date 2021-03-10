<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Presenter_Logger extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function log($log_name, $ref_presentation_id=null, $other_ref=null)
    {
        $set = array(
            'presenter_id' => $this->session->userdata('user_id'),
            'log_name' => $log_name,
            'ref_presentation_id' => $ref_presentation_id,
            'other_ref' => $other_ref,
            "date_time" => date("Y-m-d H:i:s")
        );
        $this->db->insert("presenter_logs", $set);

        return true;
    }

    public function log_no_session($presenter_id, $log_name, $ref_presentation_id=null, $other_ref=null)
    {
        $set = array(
            'presenter_id' => $presenter_id,
            'log_name' => $log_name,
            'ref_presentation_id' => $ref_presentation_id,
            'other_ref' => $other_ref,
            "date_time" => date("Y-m-d H:i:s")
        );
        $this->db->insert("presenter_logs", $set);

        return true;
    }

}
