<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_round_time extends CI_Model

{
    private $table = "round_time";

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

        $query = $sql->get($this->table);

        return $query->result();
    }
}
