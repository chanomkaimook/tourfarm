<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Document
{
	private $table_import = "doc_import";
	private $table_import_item = "doc_import_item";

	private $table_bill = "doc_bill";
	private $table_bill_item = "doc_bill_item";

	private $table_issue = "doc_issue";
	private $table_issue_item = "doc_issue_item";

	private $table_node = "doc_node";
	private $table_node_item = "doc_node_item";
	private $table_node_item_list = "doc_node_item_list";

	private $table_lost = "doc_lost";
	private $table_lost_item = "doc_lost_item";

	private $table_order = "doc_order";
	private $table_order_item = "doc_order_item";

	private $node = "node";

	private $path_barcode = FCPATH . 'asset/image/barcode/';

	public function __construct()
	{
	}


	#
	# Insert
	#

	/**
	 * Insert data document
	 *
	 * @param array|null $data = array[
	 * 								key,
	 * 								array column=>value
	 * 								array [item]=>array [column=>value]
	 * ]
	 * @return void
	 */
	public function insert_data(array $dataarray = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		/* echo "<pre>";
		print_r($dataarray);
		exit; */
		$result = [];

		if ($dataarray && count($dataarray)) {
			foreach ($dataarray as $key => $data) {
				switch ($data->DOC_ALIAS) {
					case 'import':
						$table = $this->table_import;
						$table_item = $this->table_import_item;
						break;
					case 'bill':
						$table = $this->table_bill;
						$table_item = $this->table_bill_item;
						break;
					case 'issue':
						$table = $this->table_issue;
						$table_item = $this->table_issue_item;
						break;
					case 'node':
						$table = $this->table_node;
						$table_item = $this->table_node_item;
						break;
					case 'lost':
						$table = $this->table_lost;
						$table_item = $this->table_lost_item;
						break;
					case 'order':
						$table = $this->table_order;
						$table_item = $this->table_order_item;
						break;
					default:
						$table = "";
						$table_item = "";
						break;
				}

				if ($table && $table_item) {

					$array_node = [];

					//
					// insert data
					$complete = 2; // success
					$data_comp = $this->get_completeText($complete);
					$code = $this->run_code($data->DOC_ALIAS);

					$data_table = array(
						'code'      			=> $code,
						'complete_alias'      	=> $data_comp,
						'complete'  		=> $complete,
						'remark'      		=> null,
						'user_starts'  		=> $data->USER_STARTS,
					);
					$ci->db->insert($table, $data_table);
					$new_id = $ci->db->insert_id();

					// keep log
					log_data(array('insert', 'insert', $ci->db->last_query()));

					//
					// insert list item
					if ($new_id) {
						foreach ($data->ITEM as $key => $dataitem) {
							$data_table = array(
								'doc_table_id'     	=> $new_id,
								'doc_table_code'    => $code,
								'status_alias'      => $dataitem->STATUS_ALIAS,
								'status_alias_name'      => $dataitem->STATUS_ALIAS_NAME,
								'temp'      		=> $dataitem->TEMP,

								'item_id'  			=> $dataitem->ITEM_ID,
								'item_name'  		=> $dataitem->ITEM_NAME,
								'total'  			=> $dataitem->TOTAL,
								'complete'  		=> $complete,
								'remark'      		=> $dataitem->REMARK,
								'user_starts'  		=> $dataitem->USER_STARTS,
							);

							if ($dataitem->NODE_ID) {
								$data_table['node_id'] = $dataitem->NODE_ID;
								$data_table['node_name'] = $dataitem->NODE_NAME;
								$data_table['node_cat_id'] = $dataitem->NODE_CAT_ID;
								$data_table['node_cat_name'] = $dataitem->NODE_CAT_NAME;
							}
							$ci->db->insert($table_item, $data_table);
							$new_id_item = $ci->db->insert_id();
							if ($new_id_item && $dataitem->TEMP) {
								$dataitem->TABLE_ID = $new_id;
								$dataitem->TABLE_ITEM_ID = $new_id_item;
								$dataitem->TABLE_ITEM_CODE = $code;

								$array_node[] = $dataitem;
							}
						}

						// keep log
						log_data(array('insert', 'insert', $ci->db->last_query()));
					}

					//
					// insert node
					if ($array_node && count($array_node)) {
						$this->insert_data_node($array_node);
					}

					$result = array(
						'error'     => 0,
						'txt'       => 'เพิ่มรายการสำเร็จ'
					);
				}
			}
		}

		return $result;
	}

	/**
	 * insert node
	 *
	 * @param array $data_node = array[
	 * 								key=>array [column=>value]
	 * 								]
	 * @return void
	 */
	public function insert_data_node(array $data_node = [])
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		foreach ($data_node as $keysup => $datasup) {

			$complete = 1; // waite
			$data_comp = $this->get_completeText($complete);
			$code = $this->run_code('node');

			$doc_type = $this->get_docType($datasup->DOC_ALIAS);

			$data_table = array(
				'code'      				=> $code,
				'node_id'      				=> $datasup->NODE_ID,
				'node_name'      			=> $datasup->NODE_NAME,
				'node_cat_id'      			=> $datasup->NODE_CAT_ID,
				'node_cat_name'      		=> $datasup->NODE_CAT_NAME,

				'doc_id'      				=> $datasup->TABLE_ID,
				'doc_alias'      			=> $datasup->DOC_ALIAS,
				'status_alias_name'      	=> $datasup->STATUS_ALIAS_NAME,
				'complete_alias'      	=> $data_comp,
				'complete'  		=> $complete,
				'remark'      		=> null,
				'user_starts'  		=> $datasup->USER_STARTS,
			);
			$ci->db->insert($this->table_node, $data_table);
			$new_id = $ci->db->insert_id();

			// keep log
			log_data(array('insert', 'insert', $ci->db->last_query()));

			if ($new_id) {

				switch ($datasup->DOC_ALIAS) {
					case 'import':
						$table_item = $this->table_import_item;
						break;
					case 'issue':
						$table_item = $this->table_issue_item;
						break;
					case 'order':
						$table_item = $this->table_order_item;
						break;
					default:
						$table_item = "";
						break;
				}
				if ($table_item) {
					$data_update = array(
						'doc_node_id' => $new_id,
						'doc_node_code' => $code,

					);
					$ci->db->update($table_item, $data_update, array('id' => $datasup->TABLE_ITEM_ID));
				}

				$data_table = array(
					'doc_table_id'      	=> $new_id,
					'doc_table_code'      	=> $code,
					'status_alias'      	=> $datasup->STATUS_ALIAS,
					'status_alias_name'     => $datasup->STATUS_ALIAS_NAME,

					'node_id'      				=> $datasup->NODE_ID,
					'node_name'      			=> $datasup->NODE_NAME,
					'node_cat_id'      			=> $datasup->NODE_CAT_ID,
					'node_cat_name'      		=> $datasup->NODE_CAT_NAME,

					'doc_id'      				=> $datasup->TABLE_ID,
					'doc_alias'      			=> $datasup->DOC_ALIAS,
					'doc_type'      			=> $doc_type,

					'item_id'  			=> $datasup->ITEM_ID,
					'item_name'  		=> $datasup->ITEM_NAME,
					'total'  			=> $datasup->TOTAL,
					'complete'  		=> $complete,
					'remark'      		=> $datasup->REMARK,
					'user_starts'  		=> $datasup->USER_STARTS,
				);
				$ci->db->insert($this->table_node_item, $data_table);

				// keep log
				log_data(array('insert', 'insert', $ci->db->last_query()));
			}
		}
	}

	/**
	 * insert node list (แยกรายการรอส่ง/รอรับ)
	 *
	 * @param integer|null $item_id = doc_node_item ID
	 * @param integer|null $item_total
	 * @param array $optional = array [key=>value]
	 * @return void
	 */
	public function insert_data_node_list(int $item_id = null, int $item_total = null, array $optional = [])
	{

		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$remark = null;
		if (isset($optional['remark']) && $optional['remark']) {
			$remark = trim($optional['remark']);
		}

		$sql = $ci->db->where('id', $item_id)
			->where('status', 1)
			->get($this->table_node_item);
		$num = $sql->num_rows();
		if ($num) {
			$row = $sql->row();

			$data_table = array(
				'doc_table_id'      	=> $row->DOC_TABLE_ID,
				'doc_table_code'      	=> $row->DOC_TABLE_CODE,
				'doc_table_item_id'     => $item_id,
				'status_alias'      	=> $row->STATUS_ALIAS,
				'status_alias_name'     => $row->STATUS_ALIAS_NAME,

				'node_id'      				=> $row->NODE_ID,
				'node_name'      			=> $row->NODE_NAME,
				'node_cat_id'      			=> $row->NODE_CAT_ID,
				'node_cat_name'      		=> $row->NODE_CAT_NAME,

				'doc_id'      				=> $row->DOC_ID,
				'doc_alias'      			=> $row->DOC_ALIAS,
				'doc_type'      			=> $row->DOC_TYPE,

				'item_id'  			=> $row->ITEM_ID,
				'item_name'  		=> $row->ITEM_NAME,
				'total'  			=> $item_total,
				'complete'  		=> 2,
				'remark'      		=> $remark,
				'user_starts'  		=> $row->USER_STARTS,
			);
			$ci->db->insert($this->table_node_item_list, $data_table);

			// keep log
			log_data(array('insert', 'insert', $ci->db->last_query()));

			$new_id = $ci->db->insert_id();
			if ($new_id) {

				$this->check_after_total($item_id);
		
			}
		}
	}

	/**
	 * check status total after event to document
	 *
	 * @param integer|null $id = ID doc_node_item
	 * @return void
	 */
	public function check_after_total(int $id = null)
	{
		# code...
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		if ($id) {

			$sql = $ci->db->where('id', $id)
				->where('status', 1)
				->get($this->table_node_item);
			$num = $sql->num_rows();
			if ($num) {
				$row = $sql->row();

				$total_list = 0;

				//
				// sum node_list
				$q_total_list = $ci->db->select_sum('TOTAL')
					->where('doc_table_item_id', $id)
					->get($this->table_node_item_list);
				if ($q_total_list) {
					$r_total_list = $q_total_list->row();
					$total_list = intval($r_total_list->TOTAL);
				}

				$data_update = array(
					'total_temp'	=> $total_list > 0 ? $total_list : null,
				);

				//
				// check success total
				if ($total_list >= $row->TOTAL) {
					$data_update['complete'] = 2;
					$data_update['date_complete'] = date('Y-m-d H:i:s');
				}else{
					if($row->COMPLETE == 2){
						$data_update['complete'] = 1;
					}
				}

				$ci->db->update($this->table_node_item, $data_update, array('id' => $row->ID));
			}

		}
	}

	/**
	 * return status alias
	 *
	 * @param String|null $alias	= import,issue
	 * @param Int|null $hold	= 1=hold,null=no
	 * @return void
	 */
	function get_status_alias(String $alias = null, Int $hold = 0)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$result = "";

		if ($alias) {
			/*
			1=รับเข้า
			2=ยืม
			3=เบิก
			4=ให้ยืม
			5=ส่งคืน
			6=รับกลับ
			7=ขาย
			8=สูญเสีย
			9=สั่งซื้อ
			*/

			$array_alias = [];
			$sql = $this->get_statusAlias('ID,NAME');
			foreach ($sql as $row) {
				$array_alias[$row->ID] = $row->NAME;
			}


			switch ($alias) {
				case 'import':
					$id = 1;
					if ($hold) {
						$id = 2;
					}
					break;
				case 'bill':
					$id = 7;
					break;
				case 'issue':
					$id = 3;
					if ($hold) {
						$id = 4;
					}
					break;
				case 'lost':
					$id = 8;
					break;
				case 'order':
					$id = 9;
					break;
				default:
					$id = "";
					break;
			}

			$result = array(
				'data' => array(
					'id'	=> $id,
					'name'	=> $array_alias[$id]
				)
			);
		}

		return $result;
	}

	public function get_statusAlias(String $select = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$sql = $ci->db->from('document_status');
		if ($select) {
			$sql->select('ID,NAME');
		}
		$query = $sql->get();
		$result = $query->result();

		return $result;
	}

	/**
	 * document alias
	 *
	 * @param String|null $name  = import , issue , node
	 * @return void
	 */
	public function get_documentAlias(String $name = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		if ($name) {
			switch ($name) {
				case 'import':
					$result = 'รับเข้า';
					break;
				case 'bill':
					$result = 'ขาย';
					break;
				case 'issue':
					$result = 'เบิก';
					break;
				case 'node':
					$result = 'สโตร์';
					break;
				case 'lost':
					$result = 'สูญเสีย';
					break;
				case 'order':
					$result = 'สั่งซื้อ';
					break;
				default:
					$result = null;
					break;
			}
		}

		return $result;
	}

	/**
	 * document alias
	 *
	 * @param String|null $code  = document code
	 * @return void
	 */
	public function get_documentAliasFromCode(String $code = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$result = "";

		if ($code) {

			$cut = substr($code, 0, 2);

			switch ($cut) {
				case 'IM':
					$text = 'import';
					break;
				case 'OR':
					$text = 'bill';
					break;
				case 'IS':
					$text = 'issue';
					break;
				case 'SP':
					$text = 'node';
					break;
				case 'CC':
					$text = 'lost';
					break;
				case 'PO':
					$text = 'order';
					break;
				default:
					$text = null;
					break;
			}

			$result = $this->get_documentAlias($text);
		}

		return $result;
	}

	/**
	 * document table name
	 *
	 * @param String|null $code  = document code
	 * @return void
	 */
	public function get_documentTableFromCode(String $code = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$result = "";

		if ($code) {

			$cut = substr($code, 0, 2);

			switch ($cut) {
				case 'IM':
					$result = $this->table_import;
					break;
				case 'OR':
					$result = $this->table_bill;
					break;
				case 'IS':
					$result = $this->table_issue;
					break;
				case 'SP':
					$result = $this->table_node;
					break;
				case 'CC':
					$result = $this->table_lost;
					break;
				case 'PO':
					$result = $this->table_order;
					break;
				default:
					$result = null;
					break;
			}
		}

		return $result;
	}

	/**
	 * document table detail name
	 *
	 * @param String|null $code  = document code
	 * @return void
	 */
	public function get_documentTableItemFromCode(String $code = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$result = "";

		if ($code) {

			$cut = substr($code, 0, 2);

			switch ($cut) {
				case 'IM':
					$result = $this->table_import_item;
					break;
				case 'OR':
					$result = $this->table_bill_item;
					break;
				case 'IS':
					$result = $this->table_issue_item;
					break;
				case 'SP':
					$result = $this->table_node_item;
					break;
				case 'CC':
					$result = $this->table_lost_item;
					break;
				case 'PO':
					$result = $this->table_order_item;
					break;
				default:
					$result = null;
					break;
			}
		}

		return $result;
	}

	/**
	 * Undocumented function
	 *
	 * @param String|null $select
	 * @param array $optional = [column=>value]
	 * @return void
	 */
	public function get_completeAlias(String $select = null, array $optional = [])
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$sql = $ci->db->from('document_complete');
		if ($select) {
			$sql->select($select);
		}

		if ($optional && count($optional)) {
			foreach ($optional as $column => $value) {
				$sql->where($column, $value);
			}
		}

		$query = $sql->get();
		$result = $query->result();

		return $result;
	}

	/**
	 * document complete name
	 *
	 * @param Int|null $status = data on document_complete
	 * @return String complete anme
	 */
	public function get_completeText(Int $status = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$text = $this->get_completeAlias('NAME', array('id' => $status));

		foreach ($text as $key => $row) {
			$result = $row->NAME;
		}

		return $result;
	}

	/**
	 * gencode document
	 *
	 * @param String|null $doc_alias = import,issue,node
	 * @return void
	 */
	public function run_code(String $doc_alias = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		switch ($doc_alias) {
			case 'import':
				$prefix = 'IM';
				$table = $this->table_import;
				$table_item = $this->table_import_item;
				break;
			case 'bill':
				$prefix = 'OR';
				$table = $this->table_bill;
				$table_item = $this->table_bill_item;
				break;
			case 'issue':
				$prefix = 'IS';
				$table = $this->table_issue;
				$table_item = $this->table_issue_item;
				break;
			case 'node':
				$prefix = 'SP';
				$table = $this->table_node;
				$table_item = $this->table_node_item;
				break;
			case 'lost':
				$prefix = 'CC';
				$table = $this->table_lost;
				$table_item = $this->table_lost_item;
				break;
			case 'order':
				$prefix = 'PO';
				$table = $this->table_order;
				$table_item = $this->table_order_item;
				break;
			default:
				$table = "";
				$table_item = "";
				break;
		}

		$year = date('Y');
		$yearmonth = date('Ym');

		$sql = $ci->db->from($table)
			->where('year(date_starts)', $year)
			// ->where('status', 1)
			->order_by('id', 'desc')
			->get();
		$num = $sql->num_rows();

		if ($num) {
			$numbernext = $num + 1;
			$number = str_pad($numbernext, 4, '0', STR_PAD_LEFT);
		} else {
			$numbernext = 1;
			$number = str_pad($numbernext, 4, '0', STR_PAD_LEFT);
		}

		$result = $prefix . $yearmonth . $number;

		return $result;
	}

	/**
	 * doctype for doc_node
	 * ถ้า sup มาจากใบรับเข้า(import) หมายถึง ยืมของจากซัพมา ต้องส่งคืน (out)
	 * ถ้า sup มาจากใบเบิก(issue) หมายถึง ให้ซัพยืมของไป ต้องรับคืน (in)
	 *
	 * @param String|null $doc_alias = import,issue
	 * @return void
	 */
	public function get_docType(String $doc_alias = null)
	{
		$result = "in";
		if ($doc_alias == "import") {
			$result = "out";
		} else {
			$result = "in";
		}
		return $result;
	}
	public function get_docTypeText(String $doc_type = null)
	{
		if ($doc_type == "in") {
			$result = "รอรับคืน";
		} else {
			$result = "รอตัดออก";
		}
		return $result;
	}


	public function fetch_doc_cat()
	{
		# code...
		$result = [];

		$result = array(
			'IM'	=> array(
				'text_cat'	=> 'รับเข้า'
			),
			'IS'	=> array(
				'text_cat'	=> 'เบิก'
			),
			'OR'	=> array(
				'text_cat'	=> 'ขาย'
			),
			'PO'	=> array(
				'text_cat'	=> 'สั่งซื้อ'
			),
			'CC'	=> array(
				'text_cat'	=> 'สูญเสีย'
			),
		);

		return $result;
	}

	public function fetch_node()
	{
		# code...
		$result = [];

		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$remark = null;
		if (isset($optional['remark']) && $optional['remark']) {
			$remark = trim($optional['remark']);
		}

		$sql = $ci->db->where('status', 1)
			->get($this->node);
		$num = $sql->num_rows();
		if ($num) {
			$result = $sql->result();
		}

		return $result;
	}
}
