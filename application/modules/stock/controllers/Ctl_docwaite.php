<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_docwaite extends MY_Controller
{

    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_item');
        $this->load->model('mdl_catagory');
        $this->load->model('mdl_document');
        $this->load->model('mdl_docnode');
        $this->load->model('mdl_stock');
        $this->load->library(array('document'));

        $this->middleware();

        // setting
        $this->model = $this->mdl_docnode;
    }

    public function index()
    {

        $this->template->set_layout('lay_datatable');
        $this->template->title('เอกสารที่รอตรวจสอบ');
        $this->template->build('docwaite/index');
    }

    public function get_data()
    {
        $this->load->helper('my_date');
        $data = $this->model->get_dataShow();

        $data_result = [];

        if ($data) {
            foreach ($data as $row) {
                $sub_data = [];

                $doc_alias_name = $this->document->get_documentAlias($row->DOC_ALIAS);
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

                $doc_type_text = $this->document->get_docTypeText($row->DOC_TYPE);

                //
                // received
                $received = null;
                $received_text = $row->TOTAL;
                $sql_rc = $this->db->select_sum('TOTAL')
                    ->where('doc_table_item_id', $row->ID)
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
                $sub_data['TABLE'] = $doc_table_name;
                $sub_data['ITEM'] = $row->ITEM_NAME;
                $sub_data['ITEM_ID'] = $row->ITEM_ID;
                $sub_data['TOTAL'] = $received_text;
                $sub_data['TOTAL_ONLY'] = intval($row->TOTAL);
                $sub_data['TOTAL_RECEIVED_ONLY'] = intval($r_rc->TOTAL);
                $sub_data['COMPLETE_ALIAS'] = $doc_alias_name;
                $sub_data['COMPLETE_ID'] = intval($row->COMPLETE);
                $sub_data['NODE_NAME'] = $row->NODE_NAME;
                $sub_data['REMARK'] = $row->REMARK;
                $sub_data['DOC_TYPE_TEXT'] = $doc_type_text;
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
            }
        }

        echo json_encode($result);
    }

    /**
     * get item data
     *
     * @return void
     */
    public function get_datadetail()
    {
        $this->load->helper('my_date');

        $request = $_REQUEST;
        $item_id = $request['id'];
        $optional = array(
            'doc_table_item_id'   => $item_id
        );
        $data = $this->model->get_data_detail(null, $optional);

        $result = [];

        if ($data) {

            $index = 0;
            foreach ($data as $key => $val) {
                $result['data'][$index] = $val;

                if ($val->DATE_UPDATE) {
                    $query_date = $val->DATE_UPDATE;
                    $query_user = "(แก้) ".whois('id', $val->USER_UPDATE);
                } else {
                    $query_date = $val->DATE_STARTS;
                    $query_user =  whois('id', $val->USER_STARTS);
                }

                $result['data'][$index]->REMARK = $val->REMARK ? $val->REMARK : '';
                $result['data'][$index]->USERNAME = $query_user;
                $result['data'][$index]->DATE_STARTS_TEXT = toThaiDateTimeString($query_date, 'datetime');

                $index++;
            }
        }

        echo json_encode($result);
    }

    //
    // CRUD
    //
    // insert

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
    public function delete_data_list()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->model->delete_data_list();

            echo json_encode($returns);
        }
    }

    //
    // restore
    public function restore_dataItem()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'GET') {

            $returns = $this->model->restore_dataItem();

            echo json_encode($returns);
        }
    }
}
