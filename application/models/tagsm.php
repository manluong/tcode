<?php

class TagsM extends MY_Model {

	function __construct() {
		$this->table = 'mod_tags';
		$this->id_field = array('app_id', 'app_data_id', 'tag');

		parent::__construct();
	}


	function get($app_id, $app_data_id) {
		$rs = $this->db->select('tag')
				->from($this->table)
				->where('app_id', $app_id)
				->where('app_data_id', $app_data_id)
				->order_by('tag', 'ASC')
				->get();

		if ($rs->num_rows() == 0) return array();

		$result = array();

		foreach($rs->result_array() AS $r) {
			$result[] = $r['tag'];
		}

		return $result;
	}

	function get_details($keys) {
		$results = array();

		$app_ids = extract_distinct_values($keys, 'app_id');
		$apps = $this->AppM->get_batch($app_ids, true);

		foreach($keys AS $key) {
			$rs = $this->db->select('tag')
					->from($this->table)
					->where('app_id', $key['app_id'])
					->where('app_data_id', $key['app_data_id'])
					->get();

			$tags = array();

			foreach($rs->result_array() AS $r) {
				$tags[] = $r['tag'];
			}

			$this->load->model($apps[$key['app_id']]['ci_model']);

			$temp = array(
				'app_id' => $key['app_id'],
				'app_name' => $apps[$key['app_id']]['core_apps_name'],
				'app_data_id' => $key['app_data_id'],
				'app_data_id_encoded' => encode_id($key['app_data_id']),
				'app_data_name' => $this->$apps[$key['app_id']]['ci_model']->get_data_name($key['app_data_id']),
				'tags' => $tags,
			);

			$results[] = $temp;
		}

		return $results;
	}

	function search($tag, $app_id='') {

		$this->db->select('DISTINCT app_id, app_data_id', FALSE)
			->from($this->table);

		if (is_array($tag)) {
			foreach($tag AS $t) {
				$this->db->or_like('tag', $t);
			}
		} else {
			$this->db->like('tag', $tag);
		}

		if ($app_id != '') $this->db->where('app_id', $app_id);

		$rs = $this->db->order_by('tag', 'ASC')
				->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}

	function add($app_id, $app_data_id, $tag) {
		$data = array(
			'app_id' => $app_id,
			'app_data_id' => $app_data_id,
			'tag' => $tag,
		);

		return $this->save($data, 'id');
	}

	function remove($app_id, $app_data_id, $tag) {
		return $this->db->where('app_id', $app_id)
				->where('app_data_id', $app_data_id)
				->where('tag', $tag)
				->delete($this->table);
	}


	function get_top($limit=10, $app_id='') {
		$this->db->select('tag, COUNT(tag) AS tag_count')
				->from($this->table)
				->group_by('tag')
				->order_by('tag_count', 'DESC')
				->limit($limit);

		if ($app_id != '') $this->db->where('app_id', $app_id);

		$rs = $this->db->get();

		return $rs->result_array();
	}
}
?>
