<?php

class DatasetM extends CI_Model {
	protected $db_tables = '';
	protected $fields = '';
	protected $data = '';
	protected $properties = array();

	var $loaded = false;

	var $sql = '';

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

			unset($fields[$k]['a'], $fields[$k]['v'], $fields[$k]['e'], $fields[$k]['d'], $fields[$k]['l'], $fields[$k]['s']);
			unset($fields[$k]['sort_form'], $fields[$k]['sort_list'], $fields[$k]['sort_search']);
		}

		return $fields;
	}

	function get_datatable_fields() {
		$results = array();

		$fields = $this->get_fields();
		foreach($fields AS $f) {
			$results[] = array('sTitle'=>$f['db_field']);
//			$result[] = array('sTitle'=>$f['label']);
		}

		return $results;
	}

	function get_datatable_data() {
		$results = array();

		$data = $this->get_data();
		foreach($data AS $d) {
			$results[] = array_values($d);
		}

		return $results;
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

		$this->fields = $rs->result_array();

		foreach($this->fields AS $k=>$v) {
			$this->fields[$k]['label'] = $this->lang->line($this->properties['app_name'].'_'.$v['db_field']);
		}
	}



	private function load_data() {
		//load fields
		$fields = array();
		foreach($this->fields AS $f) {
			//checks avedls
			if ($f[$this->url['subaction']] == 1) $fields[] = $f['db_table'].'.'.$f['db_field'].' ';
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
			$this->db->where($this->get_form_field(), $this->url['id_plain']);
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

		$this->sql = $this->db->last_query();
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

	private function get_form_field() {
		foreach($this->fields AS $f) {
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

}
?>
