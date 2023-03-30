<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Status
{

	private $table = 'status_alias';

	public function __construct()
	{
	}

	/**
	 * data status
	 *
	 * @param string $select
	 * @param array $optional = [column=>value]
	 * @param string $order = 'column sort,column sort'
	 * @param integer|null $limit
	 * @param integer $start
	 * @return void
	 */
	function get_data(String $select = '*', array $optional = [], String $order = 'id asc', int $limit = null, int $start = 0)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		$sql = $ci->db->select($select);
		$sql->from($this->table);

		if ($optional && count($optional)) {
			foreach ($optional as $column => $value) {
				$sql->where($column, $value);
			}
		}

		$sql->where('status', 1);
		$sql->order_by($order);

		if($limit){
			$sql->limit($limit,$start);
		}

		$result = $sql->get();

		return $result->result();
	}

	function get_status_payment(){
		$optional = array(
			'alias'	=> 'payment'
		);

		$result = $this->get_data(false,$optional);

		return $result;
	}

}
