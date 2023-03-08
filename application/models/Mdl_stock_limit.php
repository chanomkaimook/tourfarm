<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_stock_limit extends CI_Model

{
    private $table = "item_stock_limit";
    private $path = FCPATH . 'asset/image/item/';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data(int $id = null, array $optionnal = [])
    {
        $sql = $this->db->select(
            'item.ID as ITEM_ID,' .
                'item.NAME_TH as ITEM_NAME,' .
                'item.PIC as ITEM_PIC,' .
                'item.BARCODE as ITEM_BARCODE,' .
                'item.STATUS_OFFVIEW as ITEM_STATUS_OFFVIEW,' .
                'item_catagory.ID as ITEM_CATAGORY_ID,' .
                'item_catagory.NAME_TH as ITEM_CATAGORY_NAME,' .
                $this->table . '.DATE_STARTS as ITEM_DATE_STARTS,' .
                $this->table . '.USER_STARTS as ITEM_USER_STARTS,' .
                $this->table . '.MIN_VALUE as STOCK_MIN,' .
                $this->table . '.MAX_VALUE as STOCK_MAX,' .
                $this->table . '.DATE_UPDATE as ITEM_DATE_UPDATE,' .
                $this->table . '.USER_UPDATE as ITEM_USER_UPDATE'
        );
        $sql->join('item', 'item.id=' . $this->table . '.item_id', 'left');
        $sql->join('item_catagory', 'item_catagory.id=item.item_cat_id', 'left');

        if ($id) {
            $sql->where('item.id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where('item_catagory.status', 1);
        $sql->where($this->table . '.status', 1);
        $query = $sql->get($this->table);

        return $query->result();
    }

    public function get_dataShow()
    {
        $result = $this->get_data(null, array($this->table . '.status_offview' => null));

        return $result;
    }

    public function get_dataShow_noneset()
    {
        $data_item = $this->mdl_item->get_dataShow();
        $data_stock = $this->get_dataShow();

        $data_item_id = [];
        $data_stock_id = [];

        $data_result = [];

        if (isset($data_item) && count($data_item)) {
            foreach ($data_item as $key => $value) {
                $data_item_id[] = $value->ITEM_ID;
            }
        }

        if (isset($data_stock) && count($data_stock)) {
            foreach ($data_stock as $key => $value) {
                $data_stock_id[] = $value->ITEM_ID;
            }
        }

        $output_result = array_merge(array_diff($data_item_id, $data_stock_id), array_diff($data_stock_id, $data_item_id));

        if (isset($output_result) && count($output_result)) {
            foreach ($output_result as $key => $value) {
                $data_result[] = $data_item[array_keys(array_column($data_item, 'ITEM_ID'), $value)[0]];
            }
        }
        $result = $data_result;

        return $result;
    }

    #
    # Insert
    public function insert_data()
    {
        $item_name = trim($this->input->post('item_name'));
        $item_min = trim($this->input->post('item_min')) ? trim($this->input->post('item_min')) : null;
        $item_max = trim($this->input->post('item_max')) ? trim($this->input->post('item_max')) : null;

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_name) {
            return $result;
        }

        // check duplicate name
        $check_dup = check_dup(array('item_id' => $item_name, 'status' => 1), $this->table);
        if ($check_dup) {
            $result = array(
                'error' => 1,
                'txt'        => 'มีรายการนี้บนระบบแล้ว'
            );
            return $result;
        }

        //
        // check min max
        if (!empty($item_min) && !empty($item_max)) {

            if ($item_min >= $item_max) {
                $result = array(
                    'error' => 1,
                    'txt'        => 'การตั้งค่าไม่ถูกต้อง'
                );
                return $result;
            }
        }

        $data_array = array(
            'item_id'      => $item_name,
            'min_value'      => $item_min,
            'max_value'      => $item_max,
            'user_starts'  => $this->session->userdata('user_code'),
        );
        $this->db->insert($this->table, $data_array);
        $new_id = $this->db->insert_id();
        if ($new_id) {
            $result = array(
                'error'     => 0,
                'txt'       => 'เพิ่มรายการสำเร็จ'
            );

            // keep log
            log_data(array('insert', 'insert', $this->db->last_query()));
        }

        return $result;
    }

    #
    # Update
    public function update_data()
    {
        $item_id = trim($this->input->post('hidden_id'));
        $item_min = trim($this->input->post('item_min')) ? trim($this->input->post('item_min')) : null;
        $item_max = trim($this->input->post('item_max')) ? trim($this->input->post('item_max')) : null;


        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id) {
            return $result;
        }

        //
        // check min max
        if (!empty($item_min) && !empty($item_max)) {

            if ($item_min >= $item_max) {
                $result = array(
                    'error' => 1,
                    'txt'        => 'การตั้งค่าไม่ถูกต้อง'
                );
                return $result;
            }
        }

        $data_array = array(
            'min_value'      => $item_min,
            'max_value'      => $item_max,
            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );
        $this->db->update($this->table, $data_array, array('item_id' => $item_id));

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
        $this->db->update($this->table, $data_array, array('item_id' => $item_id));

        // keep log
        log_data(array('delete', 'update', $this->db->last_query()));

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }
}
