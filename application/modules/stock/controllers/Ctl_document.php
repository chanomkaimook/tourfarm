<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_document extends MY_Controller
{

    private $model;
    private $path = 'asset/image/item/';
    private $path_barcode = 'asset/image/barcode/';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_item');
        $this->load->model('mdl_document');
        $this->load->model('mdl_node');
        $this->load->model('mdl_temp');
        $this->load->model('mdl_stock');
        $this->load->library(array('document'));

        $this->middleware();

        // setting
        $this->model = $this->mdl_document;
    }

    public function index()
    {
        $data['item'] = $this->mdl_item->get_dataShow();
        $data['node'] = $this->mdl_node->get_dataShowStore();

        $this->template->set_layout('lay_datatable');
        $this->template->title('รับเข้า/เบิกออก');
        $this->template->build('document/index', $data);
    }

    public function order()
    {
        $data['item'] = $this->mdl_item->get_dataShow();
        $data['node'] = $this->mdl_node->get_dataShowSupplier();


        $this->template->set_layout('lay_datatable');
        $this->template->title('สั่งซื้อสินค้า');
        $this->template->build('document/order', $data);
    }

    public function documentall()
    {
        $this->template->set_layout('lay_datatable');
        $this->template->title('เอกสารทั้งหมด');
        $this->template->build('document/documentall');
    }

    public function fetch_datatemp()
    {
        $returns = $this->mdl_temp->get_dataShow();

        echo json_encode($returns);
    }

    public function get_docTemp()
    {
        $returns = $this->mdl_temp->get_docTemp();

        echo json_encode($returns);
    }

    public function get_data()
    {
        $this->load->helper('my_date');
        $data = $this->model->get_dataShow();

        $data_result = [];

        if ($data) {
            foreach ($data as $row) {
                $sub_data = [];

                $doc_alias_name = $this->document->get_documentAliasFromCode($row->DOC_TABLE_CODE);
                $doc_table_name = $this->document->get_documentTableItemFromCode($row->DOC_TABLE_CODE);

                if ($row->DATE_UPDATE) {
                    $query_date = $row->DATE_UPDATE;
                    $query_user = "(แก้) " . whois('id', $row->USER_UPDATE);
                } else {
                    $query_date = $row->DATE_STARTS;
                    $query_user =  whois('id', $row->USER_STARTS);
                }

                $temp_datetime = toThaiDateTimeString($query_date, 'datetime');
                $explode = explode(" ", $temp_datetime);
                $time = $explode[3];

                $datetime = toThaiDateTimeString($query_date, 'date') . "<p>" . $time . "</p>";

                $datecut = $this->mdl_stock->find_dateCut(date('Y-m-d'));

                //
                // received
                $received = null;
                $received_text = $row->TOTAL;
                $sql_rc = $this->db->select_sum('TOTAL')
                    ->where('doc_table_item_id', $row->DOC_NODE_ID)
                    ->where('status', 1)
                    ->get('doc_node_item_list');
                $r_rc = $sql_rc->row();
                if ($r_rc->TOTAL) {
                    $received = $r_rc->TOTAL;
                    $received_text = $row->TOTAL . " ( " . $r_rc->TOTAL . "/" . $row->TOTAL . " )";
                }

                $sub_data['RECEIVED'] = $received;
                $sub_data['DATECUT'] = $datecut;
                $sub_data['ID'] = $row->ID;
                $sub_data['CODE'] = $row->DOC_TABLE_CODE;
                $sub_data['DOC_NODE_ID'] = $row->DOC_NODE_ID;
                $sub_data['TABLE'] = $doc_table_name;
                $sub_data['ITEM'] = $row->ITEM_NAME;
                $sub_data['ITEM_ID'] = $row->ITEM_ID;
                $sub_data['TOTAL'] = $received_text;
                $sub_data['TOTAL_ONLY'] = intval($row->TOTAL);
                $sub_data['TOTAL_RECEIVED_ONLY'] = intval($r_rc->TOTAL);
                $sub_data['COMPLETE_ALIAS'] = $doc_alias_name;
                $sub_data['NODE_NAME'] = $row->NODE_NAME;
                $sub_data['REMARK'] = $row->REMARK;
                $sub_data['CREATER'] = $query_user;
                $sub_data['DATE_STARTS'] = array(
                    "display"   => $datetime,
                    "timestamp" => date('Y-m-d H:i:s', strtotime($query_date)),
                    "date_starts" => date('Y-m-d H:i:s', strtotime($row->DATE_STARTS)),
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
     * get item data only
     *
     * @return void
     */
    public function get_dataItemPure()
    {
        $this->load->helper('my_date');

        $request = $_REQUEST;
        $item_id = isset($request['item_id']) ? trim($request['item_id']) : "";
        $item_barcode = isset($request['item_barcode']) ? trim($request['item_barcode']) : "";

        if ($item_barcode) {
            $data = $this->mdl_item->get_data(null, array('barcode' => $item_barcode));
        } else {
            $data = $this->mdl_item->get_data($item_id);
        }

        $result = [];

        if ($data) {
            foreach ($data as $key => $val) {

                $result = array(
                    "data"  => $val
                );

                // image barcode
                if ($val->ITEM_BARCODE) {
                    $img_barcode = '<img src="' . base_url($this->path_barcode . $val->ITEM_BARCODE . '.png') . '" alt="image" height=40 class="mw-100" >';
                    $result['data']->ITEM_BARCODE_IMG = $img_barcode;
                }
            }
        }

        echo json_encode($result);
    }


    /**
     * get item data only
     *
     * @return void
     */
    public function get_dataItem()
    {
        $this->load->helper('my_date');

        $request = $_REQUEST;
        $id = isset($request['id']) ? trim($request['id']) : "";
        $item_id = isset($request['item_id']) ? trim($request['item_id']) : "";
        $table = isset($request['table']) ? trim($request['table']) : "";

        $data = $this->mdl_item->get_data($item_id);

        $optionnal = array(
            'id'    => $id,
            'item_id'    => $item_id
        );
        $data_table = $this->model->get_dataTable(null, $table, $optionnal);

        $result = [];

        if ($data) {
            foreach ($data as $key => $val) {
                $result = array(
                    "data"  => $val
                );

                // image barcode
                if ($val->ITEM_BARCODE) {
                    $img_barcode = '<img src="' . base_url($this->path_barcode . $val->ITEM_BARCODE . '.png') . '" alt="image" height=40 class="mw-100" >';
                    $result['data']->ITEM_BARCODE_IMG = $img_barcode;
                }
            }


            if ($data_table) {
                foreach ($data_table as $key => $val) {
                    if ($val->ITEM_ID == $item_id) {
                        $result['data']->TOTAL = $val->TOTAL;
                        $result['data']->NODE_ID = $val->NODE_ID;
                        $result['data']->NODE_NAME = $val->NODE_NAME;
                        $result['data']->REMARK = $val->REMARK;
                        $result['data']->TEMP = $val->TEMP;
                    }
                }
            }
        }

        echo json_encode($result);
    }

    /**
     * get item in document
     *
     * @return void
     */
    public function fetch_itemdoc()
    {
        $request = $_REQUEST;
        $result = [];
        $set_array = [];
        $optionnal = [];

        $this->load->helper('my_date');

        $table = isset($request['table']) ? trim($request['table']) : "";
        if($table == 'doc_waite'){
            $optionnal = array(
                'complete'  => 1,
                'doc_type'  => 'in',
            );
            $data_table = $this->model->get_dataTable(null, 'doc_node_item',$optionnal);
        }else{
            $data_table = $this->model->get_dataTable(null, $table);
        }

        if ($data_table) {
            foreach ($data_table as $row) {
                $set_array[] = $row;
            }
        }

        switch ($table) {
            case 'doc_issue_item':
                $data_lost = $this->model->get_dataTable(null, 'doc_lost_item');
                if ($data_lost) {
                    foreach ($data_lost as $row) {
                        $set_array[] = $row;
                    }
                }

                $optionnal['doc_type'] = 'out';
                $data_node = $this->model->get_dataTable(null, 'doc_node_item_list', $optionnal);
                if ($data_node) {
                    foreach ($data_node as $row) {
                        $set_array[] = $row;
                    }
                }
                break;
            case 'doc_import_item':
                $optionnal['doc_type'] = 'in';
                $data_node = $this->model->get_dataTable(null, 'doc_node_item_list', $optionnal);
                if ($data_node) {
                    foreach ($data_node as $row) {
                        $set_array[] = $row;
                    }
                }
                break;
            default:
                break;
        }



        $result_temp = $set_array;

        //
        // sort value 
        if ($set_array && count($set_array)) {
            $sort = [];
            foreach ($set_array as $key => $array) {
                $sort[$key] = $array->DATE_STARTS;
            }

            if ($sort && count($sort)) {
                $result_temp = [];
                arsort($sort);
                foreach ($sort as $key => $index) {
                    $result_temp[] = $set_array[$key];
                }
            }
        }

        if ($result_temp) {

            foreach ($result_temp as $row) {
                $sub = [];
                $sub = $row;
                if ($row->DATE_UPDATE) {
                    $query_date = $row->DATE_UPDATE;
                    $query_user = "(แก้) " . whois('id', $row->USER_UPDATE);
                } else {
                    $query_date = $row->DATE_STARTS;
                    $query_user =  whois('id', $row->USER_STARTS);
                }
                $sub->REMARK = $row->REMARK ? $row->REMARK : '';
                $sub->NODE_NAME = $row->NODE_NAME ? $row->NODE_NAME : '';
                $sub->CREATER = $query_user;
                $sub->DATE_STARTS_TEXT = toThaiDateTimeString($row->DATE_STARTS, 'datet');
                // $sub[] = (object) array('CREATER',$query_user);
                // $sub[] = (object) array('DATE_STARTS_TEXT',toThaiDateTimeString($row->DATE_STARTS, 'datetime'));
                $result[] = $sub;
            }
        }

        echo json_encode($result);
    }

    public function insert_item()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->model->insert_item();

            echo json_encode($returns);
        }
    }

    public function insert_data()
    {
        $result = array(
            'error' => 1,
            'txt'   => 'ไม่มีการทำรายการ'
        );

        $array = $this->model->get_listTemp();

        $returns = $this->document->insert_data($array);
        if ($returns) {

            // clear data temp item
            $this->model->clear_data();

            $result = array(
                'error' => $returns['error'],
                'txt'   => $returns['txt']
            );
        }

        echo json_encode($result);
    }

    //
    // update
    public function update_data()
    {
        # cod...
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

    public function delete_dataTemp()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->model->delete_dataTemp();

            echo json_encode($returns);
        }
    }

    //
    // clear
    public function clear_data()
    {
        # code...
        $returns = $this->model->clear_data();

        echo json_encode($returns);
    }
}
