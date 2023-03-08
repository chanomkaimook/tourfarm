<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_document extends CI_Model

{
    private $table = "item";
    private $table_temp = "doc_temp";
    private $table_temp_item = "doc_temp_item";
    private $path = FCPATH . 'asset/image/item/';
    private $type_image = 2;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * get data
     *
     * @param Int|null $id = data id on table
     * @param String|null $table
     * @param array $optionnal
     * @return void
     */
    public function get_data(Int $id = null, String $table = null, array $optionnal = [])
    {
        $result = [];
        $set_array = [];

        if ($id) {
            $optionnal['item_id'] = $id;
        }

        $data_import = $this->get_dataTable(null, 'doc_import_item', $optionnal);
        $data_issue = $this->get_dataTable(null, 'doc_issue_item', $optionnal);
        $data_bill = $this->get_dataTable(null, 'doc_bill_item', $optionnal);
        $data_lost = $this->get_dataTable(null, 'doc_lost_item', $optionnal);
        $data_order = $this->get_dataTable(null, 'doc_order_item', $optionnal);

        //
        // data table import
        if ($data_import) {
            foreach ($data_import as $row) {
                $set_array[] = $row;
            }
        }

        //
        // data table issue
        if ($data_issue) {
            foreach ($data_issue as $row) {
                $set_array[] = $row;
            }
        }

        //
        // data table issue
        if ($data_bill) {
            foreach ($data_bill as $row) {
                $set_array[] = $row;
            }
        }

        //
        // data table issue
        if ($data_lost) {
            foreach ($data_lost as $row) {
                $set_array[] = $row;
            }
        }

        //
        // data table issue
        if ($data_order) {
            foreach ($data_order as $row) {
                $set_array[] = $row;
            }
        }

        $result = $set_array;

        //
        // sort value 
        if ($set_array && count($set_array)) {
            $sort = [];
            foreach ($set_array as $key => $array) {
                $sort[$key] = $array->DATE_STARTS;
            }

            if ($sort && count($sort)) {
                $result = [];
                arsort($sort);
                foreach ($sort as $key => $index) {
                    $result[] = $set_array[$key];
                }
            }
        }

        /* echo "<pre>";
        print_r($result);
        exit; */


        return $result;
    }


    /**
     * get data table
     *
     * @param String|null $select = select column
     * @param String|null $table
     * @param array $optionnal
     * @return void
     */
    public function get_dataTable(String $select = null, String $table = null, array $optionnal = [])
    {
        $sql = $this->db->from($table);

        if ($select) {
            $sql = $this->db->select($select);
        }
        //         print_r($this->input->post());
        // exit;
        //
        // filter
        $request = $_REQUEST;
        $datebegin = date('Y-m-d');
        $dateend = null;

        if (isset($optionnal['item_id']) && $optionnal['item_id']) {
            $datebegin = null;
            $sql->where('item_id', $optionnal['item_id']);
        } else {

            if (isset($request['hidden_datestart']) && $request['hidden_datestart']) {
                $datebegin = $request['hidden_datestart'];
            }
            if (isset($request['hidden_dateend']) && $request['hidden_dateend']) {
                $dateend = $request['hidden_dateend'];
            }

            if (isset($request['item_id']) && $request['item_id']) {
                $sql->where('item_id', $request['item_id']);
            }

            if ($datebegin && $dateend) {
                $sql->where('date(' . $table . '.date_starts) >=', $datebegin);
                $sql->where('date(' . $table . '.date_starts) <=', $dateend);
            } else {

                if ((isset($optionnal['doc_type']) && $optionnal['doc_type'] == 'in') && (isset($optionnal['complete']) && $optionnal['complete'] == 1)) {

                } else {
                    $sql->where('date(' . $table . '.date_starts)', $datebegin);
                }
            }

            if (isset($request['item_filter_catagory']) && $request['item_filter_catagory']) {
                $sql->like('doc_table_code', $request['item_filter_catagory'], 'after');
            }
        }


        //
        //

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->where($table . '.status', 1);
        $query = $sql->get();
        $num = $query->num_rows();

        $result = "";
        if ($num) {
            $result = $query->result();
        }

        return $result;
    }

    public function get_dataShow()
    {
        $result = $this->get_data();

        return $result;
    }

    #
    # Select
    function get_listTemp()
    {
        $result = [];
        $array = [];

        $sql_temp = $this->db->from($this->table_temp)
            ->where('user_starts', $this->session->userdata('user_code'))
            ->where('status', 1)
            ->get();
        $num_temp = $sql_temp->num_rows();
        if ($num_temp) {
            $row_temp = $sql_temp->row();
            $array[0] = $row_temp;
        }

        $sql = $this->db->from($this->table_temp_item)
            ->where('user_starts', $this->session->userdata('user_code'))
            ->where('status', 1)
            ->get();
        $num = $sql->num_rows();
        if ($num) {

            foreach ($sql->result() as $row) {
                $array[0]->ITEM[] = $row;
            }

            $result = $array;
        }

        return $result;
    }

    #
    # Insert
    public function insert_item()
    {

        $sql_temp = $this->db->where('user_starts', $this->session->userdata('user_code'))
            ->get($this->table_temp_item);
        $num_row_temp = $sql_temp->num_rows();
        if ($num_row_temp) {
            $result = array(
                'error'     => 1,
                'txt'       => 'บันทึกหรือล้างข้อมูลรายการที่มีอยู่ก่อน'
            );
            return $result;
        }




        $item_id = trim($this->input->post('hidden_id'));
        $item_total = trim($this->input->post('item_total'));
        $item_hold = trim($this->input->post('hold')) ? trim($this->input->post('hold')) : 0;
        $item_alias = trim($this->input->post('alias')) ? trim($this->input->post('alias')) : "";
        $item_remark = trim($this->input->post('remark')) ? trim($this->input->post('remark')) : null;

        $item_node_id = trim($this->input->post('node_id')) ? trim($this->input->post('node_id')) : "";
        $item_node_name = null;
        $item_node_cat_id = null;
        $item_node_cat_name = null;

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id || !$item_alias) {
            return $result;
        }

        if ($item_node_id) {
            $node = $this->mdl_node->get_data($item_node_id);
            if ($node) {
                foreach ($node as $row_node) {
                    $item_node_name = $row_node->ITEM_NAME;
                    $item_node_cat_id = $row_node->ITEM_CATAGORY_ID;
                    $item_node_cat_name = $row_node->ITEM_CATAGORY_NAME;
                }
            }
        }

        //
        // sql data item 
        $sql_item = $this->db->select('ID,NAME_TH')
            ->from($this->table)
            ->where('id', $item_id)
            ->get();
        $row_item = $sql_item->row();

        $status_alias = $this->document->get_status_alias($item_alias, $item_hold);
        $doc_alias_name = $this->document->get_documentAlias($item_alias);

        if ($row_item) {

            $new_id = $this->check_temp($item_alias);

            $data_array = array(
                'doc_table_id'       => $new_id,
                'status_alias'       => $status_alias['data']['id'],
                'status_alias_name'  => $status_alias['data']['name'],
                'doc_alias'     => $item_alias,
                'doc_alias_name'     => $doc_alias_name,
                'item_id'       => $row_item->ID,
                'item_name'     => $row_item->NAME_TH,
                'total'         => $item_total,
                'remark'        => $item_remark,
                'user_starts'   => $this->session->userdata('user_code'),
            );

            if ($item_node_id) {
                $data_array['node_id'] = $item_node_id;
                $data_array['node_name'] = $item_node_name;
                $data_array['node_cat_id'] = $item_node_cat_id;
                $data_array['node_cat_name'] = $item_node_cat_name;
            }

            if ($item_hold) {
                $data_array['temp'] = 1;
            }

            if ($item_alias == 'order') { // สั่งซื้อ
                $data_array['temp'] = 1;
            }

            $this->db->insert($this->table_temp_item, $data_array);
            $new_id = $this->db->insert_id();
        }

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
        # code...
        $request = $_REQUEST;

        $table_id = $request['table_id'];
        $table_name = $request['table_name'];
        $item_id = $request['item_id'];

        $total = $request['total'];
        $remark = $request['remark'];

        $sql = $this->db->select('DOC_NODE_ID')
            ->from($table_name)
            ->where('id', $table_id)
            ->where('item_id', $item_id)
            ->get();
        $num = $sql->num_rows();
        if ($num) {
            $row = $sql->row();

            $data_update = array(
                'total' => $total,
                'remark' => $remark,
                'date_update' => date('Y-m-d H:i:s'),
                'user_update' => $this->session->userdata('user_code')
            );
            $this->db->update($table_name, $data_update, array('id' => $table_id, 'item_id' => $item_id));

            // keep log
            log_data(array('update', 'update', $this->db->last_query()));


            if ($row->DOC_NODE_ID) {
                $data_update_node = array(
                    'total' => $total,
                    'remark' => $remark,
                    'date_update' => date('Y-m-d H:i:s'),
                    'user_update' => $this->session->userdata('user_code')
                );
                $this->db->update('doc_node_item', $data_update_node, array('doc_table_id' => $row->DOC_NODE_ID, 'item_id' => $item_id));

                // keep log
                log_data(array('update', 'update', $this->db->last_query()));
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
        # code...
        $request = $_REQUEST;

        $table_id = $request['table_id'];
        $table_name = $request['table_name'];
        $item_id = $request['item_id'];

        $sql = $this->db->select('DOC_NODE_ID,DOC_TABLE_CODE')
            ->from($table_name)
            ->where('id', $table_id)
            ->where('item_id', $item_id)
            ->get();
        $num = $sql->num_rows();
        if ($num) {
            $row = $sql->row();

            $data_update = array(
                'status' => 0,
                'date_update' => date('Y-m-d H:i:s'),
                'user_update' => $this->session->userdata('user_code')
            );
            $this->db->update($table_name, $data_update, array('id' => $table_id, 'item_id' => $item_id));

            // keep log
            log_data(array('delete', 'update', $this->db->last_query()));

            //
            // delete table main
            $table_main = $this->document->get_documentTableFromCode($row->DOC_TABLE_CODE);
            if ($table_main) {
                $sql_checkitem = $this->db->where($table_name . '.doc_table_code', $row->DOC_TABLE_CODE)
                    ->where($table_name . '.status', 1)
                    ->get($table_name);
                $num_checkitem = $sql_checkitem->num_rows();
                if ($num_checkitem == 0) {
                    $data_update = array(
                        'status' => 0,
                        'date_update' => date('Y-m-d H:i:s'),
                        'user_update' => $this->session->userdata('user_code')
                    );
                    $this->db->update($table_main, $data_update, array('code' => $row->DOC_TABLE_CODE));

                    // keep log
                    log_data(array('delete', 'update', $this->db->last_query()));
                }
            }

            if ($row->DOC_NODE_ID) {
                $data_update_node = array(
                    'status' => 0,
                    'date_update' => date('Y-m-d H:i:s'),
                    'user_update' => $this->session->userdata('user_code')
                );
                $this->db->update('doc_node_item', $data_update_node, array('doc_table_id' => $row->DOC_NODE_ID, 'item_id' => $item_id));
                // keep log
                log_data(array('delete', 'update', $this->db->last_query()));

                $this->db->update('doc_node', $data_update_node, array('id' => $row->DOC_NODE_ID));
                // keep log
                log_data(array('delete', 'update', $this->db->last_query()));
            }
        }

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }
    public function delete_dataTemp()
    {
        $item_id = trim($this->input->post('item_id'));

        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        if (!$item_id) {
            return $result;
        }

        $sql_temp = $this->db->select($this->table_temp . '.ID as temp_id')
            ->from($this->table_temp)
            ->join($this->table_temp_item, $this->table_temp . '.id=' . $this->table_temp_item . '.doc_table_id', 'left')
            ->where($this->table_temp_item . '.id', $item_id)
            ->where($this->table_temp_item . '.status', 1)
            ->get();
        $num_temp = $sql_temp->num_rows();
        if ($num_temp) {
            $row = $sql_temp->row();
            $table_temp_id = $row->temp_id;

            $this->db->delete($this->table_temp_item, array('id' => $item_id));

            $sql = $this->db->where($this->table_temp_item . '.doc_table_id', $table_temp_id)
                ->get($this->table_temp_item);
            $num = $sql->num_rows();
            if ($num == 0) {
                $this->db->delete($this->table_temp, array('id' => $table_temp_id));
            }
        }

        // action after delete
        $this->syncDelete();

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }

    public function syncDelete()
    {
        # code...
        $num = $this->db->count_all_results($this->table_temp_item);
        if ($num == 0) {

            // clear table temp
            $this->clear_data();
        }
    }

    #
    # Truncate
    public function clear_data()
    {
        $result = array(
            'error' => 1,
            'txt'        => 'ไม่มีการทำรายการ'
        );

        $this->db->delete($this->table_temp, array('user_starts' => $this->session->userdata('user_code')));
        $this->db->delete($this->table_temp_item, array('user_starts' => $this->session->userdata('user_code')));

        // keep log
        log_data(array('delete', 'delete', $this->db->last_query()));

        $sqlcount = $this->db->count_all_results($this->table_temp);
        if ($sqlcount == 0) {
            $this->db->truncate($this->table_temp);
            $this->db->truncate($this->table_temp_item);

            log_data(array('delete', 'truncate', $this->db->last_query()));
        }

        $result = array(
            'error'     => 0,
            'txt'       => 'ทำรายการสำเร็จ'
        );

        return $result;
    }

    /**
     * insert doc_temp for start list doc_temp_item
     *
     * @param String|null $item_alias
     * @return void
     */
    public function check_temp(String $item_alias = null)
    {
        $result = "";

        if ($item_alias) {
            $sql = $this->db->select('ID')
                ->where('user_starts', $this->session->userdata('user_code'))
                ->get($this->table_temp);
            $num = $sql->num_rows();
            if ($num == 0) {
                $data_insert = array(
                    'doc_alias' => $item_alias,
                    'username'  => $this->session->userdata('user_name'),
                    'user_starts'  => $this->session->userdata('user_code')
                );
                $this->db->insert($this->table_temp, $data_insert);
                $new_id = $this->db->insert_id();
                if ($new_id) {
                    $result = $new_id;
                }
            } else {
                $row = $sql->row();

                $result = $row->ID;
            }
        }

        return $result;
    }
}
