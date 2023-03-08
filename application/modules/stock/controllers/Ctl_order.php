<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_order extends MY_Controller
{

    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_item');
        $this->load->model('mdl_node');
        $this->load->model('mdl_stock');

        $this->load->model('mdl_order');
        $this->load->library(array('document'));

        $this->middleware();

        // setting
        $this->model = $this->mdl_order;
    }

    public function index()
    {
        $data['item'] = $this->mdl_item->get_dataShow();
        $data['node'] = $this->mdl_node->get_dataShowSupplier();


        $this->template->set_layout('lay_datatable');
        $this->template->title('สั่งซื้อสินค้า');
        $this->template->build('document/order', $data);
    }

    public function get_data()
    {
        $this->load->helper('my_date');
        $data = $this->model->get_dataShow();

        $data_result = [];

        if ($data) {
            foreach ($data as $key => $row) {

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
                    ->where('doc_table_id', $row->DOC_NODE_ID)
                    ->where('status', 1)
                    ->get('doc_node_item_list');
                $r_rc = $sql_rc->row();
                if ($r_rc->TOTAL) {
                    $received = $r_rc->TOTAL;
                    $received_text = $row->TOTAL . " ( " . $r_rc->TOTAL . "/" . $row->TOTAL . " )";
                }

                $sql_node = $this->db->select('ID')
                    ->where('doc_table_id', $row->DOC_NODE_ID)
                    ->get('doc_node_item');
                $row_node = $sql_node->row();

                $sub_data['RECEIVED'] = $received;
                $sub_data['DATECUT'] = $datecut;
                $sub_data['ID'] = $row->ID;
                $sub_data['CODE'] = $row->DOC_TABLE_CODE;

                $sub_data['DOC_NODE_ID'] = $row_node->ID;
                
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
}
