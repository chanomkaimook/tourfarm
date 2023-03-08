<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_register extends CI_Model

{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data_staff()
    {
        $query = $this->db->select('*')
            ->where('verify is null')
            ->where('status', 1)
            ->get('staff');

        return $query->result();
    }

    public function update_verify()
    {
        $result = '';
        if ($this->input->post('id')) {

            $result = array(
                'error' => 0,
                'text' =>  "ยืนยันสำเร็จ",
            );

            $data_update = array(
                'verify' => $this->session->userdata('user_code')
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('staff', $data_update);

            // keep log
            log_data(array('update', 'update', $this->db->last_query()));
        }
        return $result;
    }
}
