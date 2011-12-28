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
			'a_docs_dir_dirpath' => isset($values['dirpath']) ? $values['dirpath'] : '/',
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

	function update_doc($values) {
		$data = array(
			'a_docs_dirid' => isset($values['dirid']) ? $values['dirid'] : '',
			'a_docs_desc' => isset($values['desc']) ? $values['desc'] : '',
			'a_docs_stamp' => get_current_stamp(),
		);
		if (isset($values['id'])) {
			$this->db->update('a_docs', $data)
				->where('a_docs_id', $values['id']);
		} else {
			$this->db->insert('a_docs', $data);
		}
		$values['docsid'] = $this->db->insert_id();
		$this->update_doc_ver($values);
	}

	function update_doc_ver($values) {
		$data = array(
			'a_docs_ver_docsid' => isset($values['docsid']) ? $values['docsid'] : '',
			'a_docs_ver_filename' => isset($values['filename']) ? $values['filename'] : '',
			'a_docs_ver_stamp' => get_current_stamp(),
			'a_docs_ver_downloadhit' => isset($values['a_docs_ver_downloadhit']) ? $values['a_docs_ver_downloadhit'] : '',
			'a_docs_ver_cardid' => $this->UserM->info['cardid'],
			'a_docs_ver_uploadvia' => isset($values['uploadvia']) ? $values['uploadvia'] : '',
			'a_docs_ver_filesize' => isset($values['filesize']) ? $values['filesize'] : '',
			'a_docs_ver_mime' => isset($values['mime']) ? $values['mime'] : '',
			'a_docs_ver_ocr' => isset($values['ocr']) ? $values['ocr'] : '',
			'a_docs_ver_preview' => isset($values['preview']) ? $values['preview'] : '',
			'a_docs_ver_encrypt' => isset($values['encrypt']) ? $values['encrypt'] : '',
			'a_docs_ver_encryptkeytype' => isset($values['encryptkeytype']) ? $values['encryptkeytype'] : '',
		);
		if (isset($values['ver_id'])) {
			$this->db->update('a_docs_ver', $data)
				->where('a_docs_ver_id', $values['ver_id']);
		} else {
			$this->db->insert('a_docs_ver', $data);
		}
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

	function get_sub_folders() {
		$query = $this->db->select('a_docs_dir_id, a_docs_dir_name')
			->from('a_docs_dir')
			->where(array('a_docs_dir_cardid' => $this->UserM->info['cardid'],
					'a_docs_dir_parent' => $this->url['id_plain'],
				))
			->order_by('a_docs_dir_name')
			->get();
		return $query->result_array();
	}

	// Gets docs based on dirid
	function get_docs($id) {
		$query = $this->db->select('a_docs_dir_dirpath, a_docs_ver_filename, a_docs_ver_filesize, a_docs_ver_stamp')
			->from('a_docs')
			->join('a_docs_ver', 'a_docs.a_docs_id = a_docs_ver.a_docs_ver_docsid')
			->join('a_docs_dir', 'a_docs_dir.a_docs_dir_id = a_docs.a_docs_dirid')
			->where('a_docs_dirid', $id)
			->get();
		return $query->result_array();
	}

	function get_dir_parent_path($id) {
		$query = $this->db->select('a_docs_dir_id, a_docs_dir_name, a_docs_dir_parent')
			->from('a_docs_dir')
			->where(array('a_docs_dir_id'=>$id))
			->get();
		return $query->result_array();
	}

	function get_parent($id) {
		$query = $this->db->select('a_docs_dir_parent')
			->from('a_docs_dir')
			->where(array('a_docs_dir_id'=>$id))
			->get();
		return $query->row_array();
	}

	/*function get_latest_docsid() {
		$query = $this->db->select('a_docsid')
			->from('a_docs')
			->order_by('a_docs_docsid', 'desc')
			->get();
		return $query->row_array();
	}*/

	function get_root_dir() {
		$query = $this->db->select()
			->get_where('a_docs_dir', array('a_docs_dir_cardid'=>$this->UserM->info['cardid'], 'a_docs_dir_parent'=>0));
		return $query->row_array();
	}

	// Returns docs details based on path
	function get_doc_detail($path) {
		$dirpath_filename = explode_filename_dirpath($path);
		$query = $this->db->select()
			->from('a_docs')
			->join('a_docs_ver', 'a_docs.a_docs_id = a_docs_ver.a_docs_ver_docsid')
			->where('a_docs_dir_dirpath')
			->get();
	}

	function does_folder_exists($id) {
		$query = $this->db->select()
			->from('a_docs_dir')
			->where(array('a_docs_dir_id'=>$id,
					'a_docs_dir_cardid'=>$this->UserM->info['cardid']
				))
			->get();
		return $query->row_array();
	}
}
