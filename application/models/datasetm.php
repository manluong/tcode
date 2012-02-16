<?php

class DatasetM extends CI_Model {
	protected $db_tables = '';
	protected $fields = '';
	protected $data = '';
	protected $form_data = array();
	protected $properties = array();

	var $loaded = false;

	var $sql = '';

	var $data_errors = array();

	function __construct() {
		parent::__construct();
	}

	function load($ds) {
		$this->load_properties($ds);

		if ($this->properties[$this->url['subaction']] == 0) die('The subaction you requested for this dataset is not allowed.');

		$this->load_tables($ds);
		$this->load_fields($ds);

		$this->loaded = true;
	}

	function get_data() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$this->load_data();
		return $this->data;
	}

	function get_fields() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$fields = $this->fields;

		//remove unneeded data
		foreach($fields AS $k=>$f) {
			//checks avedls
			if ($f[$this->url['subaction']] == 0) {
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

	function get_datatable_fields() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$results = array();

		$fields = $this->get_fields();
		foreach($fields AS $f) {
			$results[] = array('sTitle'=>$f['name']);
//			$result[] = array('sTitle'=>$f['label']);
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
				'label' => $this->fields[$field_key]['db_field'],
//				'label' => $this->fields[$field_key]['label'],
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
			if ($this->url['subaction']=='e') {
				$fields[$field_key]['value'] = $this->data[$field_key];
			} elseif ($this->url['subaction']=='a') {
				$fields[$field_key]['value'] = $field['default_value'];
			}
			$fields[$field_key]['label'] = $this->fields[$field_key]['db_field'];
			$fields[$field_key]['db_field'] = $field_key;
			$fields[$field_key]['name'] = $field_key;	//temp override
		}

		return array_values($fields);
	}


	function save() {
		if (!$this->loaded) die('No dataset loaded, please call $this->DatasetM->load($dataset_name) first.');

		$this->load_submit_data();

		if ($this->verify_data()) {
			//data verified

			//load old data
			if ($this->url['subaction'] == 'es') $this->load_data();

			//go through each table
			foreach($this->db_tables AS $order=>$table) {

				//gather the data for that table in a var
				$data = array();
				foreach($this->form_data AS $key_field=>$d) {
					if ($this->fields[$key_field]['db_table'] != $table['db_table']) continue;
					$data[$this->fields[$key_field]['db_field']] = $d;
				}

				if ($this->url['subaction'] == 'es') {
					//define the primary fields
					$primary_field = $this->get_form_field($table['db_table']);
					$primary_key_field = $table['db_table'].'_'.$primary_field;

					$this->db->where($primary_field, $this->data[$primary_key_field])
							->update($table['db_table'], $data);
				} elseif ($this->url['subaction'] == 'as') {
					$this->db->insert($table['db_table'], $data);
				}
			}
			return TRUE;
		} else {
			//error in data
			return FALSE;
		}
	}

	function get_save_errors() {
		$result = array();

		foreach($this->data_errors AS $field=>$error_array) {
			$result[$field] = implode('<br />',$error_array);
		}

		return $result;
	}


	private function load_properties($ds) {
		$rs = $this->db->select()
				->from('core_dataset')
				->where('dataset_name', $ds)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) die($ds.' dataset does not exist.');

		$this->properties = $rs->row_array();
	}

	private function load_tables($ds) {
		$rs = $this->db->select()
				->from('core_dataset_tables')
				->where('dataset_name', $ds)
				->get();

		if ($rs->num_rows() == 0) die('No tables were configured for this dataset.');

		foreach($rs->result_array() AS $t) {
			if (isset($this->db_tables[$t['sort']])) die('More than 1 tables have the same sort number');
			$this->db_tables[$t['sort']] = $t;
		}
	}

	private function load_fields($ds) {
		$rs = $this->db->select('core_dataset_fields.*, core_fields.*')
				->from('core_dataset_fields')
				->join('core_fields', 'core_dataset_fields.db_field=core_fields.name', 'left')
				->where('dataset_name', $ds)
				->get();

		if ($rs->num_rows() == 0) die('No fields were configured for this dataset.');

		foreach($rs->result_array() AS $r) {
			//fill in label
			$r['label'] = $this->lang->line($this->properties['app_name'].'_'.$r['db_field']);

			//fill in select_options
			if ($r['form_type'] == 'select') $r['select_options'] = $this->get_select_options($r);

			//if form type not defined, default to text
			if ($r['form_type'] == '') $r['form_type'] = 'text';

			$this->fields[$r['db_table'].'_'.$r['db_field']] = $r;
		}
	}



	private function load_data() {
		//load fields
		$fields = array();
		foreach($this->fields AS $f) {
			//checks avedls
			if ($f[$this->url['subaction']] == 1) $fields[] = $f['db_table'].'.'.$f['db_field'].' AS '.$f['db_table'].'_'.$f['db_field'];
		}
		$this->db->select(implode(', ', $fields), FALSE);

		//primary table
		$this->db->from($this->db_tables[0]['db_table']);

		//join any secondary tables
		if (count($this->db_tables)>1) {
			//join field of primary table
			$pj_field = $this->get_join_field($this->db_tables[0]['db_table']);

			foreach($this->db_tables AS $k=>$v) {
				if ($k == 0) continue;
				//join field of secondary table
				$cj_field = $this->get_join_field($v['db_table'], 'child_join');
				$this->db->join($v['db_table'], $pj_field.'='.$cj_field, 'left');
			}
		}

		//add where statement based on subaction
		if ($this->url['subaction'] == 'l') {
			if ($this->url['id_plain']!=0) $this->db->where($this->get_list_field(), $this->url['id_plain']);
		} else {
			$this->db->where($this->get_form_field($this->db_tables[0]['db_table']), $this->url['id_plain']);
		}

		//order by fields based on subaction
		if ($this->url['subaction'] == 'l') {
			$sort_fields = $this->get_sort_fields('sort_list');
		} elseif ($this->url['subaction'] == 's') {
			$sort_fields = $this->get_sort_fields('sort_search');
		} else {
			$sort_fields = $this->get_sort_fields('sort_form');
		}
		if ($sort_fields != '') $this->db->order_by($sort_fields);

		$rs = $this->db->get();


		if ($this->url['subaction'] == 'l') {
			$this->data = $rs->result_array();
		} else {
			$this->data = $rs->row_array();
		}

		//store the SQL for debugging
		$this->sql = $this->db->last_query();
	}



	private function load_submit_data() {
		foreach($this->fields AS $key_field=>$f) {
			if ($this->url['subaction']=='es' && $f['e'] == 0) continue;
			if ($this->url['subaction']=='as' && $f['a'] == 0) continue;

			$this->form_data[$key_field] = $this->input->get_post($key_field, TRUE);
		}
	}


	private function verify_data() {
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
	private function get_join_field($table, $join='parent_join') {
		foreach($this->fields AS $f) {
			if ($f['db_table'] != $table) continue;
			if ($f[$join] == 1) return $f['db_table'].'.'.$f['db_field'];
		}
		die('This dataset has multiple tables but no join fields configured');
	}

	private function get_list_field() {
		foreach($this->fields AS $f) {
			if ($f['list_id'] == 1) return $f['db_table'].'.'.$f['db_field'];
		}
		die('This dataset does not have a list_id field selected');
	}

	private function get_form_field($table) {
		foreach($this->fields AS $f) {
			if ($f['db_table'] != $table) continue;
			if ($f['form_id'] == 1) return $f['db_table'].'.'.$f['db_field'];
		}
		die('This dataset does not have a form_id field selected');
	}

	private function get_sort_fields($sort_field) {
		$fields = array();
		foreach($this->fields AS $f) {
			if ($f[$sort_field] == 0) continue;
			if ($f[$this->url['subaction']] == 0) continue;	//checks avedls
			$fields[$f[$sort_field]] = $f['db_table'].'.'.$f['db_field'];
		}
		return implode(', ',$fields);
	}

	private function get_select_options($field) {
		$result = array();

		//if field is not required, add blank selection
		if (!$field['required']) $result[] = array('key'=>'', 'value'=>'');

		if ($field['select_source'] == 'group') {
			$rs = $this->db->select('core_select_name, core_select_value')
					->from('core_select')
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
