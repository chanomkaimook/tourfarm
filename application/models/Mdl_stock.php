<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_stock extends CI_Model

{
    private $table = "item_stock";
    private $stockcut = "item_stock_cut";
    private $path = FCPATH . 'asset/image/item/';
    private $type_image = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data(int $id = null, array $optionnal = [])
    {
        $request = $_REQUEST;

        $sql = $this->db->select(
            $this->table . '.TOTAL as ITEM_TOTAL,' .
                'item.ID as ITEM_ID,' .
                'item.NAME_TH as ITEM_NAME,' .
                'item.PIC as ITEM_PIC,' .
                'item.ITEM_CAT_ID as ITEM_CAT_ID,' .
                'item.BARCODE as ITEM_BARCODE,' .
                'item.COST as ITEM_COST,' .
                'item.DATE_STARTS as ITEM_DATE_STARTS,' .
                'item.USER_STARTS as ITEM_USER_STARTS,' .
                'item.STATUS_OFFVIEW as ITEM_STATUS_OFFVIEW,' .
                'item_catagory.ID as ITEM_CATAGORY_ID,' .
                'item_catagory.NAME_TH as ITEM_CATAGORY_NAME,'.
                'item_stock_limit.MIN_VALUE as STOCK_MIN,'.
                'item_stock_limit.MAX_VALUE as STOCK_MAX'
        );
        if ($id) {
            $sql->where($this->table . '.item_id', $id);
        }

        //
        // filter
        if (isset($request['item_filter_catagory']) && $request['item_filter_catagory']) {
            $sql->where('item.item_cat_id', $request['item_filter_catagory']);
        }
        if (isset($request['item_filter_statusview']) && $request['item_filter_statusview']) {
            $sql->where('item.status_offview', $request['item_filter_statusview']);
        } else {
            $sql->where('item.status_offview is null', null, false);
        }
        //
        //

        if ($optionnal && count($optionnal)) {
            foreach ($optionnal as $column => $value) {
                $sql->where($column, $value);
            }
        }

        $sql->join('item', 'item.id=' . $this->table . '.item_id', 'left');
        $sql->join('item_catagory', 'item_catagory.id=item.item_cat_id', 'left');
        $sql->join('item_stock_limit', 'item_stock_limit.item_id=item.id', 'left');
        $sql->where('item.status', 1);
        $sql->where($this->table . '.status', 1);
        $query = $sql->get($this->table);

        return $query->result();
    }

    public function get_dataShow()
    {
        $result = $this->get_data(null, array($this->table . '.status_offview' => null));

        return $result;
    }

    /**
     * check date cut
     *
     * @param array $array = [date=>date select]
     * @return void
     */
    public function check_dateCut(array $array = [])
    {
        $result = "";
        $datestart = "";

        $dateset = $array['date'];

        $sql = $this->db->select('*')
            ->from($this->stockcut)
            ->where($this->stockcut . '.date_cut <=', $dateset)
            ->limit('2')
            ->order_by($this->stockcut . '.id', 'desc');
        $q = $sql->get();
        $num = $q->num_rows();
        if ($num) {
            if ($num == 1) {
                $r = $q->row();
                $datecut = $r->DATE_CUT;
            } else {
                $index = 1;
                foreach ($q->result() as $r) {
                    if ($index == 1) {
                        $datecut = $r->DATE_CUT;
                    } else {
                        $datestart = $r->DATE_CUT;
                    }
                    $index++;
                }
            }
        } else {
            // Begin
            // create date cut
            $new_id = $this->insert_dateCut($dateset, 1);
            if ($new_id) {
                $sql = $this->db->from($this->stockcut)
                    ->where('id', $new_id)
                    ->get();
                $r = $sql->row();
                $datecut = $r->DATE_CUT;

                $this->insert_stockItem(null, $datecut);
            }
        }


        //	หาระยะห่างในการเก็บรอบตัด stock
        $sqlcut = $this->db->select("timestampdiff(month,'" . $datecut . "','" . $dateset . "') as lengthcut")
            ->from($this->stockcut);
        $qcut = $sqlcut->get();
        $numcut = $qcut->num_rows();
        if ($numcut) {
            $rcut = $qcut->row();

            // เมื่อระยะเวลาปัจจุบันห่างจากจุดตัดล่าสุด 4 เดือน
            // ระบบจะสร้างจุดตัดที่ห่าง 3 เดือนจากจุดตัดล่าสุด

            if ($rcut->lengthcut >= 4) {
                // create date cut
                $id = $this->insert_dateCut($dateset);

                if ($id) {
                    $sqlnew = $this->db->select('*')
                        ->from($this->stockcut)
                        ->where($this->stockcut . '.date_cut <=', $dateset)
                        ->order_by($this->stockcut . '.id', 'desc')
                        ->limit('2');
                    $qnew = $sqlnew->get();

                    $index = 1;
                    foreach ($qnew->result() as $rnew) {

                        $datestart = "";

                        if ($index == 1) {
                            $datecut = $rnew->DATE_CUT;
                        } else {
                            $datestart = $rnew->DATE_CUT;
                        }
                        $index++;
                    }

                    $this->insert_stockItem($datestart, $datecut);
                }
            }

            $result = array(
                'datecut'        => $datecut,
                'datestart'        => $datestart
            );
        }


        return $result;
    }

    /**
     * Insert date cut
     *
     * @param String|null $date = yyyy-mm-dd
     * @param String|null $previousmonth = false= month now
     * @return int 
     */
    public function insert_dateCut(String $date = null, String $previous_month = null)
    {
        # code...
        $result = "";
        if ($date) {
            if ($previous_month) {
                $newcut = date('Y-m') . '-01';
            } else {
                $datetemp = date_create($date);
                date_sub($datetemp, date_interval_create_from_date_string("1 months"));
                $yearmonth = date_format($datetemp, 'Y-m');

                $newcut = $yearmonth . '-01';
            }

            $datainsert = array(
                'date_cut'    => $newcut
            );

            $this->db->insert($this->stockcut, $datainsert);
            $result = $this->db->insert_id();
        }

        return $result;
    }

    /**
     * create list item stock
     *
     * @param String|null $datebegin = yyyy-mm-dd date start for table document to count
     * @param String|null $dateend = yyyy-mm-dd date stop for table document to count
     * @param Array|null $optional = array[key=>value]
     * @return void
     */
    public function insert_stockItem(String $datebegin = null, String $dateend = null, array $optional = [])
    {

        if (isset($optional['where']) && $optional['where']) {
            $data_item = $this->mdl_item->get_data(null, $optional['where']);
        } else {
            $data_item = $this->mdl_item->get_data();
        }

        if ($data_item) {
            $data_insert = [];

            //
            // ลดลง 1 วัน จากวันที่หาวันตัด stock ได้
            // เพื่อให้ระบบเก็บค่าเริ่มต้นของจำนวนสินค้าก่อนถึงวันตัดจริง
            // จะได้ไม่รวมกับยอดที่หาได้จากสูตรอื่นๆ ( >= date , <= date)
            $datetemp = date_create($dateend);
            date_sub($datetemp, date_interval_create_from_date_string("1 day"));
            $dateend_cut = date_format($datetemp, 'Y-m-d');

            foreach ($data_item as $row) {

                $item_total = $this->calculate_itemTotal($row->ITEM_ID, $datebegin, $dateend_cut);

                $data_insert[] = array(
                    'item_id'   => $row->ITEM_ID,
                    'date_cut'   => $dateend,
                    'total'   => $item_total,
                    'user_starts'   => $this->session->userdata('user_code'),
                );
            }
            $this->db->insert_batch($this->table, $data_insert);
        }
    }


    #
    ##
    ###################################
    #   Function
    ###################################
    ##
    #

    /**
     * calculate item total
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function calculate_itemTotal(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        // for keep item total
        $total = 0;
        $import_total = 0;
        $issue_total = 0;
        $node_import_total = 0;
        $node_issue_total = 0;

        #
        # import
        $import_total = $this->import_total($item_id, $datebegin, $dateend);

        #
        # issue
        $issue_total = $this->issue_total($item_id, $datebegin, $dateend);

        #
        # node in
        // $node_issue_total = $this->node_issue_total($item_id, $datebegin, $dateend);

        #
        # node out
        // $node_import_total = $this->node_import_total($item_id, $datebegin, $dateend);

        #
        # sale
        $bill_total = $this->bill_total($item_id, $datebegin, $dateend);

        #
        # lost
        $lost_total = $this->lost_total($item_id, $datebegin, $dateend);

        #
        # lost
        $node_issue_total_list = $this->node_issue_total_list($item_id, $datebegin, $dateend);
        $node_import_total_list = $this->node_import_total_list($item_id, $datebegin, $dateend);

        // calculate total
        // $result = ($import_total + $node_issue_total) - ($issue_total + $node_import_total) - $bill_total - $lost_total;
        $result = ($import_total + $node_issue_total_list) - ($issue_total + $node_import_total_list) - $bill_total - $lost_total;

        return $result;
    }

    /**
     * data total from document import
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function import_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;

        if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_import_item', $optional);
        // echo "<pre>";print_r($data);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * data total from document issue
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function issue_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;

        if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_issue_item', $optional);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * data total from document node (in)
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function node_issue_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;
        $optional['doc_type'] = 'in';

        if ($datebegin) {
            $optional['date(date_complete) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_complete) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_node_item', $optional);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * data total from document node (in) list
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function node_issue_total_list(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;
        $optional['doc_type'] = 'in';

        if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_node_item_list', $optional);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * data total from document node (out)
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function node_import_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;
        $optional['doc_type'] = 'out';

        if ($datebegin) {
            $optional['date(date_complete) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_complete) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_node_item', $optional);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * data total from document node (out) list
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function node_import_total_list(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;
        $optional['doc_type'] = 'out';

        if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_node_item_list', $optional);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * data total hold from document node (in)
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function hold_issue_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 1;
        $optional['status'] = 1;
        $optional['doc_type'] = 'in';

        /* if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        } */

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL,TOTAL_TEMP', 'doc_node_item', $optional);
        if ($data) {
            $total = 0;
            $total_temp = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
                
                if($row->TOTAL_TEMP){
                    $total_temp += intval($row->TOTAL_TEMP);
                }
            }
            $result = $total - $total_temp;
        }

        return $result;
    }

    /**
     * data total hold from document node (out)
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function hold_import_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 1;
        $optional['status'] = 1;
        $optional['doc_type'] = 'out';

        /* if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        } */

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_node_item', $optional);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * find date cut
     *
     * @param String|null $dateset = yyyy-mm-dd
     * @return void
     */
    public function find_dateCut(String $dateset = null)
    {
        $result = "";

        if ($dateset) {
            $sql = $this->db->select('*')
                ->from($this->stockcut)
                ->where($this->stockcut . '.date_cut <=', $dateset)
                ->order_by($this->stockcut . '.id', 'desc');
            $q = $sql->get();
            $num = $q->num_rows();
            if ($num) {
                $row = $q->row();

                $result = $row->DATE_CUT;
            }
        }

        return $result;
    }

    /**
     * item total หาค่าคงเหลือ(ค่าทั้งหมดก่อนวันที่ค้นหา)
     *
     * @param integer|null $itemid = item id
     * @param string|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function item_total(int $itemid = null, string $dateend = null)
    {
        $result = 0;

        if ($itemid && $dateend) {
            $find_dateCut = $this->find_dateCut($dateend);
            if ($find_dateCut) {

                //
                // calculate
                // echo $find_dateCut;
                //
                // ลดลง 1 วัน จากวันที่จะค้นหาล่าสุด
                // จะได้ไม่รวมกับยอดของวันปัจุบันที่ค้นหา ที่ได้จากสูตรอื่นๆ ( >= date , <= date)
                $datetemp = date_create($dateend);
                date_sub($datetemp, date_interval_create_from_date_string("1 day"));
                $dateend_cut = date_format($datetemp, 'Y-m-d');

                $result = $this->calculate_itemTotal($itemid, $find_dateCut, $dateend_cut);
            }
        }

        return $result;
    }

    /**
     * data total from document sale
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function bill_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;

        if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_bill_item', $optional);
        // echo "<pre>";print_r($data);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * data total from document lost
     *
     * @param Int|null $item_id = item ID
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function lost_total(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        $result = 0;

        $optional['item_id'] = $item_id;
        $optional['complete'] = 2;
        $optional['status'] = 1;

        if ($datebegin) {
            $optional['date(date_starts) >='] = $datebegin;
        }

        if ($dateend) {
            $optional['date(date_starts) <='] = $dateend;
        }

        $data = $this->mdl_document->get_dataTable('ITEM_ID,TOTAL', 'doc_lost_item', $optional);
        // echo "<pre>";print_r($data);
        if ($data) {
            $total = 0;
            foreach ($data as $row) {
                $total += intval($row->TOTAL);
            }
            $result = $total;
        }

        return $result;
    }

    /**
     * view detail item on stock
     *
     * @param Int|null $item_id = item id
     * @param String|null $datebegin = yyyy-mm-dd
     * @param String|null $dateend = yyyy-mm-dd
     * @return void
     */
    public function item_detail_stock(Int $item_id = null, String $datebegin = null, String $dateend = null)
    {
        // คำนวณ เบิกออก รับเข้า รอคืน รอรับ
        // สูตร ($import_total + $node_issue_total) - ($issue_total + $node_import_total);
        // สูตร (รับเข้า + รอรับ) - (เบิกออก + รอคืน);
        // สูตร รับเข้า - เบิกออก;

        $result = [];

        //
        // setting
        $import_total = intval($this->import_total($item_id, $datebegin, $dateend));
        $issue_total = intval($this->issue_total($item_id, $datebegin, $dateend));
        $bill_total = intval($this->bill_total($item_id, $datebegin, $dateend));
        $lost_total = intval($this->lost_total($item_id, $datebegin, $dateend));

        $node_import_total = intval($this->node_import_total($item_id, $datebegin, $dateend));
        $node_issue_total = intval($this->node_issue_total($item_id, $datebegin, $dateend));

        $hold_import_total = intval($this->hold_import_total($item_id, $datebegin, $dateend));
        $hold_issue_total = intval($this->hold_issue_total($item_id, $datebegin, $dateend));
        $node_import_total_list = intval($this->node_import_total_list($item_id, $datebegin, $dateend));
        $node_issue_total_list = intval($this->node_issue_total_list($item_id, $datebegin, $dateend));

        if ($datebegin != $dateend) {
            $item_total = intval($this->item_total($item_id, $datebegin));
        } else {
            $item_total = intval($this->item_total($item_id, $dateend));
        }


        // $import_total_net = $import_total + $node_issue_total_list;
        // $issue_total_net = $issue_total + $lost_total;


        $result['import_total'] = $import_total;
        $result['issue_total'] = $issue_total;

        $result['node_import_total'] = $node_import_total_list;
        $result['node_issue_total'] = $node_issue_total_list;

        $h_im_net_total = $hold_import_total;
        $h_is_net_total = $hold_issue_total;
        $result['hold_import_total_only'] = $hold_import_total;
        $result['hold_issue_total_only'] = $hold_issue_total;
        $result['hold_import_total'] = $h_im_net_total;
        $result['hold_issue_total'] = $h_is_net_total;

        $result['item_total'] = $item_total;

        $import_net_total = $import_total + $node_issue_total_list;
        $issue_net_total = $issue_total + $node_import_total_list + $lost_total;
        $result['import_net_total'] = $import_net_total;
        $result['issue_net_total'] = $issue_net_total;
        $result['bill_total'] = $bill_total;



        $result['temp_total'] = $item_total + ($import_net_total - $issue_net_total) - $bill_total;
        $result['net_total'] = $item_total + (($import_net_total + $h_is_net_total) - ($issue_net_total + $h_im_net_total)) - $bill_total;

        return $result;
    }

    /**
     * update item on stock
     *
     * @param string $date
     * @return void
     */
    public function check_updateStock(string $date = null)
    {
        # code...
        if (!$date) {
            $date = date('Y-m-d');
        }

        $datecut = $this->find_dateCut($date);

        if ($datecut) {

            $item = $this->mdl_item->get_dataShow();
            $itemstock = $this->get_dataShow();

            if (count($item)) {

                #
                # หากไม่มีพบสินค้าใน stock เลย
                # ให้เพิ่มสินค้าเข้ามาใหม่

                #
                # ลด 1 วันเพื่อหารอบตัดก่อนหน้า
                $datetemp = date_create($datecut);
                date_sub($datetemp, date_interval_create_from_date_string("1 day"));
                $dateend_cut = date_format($datetemp, 'Y-m-d');
                $datecut_previous = $this->find_dateCut($dateend_cut);

                if (count($itemstock) == 0) {
                    $this->insert_stockItem($datecut_previous, $datecut);
                } else if (count($item) > count($itemstock)) {
                    #
                    # ค้นหา id ไอเทมล่าสุดที่ stock มี
                    # เพิ่มไอเทมจากตาราง item ที่มากกว่า id ทีหาได้จากตารงา stock
                    # (เพราะ ไอเทมที่เพิ่มมาจะมี id มากกว่าเสมอ)
                    $sql = $this->db->select_max('ID')
                        ->where('date_cut', $datecut)
                        ->get($this->table);
                    $row = $sql->row();

                    $optional = array(
                        'where' => array(
                            'item.id >'    => $row->ID
                        )
                    );
                    $this->insert_stockItem($datecut_previous, $datecut, $optional);
                }
            }
        }

    }
}
