<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MY_Model extends CI_Model {
	var $table = '';
	var $id_field = '';

	var $cache = array();
	var $cache_enabled = FALSE;

	function __construct() {
		parent::__construct();
	}



	function get($id) {
		if ($this->cache_enabled) {
			if (isset($this->cache[$this->table][$id])) return $this->cache[$this->table][$id];
		}

		$rs = $this->db->select()
				->from($this->table)
				->where($this->id_field, $id)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();

		if ($this->cache_enabled) $this->cache[$this->table][$result[$this->id_field]] = $result;

		return $result;
	}

	function get_list($limit=0, $offset=0) {
		$rs = $this->db->select()
				->from($this->table)
				->get();

		$results = $rs->result_array();

		return $results;
	}

	function get_batch($ids, $id_as_key=FALSE) {
		if (count($ids)==0) return array();

		$results = array();
		if ($this->cache_enabled) {
			foreach($ids AS $k=>$id) {
				//var_dump($this->cache[$this->table][$id]);die();
				if (isset($this->cache[$this->table][$id])) {
					$results[] = $this->cache[$this->table][$id];
					unset($ids[$k]);
				}
			}
		}

		if (count($ids)>0) {
			$rs = $this->db->select()
					->from($this->table)
					->where_in($this->id_field, $ids)
					->get();

			$temp = $rs->result_array();

			if ($this->cache_enabled) {
				foreach($temp AS $t) {
					$this->cache[$this->table][$t[$this->id_field]] = $t;
				}
			}

			$results = $results + $temp;
		}

		if ($id_as_key) {
			$temp = $results;
			$results = array();

			foreach($temp AS $t) {
				$results[$t[$this->id_field]] = $t;
			}
		}

		return $results;
	}


	function save($data, $id_field='') {
		if ($id_field == '' || !isset($data[$id_field])) {
			$rs = $this->db->insert($this->table, $data);
			return $this->db->insert_id();
		} else {
			$rs = $this->db->where($id_field, $data[$id_field])
					->update($this->table, $data);
			return $data[$id_field];
		}

	}



	function fill_card_info(&$data, $mode='single') {
		if ($mode == 'single') {
			$data['card_info'] = $this->UserM->get($data['created_cardid']);
		} elseif ($mode == 'many') {
			$ids = extract_distinct_values($data, 'created_cardid');
			$cards = $this->UserM->get_batch($ids, TRUE);

			foreach($data AS $k=>$v) {
				$data[$k]['card_info'] = $cards[$v['created_cardid']];
			}
		}
	}





}