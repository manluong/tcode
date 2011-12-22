<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class docsM extends My_Model {

	function __construct() {
		parent::__construct();
		$this->load->helper('date');
	}

	function update_folder($values) {
		$data = array(
			'a_docs_dir_name' => $values['name'],
			'a_docs_dir_parent' => isset($values['parent']) ? $values['parent'] : 0,
			'a_docs_dir_stamp' => get_current_stamp(),
			//'tag' => isset($values['tag'])? : '',
			'a_docs_dir_desc' => isset($values['desc'])? $values['desc'] : '',
			'a_docs_dir_cardid' => $this->UserM->info['cardid'],
			'a_docs_dir_dirtype' => isset($value['dirtype']) ? $value['dirtype'] : '',
			'a_docs_dir_hide' => isset($value['hide']) ? $value['hide'] : '',
			'a_docs_dir_nofile' => isset($value['nofile']) ? $value['nofile'] : '',
			'a_docs_dir_nodir' => isset($value['nodir']) ? $value['nodir'] : '',
			'a_docs_dir_filemaxsize' => isset($value['filemaxsize']) ? $value['filemaxsize'] : '',
			'a_docs_dir_dirmaxsize' => isset($value['dirmaxsize']) ? $value['dirmaxsize'] : '',
			'a_docs_dir_listmime' => isset($value['listmime']) ? $value['listmime'] : '',
			'a_docs_dir_listext' => isset($value['listext']) ? $value['listext'] : '',
			'a_docs_dir_listtype' => isset($value['listtype']) ? $value['listtype'] : '',
			'a_docs_dir_socialcomment' => isset($value['socialcomment']) ? $value['socialcomment'] : '',
			'a_docs_dir_sociallike' => isset($value['sociallike']) ? $value['sociallike'] : '',
			'a_docs_dir_socialstar' => isset($value['socialstar']) ? $value['socialstar'] : '',
			'a_docs_dir_socialack' => isset($value['socialack']) ? $value['socialack'] : '',
			'a_docs_dir_encrypt' => isset($value['encrypt']) ? $value['encrypt'] : '',
			'a_docs_dir_browsestyle' => isset($value['browsestyle']) ? $value['browsestyle'] : '',
			'a_docs_dir_reapp' => isset($value['reapp']) ? $value['reapp'] : '',
			'a_docs_dir_rean' => isset($value['rean']) ? $value['rean'] : '',
			'a_docs_dir_reaved' => isset($value['reaved']) ? $value['reaved'] : '',
			'a_docs_dir_noocrindex' => isset($value['noocrindex']) ? $value['noocrindex'] : '',
		);
		if (isset($values['id'])) {
			$this->db->update('a_docs_dir', $data)
				->where('id', $values['id']);
		} else {
			$this->db->insert('a_docs_dir', $data);
		}
		return $this->db->insert_id();
	}

	function update_doc($doc) {

	}

	function update_docs_setting($values) {

	}

	function update_dir_setting($values) {

	}

	function update_permissions($values) {

	}

	function update_permission_grp($values) {

	}

	function update_doc_status() {

	}

	function get_folders() {
		$query = $this->db->select('a_docs_dir_id, a_docs_dir_name')
			->from('a_docs_dir')
			->where('a_docs_dir_cardid', $this->UserM->info['cardid'])
			->order_by('a_docs_dir_name')
			->get();
		return $query->result_array();
	}

	function get_root_dir() {
		$query = $this->db->select('a_docs_dir_id')
			->get_where('a_docs_dir', array('a_docs_dir_cardid'=>$this->UserM->info['cardid']));
		return $query->row();
	}
}
