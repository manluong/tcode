<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class docsM extends My_Model {

	function __construct() {
		parent::__construct();
	}

	function delete_docs($id) {
		$this->db->delete('a_docs_ver', array('a_docs_ver_docsid'=>$id));
		$this->db->delete('a_docs', array('a_docs_id'=>$id));
	}

	function does_folder_exists($id) {
		$query = $this->db->select()
			->from('a_docs_dir')
			->where(array('a_docs_dir_docs_id'=>$id,
					'a_docs_dir_cardid'=>$this->UserM->info['cardid']
				))
			->get();
		return $query->row_array();
	}

	// Pass in docs id
	function get_dirpath($id) {
		$query = $this->db->select('a_docs_dir_dirpath')
			->from('a_docs_dir')
			->where('a_docs_dir_docs_id', $id)
			->get();
		return $query->row_array();
	}

	// Pass in docs_dir_id
	function get_dirpath_dir($id) {
		$query = $this->db->select()
			->from('a_docs_dir')
			->where('a_docs_dir_docs_id', $id)
			->get();
		return $query->row_array();
	}

	// Returns all docs in a dirid.
	// Presents the latest ver id as link
	function get_docs($id) {
		$query = $this->db->select('a_docs_id, a_docs_displayname, a_docs_dir_dirpath, a_docs_ver_id, a_docs_ver_filename, a_docs_ver_filesize, a_docs_ver_stamp')
			->from('a_docs')
			->join('a_docs_ver', 'a_docs.a_docs_id = a_docs_ver.a_docs_ver_docsid')
			->join('a_docs_dir', 'a_docs_dir.a_docs_dir_docs_id = a_docs.a_docs_parentid')
			->where(array('a_docs_parentid'=> $id, 'a_docs_isdir' => FALSE))
			->order_by('a_docs_ver_id')
			->group_by('a_docs_ver_id')
			->get();
		return $query->result_array();
	}

	// Pass in a_docs_ver_id
	function get_docs_detail($ver_id) {
		$query = $this->db->select()
			->from('a_docs')
			->join('a_docs_ver', 'a_docs_ver.a_docs_ver_docsid = a_docs.a_docs_id')
			->join ('a_docs_dir', 'a_docs_dir.a_docs_dir_docs_id = a_docs.a_docs_parentid')
			->where('a_docs_ver_id', $ver_id)
			->get();
		return $query->row_array();
	}

	function get_parent_id($id) {
		$query = $this->db->select('a_docs_parentid')
			->from('a_docs')
			->where(array('a_docs_id'=>$id))
			->get();
		return $query->row_array();
	}

	function get_root_dir() {
		$query = $this->db->select()
			->from('a_docs')
			->join('a_docs_dir', 'a_docs_dir.a_docs_dir_docs_id = a_docs.a_docs_id')
			->where(array('a_docs_dir_cardid'=>$this->UserM->info['cardid'], 'a_docs_parentid'=>0))
			->get();
		return $query->row_array();
	}

	function get_sub_folders($id) {
		$query = $this->db->select('a_docs_id, a_docs_displayname')
			->from('a_docs')
			->join('a_docs_dir', 'a_docs_dir.a_docs_dir_docs_id = a_docs.a_docs_id')
			->where(array('a_docs_dir_cardid' => $this->UserM->info['cardid'],
					'a_docs_isdir <>' => '',
					'a_docs_parentid' => $id,
				))
			->order_by('a_docs_displayname')
			->get();
		return $query->result_array();
	}

	function update_a_docs_dir($values) {
		// Leaving a_docs_dir_id as autoincrement
		$data = array(
			'a_docs_dir_docs_id' => $values['a_docs_id'],
			'a_docs_dir_dirpath' => isset($values['dirpath']) ? $values['dirpath'] : '',
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
			'a_docs_dir_reaction' => isset($value['rean']) ? $value['rean'] : '',
			'a_docs_dir_resubaction' => isset($value['reaved']) ? $value['reaved'] : '',
			'a_docs_dir_noocrindex' => isset($value['noocrindex']) ? $value['noocrindex'] : '',
		);
		if (isset($values['id'])) {
			$this->db->where(array('id'=> $values['id'], 'a_docs_isdir' => TRUE))
				->update('a_docs_dir', $data);
		} else {
			$this->db->insert('a_docs_dir', $data);
		}
		return $values['a_docs_id'];
	}

	// Call this when creating/updating folders.
	function update_a_docs_dir_directory($values) {
		$data = array(
			'a_docs_parentid' => isset($values['parentid'])? $values['parentid'] : '',
			'a_docs_isdir' => TRUE,
			'a_docs_displayname' => isset($values['displayname'])? $values['displayname'] : '',
			'a_docs_desc' => isset($values['desc'])? $values['desc'] : '',
			'a_docs_status' => isset($values['a_docs_status'])? $values['a_docs_status'] : '',
			'a_docs_stamp' => get_current_stamp(),
		);
		if(isset($values['id'])) {
			$this->db->where(array('id'=> $value['id'], 'a_docs_isdir' => TRUE))
				->update('a_docs', $data);
		} else {
			$this->db->insert('a_docs', $data);
		}
		$values['a_docs_id'] = $this->db->insert_id();
		return 	$this->update_a_docs_dir($values);
	}

	function update_docs($values) {
		//parentid == parentid for folders. and current dirid for docs
		$data = array(
			'a_docs_parentid' => $values['parentid'],
			'a_docs_isdir' => FALSE,
			'a_docs_displayname' => isset($values['a_docs_displayname']) ? $values['a_docs_displayname'] : '',
			'a_docs_desc' => isset($values['a_docs_desc']) ? $values['a_docs_desc'] : '',
			'a_docs_status' => isset($values['a_docs_status']) ? $values['a_docs_status'] : '',
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

	// Pass in new parentid, ver_id
	function update_docs_location($id, $docs_id) {
		if (isset($id) && isset($docs_id)) {
			$data = array(
				'a_docs_parentid' => $id,
			);
			$this->db->where('a_docs_id', $docs_id)
				->update('a_docs', $data);
		}
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

	function update_docs_display_name($title, $id) {
		$data = array('a_docs_displayname' => $title);
		$this->db->where('a_docs_id', $id)
			->update('a_docs',$data);
		return $this->db->affected_rows();
	}

	/*
	function update_docs_setting($values) {

	}

	function update_dir_setting($values) {

	}

	function update_permissions($values) {

	}

	function update_permission_grp($values) {

	}

	function update_doc_status() {

	}*/

	/*function get_latest_docsid() {
		$query = $this->db->select('a_docsid')
			->from('a_docs')
			->order_by('a_docs_docsid', 'desc')
			->get();
		return $query->row_array();
	}*/

	/*
	function get_dir_parent_id($id) {
		$query = $this->db->select('a_docs_id, a_docs_parentid, a_docs_displayname')
			->from('a_docs')
			->where(array('a_docs_id'=>$id, 'a_docs_parentid <>'=>' 0'))
			->get();
		return $query->row_array();
	}*/
}
