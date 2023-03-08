<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_item extends MY_Controller
{

    private $model;
    private $path = 'asset/image/item/';
    private $path_barcode = 'asset/image/barcode/';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_catagory');
        $this->load->model('mdl_item');
        $this->load->library('image');

        $this->middleware();

        // setting
        $this->model = $this->mdl_item;
    }

    public function index()
    {
        $data['catagory'] = $this->mdl_catagory->get_dataShow();

        $this->template->set_layout('lay_datatable');
        $this->template->title('ข้อมูลสินค้า');
        $this->template->build('item/index',$data);
    }

    public function get_data()
    {
        $this->load->helper('my_date');
        $data = $this->model->get_data();

        $data_result = [];

        if ($data) {
            foreach ($data as $row) {

                $img = "";
                if($row->ITEM_PIC){
                    $dataimage = array(
                        'data-id' => $row->ITEM_ID
                    );
                    $img = imageis(base_url($this->path),$row->ITEM_PIC,'icon',$dataimage);
                }
                
                $block_barcode_img = "";
                if ($row->ITEM_BARCODE) {
                    $dataimage = array(
                        'data-id' => $row->ITEM_BARCODE,
                    );
                    $block_barcode_img = imageis(base_url($this->path_barcode), $row->ITEM_BARCODE . '.png', null, $dataimage);
                }

                $item = "<div><span>".$row->ITEM_NAME."</span><br><div class=\"barcodeimg\">".$block_barcode_img."</div></div>";

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
                $sub_data['ITEM'] = $item;
                $sub_data['CATAGORY'] = $row->ITEM_CATAGORY_NAME;
                $sub_data['BARCODE'] = $row->ITEM_BARCODE;
                $sub_data['COST'] = $row->ITEM_COST;
                $sub_data['PIC'] = $img;
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
               
                if($val->ITEM_PIC){
                    $img = '<img src="'.base_url($this->path.$val->ITEM_PIC).'" alt="image" height=300 class="mw-100" >';
                    $result['data']->ITEM_PIC_TEMP = $img;
                }

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
