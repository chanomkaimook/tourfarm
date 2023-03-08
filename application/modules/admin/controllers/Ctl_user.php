<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_user extends MY_Controller
{
    public $test;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_user');

        $this->middleware();
    }

    public function index()
    {

        $this->template->set_layout('lay_datatable');
        $this->template->title('ผู้ใช้งาน');
        $this->template->build('users');
    }

    public function fetch_data()
    {
        $this->load->helper('my_date');
        $data = $this->mdl_user->get_data_staff();

        $data_result = [];
        if ($data) {
            foreach ($data as $row) {
                $sub_data = [];

                $date_start = toThaiDateTimeString($row->DATE_START, 'datetime');

                $sub_data['ID'] = $row->ID;
                $sub_data[] = $row->ROLE;
                $sub_data[] = $row->NAME;
                $sub_data[] = $row->LASTNAME;
                $sub_data[] = $row->USERNAME;
                $sub_data[] = $date_start;
                $sub_data[] = $row->VERIFY;

                $data_result[] = $sub_data;
            }
        }
        $result = array(
            'data' => $data_result
        );

        echo json_encode($result);
    }

    public function get_user()
    {
        $data = $this->mdl_user->get_user();
        $result = array(
            'data' => $data
        );
        echo json_encode($result);
    }

    public function update_user()
    {
        $data = $this->mdl_user->update_user();
        $result = array(
            'data' => $data
        );

        echo json_encode($result);
    }

    public function delete_user()
    {
        $data = $this->mdl_user->delete_user();
        $result = array(
            'data' => $data
        );

        echo json_encode($result);
    }
}
