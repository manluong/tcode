<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MY_Model extends CI_Model {
	var $table = '';
	var $id_field = '';

	var $cache = array();
	var $cache_enabled = FALSE;

	var $where = array();

	function __construct() {
		parent::__construct();
	}



	function get($id) {
		if ($this->cache_enabled) {
			if (isset($this->cache[$this->table][$id])) return $this->cache[$this->table][$id];
		}

		$this->db->select()
			->from($this->table)
			->where($this->id_field, $id);

		if (count($this->where) > 0) {
			foreach($this->where AS $w) {
				$this->db->where($w);
			}
		}

		$rs = $this->db->get();
		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();

		if ($this->cache_enabled) $this->cache[$this->table][$result[$this->id_field]] = $result;

		return $result;
	}

	function get_list($limit=0, $offset=0) {
		$this->db->select()
			->from($this->table);

		if (count($this->where) > 0) {
			foreach($this->where AS $w) {
				$this->db->where($w);
			}
		}

		$rs = $this->db->get();

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





class DatasetM extends CI_Model {
	protected $db_tables = '';
	protected $fields = '';
	protected $data = '';
	protected $form_data = array();
	protected $properties = array();

	protected $where = array();
	protected $search_where = array();

	var $loaded = false;

	var $id = '';
	var $subaction = '';

	var $sql = '';

	var $data_errors = array();

	function __construct() {
		parent::__construct();
	}

	function load($ds) {
		$this->load_properties($ds);

		$subaction = $this->url['subaction'];
		if ($subaction == 'as') $subaction = 'a';
		if ($subaction == 'es') $subaction = 'e';

		//if ($this->properties[$subaction] == 0) die('The subaction:['.$subaction.'] you requested for this dataset is not allowed.');

		$this->subaction = $subaction;

		$this->load_tables($ds);
		$this->load_fields($ds);

		$this->id = $this->url['id_plain'];

		$this->loaded = true;

		return $this;
	}

	function get_data() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$this->load_data();

		$result = array();

		if ($this->subaction == 'l') {
			foreach($this->data AS $order=>$row) {
				foreach($row AS $key_field => $data) {
					if ($this->fields[$key_field][$this->subaction] == 0) continue;

					$result[$order][$key_field] = $data;
				}
			}
		} else {
			foreach($this->data AS $key_field=>$data) {
				if ($this->fields[$key_field][$this->subaction] == 0) continue;

				$result[$key_field] = $data;
			}
		}
		return $result;
	}

	function get_fields() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$fields = $this->fields;

		//remove unneeded data
		foreach($fields AS $k=>$f) {
			//checks avedls
			if ($f[$this->subaction] == 0) {
				unset($fields[$k]);
				continue;
			}

			unset($fields[$k]['dataset_name'], $fields[$k]['parent_join'], $fields[$k]['child_join']);
			unset($fields[$k]['a'], $fields[$k]['v'], $fields[$k]['e'], $fields[$k]['d'], $fields[$k]['l'], $fields[$k]['s'], $fields[$k]['sq']);
			unset($fields[$k]['sort_form'], $fields[$k]['sort_list'], $fields[$k]['sort_search']);
			unset($fields[$k]['sel_source'], $fields[$k]['sel_groupname'], $fields[$k]['sel_sql'], $fields[$k]['sel_sqlkey'], $fields[$k]['sel_sqlname']);

			unset($fields[$k]['db_primary']);
		}

		return $fields;
	}

	function get_list_ids() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$result = array();

		foreach($this->data AS $order=>$row) {
			foreach($row AS $key_field => $data) {
				if ($this->fields[$key_field]['db_table'] != $this->db_tables[0]['db_table']) continue;
				$form_key_field = $this->fields[$key_field]['db_table'].'_'.$this->get_form_field($this->fields[$key_field]['db_table']);
				if ($key_field != $form_key_field) continue;

				$result[$order] = $data;
			}
		}

		return $result;
	}

	function get_datatable_fields() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$results = array();

		$fields = $this->get_fields();
		foreach($fields AS $f) {
			$results[] = array('sTitle'=>$f['label']);
		}

		return $results;
	}

	function get_datatable_data() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$results = array();

		$data = $this->get_data();
		foreach($data AS $d) {
			$results[] = array_values($d);
		}

		return $results;
	}

	function get_view_data() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$result = array();
		foreach($this->get_data() AS $field_key=>$value) {
			$result[] = array(
				'fieldname' => $this->fields[$field_key]['db_field'],
				'label' => $this->fields[$field_key]['label'],
				'value' => $value,
			);
		}

		return $result;
	}

	function get_form_data() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$fields = $this->get_fields();
		$this->load_data();


		foreach($fields AS $field_key=>$field) {
			if ($this->subaction=='e') {
				$fields[$field_key]['value'] = $this->data[$field_key];
			} elseif ($this->subaction=='a') {
				$fields[$field_key]['value'] = $field['default_value'];
			}
			$fields[$field_key]['label'] = $this->fields[$field_key]['label'];
			$fields[$field_key]['db_field'] = $field_key;
			$fields[$field_key]['name'] = $field_key;	//temp override
		}

		return array_values($fields);
	}

	function search($string) {
		$search_fields = $this->get_search_fields();

		foreach($search_fields AS $s) {
			$this->search_where[] = $s." LIKE '".$string."'";
		}

		$this->load_data();

		return $this->data;
	}

	function delete() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$this->load_data(true);

		$pj_value = '';

		foreach($this->db_tables AS $order=>$table) {
			if ($order == 0) {
				$form_field = $this->get_form_field($table['db_table']);
				$form_value = $this->data[$table['db_table'].'_'.$form_field];

				$this->db->where($form_field, $form_value)
						->delete($table['db_table']);

				$pj_value = $this->data[$table['db_table'].'_'.$this->get_join_field($table['db_table'], 'parent_join')];
			} else {
				if ($pj_value == '') die('Unexpected error. There should be a primary table.');

				$cj_field = $this->get_join_field($table['db_table'], 'child_join');

				$this->db->where($cj_field, $pj_value)
						->delete($table['db_table']);
			}
		}
	}

	function save() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$this->load_submit_data();

		if (!$this->verify_data()) return FALSE;

		if ($this->subaction == 'e') {
			return $this->edit_save();
		} elseif ($this->subaction == 'a') {
			return $this->add_save();
		}
	}

	private function edit_save() {
		//load old data
		$this->load_data(true);

		//go through each table
		foreach($this->db_tables AS $order=>$table) {

			//gather the data for that table in a var
			$data = array();
			foreach($this->form_data AS $field_key=>$d) {
				if ($this->fields[$field_key]['db_table'] != $table['db_table']) continue;
				$data[$this->fields[$field_key]['db_field']] = $d;
			}

			//define the primary fields
			$form_field = $this->get_form_field($table['db_table']);
			$form_field_key = $table['db_table'].'_'.$form_field;

			$this->db->where($form_field, $this->data[$form_field_key])
					->update($table['db_table'], $data);
		}

		return TRUE;
	}

	private function add_save() {
		$table_keys = array();

		//go through each table
		foreach($this->db_tables AS $order=>$table) {
			//gather the data for that table in a var
			$data = array();
			foreach($this->form_data AS $field_key=>$d) {
				if ($this->fields[$field_key]['db_table'] != $table['db_table']) continue;
				$data[$this->fields[$field_key]['db_field']] = $d;
			}

			$this->db->insert($table['db_table'], $data);

			//define the primary fields
			$form_field_key = $table['db_table'].'_'.$this->get_form_field($table['db_table']);

			//if the form_id field value exists in the data
			$value = (isset($data[$this->fields[$form_field_key]['db_field']]))
					? $data[$this->fields[$form_field_key]['db_field']]
					: $this->db->insert_id();

			$table_keys[$order] = array(
				'form_field_key' => $form_field_key,
				'value' => $value,
			);

			//if primary table, store the parent_join field value
			if ($order == 0) $pt_value = $value;
		}

		//save pk-fk link for multiple tables
		if (count($this->db_tables)>1) {
			foreach($this->db_tables AS $order=>$table) {
				//skip primary table
				if ($order == 0) continue;

				//st = secondary table
				$st_form_field = $this->fields[$table_keys[$order]['form_field_key']];
				$st_form_value = $table_keys[$order]['value'];

				$cj_field = $this->fields[$table['db_table'].'_'.$this->get_join_field($table['db_table'], 'child_join')]['db_field'];

				$data = array(
					$cj_field => $pt_value,
				);

				$this->db->where($st_form_field, $st_form_value)
						->update($table['db_table'], $data);
			}
		}

		return TRUE;
	}

	function get_save_errors() {
		$result = array();

		foreach($this->data_errors AS $field=>$error_array) {
			$result[$field] = implode('<br />',$error_array);
		}

		return $result;
	}


	protected function load_properties($ds) {
		$rs = $this->db->select()
				->from('global_setting.core_dataset')
				->where('dataset_name', $ds)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) die($ds.' dataset does not exist.');

		$this->properties = $rs->row_array();
	}

	protected function load_tables($ds) {
		$rs = $this->db->select()
				->from('global_setting.core_dataset_tables')
				->where('dataset_name', $ds)
				->get();

		if ($rs->num_rows() == 0) die('No tables were configured for this dataset.');

		foreach($rs->result_array() AS $t) {
			if (isset($this->db_tables[$t['sort']])) die('More than 1 tables have the same sort number');
			$this->db_tables[$t['sort']] = $t;
			//load the language for each table
			$this->lang->load($this->LangM->get_array($t['app_name'], $this->lang->lang_use));
		}

		if (!isset($this->db_tables[0])) die('Primary table not set. It must have a sort order of 0.');
	}

	protected function load_fields($ds) {
		$rs = $this->db->select('core_dataset_fields.*, core_fields.*')
				->from('global_setting.core_dataset_fields')
				->join('global_setting.core_fields', 'core_dataset_fields.db_field=core_fields.name', 'left')
				->where('dataset_name', $ds)
				->get();

		if ($rs->num_rows() == 0) die('No fields were configured for this dataset.');

		foreach($rs->result_array() AS $r) {
			//fill in label
			$db_table_app_name = $this->get_table_app_name($r['db_table']);
			$r['label'] = $this->lang->line($db_table_app_name.'_'.$r['db_field']);

			//fill in select_options
			if ($r['form_type'] == 'select') $r['select_options'] = $this->get_select_options($r);

			//if form type not defined, default to text
			if ($r['form_type'] == '') $r['form_type'] = 'text';

			$field_key = $r['db_table'].'_'.$r['db_field'];
			$this->fields[$field_key] = $r;
		}
	}



	protected function load_data($unfiltered_fields = false) {
		//load fields
		$fields = array();
		foreach($this->fields AS $f) {
			$fields[] = $f['db_table'].'.'.$f['db_field'].' AS '.$f['db_table'].'_'.$f['db_field'];
		}
		$this->db->select(implode(', ', $fields), FALSE);

		//primary table
		$this->db->from($this->db_tables[0]['db_table']);

		//join any secondary tables
		if (count($this->db_tables)>1) {
			//join field of primary table
			$pj_field = $this->db_tables[0]['db_table'].'.'.$this->get_join_field($this->db_tables[0]['db_table']);

			foreach($this->db_tables AS $k=>$v) {
				if ($k == 0) continue;
				//join field of secondary table
				$cj_field = $v['db_table'].'.'.$this->get_join_field($v['db_table'], 'child_join');
				$this->db->join($v['db_table'], $pj_field.'='.$cj_field, 'left');
			}
		}

		//add where statement based on subaction
		if ($this->subaction == 'l') {
			if ($this->id!=0) $this->db->where($this->get_list_field(), $this->id);
		} else {
			$this->db->where($this->db_tables[0]['db_table'].'.'.$this->get_form_field($this->db_tables[0]['db_table']), $this->id);
		}

		//if there are criterias in $this->where and $this->search_where, load them
		foreach($this->where AS $w) {
			$this->db->where($w);
		}

		foreach($this->search_where AS $w) {
			$this->db->or_where($w);
		}

		//order by fields based on subaction
		if ($this->subaction == 'l') {
			$sort_fields = $this->get_sort_fields('sort_list');
		} elseif ($this->subaction == 's' || $this->subaction == 'sq') {
			$sort_fields = $this->get_sort_fields('sort_search');
		} else {
			$sort_fields = $this->get_sort_fields('sort_form');
		}
		if ($sort_fields != '') $this->db->order_by($sort_fields);

		$rs = $this->db->get();


		if ($this->subaction == 'l') {
			$this->data = $rs->result_array();
		} else {
			$this->data = $rs->row_array();
		}

		//store the SQL for debugging
		$this->sql = $this->db->last_query();
	}



	protected function load_submit_data() {
		foreach($this->fields AS $key_field=>$f) {
			if ($f[$this->subaction] == 0) continue;

			$this->form_data[$key_field] = $this->input->get_post($key_field, TRUE);
		}
	}


	protected function verify_data() {
		$has_error = FALSE;

		//check for required field
		foreach($this->form_data AS $key_field => $data) {
			if (!$this->fields[$key_field]['required']) continue;
			if ($data !== FALSE && strlen($data) > 0) continue;

			$has_error = TRUE;
			$this->data_errors[$key_field][] = 'Required Field';
		}

		//check for min length
		foreach($this->form_data AS $key_field => $data) {
			if ($this->fields[$key_field]['min'] == 0) continue;

			if ($this->fields[$key_field]['form_type'] == 'select') {
				if (count($data) >= $this->fields[$key_field]['min']) continue;

				$has_error = TRUE;
				$this->data_errors[$key_field][] = 'You need to select at least '.$this->fields[$key_field]['min'].' items.';
			} else {
				if (strlen($data) >= $this->fields[$key_field]['min']) continue;

				$has_error = TRUE;
				$this->data_errors[$key_field][] = 'You need to enter at least '.$this->fields[$key_field]['min'].' characters.';
			}
		}

		//check for max length
		foreach($this->form_data AS $key_field => $data) {
			if ($this->fields[$key_field]['max'] == 0) continue;

			if ($this->fields[$key_field]['form_type'] == 'select') {
				if (count($data) <= $this->fields[$key_field]['max']) continue;

				$has_error = TRUE;
				$this->data_errors[$key_field][] = 'You can only select up to '.$this->fields[$key_field]['max'].' items.';
			} else {
				if (strlen($data) <= $this->fields[$key_field]['max']) continue;

				$has_error = TRUE;
				$this->data_errors[$key_field][] = 'You can only enter up to '.$this->fields[$key_field]['max'].' characters.';
			}
		}

		//check for valid select options
		foreach($this->form_data AS $key_field => $data) {
			if ($this->fields[$key_field]['form_type'] != 'select') continue;

			$select_options = array();
			foreach($this->fields[$key_field]['select_options'] AS $s) {
				$select_options[] = $s['key'];
			}

			foreach($data AS $d) {
				if (in_array($d, $select_options)) continue;

				$has_error = TRUE;
				$this->data_errors[$key_field][] = 'Invalid selection detected: '.$d;
			}
		}

		//check for valid email
		foreach($this->form_data AS $key_field => $data) {
			if ($this->fields[$key_field]['form_type'] != 'email') continue;

			if (strpos($data, '@') !== FALSE) continue;

			$has_error = TRUE;
			$this->data_errors[$key_field][] = 'Invalid email address';
		}

		return !$has_error;
	}




	// $join = parent_join | child_join
	protected function get_join_field($table, $join='parent_join') {
		foreach($this->fields AS $f) {
			if ($f['db_table'] != $table) continue;
			if ($f[$join] == 1) return $f['db_field'];
		}
		die('This dataset has multiple tables but no join fields configured');
	}

	protected function get_list_field() {
		foreach($this->fields AS $f) {
			if ($f['list_id'] == 1) return $f['db_table'].'.'.$f['db_field'];
		}
		die('This dataset does not have a list_id field selected');
	}

	protected function get_form_field($table) {
		foreach($this->fields AS $f) {
			if ($f['db_table'] != $table) continue;
			if ($f['form_id'] == 1) return $f['db_field'];
		}
		die('This dataset does not have a form_id field selected');
	}

	protected function get_sort_fields($sort_field) {
		$fields = array();
		foreach($this->fields AS $f) {
			if ($f[$sort_field] == 0) continue;
			if ($f[$this->subaction] == 0) continue;	//checks avedls
			$fields[$f[$sort_field]] = $f['db_table'].'.'.$f['db_field'];
		}
		return implode(', ',$fields);
	}

	protected function get_search_fields() {
		$fields = array();
		foreach($this->fields AS $f) {
			if ($f[$this->subaction] == 0) continue;	//checks avedls
			$fields[$f['sort_search']] = $f['db_table'].'.'.$f['db_field'];
		}
		return $fields;
	}

	protected function get_table_app_name($table) {
		foreach($this->db_tables AS $t) {
			if ($t['db_table'] != $table) continue;

			return $t['app_name'];
		}
	}

	protected function get_select_options($field) {
		$result = array();

		//if field is not required, add blank selection
		if (!$field['required']) $result[] = array('key'=>'', 'value'=>'');

		if ($field['sel_source'] == 'group') {
			$rs = $this->db->select('core_select_name, core_select_value')
					->from('global_setting.core_select')
					->where('core_select_group', $field['sel_groupname'])
					->get();

			$key_field = 'core_select_value';
			$val_field = 'core_select_name';
		} else {
			$rs = $this->db->query($field['sel_sql']);

			$key_field = $field['sel_sqlkey'];
			$val_field = $field['sel_sqlname'];
		}

		foreach($rs->result_array() AS $r) {
			$result[] = array(
				'key' => $r[$key_field],
				'value' => $r[$val_field]
			);
		}

		return $result;
	}

}
