<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_bill extends CI_Model

{
    private $table = "doc_bill";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data(int $id = null, array $optionnal = [])
    {
        $sql = $this->db->select('*');
        if ($id) {
            $sql->where('id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where('status', 1);
        $query = $sql->get($this->table);

        return $query->result();
    }

    public function get_dataShow()
    {
        $optional = [];

        $datebegin = null;
        $dateend = null;
        if($this->input->get('hidden_datestart') || $this->input->post('hidden_datestart')){
            $datebegin = $this->input->get('hidden_datestart') ? $this->input->get('hidden_datestart') : $this->input->post('hidden_datestart');
        }
        if($this->input->get('hidden_dateend') || $this->input->post('hidden_dateend')){
            $dateend = $this->input->get('hidden_dateend') ? $this->input->get('hidden_dateend') : $this->input->post('hidden_dateend');
        }

        if ($datebegin && $dateend) {
            $optional['date(' . $this->table . '.booking_date) >='] = $datebegin;
            $optional['date(' . $this->table . '.booking_date) <='] = $dateend;
        } else if ($datebegin || $dateend) {
            $datesearch = $datebegin ? $datebegin : $dateend;
            $optional['date(' . $this->table . '.booking_date)'] = $datesearch;
        }

        $payment_id = trim($this->input->get('hidden_payment')) ? trim($this->input->get('hidden_payment')) : $this->input->post('hidden_payment');
        if ($payment_id) {
            $optional['payment_id'] = $payment_id;
        }

        $complete_id = trim($this->input->get('complete')) ? trim($this->input->get('complete')) : $this->input->post('complete');

        if ($complete_id) {
            $optional['complete_id'] = $complete_id;
        }

        $result = $this->get_data(null, $optional);

        return $result;
    }

    public function get_dataShowToday()
    {
        $optional = [];

        $optional['booking_date'] = date('Y-m-d');

        $payment_id = trim($this->input->get('hidden_payment')) ? trim($this->input->get('hidden_payment')) : $this->input->post('hidden_payment');
        if ($payment_id) {
            $optional['payment_id'] = $payment_id;
        }

        $result = $this->get_data(null, $optional);

        return $result;
    }

    public function get_dataShow_booking()
    {
        $array = [];

        $complete_id = 2;
        $array = array(
            'complete_id'   => $complete_id
        );

        $result = $this->get_data(null, $array);

        return $result;
    }

    public function get_dataShow_waite()
    {
        $array = [];

        $array['payment_id'] = 4;

        $result = $this->get_data(null, $array);

        return $result;
    }

    #
    # Insert
    public function insert_data()
    {
        $customer_name = trim($this->input->post('customer'));

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$customer_name) {
            return $result;
        } else {
            $this->check_add_customer($customer_name);
        }

        $sql_cus = $this->db->from('customers')
            ->where('name_th', $customer_name)
            ->where('status', 1)
            ->get();
        $row_cus = $sql_cus->row();
        $this->load->helper('my_sql');
        $booking_date = trim($this->input->post('booking_date')) ? trim($this->input->post('booking_date')) : null;
        $complete_id = $booking_date ? 2 : 1;     // 2 = จองแล้ว
        $complete_alias = get_status_alias($complete_id);
        $payment_id = trim($this->input->post('payment'));
        $payment_alias = get_status_alias($payment_id);
        $customer_id = $row_cus->ID;
        $customer_name = $row_cus->NAME_TH;
        $agent_name = trim($this->input->post('agent_name')) ? trim($this->input->post('agent_name')) : null;
        $agent_contact = trim($this->input->post('agent_contact')) ? trim($this->input->post('agent_contact')) : null;


        $round_id = trim($this->input->post('round'));
        $row_round = $this->mdl_round->get_dataItem($round_id);
        // print_r($row_round[0]);
        $round_name = $row_round[0]->NAME;
        $time_start = $row_round[0]->TIME_START;
        $time_end = $row_round[0]->TIME_END;

        $totals = intval($this->input->post('totals'));
        $remark = trim($this->input->post('remark')) ? trim($this->input->post('remark')) : null;

        // check duplicate event
        $check_eventDuplicate = $this->check_eventDuplicate(null, $customer_id, $booking_date);

        if ($check_eventDuplicate['error']) {
            $result = array(
                'error'     => 1,
                'txt'       => $check_eventDuplicate['txt']
            );
            return $result;
        }

        $data_array = array(
            'complete_id'       => $complete_id,
            'complete_alias'    => $complete_alias,
            'payment_id'        => $payment_id,
            'payment_alias'     => $payment_alias,
            'customer_id'       => $customer_id,
            'customer_name'     => $customer_name,
            'agent_name'        => $agent_name,
            'agent_contact'     => $agent_contact,
            'booking_date'      => $booking_date,
            'round_id'          => $round_id,
            'round_name'        => $round_name,
            'time_start'        => $time_start,
            'time_end'          => $time_end,
            'totals'            => $totals,
            'remark'            => $remark,
            'date_starts'       => date('Y-m-d H:i:s'),
            'user_starts'       => $this->session->userdata('user_code'),
        );
        $this->db->insert($this->table, $data_array);
        $new_id = $this->db->insert_id();

        // keep log
        log_data(array('insert', 'insert', $this->db->last_query()));

        if ($new_id) {

            $result = array(
                'error'     => 0,
                'txt'       => 'เพิ่มรายการสำเร็จ'
            );
        }

        return $result;
    }

    #
    # Update
    public function update_data()
    {
        $item_id = trim($this->input->post('item_id'));
        $customer_name = trim($this->input->post('customer'));

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$customer_name || !$item_id) {
            return $result;
        }

        $this->check_add_customer($customer_name);


        $this->load->helper('my_sql');

        // data customers
        $sql_cus = $this->db->from('customers')
            ->where('name_th', $customer_name)
            ->where('status', 1)
            ->get();
        $row_cus = $sql_cus->row();

        $booking_date = trim($this->input->post('booking_date')) ? trim($this->input->post('booking_date')) : null;
        $complete_id = $booking_date ? 2 : 1;     // 2 = จองแล้ว
        $complete_alias = get_status_alias($complete_id);
        $payment_id = trim($this->input->post('payment'));
        $payment_alias = get_status_alias($payment_id);
        $customer_id = $row_cus->ID;
        $customer_name = $row_cus->NAME_TH;
        $agent_name = trim($this->input->post('agent_name')) ? trim($this->input->post('agent_name')) : null;
        $agent_contact = trim($this->input->post('agent_contact')) ? trim($this->input->post('agent_contact')) : null;

        $round_id = trim($this->input->post('round'));
        $row_round = $this->mdl_round->get_dataItem($round_id);

        $round_name = $row_round[0]->NAME;
        $time_start = $row_round[0]->TIME_START;
        $time_end = $row_round[0]->TIME_END;

        $totals = intval($this->input->post('totals'));
        $remark = trim($this->input->post('remark')) ? trim($this->input->post('remark')) : null;

        // check duplicate event
        $check_eventDuplicate = $this->check_eventDuplicate($item_id, $customer_id, $booking_date);

        if ($check_eventDuplicate['error']) {
            $result = array(
                'error'     => 1,
                'txt'       => $check_eventDuplicate['txt']
            );
            return $result;
        }

        $data_array = array(
            'complete_id'       => $complete_id,
            'complete_alias'    => $complete_alias,
            'payment_id'        => $payment_id,
            'payment_alias'     => $payment_alias,
            'customer_id'       => $customer_id,
            'customer_name'     => $customer_name,
            'agent_name'        => $agent_name,
            'agent_contact'     => $agent_contact,
            'booking_date'      => $booking_date,
            'round_id'          => $round_id,
            'round_name'        => $round_name,
            'time_start'        => $time_start,
            'time_end'          => $time_end,
            'totals'            => $totals,
            'remark'            => $remark,
            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );
        $this->db->update($this->table, $data_array, array('id' => $item_id));

        // keep log
        log_data(array('update', 'update', $this->db->last_query()));

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }

    public function check_eventDuplicate(int $item_id = null, int $customer_id = null, string $booking_date = null)
    {
        # code...
        // check duplicate name
        $result = [];

        if ($booking_date) {
            $array = [];
            $array['customer_id'] = $customer_id;
            $array['booking_date'] = $booking_date;
            $array['status'] = 1;

            if ($item_id) {
                $array['id !='] = $item_id;
            }

            $check_dup = check_dup($array, $this->table);
            if ($check_dup) {
                $result = array(
                    'error' => 1,
                    'txt'        => 'ลูกค้ามีการจองในวันนี้แล้ว'
                );
                return $result;
            }
        }
    }

    public function update_bill_booking(int $item_id = null, int $customer_id = null, string $booking_date = null)
    {
        $item_id = trim($this->input->post('item_id')) ? trim($this->input->post('item_id')) : $item_id;
        $booking_date = trim($this->input->post('booking_date')) ? trim($this->input->post('booking_date')) : $booking_date;
        $customer_id = trim($this->input->post('customer_id')) ? trim($this->input->post('customer_id')) : $customer_id;

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$booking_date || !$item_id) {
            return $result;
        }

        // check duplicate event
        $check_eventDuplicate = $this->check_eventDuplicate($item_id, $customer_id, $booking_date);

        if ($check_eventDuplicate['error']) {
            $result = array(
                'error'     => 1,
                'txt'       => $check_eventDuplicate['txt']
            );
            return $result;
        }

        $complete_id = 2;
        $data_array = array(
            'complete_id'       => $complete_id,
            'complete_alias'    => get_status_alias($complete_id),

            'booking_date'      => $booking_date,
            'booking_user'      => $this->session->userdata('user_code'),

            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );
        $this->db->update($this->table, $data_array, array('id' => $item_id));

        // keep log
        log_data(array('update', 'update', $this->db->last_query()));

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }

    #
    # Delete
    public function delete_data()
    {
        $item_id = trim($this->input->post('item_id'));

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id) {
            return $result;
        }

        $data_array = array(
            'status'      => 0,
            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );
        $this->db->update($this->table, $data_array, array('id' => $item_id));

        // keep log
        log_data(array('delete', 'update', $this->db->last_query()));

        // action after delete
        $this->syncDelete($item_id);

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }

    public function syncDelete(int $item_id = null)
    {
    }

    public function check_add_customer(String $customername = null)
    {
        # code...
        $this->load->model('mdl_customer');
        $this->mdl_customer->insert_data($customername);
    }

    #
    # Cancel
    public function cancel_event()
    {
        $item_id = trim($this->input->post('item_id'));

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id) {
            return $result;
        }

        $complete_id = 1;
        $data_array = array(
            'complete_id'       => $complete_id,
            'complete_alias'    => get_status_alias($complete_id),
            'booking_date'      => null,
            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );
        $this->db->update($this->table, $data_array, array('id' => $item_id));

        // keep log
        log_data(array('update', 'update', $this->db->last_query()));

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }
}
