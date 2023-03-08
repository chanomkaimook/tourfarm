<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_stock extends MY_Controller
{

    private $model;
    private $path = 'asset/image/item/';
    private $path_barcode = 'asset/image/barcode/';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_item');
        $this->load->model('mdl_stock');
        $this->load->model('mdl_document');

        $this->middleware();

        // setting
        $this->model = $this->mdl_stock;
    }

    public function index()
    {
        $this->template->set_layout('lay_datatable');
        $this->template->title('คลังสินค้า');
        $this->template->build('stocks/index');
    }

    public function get_data()
    {
        $request = $_REQUEST;

        if ($request['hidden_datestart']) {
            $date = $request['hidden_datestart'];
            $__check_updatestock = false;
        } else {
            $date = date('Y-m-d');
            $__check_updatestock = true;
        }

        $date_search = $date;

        /**
         * 
         * todo เริ่มจากการหา รอบวันตัด stock และ วันตั้งต้น เพื่อเป็นค่าวันเริ่มต้น สำหรับคำนวณ
         * * เมื่อระยะเวลาปัจจุบันห่างจากจุดตัดล่าสุด 4 เดือน
         * * ระบบจะสร้างจุดตัดที่ห่าง 3 เดือนจากจุดตัดล่าสุด
         */
        if ($__check_updatestock === true) {
            $arrayset = array(
                'date'    => $date
            );
            $datestock = $this->model->check_dateCut($arrayset);

            $check_updatestock = $this->model->check_updateStock($date);
        }

        $this->load->helper('my_date');
        $data = $this->model->get_data();
        $data_result = [];

        if ($data) {
            foreach ($data as $row) {

                $img = "";
                if ($row->ITEM_PIC) {
                    $dataimage = array(
                        'data-id' => $row->ITEM_ID
                    );
                    $img = imageis(base_url($this->path), $row->ITEM_PIC, 'icon', $dataimage);
                }

                $barcode_img = "";
                if ($row->ITEM_BARCODE) {
                    $dataimage = array(
                        'data-id' => $row->ITEM_BARCODE,
                    );
                    $barcode_img = imageis(base_url($this->path_barcode), $row->ITEM_BARCODE . '.png', null, $dataimage);
                }

                $sub_data = [];

                $html_block1 = '<div class="avatar-md rounded">' . $img . '</div>';

                $block_name = '<div class="h4 mb-0">' . $row->ITEM_NAME . '</div>';
                $block_cost = '<div class="font-weight-bold cost">ราคาทุน ' . $row->ITEM_COST . ' บาท</div>';
                $block_barcode = '<div class="barcode">' . $row->ITEM_BARCODE . '</div>';
                // $block_barcode_img = '<div class="barcodeimg">' . $block_barcode_img . '</div>';
                $html_block2 = '<div class="row px-1"><div class="col">' . $block_name . $block_cost  . $block_barcode . '</div></div>';

                $html_item = '<div class="d-flex">' . $html_block1 . $html_block2 . '</div>';

                //
                // calculate

                // find date cut less then datesearch
                // for set date to start total
                /* $find_dateCut = $this->model->find_dateCut($date_search);
                if($find_dateCut){
                    $datebegin = $find_dateCut;
                }else{
                    $datebegin = $date_search;
                } */

                $datebegin = $date_search;
                $dateend = $date_search;

                //
                // filter
                // echo"<pre>";print_r($request);
                if (isset($request['hidden_datestart']) && $request['hidden_datestart']) {
                    $datebegin = $request['hidden_datestart'];
                    $dateend = $request['hidden_datestart'];
                }

                if (isset($request['hidden_dateend']) && $request['hidden_dateend']) {
                    $dateend = $request['hidden_dateend'];
                }
                //
                //

                //
                // item detail on stock
                // return array
                $item_detail = $this->model->item_detail_stock($row->ITEM_ID, $datebegin, $dateend);

                $node_import_total = $item_detail['node_import_total'];
                $node_issue_total = $item_detail['node_issue_total'];

                $hold_import_total = $item_detail['hold_import_total'];
                $hold_issue_total = $item_detail['hold_issue_total'];

                $item_total = $item_detail['item_total'];

                // net total
                $import_net_total = $item_detail['import_net_total'];
                $issue_net_total = $item_detail['issue_net_total'];
                $bill_total = $item_detail['bill_total'];

                $sub_data['ITEM'] = $html_item;
                $sub_data['TOTAL'] = $item_total;
                $sub_data['ID'] = $row->ITEM_ID;
                $sub_data['NAME'] = $row->ITEM_NAME;
                $sub_data['CATAGORY'] = $row->ITEM_CATAGORY_NAME;
                $sub_data['BARCODE'] = $row->ITEM_BARCODE;
                $sub_data['PIC'] = $img;

                $sub_data['SUP_IM_TOTAL'] = $node_import_total;
                $sub_data['SUP_IS_TOTAL'] = $node_issue_total;

                $sub_data['IMPORT_TOTAL'] = $import_net_total;
                $sub_data['ISSUE_TOTAL'] = $issue_net_total;

                $sub_data['HOLD_IM_TOTAL'] = $hold_import_total;
                $sub_data['HOLD_IS_TOTAL'] = $hold_issue_total;

                $sub_data['BILL_TOTAL'] = $bill_total;

                $sub_data['TEMP_TOTAL'] = $item_detail['temp_total'];
                $sub_data['NET_TOTAL'] = $item_detail['net_total'];


                $sub_data['STATUS_OFFVIEW'] = status_offview($row->ITEM_STATUS_OFFVIEW);
                $sub_data['CREATER'] = whois('id', $row->ITEM_USER_STARTS);
                $sub_data['DATE_STARTS'] = date('Y-m-d H:i:s', strtotime($row->ITEM_DATE_STARTS));
                $sub_data['DATE_STARTS_TEXT'] = toThaiDateTimeString($row->ITEM_DATE_STARTS, 'datetime');

                $sub_data['STOCK_MIN'] = $row->STOCK_MIN;
                $sub_data['STOCK_MAX'] = $row->STOCK_MAX;

                if ($this->input->get('zero')) {
                    if ($item_detail['temp_total'] != 0) {
                        $data_result[] = $sub_data;
                    }
                } else {
                    $data_result[] = $sub_data;
                }
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
            foreach ($data as $key => $val) {
                $result = array(
                    "data"  => $val
                );

                if ($val->ITEM_PIC) {
                    $img = '<img src="' . base_url($this->path . $val->ITEM_PIC) . '" alt="image" height=300 class="mw-100" >';
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
