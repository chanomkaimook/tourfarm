<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_docnode extends CI_Model

{
    private $table_main = "doc_node";
    private $table = "doc_node_item";
    private $table_list = "doc_node_item_list";
    private $path = FCPATH . 'asset/image/item/';
    private $type_image = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data(int $id = null, array $optionnal = [])
    {
        $sql = $this->db->select('*');

        if ($id) {
            $sql->where($this->table . '.id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where($this->table . '.status', 1);
        $query = $sql->get($this->table);

        return $query->result();
    }

    public function get_dataShow()
    {
        $optional = array();

        $request = $_REQUEST;
        $datebegin = null;
        $dateend = null;
        if (isset($request['hidden_datestart']) && $request['hidden_datestart']) {
            $datebegin = $request['hidden_datestart'];
        }
        if (isset($request['hidden_dateend']) && $request['hidden_dateend']) {
            $dateend = $request['hidden_dateend'];
        }

        if ($datebegin && $dateend) {
            $optional['date(' . $this->table . '.date_starts) >='] = $datebegin;
            $optional['date(' . $this->table . '.date_starts) <='] = $dateend;
        } else if ($datebegin || $dateend) {
            $datesearch = $datebegin ? $datebegin : $dateend;
            $optional['date(' . $this->table . '.date_starts)'] = $datesearch;
        }

        if (isset($request['item_filter_complete']) && $request['item_filter_complete']) {
            $optional['complete'] = $request['item_filter_complete'];
        }

        $result = $this->get_data(null, $optional);

        return $result;
    }

    public function get_count_waite(){

        $sql = $this->db->from($this->table)
        ->where('complete',1)
        ->where('status',1);
        $result = $sql->count_all_results(null,false);

        return $result;
    }
    
    public function get_data_detail(int $id = null, array $optionnal = [])
    {
        $sql = $this->db->select('*');

        if ($id) {
            $sql->where($this->table_list . '.id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where($this->table_list . '.status', 1);
        $query = $sql->get($this->table_list);

        return $query->result();
    }

    #
    # Insert

    #
    # Update
    public function update_data()
    {
        $item_id = trim($this->input->post('item_id'));
        $item_total = trim($this->input->post('item_total'));
        $remark = trim($this->input->post('remark'));

        $optional['remark'] = $remark;

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id || !$item_total) {
            return $result;
        }

        $node_list = $this->document->insert_data_node_list($item_id, $item_total, $optional);

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
            'complete'      => 3,       // ไม่ได้คืนกลับ
            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );
        $this->db->update($this->table, $data_array, array('id' => $item_id));

        // keep log
        log_data(array('delete', 'update', $this->db->last_query()));

        $sql = $this->db->where('id',$item_id)
        ->get($this->table);
        $num = $sql->num_rows();
        if($num){
            $row = $sql->row();

            $this->db->update($this->table_main, $data_array, array('id' => $row->DOC_TABLE_ID));
        }

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }
    public function delete_data_list()
    {
        $item_id = trim($this->input->post('item_id'));

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id) {
            return $result;
        }

        $sql = $this->db->select($this->table . '.complete as item_complete,'.$this->table . '.id as id')
            ->join($this->table_list, $this->table . '.id=' . $this->table_list . '.doc_table_item_id', 'left')
            ->where($this->table_list . '.id', $item_id)
            ->where($this->table . '.status', 1)
            ->get($this->table);
        $row = $sql->row();
        /* if ($row->item_complete == 2 || $row->item_complete == 3) {
            $result = array(
                'error'     => 1,
                'txt'       => 'ลบรายการนี้ไม่ได้'
            );

            return $result;
        } */

        $this->db->delete($this->table_list, array('id' => $item_id));
        
        $this->document->check_after_total($row->id);

        // keep log
        log_data(array('delete', 'delete', $this->db->last_query()));

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }

    public function restore_dataItem()
    {
        $request = $_REQUEST;
        $item_id = trim($request['id']);

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id) {
            return $result;
        }

        $data_array = array(
            'complete'      => 1,       // รอ
            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );
        $this->db->update($this->table, $data_array, array('id' => $item_id));

        // keep log
        log_data(array('restore', 'update', $this->db->last_query()));

        $sql = $this->db->where('id',$item_id)
        ->get($this->table);
        $num = $sql->num_rows();
        if($num){
            $row = $sql->row();

            $this->db->update($this->table_main, $data_array, array('id' => $row->DOC_TABLE_ID));
        }

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }
}
