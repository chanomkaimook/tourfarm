<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_temp extends CI_Model

{
    private $table = "doc_temp_item";
    private $table_temp = "doc_temp";
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
            $sql->where($this->table.'.id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where($this->table.'.status', 1);
        $query = $sql->get($this->table);

        return $query->result();
    }

    public function get_dataShow()
    {
        $result = $this->get_data(null,array('user_starts'=>$this->session->userdata('user_code')));
        
        return $result;
    }


    /**
     * table doc_temp
     *
     * @param integer|null $id
     * @param array $optionnal
     * @return result
     */
    public function get_dataTemp(int $id = null, array $optionnal = [])
    {
        $sql = $this->db->select('*');

        if ($id) {
            $sql->where($this->table_temp.'.id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where($this->table_temp.'.status', 1);
        $query = $sql->get($this->table_temp);

        return $query->result();
    }

    public function get_docTemp()
    {
        $result = "";
        
        $sql = $this->get_dataTemp();
        foreach($sql as $key => $row){
            $result = $row;
        }

        return $result;
    }
}
