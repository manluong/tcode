<?php

class CommentsM extends MY_Model {
	var $results_per_page = 5;

	function __construct() {
		$this->table = 'mod_comments';
		$this->id_field = 'id';

		parent::__construct();
	}


	function get($id) {
		$rs = $this->db->select()
				->from($this->table)
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
				->from($this->table)
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
		}

		return $results;
	}

	function get_page($app_id, $app_data_id, $page=1) {
		$page--;
		if ($page<0) $page = 0;

		$rs = $this->db->select()
				->from($this->table)
				->where('app_id', $app_id)
				->where('app_data_id', $app_data_id)
				->where('parent_id', 0)
				->order_by('id', 'DESC')
				->limit($this->results_per_page, ($this->results_per_page*$page))
				->get();

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		foreach($results AS $k=>$v) {
			$results[$k]['replies'] = $this->get_replies($v['id']);
		}

		return $results;
	}

	function get_replies($id, $limit=5) {
		$this->db->select()
				->from($this->table)
				->where('parent_id', $id)
				->order_by('id', 'DESC');

		if ($limit>0) $this->db->limit($limit);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		$results = array_combine(array_reverse(array_keys($results)), array_reverse(array_values($results)));

		return $results;
	}

	function get_more_replies($id) {
		//Retrieve all comments, except the first 5.
		$this->db->select()
				->from($this->table)
				->where('parent_id', $id)
				->limit(1000000, 5)	//update this if you have a better idea
				->order_by('id', 'DESC');

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		//$rs = $this->db->query("SELECT * FROM comments WHERE parent_id=$id LIMIT 0 OFFSET 5");

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		$results = array_combine(array_reverse(array_keys($results)), array_reverse(array_values($results)));

		return $results;
	}

	function save_reply(&$data) {
		$data['created_cardid'] = $this->UserM->get_cardid();
		$data['created_stamp'] = get_current_stamp();

		$data['id'] = $this->save($data, 'id');

		if ($data['parent_id'] != 0) {
			$this->update_comment_stats($data['parent_id']);
		}
	}

	function update_comment_stats($id) {
		$modified_cardid = $this->UserM->get_cardid();
		$modified_stamp = get_current_stamp();

		$sql = "UPDATE ".$this->table." SET reply_count=reply_count+1, modified_cardid=?, modified_stamp=? WHERE id=?";
		$this->db->query($sql, array($modified_cardid, $modified_stamp, $id));
	}


}
?>
