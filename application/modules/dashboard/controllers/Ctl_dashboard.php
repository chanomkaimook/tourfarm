<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_bill');

        $this->middleware();
    }

    public function index()
    {
        $this->template->set_layout('lay_datatable');
        $this->template->title('Dashboard');
        $this->template->build('dashboard');
    }

    public function fetch_order()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $optional = [];

            if($this->input->post('hidden_datestart') && $this->input->post('hidden_dateend')){
                $optional['booking_date >='] = $this->input->post('hidden_datestart');
                $optional['booking_date <='] = $this->input->post('hidden_dateend');
            }else{
                $optional['booking_date'] = $this->input->post('hidden_datestart');
            }

            $total_order = 0;
            $total_customer= 0;
            $total_waite= 0;

            $get_data = $this->mdl_bill->get_data(null,$optional);

            $get_data_waite = $this->mdl_bill->get_dataShow_waite();

            if ($get_data) {

                $total_customer = 0;
                $total_waite = 0;

                foreach ($get_data as $row) {
                    if ($row->TOTALS) {
                        $total_customer += intval($row->TOTALS);
                    }

                    if ($row->PAYMENT_ID == 4) {     // 4=waite
                        $total_waite++;
                    }
                }
            }

            $result['total_order'] = count($get_data);
            $result['total_customer'] = $total_customer;
            $result['total_waite_today'] = $total_waite;
            $result['total_waite'] = count($get_data_waite);

            echo json_encode($result);
        }
    }
}
