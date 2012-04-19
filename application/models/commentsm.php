<?php

class CommentsM extends MY_Model {
	var $results_per_page = 5;
	var $older_comments_top = FALSE;
	var $older_replies_top = TRUE;
	var $get_all = FALSE;
	var $threaded = TRUE;

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
		$this->insert_timeago_stamp($result);
		if ($result['parent_id'] != 0) {
			$result['in_reply_to']['name'] = $this->get_reply_to_name($result['parent_id']);
		}

		return $result;
	}

	function get_list($app_id, $app_data_id, $limit=5) {
		$this->db->select()
			->from($this->table)
			->where('app_id', $app_id)
			->where('app_data_id', $app_data_id)
			->where('parent_id', 0)
			->limit($limit);

		if ($this->older_comments_top) {
			$this->db->order_by('id', 'DESC');
		} else {
			$this->db->order_by('id', 'ASC');
		}

		$rs = $this->db->get();
		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		foreach($results AS $k=>$v) {
			$results[$k]['replies'] = $this->get_replies($v['id']);
			$this->insert_timeago_stamp($results[$k]);
			if ($v['parent_id'] != 0) {
				$results[$k]['in_reply_to']['name'] = $this->get_reply_to_name($v['parent_id']);
			}
		}

		if ($this->older_comments_top) {
			$results = array_reverse_order($results);
		}

		return $results;
	}

	function get_page($app_id, $app_data_id, $page=1, $replies=5) {
		$page--;
		if ($page<0) $page = 0;

		$this->db->select()
			->from($this->table)
			->where('app_id', $app_id)
			->where('app_data_id', $app_data_id);

		if ($this->threaded) {
			$this->db->where('parent_id', 0);
		}

		if ($this->get_all) {
			$this->db->limit(10000000, ($this->results_per_page*$page));
		} else {
			$this->db->limit($this->results_per_page, ($this->results_per_page*$page));
		}

		if ($this->older_comments_top) {
			$this->db->order_by('id', 'DESC');
		} else {
			$this->db->order_by('id', 'ASC');
		}

		$rs = $this->db->get();
		if ($rs->num_rows() == 0) return array();

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		if ($replies > 0) {
			foreach($results AS $k=>$v) {
				$results[$k]['replies'] = $this->get_replies($v['id'], $replies);
			}
		}

		if ($this->older_comments_top) {
			$results = array_reverse_order($results);
		}

		foreach($results AS $k=>$v) {
			$this->insert_timeago_stamp($results[$k]);
			if ($v['parent_id'] != 0) {
				$results[$k]['in_reply_to']['name'] = $this->get_reply_to_name($v['parent_id']);
			}
		}

		return $results;
	}

	function get_remaining($app_id, $app_data_id, $last_id) {
		$this->db->select()
			->from($this->table)
			->where('app_id', $app_id)
			->where('app_data_id', $app_data_id)
			->where('id <', $last_id);

		if ($this->threaded) {
			$this->db->where('parent_id', 0);
		}

		if ($this->older_comments_top) {
			$this->db->order_by('id', 'DESC');
		} else {
			$this->db->order_by('id', 'ASC');
		}

		$rs = $this->db->get();
		if ($rs->num_rows() == 0) return array();

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		if ($this->older_comments_top) {
			$results = array_reverse_order($results);
		}

		foreach($results AS $k=>$v) {
			$this->insert_timeago_stamp($results[$k]);

			if ($this->threaded) {
				$results[$k]['replies'] = $this->get_replies($v['id'], $replies);
			} else {
				if ($v['parent_id'] != 0) {
					$results[$k]['in_reply_to']['name'] = $this->get_reply_to_name($v['parent_id']);
				}
			}
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

		if ($this->older_replies_top) {
			$results = array_reverse_order($results);
		}

		foreach($results AS $k=>$v) {
			$this->insert_timeago_stamp($results[$k]);
		}

		return $results;
	}

	function get_more_replies($id, $skip_rows=5) {
		//Retrieve all comments, except the first $skip_rows.
		$this->db->select()
				->from($this->table)
				->where('parent_id', $id)
				->limit(1000000, $skip_rows)	//update this if you have a better idea
				->order_by('id', 'DESC');

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		//$rs = $this->db->query("SELECT * FROM comments WHERE parent_id=$id LIMIT 0 OFFSET 5");

		$results = $rs->result_array();

		$this->fill_card_info($results, 'many');

		if ($this->older_replies_top) {
			$results = array_reverse_order($results);
		}

		foreach($results AS $k=>$v) {
			$this->insert_timeago_stamp($results[$k]);
		}

		return $results;
	}

	function save(&$data) {
		$data['created_cardid'] = $this->UserM->get_card_id();
		$data['created_stamp'] = get_current_stamp();

		$data['id'] = parent::save($data, 'id');

		if ($data['parent_id'] != 0) {
			$this->update_comment_stats($data['parent_id']);
		}
	}

	function update_comment_stats($id) {
		$modified_cardid = $this->UserM->get_card_id();
		$modified_stamp = get_current_stamp();

		$sql = "UPDATE ".$this->table." SET reply_count=reply_count+1, modified_cardid=?, modified_stamp=? WHERE id=?";
		$this->db->query($sql, array($modified_cardid, $modified_stamp, $id));
	}

	function get_comment_count($app_id, $app_data_id) {
		$rs = $this->db->select()
				->from($this->table)
				->where('app_id', $app_id)
				->where('app_data_id', $app_data_id)
				->where('parent_id', 0)
				->get();

		return $rs->num_rows();
	}

	function get_reply_to_name($id) {
		$rs = $this->db->select('created_cardid')
				->from('mod_comments')
				->where('id', $id)
				->get();

		$result = $rs->row_array();

		return $this->UserM->get_data_name($result['created_cardid']);
	}

	function insert_timeago_stamp(&$result) {
		$result['created_stamp_iso8601'] = parse_stamp_user($result['created_stamp'], 'ISO_8601');
		$result['created_stamp_iso'] = parse_stamp_user($result['created_stamp'], 'ISO_DATE');
	}
}
?>
