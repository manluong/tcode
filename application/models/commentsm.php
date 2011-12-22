<?php

class CommentsM extends MY_Model {

	function __construct() {
		parent::__construct();
	}


	function get($id) {
		$rs = $this->db->select()
				->from('comments')
				->where('id', $id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();

		$this->fill_card_info($result);

		$result['replies'] = $this->get_replies($id);

		return $result;
	}

	function get_list($app_id, $app_data_id, $limit=5) {
		$rs = $this->db->select()
				->from('comments')
				->where('app_id', $app_id)
				->where('app_data_id', $app_data_id)
				->where('parent_id', 0)
				->order_by('id', 'DESC')
				->limit($limit)
				->get();

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		foreach($results AS $k=>$v) {
			$results[$k]['replies'] = $this->get_replies($v['id']);
			$results[$k]['datetime'] = $v['created_stamp'];
		}

		return $results;
	}

	function get_replies($id, $limit=5) {
		$this->db->select()
				->from('comments')
				->where('parent_id', $id)
				->order_by('id', 'DESC');

		if ($limit>0) $this->db->limit($limit);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		foreach($results AS $k=>$v) {
			$results[$k]['datetime'] = $v['created_stamp'];
		}

		$results = array_combine(array_reverse(array_keys($results)), array_reverse(array_values($results)));

		return $results;
	}

	function save_reply($data) {
		$data['created_cardid'] = $this->UserM->get_cardid();
		$data['created_stamp'] = get_current_stamp();

		$this->db->insert('comments', $data);
	}


}
?>
