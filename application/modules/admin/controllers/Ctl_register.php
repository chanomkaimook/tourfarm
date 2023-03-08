<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_register extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_register');

        $this->middleware();
    }

    public function index()
    {
        // clear data less
        $this->clear_data();

        $this->template->set_layout('lay_datatable');
        $this->template->title('ลงทะเบียน');
        $this->template->build('register');
    }

    public function fetch_data()
    {
        $this->load->helper('my_date');
        $data = $this->mdl_register->get_data_staff();

        $data_result = [];

        if ($data) {
            foreach ($data as $row) {
                $sub_data = [];

                $sub_data['ID'] = $row->ID;
                $sub_data['ROLE'] = $row->ROLE;
                $sub_data['NAME'] = $row->NAME;
                $sub_data['LASTNAME'] = $row->LASTNAME;
                $sub_data['USERNAME'] = $row->USERNAME;
                $sub_data['DATE_START'] = $row->DATE_START;
                $sub_data['DATE_START_TEXT'] = toThaiDateTimeString($row->DATE_START, 'datetime');
                $sub_data['VERIFY'] = $row->VERIFY;

                $data_result[] = $sub_data;
            }
        }

        $result = array(
            "recordsTotal"      =>     count($data),
            "recordsFiltered"   =>     count($data),
            "data"              =>     $data_result
        );

        echo json_encode($result);
    }

    public function update_verify()
    {
        $error = 1;
        $message = 'ไม่พบรายการ';

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
            // log_data(array('update', 'update', $this->db->last_query()));

            $error = 0;
            $message = 'ยืนยันตัวตนแล้ว ';
        }

        $result = array(
            'error' => $error,
            'message' => $message
        );
        echo json_encode($result);
    }

    public function clear_data()
    {
        # code...
        $sql = $this->db->select('ID')
            ->from('staff')
            ->where('date(date_start) >= (date_add(CURDATE(),INTERVAL 1 day))', null, false)
            ->get();
        if ($sql) {
            $this->db->where('date(date_start) >= (date_add(CURDATE(),INTERVAL 1 day))', null, false);
            $this->db->delete('staff');

            // keep log
            log_data(array('delete', 'delete', $this->db->last_query()));
        }
    }
}
