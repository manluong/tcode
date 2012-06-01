<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MY_Model extends CI_Model {
	public $table = '';
	public $database = '';
	public $id_field = 'id';

	public $cache = array();
	public $cache_enabled = FALSE;

	public $errors = array();
	public $field_errors = array();
	public $last_sql = '';

	public $select_fields = array();
	public $where = array();
	public $order_by = array();
	public $limit = 0;
	public $offset = 0;

	public $sett_filter_deleted = TRUE;
	public $sett_has_system_fields = TRUE;
	public $sett_fill_card_info = FALSE;
	public $sett_fill_details = TRUE;
	public $sett_skip_validation = FALSE;

	public $data_fields = array();
	public $search_fields = array();
	public $system_fields = array(
		'id'=>array(
			'type' => 'id',
		),
		'modified_stamp'=>array(
			'type' => 'datetime',
		),
		'modified_card_id'=>array(
			'type' => 'id',
		),
		'created_stamp'=>array(
			'type' => 'datetime',
		),
		'created_card_id'=>array(
			'type' => 'id',
		),
		'deleted'=>array(
			'type' => 'boolean',
			'default' => 0,
		),
	);

	private $CI;

	function __construct() {
		parent::__construct();

		$this->CI =& get_instance();
	}

	function set_where($where) {
		$this->where[] = $where;
		return $this;
	}

	function set_order_by($order_by) {
		$this->order_by[] = $order_by;
		return $this;
	}

	function set_offset($offset) {
		$this->offset = $offset;
		return $this;
	}

	function set_limit($limit) {
		$this->limit = $limit;
		return $this;
	}

	function set_database($database) {
		$this->database = $database.'.';
	}

	function reset() {
		$this->select_fields = array();
		$this->where = array();
		$this->order_by = array();
		$this->offset = 0;
		$this->limit = 0;
		$this->database = '';
	}

	function get($id) {
		if ($this->cache_enabled) {
			if (isset($this->cache[$this->database.$this->table][$id])) return $this->cache[$this->database.$this->table][$id];
		}

		if (count($this->select_fields) > 0) {
			$this->db->select(implode(', ', $this->select_fields));
		} else {
			$this->db->select();
		}

		$this->db->from($this->database.$this->table)
			->where($this->id_field, $id);

		if (count($this->where) > 0) {
			foreach($this->where AS $w) {
				$this->db->where($w, NULL, FALSE);
			}
		}

		if ($this->sett_has_system_fields && $this->sett_filter_deleted) {
			$this->db->where('deleted', 0);
		}

		$rs = $this->db->get();
		$this->last_sql = $this->db->last_query();
		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();

		if ($this->cache_enabled) $this->cache[$this->database.$this->table][$result[$this->id_field]] = $result;

		if ($this->sett_fill_details) $this->fill_details($result, SINGLE_DATA);
		if ($this->sett_fill_card_info) $this->fill_card_info($result, SINGLE_DATA);

		return $result;
	}

	function get_list() {
		if (count($this->select_fields) > 0) {
			$this->db->select(implode(', ', $this->select_fields));
		} else {
			$this->db->select();
		}

		$this->db->from($this->database.$this->table);

		if (count($this->where) > 0) {
			foreach($this->where AS $w) {
				$this->db->where($w, NULL, FALSE);
			}
		}

		if ($this->sett_has_system_fields && $this->sett_filter_deleted) {
			$this->db->where('deleted', 0);
		}

		if (count($this->order_by) > 0) {
			foreach($this->order_by AS $w) {
				$this->db->order_by($w);
			}
		}

		if ($this->limit > 0  && $this->offset > 0) {
			$this->db->limit($this->limit, $this->offset);
		} elseif ($this->limit > 0 && $this->offset == 0) {
			$this->db->limit($this->limit);
		} elseif ($this->limit == 0 && $this->offset > 0) {
			$this->db->limit($this->limit, $this->offset);
		}

		$rs = $this->db->get();
		$this->last_sql = $this->db->last_query();

		if ($rs->num_rows() == 0) return FALSE;

		$results = $rs->result_array();

		if ($this->sett_fill_details) $this->fill_details($results, MULTIPLE_DATA);
		if ($this->sett_fill_card_info) $this->fill_card_info($results, MULTIPLE_DATA);

		return $results;
	}

	function get_batch($ids, $id_as_key=FALSE) {
		if (count($ids)==0) return array();

		$results = array();
		if ($this->cache_enabled) {
			foreach($ids AS $k=>$id) {
				//var_dump($this->cache[$this->database.$this->table][$id]);die();
				if (isset($this->cache[$this->database.$this->table][$id])) {
					$results[] = $this->cache[$this->database.$this->table][$id];
					unset($ids[$k]);
				}
			}
		}

		if (count($ids)>0) {
			if (count($this->select_fields) > 0) {
				$this->db->select(implode(', ', $this->select_fields));
			} else {
				$this->db->select();
			}

			$this->db->from($this->database.$this->table)
				->where_in($this->id_field, $ids);

			if ($this->sett_has_system_fields && $this->sett_filter_deleted) {
				$this->db->where('deleted', 0);
			}

			$rs = $this->db->get();
			$this->last_sql = $this->db->last_query();

			if ($rs->num_rows() == 0) return FALSE;

			$temp = $rs->result_array();

			if ($this->cache_enabled) {
				foreach($temp AS $t) {
					$this->cache[$this->database.$this->table][$t[$this->id_field]] = $t;
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

		if ($this->sett_fill_details) $this->fill_details($results, MULTIPLE_DATA);
		if ($this->sett_fill_card_info) $this->fill_card_info($results, MULTIPLE_DATA);

		return $results;
	}

	function get_total_records() {
		$this->db->select($this->id_field)
			->from($this->database.$this->table);

		if (count($this->where) > 0) {
			foreach($this->where AS $w) {
				$this->db->where($w, NULL, FALSE);
			}
		}

		if ($this->sett_has_system_fields && $this->sett_filter_deleted) {
			$this->db->where('deleted', 0);
		}

		$rs = $this->db->get();
		$this->last_sql = $this->db->last_query();

		return $rs->num_rows();
	}

	function get_errors() {
		return $this->errors;
	}

	function get_error_string() {
		return implode("\n", $this->errors);
	}

	function is_valid(&$data) {
		$is_new = !(isset($data[$this->id_field]) && $data[$this->id_field] !== FALSE);
		$has_error = FALSE;

		if (!$is_new) {
			$existing_data = $this->get($data[$this->id_field]);
		}

		foreach($this->data_fields AS $field=>$field_detail) {
			if ($is_new && isset($d['db_save_skip']) && $d['db_save_skip']===TRUE) continue;
			if (!$is_new && isset($d['db_edit_skip']) && $d['db_edit_skip']===TRUE) continue;

			if (isset($data[$field])) { //data_field exists in form data
				if (isset($field_detail['allow_blank']) && $field_detail['allow_blank']===FALSE && strlen($data[$field])==0) {
					$this->errors[] = $this->lang->line('error-cannot_be_blank').' : '.$this->lang->line($this->table.'-'.$field);
					$this->field_errors[$field][] = $this->lang->line('error-cannot_be_blank');
					$has_error = TRUE;
				}

				switch($field_detail['type']) {
					case 'id':
					case 'text':
						break;
					case 'numeric':
						if (!is_numeric($data[$field])) {
							$this->errors[] = $this->lang->line('error-not_numeric_field').' : '.$this->lang->line($this->table.'-'.$field);
							$this->field_errors[$field][] = $this->lang->line('error-not_numeric_field');
							$has_error = TRUE;
						}
						break;
					case 'date':
						//TODO: date validation
						break;
					case 'datetime':
						//TODO: datetime validation
						break;
					case 'selection':
						if (!in_array($data[$field], array_keys($field_detail['options']))) {
							$this->errors[] = $this->lang->line('error-invalid_option').' : '.$this->lang->line($this->table.'-'.$field);
							$this->field_errors[$field][] = $this->lang->line('error-invalid_option');
							$has_error = TRUE;
						}
						break;
					case 'boolen':
						//TODO: bool validation. use is_bool?
						break;
					case 'email':
						$result = preg_match('/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i', $data[$field]);
						if ($result === 0 || $result === FALSE) {
							$this->errors[] = $this->lang->line('error-invalid_email').' : '.$this->lang->line($this->table.'-'.$field);
							$this->field_errors[$field][] = $this->lang->line('error-invalid_email');
							$has_error = TRUE;
						}
						break;
				}

				if (isset($field_detail['regex_validation']) && $field_detail['regex_validation']!=='') {
					$result = preg_match($field_detail['regex_validation'], $data[$field]);
					if ($result === 0 || $result === FALSE) {
						$this->errors[] = $this->lang->line('error-regex_failed').' : '.$this->lang->line($this->table.'-'.$field);
						$this->field_errors[$field][] = $this->lang->line('error-regex_failed');
						$has_error = TRUE;
					}
				}

				if (isset($field_detail['min_length']) && is_numeric($field_detail['min_length'])) {
					if (strlen($data[$field]) < $field_detail['min_length']) {
						$error_string = str_replace('#char#', $field_detail['min_length'], $this->lang->line('error-min_length'));
						$this->errors[] = $error_string.' : '.$this->lang->line($this->table.'-'.$field);
						$this->field_errors[$field][] = $error_string;
						$has_error = TRUE;
					}
				}

				if (isset($field_detail['max_length']) && is_numeric($field_detail['max_length'])) {
					if (strlen($data[$field]) > $field_detail['max_length']) {
						$error_string = str_replace('#char#', $field_detail['max_length'], $this->lang->line('error-max_length'));
						$this->errors[] = $error_string.' : '.$this->lang->line($this->table.'-'.$field);
						$this->field_errors[$field][] = $error_string;
						$has_error = TRUE;
					}
				}
			} else { //data_field not in form data
				if (isset($field_detail['required']) && $field_detail['required']===TRUE) {
					if ($is_new) {
						$this->errors[] = $this->lang->line('error-required_field').' : '.$this->lang->line($this->table.'-'.$field);
						$this->field_errors[$field][] = $this->lang->line('error-required_field');
						$has_error = TRUE;
					} else {
						if (isset($existing_data[$field])) continue;
					}
				}
			}
		}

		return !$has_error;
	}

	function get_differences($new_data) {
		$id = $new_data[$this->id_field];

		$this->reset();
		$existing = $this->get($id);

		$diff = array();
		foreach($new_data AS $field => $new_value) {
			if ($existing[$field] === $new_value) continue;

			$diff[] = array(
				'table' => $this->table,
				'data_id' => $id,
				'field' => $field,
				'old' => $existing[$field],
				'new' => $new_value,
			);
		}

		return $diff;
	}

	function save_differences($diff) {
		if (count($diff) == 0) return;

		foreach($diff AS $k=>$v) {
			$diff[$k]['created_stamp'] = get_current_stamp();
			$diff[$k]['created_card_id'] = $this->UserM->get_card_id();
		}

		$this->db->insert_batch($this->database.'log_audit', $diff);
	}


	function save($data=FALSE) {
		//get data from POST based on data_fields
		if ($data === FALSE) {
			$data = $this->get_form_data();
			if ($data === FALSE) {
				$this->errors[] = $this->lang->line('error-no_save_data');
				$this->field_errors[$this->id_field][] = $this->lang->line('error-no_save_data');
				return FALSE;
			}
		}

		$is_new = (!isset($data[$this->id_field]) || $data[$this->id_field] === FALSE || !is_numeric($data[$this->id_field]));

		//perform validation
		if ($this->sett_skip_validation) {
			$is_valid = TRUE;
		} else {
			$is_valid = $this->is_valid($data);
		}

		//if all ok, proceed to save/update
		if (!$is_valid) return FALSE;

		if ($is_new) {
			if ($this->sett_has_system_fields) {
				$data['created_stamp'] = get_current_stamp();
				$data['created_card_id'] = $this->CI->UserM->get_card_id();
			}

			$rs = $this->db->insert($this->database.$this->table, $data);
			$this->last_sql = $this->db->last_query();
			return $this->db->insert_id();
		} else {
			$id = $data[$this->id_field];

			$diff = $this->get_differences($data);
			$this->save_differences($diff);

			if ($this->sett_has_system_fields) {
				$data['modified_stamp'] = get_current_stamp();
				$data['modified_card_id'] = $this->CI->UserM->get_card_id();
			}

			$rs = $this->db->where($this->id_field, $id)
					->update($this->database.$this->table, $data);
			$this->last_sql = $this->db->last_query();
			return $id;
		}
	}


	function delete($id, $actual_delete=FALSE) {
		if ($this->sett_has_system_fields && $actual_delete===FALSE) {
			$data = array(
				'modified_stamp' => get_current_stamp(),
				'modified_card_id' => $this->CI->UserM->get_card_id(),
				'deleted' => 1,
			);
			$rs = $this->db->where($this->id_field, $id)
					->limit(1)
					->update($this->database.$this->table, $data);
		} else {
			$rs = $this->db->where($this->id_field, $id)
					->limit(1)
					->delete($this->database.$this->table);
		}
		$this->last_sql = $this->db->last_query();

		if ($rs === FALSE) $this->errors[] = $this->db->_error_message();

		return $rs;
	}



	function fill_card_info(&$data, $mode=SINGLE_DATA) {
		//end if there's no data
		if (count($data) == 0) return;

		$card_id_fields = array();
		$card_ids = array();

		if ($mode == SINGLE_DATA) $data = array($data);

		//gather fields that have card_id inside, together with the values
		foreach($data AS $k=>$row) {
			//on first loop, gather the card_id fields
			if ($k == 0) {
				foreach($row AS $sk=>$sv) {
					if (strpos($sk, 'card_id') !== FALSE) $card_id_fields[] = $sk;
				}
			}

			//on first loop and next, gather the card_id based on the fields gathered in first loop
			foreach($card_id_fields AS $field) {
				$card_ids[$row[$field]] = '';	//store ID as key so duplicate card_ids will be ignored
			}
		}

		$card_ids = array_keys($card_ids);	//the card_ids were stored as array keys, so put them back into array values

		//fetch card info based on gathered keys
		$cards = $this->CI->UserM->get_batch($card_ids, TRUE);

		foreach($data AS $k=>$v) {
			foreach($card_id_fields AS $field) {
				$card_field_name = str_replace('card_id', 'card_info', $field);
				if (isset($cards[$v[$field]])) {
					$data[$k][$card_field_name] = $cards[$v[$field]];
				} else {
					$data[$k][$card_field_name] = 'No card info for this card_id: '.$v[$field];
				}
			}
		}

		if ($mode == SINGLE_DATA) $data = $data[0];
	}

	function fill_details(&$data, $mode=SINGLE_DATA) {
		$df = $this->data_fields;

		//echo 'fill start<pre>',print_r($data,true),'</pre>';
		if ($mode == SINGLE_DATA) $data = array($data);

		foreach($data AS $k=>$v) {
			$temp = array();
			foreach($v AS $sk=>$sv) {
				if (isset($df[$sk])) $temp[$sk.'_label'] = $this->get_label($sk);
				if (isset($df[$sk]['options'])) {
					$temp[$sk.'_options'] = $this->get_options($sk);
				}
			}

			foreach($temp AS $sk=>$sv) {
				$data[$k][$sk] = $sv;
			}
		}

		if ($mode == SINGLE_DATA) $data = $data[0];
		//echo 'fill end<pre>',print_r($data,true),'</pre>';
	}

	function get_form_data() {
		$data = array();

		$data[$this->id_field] = $this->input->post($this->id_field);

		$is_new = ($data[$this->id_field] === FALSE);

		foreach($this->data_fields AS $f => $d) {
			if ($is_new && isset($d['db_save_skip']) && $d['db_save_skip']===TRUE) continue;
			if (!$is_new && isset($d['db_edit_skip']) && $d['db_edit_skip']===TRUE) continue;

			$form_field = $this->input->post($f);
			if ($form_field !== FALSE) $data[$f] = $form_field;
		}

		if (count($data) == 0) return FALSE;

		return $data;
	}

	function search($search_string) {
		if (count($this->select_fields) > 0) {
			$this->db->select(implode(', ', $this->select_fields));
		} else {
			$this->db->select();
		}

		$this->db->from($this->database.$this->table);

		foreach($this->search_fields AS $sf) {
			if (count($sf) == 1) {
				$sf = array_shift($sf);
				$this->db->or_like($sf, $search_string, 'after');
			} else {
				$sf = implode(",' ',", $sf);
				$this->db->or_like("CONCAT($sf)", $search_string, 'after');
			}
		}

		if ($this->sett_has_system_fields && $this->sett_filter_deleted) {
			$this->db->where('deleted', 0);
		}

		if (count($this->where) > 0) {
			foreach($this->where AS $w) {
				$this->db->where($w, NULL, FALSE);
			}
		}

		$rs = $this->db->get();
		$this->last_sql = $this->db->last_query();

		return $rs->result_array();
	}
	function get_options($datafield) {
		if (!isset($this->data_fields[$datafield]['options'])) return array();

		$options = array();

		foreach($this->data_fields[$datafield]['options'] AS $ok=>$ov) {
			if ($ov == '') {
				$options[$ok] = '';
			} else {
				$options[$ok] = $this->lang->line($ov);
			}
		}

		return $options;
	}

	function get_label($datafield) {
		return $this->lang->line($this->table.'-'.$datafield);
	}
}