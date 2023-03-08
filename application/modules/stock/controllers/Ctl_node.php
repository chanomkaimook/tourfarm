<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_node extends MY_Controller
{

    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_node');
        $this->load->model('mdl_nodecatagory');
        $this->load->library('image');

        $this->middleware();

        // setting
        $this->model = $this->mdl_node;
    }

    public function index()
    {
        $data['catagory'] = $this->mdl_nodecatagory->get_dataShow();

        $this->template->set_layout('lay_datatable');
        $this->template->title('ข้อมูลผู้ติดต่อ');
        $this->template->build('node/index',$data);
    }

    public function get_data()
    {
        $this->load->helper('my_date');
        $data = $this->model->get_data();

        $data_result = [];

        if ($data) {
            foreach ($data as $row) {

                if ($row->ITEM_DATE_UPDATE) {
                    $query_date = $row->ITEM_DATE_UPDATE;
                    $query_user = "(แก้) ".whois('id', $row->ITEM_USER_UPDATE);
                } else {
                    $query_date = $row->ITEM_DATE_STARTS;
                    $query_user =  whois('id', $row->ITEM_USER_STARTS);
                }

                $sub_data = [];

                $sub_data['ID'] = $row->ITEM_ID;
                $sub_data['NAME'] = $row->ITEM_NAME;
                $sub_data['CATAGORY'] = $row->ITEM_CATAGORY_NAME;
                $sub_data['STATUS_OFFVIEW'] = status_offview($row->ITEM_STATUS_OFFVIEW);
                $sub_data['CREATER'] = $query_user;
                $sub_data['DATE_STARTS'] = array(
                    "display"   => toThaiDateTimeString($query_date, 'datetime'),
                    "timestamp" => date('Y-m-d H:i:s', strtotime($query_date))
                );

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

    /**
     * get item data
     *
     * @return void
     */
    public function get_dataItem()
    {
        $this->load->helper('my_date');

        $request = $_REQUEST;
        $item_id = $request['id'];
        $data = $this->model->get_data($item_id);

        $result = [];

        if ($data) {
            foreach($data as $key => $val){
                $result = array(
                    "data"  => $val
                );
            }   
        }

        echo json_encode($result);
    }

    //
    // CRUD
    //
    // insert
    public function insert_data()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->model->insert_data();

            echo json_encode($returns);
        }
    }


    //
    // update
    public function update_data()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->model->update_data();

            echo json_encode($returns);
        }
    }

    //
    // delete
    public function delete_data()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->model->delete_data();

            echo json_encode($returns);
        } 
    }
}
