<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_customer extends CI_Model

{
    private $table = "customers";

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
        $result = $this->get_data(null, array('status_offview' => null));

        return $result;
    }

    #
    # Insert
    public function insert_data(String $customername = null)
    {
        $item_name = trim($this->input->post('item_name'));
        if (trim($customername)) {
            $item_name = trim($customername);
        }

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_name) {
            return $result;
        }

        // check duplicate name
        $check_dup = check_dup(array('name_th' => $item_name, 'status' => 1), $this->table);
        if ($check_dup) {
            $result = array(
                'error' => 1,
                'txt'        => 'มีรายการนี้บนระบบแล้ว'
            );
            return $result;
        }

        $data_array = array(
            'name_th'              => $item_name,
            'name_us'              => $item_name,
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
        $item_name = trim($this->input->post('item_name'));
        $item_statusoff = trim($this->input->post('item_statusoff'));

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_name || !$item_id) {
            return $result;
        }

        // check duplicate name
        $check_dup = check_dup(array('name_th' => $item_name, 'id !=' => $item_id, 'status' => 1), $this->table);
        if ($check_dup) {
            $result = array(
                'error' => 1,
                'txt'        => 'มีรายการนี้บนระบบแล้ว'
            );
            return $result;
        }

        $data_array = array(
            'name_th'           => $item_name,
            'name_us'           => $item_name,
            'status_offview'    => $item_statusoff == "false" ? 1 : null,
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
}
