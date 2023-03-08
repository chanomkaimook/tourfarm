<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_calendars extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url', 'form');
        $this->load->model('mdl_calendar');
        $this->load->library('image');

        $this->middleware();
    }

	public function index()
	{

		$this->template->set_layout('lay_calendar');
        $this->template->title('ใบลาออนไลน์');	
        $this->template->build('calendar');
	}

    public function get_data()
    {
        $result = $this->mdl_calendar->get_data_calendar();

        $data = [
            "result" => $result,

        ];
        // echo "<pre>";
        // echo print_r($data);
        echo json_encode($data);
    }


    public function insert_data()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->mdl_calendar->insert_data_calendar();

            echo $returns;
        } else {
            echo "no";
        }
    }

    public function update_data()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // print_r($this->input->post());die;
            $returns = $this->mdl_calendar->update_data_calendar();

            echo $returns;   //จะส่งค่ากลับไปที่ view->table_data  ตรงส่วนนี้ jquery .then(res => res.json())  
        } else {
            echo "no";
        }
    }

    public function delete_data()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // print_r($this->input->post());die;
            $returns = $this->mdl_calendar->delete_data_calendar();

            echo $returns;   //จะส่งค่ากลับไปที่ view->table_data  ตรงส่วนนี้ jquery .then(res => res.json())  
        } else {
            echo "no";
        }
    }
    public function delete_image_data()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // print_r($this->input->post());die;
            $returns = $this->mdl_calendar->delete_image_data();

            echo $returns;   //จะส่งค่ากลับไปที่ view->table_data  ตรงส่วนนี้ jquery .then(res => res.json())  
        } else {
            echo "no";
        }
    }
}
