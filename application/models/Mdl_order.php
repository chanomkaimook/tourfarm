<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_order extends CI_Model

{
    private $table = "doc_order_item";
    private $path = FCPATH . 'asset/image/item/';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data(int $id = null, array $optionnal = [])
    {
        $sql = $this->db->select($this->table.'.*')
        ->join('doc_node',$this->table.'.doc_node_id=doc_node.id','left');

        if ($id) {
            $sql->where($this->table . '.id', $id);
        }

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where('doc_node.complete !=', 3);
        $sql->where($this->table . '.status', 1);
        $query = $sql->get($this->table);

        return $query->result();
    }

    public function get_dataShow()
    {
        $optional = array();

        $request = $_REQUEST;
        $datebegin = date('Y-m-d');
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

        if (isset($request['item_filter_node']) && $request['item_filter_node']) {
            $optional[$this->table . '.node_id'] = $request['item_filter_node'];
        }

        $result = $this->get_data(null, $optional);

        return $result;
    }
}
