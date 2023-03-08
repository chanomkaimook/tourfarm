<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_item extends CI_Model

{
    private $table = "item";
    private $path = FCPATH . 'asset/image/item/';
    private $type_image = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data(int $id = null, array $optionnal = [])
    {
        $sql = $this->db->select(
            $this->table.'.ID as ITEM_ID,'.
            $this->table.'.NAME_TH as ITEM_NAME,'.
            $this->table.'.PIC as ITEM_PIC,'.
            $this->table.'.ITEM_CAT_ID as ITEM_CAT_ID,'.
            $this->table.'.BARCODE as ITEM_BARCODE,'.
            $this->table.'.COST as ITEM_COST,'.
            $this->table.'.DATE_STARTS as ITEM_DATE_STARTS,'.
            $this->table.'.USER_STARTS as ITEM_USER_STARTS,'.
            $this->table.'.DATE_UPDATE as ITEM_DATE_UPDATE,'.
            $this->table.'.USER_UPDATE as ITEM_USER_UPDATE,'.
            $this->table.'.STATUS_OFFVIEW as ITEM_STATUS_OFFVIEW,'.
            'item_catagory.ID as ITEM_CATAGORY_ID,'.
            'item_catagory.NAME_TH as ITEM_CATAGORY_NAME,'
        );
        $sql->join('item_catagory','item_catagory.id='.$this->table.'.item_cat_id','left');

        if ($id) {
            $sql->where($this->table.'.id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where('item_catagory.status', 1);
        $sql->where($this->table.'.status', 1);
        $query = $sql->get($this->table);

        return $query->result();
    }

    public function get_dataShow()
    {
        $result = $this->get_data(null, array($this->table.'.status_offview' => null));

        return $result;
    }

    #
    # Insert
    public function insert_data()
    {
        $item_name = trim($this->input->post('item_name'));
        $item_catagory = trim($this->input->post('item_catagory'));
        $item_barcode = trim($this->input->post('item_barcode'));
        $item_mac5 = trim($this->input->post('item_mac5')) ? trim($this->input->post('item_mac5')) : null;
        $item_cost = filter_var(trim($this->input->post('item_cost')),FILTER_VALIDATE_FLOAT) ? filter_var(trim($this->input->post('item_cost')),FILTER_VALIDATE_FLOAT) : null;

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_name) {
            return $result;
        }

        // check duplicate name
        $check_dup = check_dup(array('name_th' => $item_name,'status' => 1), $this->table);
        if ($check_dup) {
            $result = array(
                'error' => 1,
                'txt'        => 'มีรายการนี้บนระบบแล้ว'
            );
            return $result;
        }

        // check duplicate barcode
        $check_barcode = check_dup(array('barcode' => $item_barcode,'status' => 1), $this->table);
        if ($check_barcode) {
            $result = array(
                'error' => 1,
                'txt'        => 'มี barcode นี้บนระบบแล้ว'
            );
            return $result;
        }
        
        $this->load->library('Barcode');
        // $barcode = $this->barcode->randText(15);
        if($item_barcode){
            $this->barcode->generate_image_barcode($item_barcode);
        }

        $data_array = array(
            'name_th'      => $item_name,
            'name_us'      => $item_name,
            'item_cat_id'  => $item_catagory,
            'barcode'      => $item_barcode,
            'mac5'      => $item_mac5,
            'cost'      => $item_cost,
            'user_starts'  => $this->session->userdata('user_code'),
        );
        $this->db->insert($this->table, $data_array);
        $new_id = $this->db->insert_id();

        // keep log
        log_data(array('insert', 'insert', $this->db->last_query()));

        if ($new_id) {

            // update image
            if ($_FILES) {
                $this->load->library('image');
                $upload_image = $this->image->upload_image($_FILES['image'], array($this->path),$this->type_image);

                if ($upload_image['error'] == 0) {
                    $data_img_array = [];
                    foreach ($upload_image['data'] as $key => $val) {
                        $data_img_array = array(
                            'pic '  => $val
                        );
                        $this->db->where('id', $new_id);
                        $this->db->update($this->table, $data_img_array);

                        // keep log
                        log_data(array('update', 'update', $this->db->last_query()));
                    }
                }
            }

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
        $item_catagory = trim($this->input->post('item_catagory'));
        $item_barcode = trim($this->input->post('item_barcode'));
        $item_statusoff = trim($this->input->post('item_statusoff'));
        $item_cost = filter_var(trim($this->input->post('item_cost')),FILTER_VALIDATE_FLOAT) ? filter_var(trim($this->input->post('item_cost')),FILTER_VALIDATE_FLOAT) : null;

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_name || !$item_id) {
            return $result;
        }

        // check duplicate name
        $check_dup = check_dup(array('name_th' => $item_name,'id !=' => $item_id,'status' => 1), $this->table);
        if ($check_dup) {
            $result = array(
                'error' => 1,
                'txt'        => 'มีรายการนี้บนระบบแล้ว'
            );
            return $result;
        }

        // check duplicate barcode
        $check_barcode = check_dup(array('barcode' => $item_barcode,'id !=' => $item_id,'status' => 1), $this->table);
        if ($check_barcode) {
            $result = array(
                'error' => 1,
                'txt'        => 'มี barcode นี้บนระบบแล้ว'
            );
            return $result;
        }

        $this->load->library('Barcode');
        // $barcode = $this->barcode->randText(15);
        if($item_barcode){
            $this->barcode->generate_image_barcode($item_barcode);
        }

        $data_array = array(
            'name_th'       => $item_name,
            'name_us'       => $item_name,
            'barcode'       => $item_barcode,
            'cost'          => $item_cost,
            'status_offview'      => $item_statusoff=="false" ? 1 : null ,
            'date_update'  => date('Y-m-d H:i:s'),
            'user_update'  => $this->session->userdata('user_code'),
        );

        //
        // prevent from hidding item
        if($item_catagory){
            $data_array['item_cat_id'] = $item_catagory;
        }

        $this->db->update($this->table, $data_array, array('id' => $item_id));

        // keep log
        log_data(array('update', 'update', $this->db->last_query()));

        // update image
        if ($_FILES) {
            $this->load->library('image');
            $upload_image = $this->image->upload_image($_FILES['image'], array($this->path),$this->type_image);

            if ($upload_image['error'] == 0) {
                $data_img_array = [];
                foreach ($upload_image['data'] as $key => $val) {
                    $data_img_array = array(
                        'pic '  => $val
                    );
                    $this->db->where('id', $item_id);
                    $this->db->update($this->table, $data_img_array);

                    // keep log
                    log_data(array('update', 'update', $this->db->last_query()));
                }
            }
        }

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
        # code...
        if ($item_id) {

            $sql = $this->db->where('id', $item_id)
                ->get($this->table);
            $row = $sql->row();

            if ($row->PIC) {
                $array[] = $this->path . "/" . $row->PIC;

                // delete file
                delete_file($array, $this->table);
                
            }
        }
    }

}
