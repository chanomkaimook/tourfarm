<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_profile extends MY_Controller
{
    public $test;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_profile');

        $this->middleware();
        
    }

    public function index()
    {
        $sql_owner = $this->db->from('employee')->where('status', 1)->get();

        $data = [
         'data' => (array)$this->mdl_profile->get_data_staff(),
         'sql_owner' => $sql_owner->result(),
        ];

        $this->template->set_layout('lay_main');
        $this->template->title('ประวัติส่วนตัว');
        $this->template->build('profile',$data);
    }


    public function update_profile()
    {
        $data = $this->mdl_profile->update_profile();
        $result = array(
         'data' => $data
        );
        
        echo json_encode($result);
    }

    public function get_user_owner()
    {
        $data = $this->mdl_profile->get_user_owner();
       $result = array(
        'data' => $data
       );
       echo json_encode($result);
    }

}
