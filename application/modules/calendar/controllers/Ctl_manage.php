<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_manage extends MY_Controller
{

    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_bill');
        $this->load->model('mdl_round');
        $this->load->model('mdl_round_time');

        $this->middleware();

        // setting
        $this->model = $this->mdl_bill;
    }

    public function index()
    {
        $data['time'] = $this->mdl_round_time->get_data();
        $data['round'] = $this->mdl_round->get_data();

        $this->template->set_layout('lay_calendar');
        $this->template->title('จัดการรอบเข้าชม');
        $this->template->set_partial('headlink', 'partials/link/touchspin');
        $this->template->set_partial('footerscript', 'partials/script/touchspin');
        $this->template->build('manage/index', $data);
    }

    public function datatable()
    {
        $data['time'] = $this->mdl_round_time->get_data();
        $data['round'] = $this->mdl_round->get_data();

        $this->template->set_layout('lay_datatable');
        $this->template->title('ตารางจอง');
        $this->template->set_partial('headlink', 'partials/link/touchspin');
        $this->template->set_partial('footerscript', 'partials/script/touchspin');
        $this->template->build('datatable/index', $data);
    }

    public function get_data()
    {
        $this->load->helper('my_date');
        // $data = $this->model->get_data();

        $request = $_REQUEST;

        if ($this->input->get('dashboard')) {

            $hidden_start = "";
            $hidden_end = "";
            if($this->input->get('hidden_datestart') || $this->input->post('hidden_datestart')){
                $hidden_start = $this->input->get('hidden_datestart') ? $this->input->get('hidden_datestart') : $this->input->post('hidden_datestart');
            }
            if($this->input->get('hidden_dateend') || $this->input->post('hidden_dateend')){
                $hidden_end = $this->input->get('hidden_dateend') ? $this->input->get('hidden_dateend') : $this->input->post('hidden_dateend');
            }

            if (!$hidden_start && !$hidden_end) {
                $data = $this->model->get_dataShowToday();
            } else {
                $data = $this->model->get_dataShow();
            }
        } else {
            $data = $this->model->get_dataShow();
        }


        $data_result = [];

        if ($data) {
            foreach ($data as $row) {

                if ($row->DATE_UPDATE) {
                    $query_date = $row->DATE_UPDATE;
                    $query_user = "(แก้) " . whois('id', $row->USER_UPDATE);
                } else {
                    $query_date = $row->DATE_STARTS;
                    $query_user =  whois('id', $row->USER_STARTS);
                }

                if ($row->PAYMENT_ID) {
                    switch ($row->PAYMENT_ID) {
                        case 4:    // waite
                            $html_payment_arias = '<span class="text-warning">' . $row->PAYMENT_ALIAS . '</span>';
                            break;
                        case 5:    // paid
                            $html_payment_arias = '<span class="text-success">' . $row->PAYMENT_ALIAS . '</span>';
                            break;
                        default:
                            $html_payment_arias = '<span class=""></span>';
                            break;
                    }
                }
                $detail_name = "ลูกค้า: " . $row->CUSTOMER_NAME .
                    "<br>จำนวน: " . $row->TOTALS .
                    "<br>หมายเหตุ: " . $row->REMARK .
                    "<br>โดย: " . $query_user .
                    "<br>สถานะ: " . $html_payment_arias;

                $sub_data = [];

                $sub_data['ID'] = $row->ID;
                $sub_data['NAME'] = $row->CUSTOMER_NAME;
                $sub_data['DETAIL_NAME'] = $detail_name;
                $sub_data['AGENT_NAME'] = $row->AGENT_NAME;
                $sub_data['AGENT_CONTACT'] = $row->AGENT_CONTACT;
                $sub_data['TOTALS'] = $row->TOTALS;
                $sub_data['REMARK'] = $row->REMARK;
                $sub_data['COMPLETE_ALIAS'] = $row->COMPLETE_ALIAS;
                $sub_data['PAYMENT_ID'] = $row->PAYMENT_ID;
                $sub_data['PAYMENT_ALIAS'] = $row->PAYMENT_ALIAS;
                $sub_data['ROUND_ID'] = $row->ROUND_ID;
                $sub_data['BOOKING_DATE'] = array(
                    "display"   =>  $row->BOOKING_DATE ? toThaiDateTimeString($row->BOOKING_DATE, 'date') : '',
                    "data"      =>  $row->BOOKING_DATE ? $row->BOOKING_DATE : '',
                    "timestamp" => $row->BOOKING_DATE ? date('Y-m-d', strtotime($row->BOOKING_DATE)) : ''
                );
                $sub_data['ROUND_NAME'] = $row->ROUND_NAME;
                $sub_data['TIME_START'] = array(
                    "display"   => toTime($row->TIME_START, 'H:i'),
                    "data"      => $row->TIME_START,
                );
                $sub_data['TIME_END'] = array(
                    "display"   => toTime($row->TIME_END, 'H:i'),
                    "data"      => $row->TIME_END,
                );
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
            foreach ($data as $key => $val) {
                $result = array(
                    "data"  => $val
                );
            }
        }

        echo json_encode($result);
    }


    /**
     * data customer
     */
    public function fetch_customer()
    {
        # code...
        $this->load->model('mdl_customer');

        $returns = $this->mdl_customer->get_dataShow();

        echo json_encode($returns);
    }

    public function fetch_bill()
    {
        # code...
        $this->load->model('mdl_bill');

        $returns = $this->mdl_bill->get_dataShow();

        echo json_encode($returns);
    }

    public function fetch_bill_booking()
    {
        # code...
        $this->load->model('mdl_bill');

        $returns = $this->mdl_bill->get_dataShow_booking();

        echo json_encode($returns);
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
    public function update_bill_booking()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $item_id = $this->input->post('item_id');
            $booking_date = $this->input->post('booking_date');

            $sql = $this->db->from('doc_bill')
                ->where('id', $item_id)
                ->get();
            $num = $sql->num_rows();
            if ($num) {
                $row = $sql->row();
                $customer_id = $row->CUSTOMER_ID;

                $returns = $this->model->update_bill_booking($item_id, $customer_id, $booking_date);

                echo json_encode($returns);
            }
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

    //
    // cancel event
    public function cancel_event()
    {
        # code...
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $returns = $this->model->cancel_event();

            echo json_encode($returns);
        }
    }
}
