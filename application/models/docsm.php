<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class docsM extends My_Model {

	function __construct() {
		parent::__construct();
	}

	// Deletes all docs and ver
	function delete_all_docs($docs_id) {
		$this->db->delete('a_docs_ver', array('a_docs_ver_docsid'=>$docs_id));
		$this->db->delete('a_docs', array('a_docs_id'=>$docs_id));
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	function delete_single_ver($docs_id, $ver_id) {
		$this->db->delete('a_docs_ver', array('a_docs_ver_docsid'=>$docs_id, 'a_docs_ver_id'=>$ver_id));
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
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

	// Checks if path exists in a_docs_dir
	function does_path_exists($path) {
		$query = $this->db->select('a_docs_dir_dirpath')
			->get_where('a_docs_dir', array('a_docs_dir_dirpath'=>$path), 1);
		return ($query->num_rows()) ? TRUE : FALSE;
	}

	// Returns the latest version if file is found
	function does_file_exist($path, $filename) {
		$path = explode('/', $path);
		$folder_id = $this->get_folder_id($path);

		$query = $this->db->query('SELECT a_docs_id, a_docs_ver_id
			FROM a_docs
			LEFT JOIN a_docs_ver ON a_docs_ver.a_docs_ver_docsid = a_docs.a_docs_id
			WHERE a_docs_parentid = (SELECT a_docs_id
				FROM a_docs
				LEFT JOIN a_docs_dir ON a_docs_dir.a_docs_dir_docs_id = a_docs.a_docs_id
				WHERE a_docs_dir_dirpath = ?
			)
			AND a_docs_ver_filename = ?
			LIMIT 1
		', array($path, $filename));
		if ($query->num_rows()) {
			$row = $query->row_array();
			return array('docs_id'=>$row['a_docs_id'], 'ver_id'=>$row['a_docs_ver_id']);
		}
		return FALSE;
	}

	// Pass in docs id. Return dirpath of doc
	function get_dirpath($id) {
		$query = $this->db->query('SELECT a_docs_dir_dirpath FROM a_docs_dir WHERE a_docs_dir_docs_id = (SELECT a_docs_parentid FROM a_docs WHERE a_docs_id = '.$id.')');
		return $query->row_array();
	}

	/* Can remove if confirm not in use
	// Gets the latest version
	function get_current_ver_id($docs_id) {
		// TODO: change to get from the current_ver column
		$query = $this->db->select('a_docs_ver_id')
			->from('a_docs_ver')
			->where('a_docs_ver_docsid', $docs_id)
			->order_by('a_docs_ver_id', 'desc')
			->limit(1)
			->get();
		if ($query->num_rows()) {
			$ver_id = $query->row_array();
			return $ver_id['a_docs_ver_id'];
		}
		return FALSE;
	}*/

	function get_docs_id_from_path($path, $filename) {
		// Similiar to does_file_exist
		$i = $this->does_file_exist($path, $filename);
		return ($i) ? $i['docs_id'] : FALSE;
	}



	// Gets direct dirpath stored in a_docs_dir_dirpath
	// Pass in docs_dir_id
	function get_dirpath_dir($id) {
		$query = $this->db->select()
			->from('a_docs_dir')
			->where('a_docs_dir_docs_id', $id)
			->get();
		return $query->row_array();
	}

	/* Previous version before implementing current_version
	// Returns all docs in a dirid.
	// Presents the latest ver id as link
	function get_docs($dir_id) {
		$query = $this->db->query('SELECT * FROM
				(SELECT * FROM a_docs LEFT JOIN a_docs_ver ON a_docs.a_docs_id = a_docs_ver.a_docs_ver_docsid
					WHERE a_docs_parentid = '.$id.' AND a_docs_isdir = 0 ORDER BY a_docs_ver_id DESC )
				AS a LEFT JOIN a_docs_dir ON a_docs_dir.a_docs_dir_docs_id = a.a_docs_parentid
				GROUP BY a_docs_id ORDER BY a_docs_ver_id DESC');
		return $query->result_array();
	}*/
	// Returns all docs in a dirid that has a_docs_ver_current_version = 1.
	function get_docs($dir_id) {
		$query = $this->db->select()
			->from('a_docs')
			->join('a_docs_ver', 'a_docs.a_docs_id = a_docs_ver.a_docs_ver_docsid')
			->where(array('a_docs_parentid'=>$dir_id, 'a_docs_ver_current_version'=>1))
			->get();
		return $query->result_array();
	}



	function get_docs_ver_detail($docs_id, $ver_id) {
		$query = $this->db->select()
			->from('a_docs')
			->join('a_docs_ver', 'a_docs_ver.a_docs_ver_docsid = a_docs.a_docs_id')
			->join('a_docs_dir', 'a_docs_dir.a_docs_dir_docs_id = a_docs.a_docs_parentid')
			->join('card', 'a_docs_ver.a_docs_ver_cardid = card.card_id')
			->where(array('a_docs_ver_id'=> $ver_id, 'a_docs_ver_docsid'=>$docs_id))
			->limit(1)
			->get();
		return $query->row_array();
	}

	function get_dir_id_from_docs_id($docs_id) {
		//$query = 'SELECT a_docs_parentid FROM a_docs WHERE a_docs_id = (SELECT a_docs_parentid FROM a_docs WHERE a_docs_id = '.$docs_id.') AND a_docs_isdir = 1';
		$query = $this->db->select('a_docs_parentid')
			->from('a_docs')
			->where(array('a_docs_id'=>$docs_id))
			->get();
		if ($query->num_rows() > 0) {
			$i = $query->row_array();
			return $i['a_docs_parentid'];
		}
		return FALSE;
	}

	// Pass in docs id
	function get_docs_dir_ver($dir_id) {
		$query = $this->db->query('SELECT a_docs_dir_versioning FROM a_docs_dir
				LEFT JOIN a_docs ON a_docs.a_docs_parentid = a_docs_dir.a_docs_dir_docs_id
				WHERE a_docs_dir_docs_id = ? AND (a_docs_dir_versioning <> "" OR a_docs_dir_versioning IS NOT NULL)
			', array($dir_id));
		return $query->row_array();
	}

	function get_docs_settings() {
		$query = $this->db->get('a_docs_setting');
		return $query->row_array();
	}

	function get_file_name($ver_id) {
		$query = $this->db->select('a_docs_ver_filename')
			->from('a_docs_ver')
			->where('a_docs_ver_id', $ver_id)
			->get();
		return $query->row_array();
	}

	function get_parent_id($id) {
		$query = $this->db->select('a_docs_parentid')
			->where(array('a_docs_id'=>$id))
			->get('a_docs',1);
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

	function search_filename($filename) {
		$query = $this->db->select('a_docs_ver_filename')
			->from('a_docs_ver')
			->where('a_docs_ver_filename', $filename)
			->get();
		return $query->row_array();
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

	/*
	function update_a_docs_dir_directory($values) {
		if(isset($values['id'])) {
			$this->db->where(array('id'=> $value['id'], 'a_docs_isdir' => TRUE))
				->update('a_docs', $data);
		} else {
			$this->db->insert('a_docs', $data);
		}
		$values['a_docs_id'] = $this->db->insert_id();
		return 	$this->update_a_docs_dir($values);
	}*/

	function insert_a_docs_entry($data) {
		$this->db->insert('a_docs',$data);
		return $this->db->insert_id();
	}

	function insert_a_docs_dir_entry($data) {
		$this->db->insert('a_docs_dir',$data);
		return $this->db->insert_id();
	}

	function insert_docs($values) {
		//parentid == parentid for folders. and current dirid for docs
		$data = array(
			'a_docs_parentid' => $values['a_docs_parentid'],
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
		$values['a_docs_ver_docsid'] = $this->db->insert_id();
		$this->insert_docs_ver($values);
		return $values['a_docs_ver_docsid'];
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

	function update_docs_ver($values) {
		if (isset($values['a_docs_ver_id'])) {
			$this->db->where('a_docs_ver_id', $values['a_docs_ver_id'])
				->update('a_docs_ver', $values);
		}
	}

	// Sets all current_version to 1 or 0
	function set_all_current_ver($docs_id, $val) {
		if ($val !== 0 && $val !== 1) return FALSE;
		$this->db->where('a_docs_ver_docsid', $docs_id)
			->update('a_docs_ver', array('a_docs_ver_current_version'=>$val));
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		}
		return FALSE;
	}

	function set_current_ver($docs_id, $ver_id) {
		$this->db->where(array('a_docs_ver_id'=>$ver_id, 'a_docs_ver_docsid'=>$docs_id))
			->update('a_docs_ver', array('a_docs_ver_current_version'=>1));
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		}
		return FALSE;
	}

	// Returns ver_id of specified docs_id
	function get_current_ver_id($docs_id) {
		$query = $this->db->select('a_docs_ver_id')
			->from('a_docs_ver')
			->where(array('a_docs_ver_docsid'=>$docs_id, 'a_docs_ver_current_version'=>1))
			->get();
		if ($query->num_rows() > 0) {
			$i = $query->row_array();
			return $i['a_docs_ver_id'];
		}
		return FALSE;
	}

	function insert_docs_ver($values) {
		$data = array(
			'a_docs_ver_docsid' => isset($values['a_docs_ver_docsid']) ? $values['a_docs_ver_docsid'] : '',
			'a_docs_ver_filename' => isset($values['a_docs_ver_filename']) ? $values['a_docs_ver_filename'] : '',
			'a_docs_ver_stamp' => get_current_stamp(),
			'a_docs_ver_downloadhit' => isset($values['a_docs_ver_downloadhit']) ? $values['a_docs_ver_downloadhit'] : '',
			'a_docs_ver_cardid' => $this->UserM->info['cardid'],
			'a_docs_ver_uploadvia' => isset($values['a_docs_ver_uploadvia']) ? $values['a_docs_ver_uploadvia'] : '',
			'a_docs_ver_filesize' => isset($values['a_docs_ver_filesize']) ? $values['a_docs_ver_filesize'] : '',
			'a_docs_ver_mime' => isset($values['a_docs_ver_mime']) ? $values['a_docs_ver_mime'] : '',
			'a_docs_ver_ocr' => isset($values['a_docs_ver_ocr']) ? $values['a_docs_ver_ocr'] : '',
			'a_docs_ver_preview' => isset($values['a_docs_ver_preview']) ? $values['a_docs_ver_preview'] : '',
			'a_docs_ver_encrypt' => isset($values['enca_docs_ver_encryptrypt']) ? $values['a_docs_ver_encrypt'] : '',
			'a_docs_ver_encryptkeytype' => isset($values['a_docs_ver_encryptkeytype']) ? $values['a_docs_ver_encryptkeytype'] : '',
			'a_docs_ver_current_version' => isset($values['a_docs_ver_current_version']) ? $values['a_docs_ver_current_version'] : '',
		);
		$this->db->insert('a_docs_ver', $data);
		return;
	}

	function update_docs_display_name($title, $docs_id) {
		$this->db->where('a_docs_id', $docs_id)
			->update('a_docs',array('a_docs_displayname'=>$title));
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		}
		return FALSE;
	}

	// Deletes doc entry in a_docs. Eg, when no versions exists, this entry should be removed too.
	function delete_docs($docs_id) {
		$this->db->delete('a_docs', array('a_docs_id'=>$docs_id));
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
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



	//changes by erik ==========

	function get_filehash($file_id) {
		$rs = $this->db->select('ver.hash AS filehash')
				->from('a_docs AS docs')
				->join('a_docs_ver AS ver', 'docs.current_version_id=ver.id')
				->where('docs.id', $file_id)
				->where('deleted', 0)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$row = $rs->row_array();
		return $row['filehash'];
	}

	function new_file($file_info, $full_path='') {
		$path = explode('/', $full_path);

		if (count($path) == 1 && $path[0]==='') {
			$dir_id = 0;
		} else {
			$dir_id = $this->create_dir($full_path);
		}

		$this->db->trans_start();

		$data = array(
			'dir_id' => $dir_id,
			'display_name' => $file_info['filename'],
			'created_card_id' => $this->UserM->get_cardid(),
			'created_stamp' => get_current_stamp()
		);
		$this->db->insert('a_docs', $data);
		$docs_id = $this->db->insert_id();

		$hash = generate_hash();
		$data = array(
			'docs_id' => $docs_id,
			'hash' => $hash,
			'file_name' => $file_info['filename'],
			'file_ext' => $file_info['extension'],
			'file_size' => $file_info['filesize'],
			'mime' => $file_info['mime'],
			'created_card_id' => $this->UserM->get_cardid(),
			'created_stamp' => get_current_stamp()
		);
		$this->db->insert('a_docs_ver', $data);
		$ver_id = $this->db->insert_id();

		$data = array(
			'current_version_id'=>$ver_id
		);
		$this->db->where('id', $docs_id)
				->update('a_docs', $data);

		$this->db->trans_complete();

		return array(
			'id' => $docs_id,
			'hash' => $hash,
		);
	}

	function overwrite_file($hash_or_id, $file_info, $versioning='') {
		$existing_info = $this->get_detail($hash_or_id);

		if ($versioning == '') {
			$dir_info = $this->get_dir_detail($existing_info['dir_id']);
			$versioning = ($dir_info['has_versioning'] == 1);
		}

		$this->db->trans_start();

		if ($versioning) {
			$hash = generate_hash();
			$docs_id = $existing_info['docs_id'];

			$data = array(
				'docs_id' => $docs_id,
				'version' => $this->get_next_version($docs_id),
				'hash' => $hash,
				'file_name' => $file_info['filename'],
				'file_ext' => $file_info['extension'],
				'file_size' => $file_info['filesize'],
				'mime' => $file_info['mime'],
				'created_card_id' => $this->UserM->get_cardid(),
				'created_stamp' => get_current_stamp()
			);
			$this->db->insert('a_docs_ver', $data);
			$ver_id = $this->db->insert_id();

			$data = array(
				'current_version_id'=>$ver_id
			);
			$this->db->where('id', $docs_id)
					->update('a_docs', $data);
		} else {
			$hash = $existing_info['hash'];
			$ver_id = $existing_info['current_version_id'];

			$data = array(
				'file_name' => $file_info['filename'],
				'file_ext' => $file_info['extension'],
				'file_size' => $file_info['filesize'],
				'mime' => $file_info['mime'],
				'modified_card_id' => $this->UserM->get_cardid(),
				'modified_stamp' => get_current_stamp()
			);
			$this->db->where('id', $ver_id)
					->update('a_docs_ver', $data);
		}

		$this->db->trans_complete();

		return array(
			'id' => $docs_id,
			'hash' => $hash,
		);
	}

	function get_dir_id($path, $parent_dir_id=0) {
		$path = explode('/', $path);
		$id = 0;

		if (count($path) == 1) {
			if ($path[0] == '' && $parent_dir_id == 0) {
				//referring to root folder which is id 0
				return 0;
			}

			if ($path[0] == '' && $parent_dir_id != 0) {
				//path might have double slashes. e.g. 'a/b//c/d';
				return FALSE;
			}

			$rs = $this->db->select('id')
					->from('a_docs_dir')
					->where('name', $path[0])
					->where('parent_id', $parent_dir_id)
					->where('deleted', 0)
					->limit(1)
					->get();

			//path not found in DB
			if ($rs->num_rows() == 0) return FALSE;

			$result = $rs->row_array();
			return $result['id'];
		}

		foreach($path AS $i=>$p) {
			if ($i==0 && $p=='') continue; //happens if path starts with /
			$id = $this->get_dir_id($p, $id);	//recursive
		}

		return $id;
	}

	function get_subdir_ids($dir_ids, $recurse=FALSE) {
		if (!is_array($dir_ids)) $dir_ids = array($dir_ids);
		$results = array();

		$rs = $this->db->select('id')
				->where_in('parent_id', $dir_id);

		if ($rs->num_rows() == 0) return array();

		foreach($rs->result_array() AS $r) {
			$results[] = $r['id'];
		}

		if ($recurse) {
			$results = array_merge($results, $this->get_subdir_id($results, $recurse));
		}

		return $results;
	}

	function get_doc_ids_in_dir_ids($dir_ids) {
		$rs = $this->db->select('id')
				->from('a_docs')
				->where_in('dir_id', $dir_ids)
				->where('deleted', 0)
				->get();

		if ($rs->num_rows() == 0) return array();

		$results = array();
		foreach($rs->result_array() AS $r) {
			$results[] = $r['id'];
		}

		return $results;
	}

	//if directory exists, return it's id, if not, create it and return the new id
	function create_dir($path, $parent_dir_id=0) {
		$path = explode('/', $path);
		$dir_id = 0;

		if (count($path) == 1) {
			if ($path[0] == '') return 0; //no need to create because it's asking to create root directory

			$dir_id = $this->get_dir_id($path[0], $parent_dir_id);

			if ($dir_id !== FALSE) return $dir_id;

			$data = array(
				'parent_id' => $parent_dir_id,
				'name' => $path[0],
				'created_card_id' => $this->UserM->get_cardid(),
				'created_stamp' => get_current_stamp()
			);
			$this->db->insert('a_docs_dir', $data);
			return $this->db->insert_id();
		}

		foreach($path AS $i=>$p) {
			if ($i==0 && $p=='') continue;

			$dir_id = $parent_dir_id = $this->get_dir_id($p, $parent_dir_id);

			if ($dir_id === FALSE) {
				$dir_id = $parent_dir_id = $this->create_dir($p, $parent_dir_id);
			}
		}

		return $dir_id;
	}

	function get_next_version($docs_id) {
		$rs = $this->db->select('version')
				->from('a_docs_ver')
				->order_by('version', 'DESC')
				->where('docs_id', $docs_id)
				->where('deleted', 0)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return 1;

		$row = $rs->row_array();

		return ($row['version']+1);
	}

	function delete($hash_or_id) {
		$data = array(
			'modified_card_id' => $this->UserM->get_cardid(),
			'modified_stamp' => get_current_stamp(),
			'deleted' => 1,
		);

		if (strlen($hash_or_id)==32) {
			$this->db->where('hash', $hash_or_id)
					->update('a_docs_ver', $data);
		} else {
			$this->db->where('id', $hash_or_id)
					->update('a_docs', $data);

			$this->db->where('docs_id', $hash_or_id)
					->update('a_docs_ver', $data);
		}
	}

	function delete_dir_by_path($dir_path) {
		$dir_id = $this->get_dir_id($dir_path);
		return $this->delete_dir_by_id($dir_id);
	}

	function delete_dir_by_id($dir_id) {
		$subdir_ids = $this->get_subdir_ids($dir_id, TRUE);

		$dir_ids = array_merge($dir_id, $subdir_ids);

		$data = array(
			'modified_card_id' => $this->UserM->get_cardid(),
			'modified_stamp' => get_current_stamp(),
			'deleted' => 1,
		);

		$this->db->where_in('id', $dir_ids)
				->update('a_docs_dir', $data);

		$this->db->where_in('dir_id', $dir_ids)
				->update('a_docs', $data);

		$doc_ids = $this->get_doc_ids_in_dir_ids($dir_ids);

		$this->db->where_in('doc_id', $doc_ids)
				->update('a_docs_ver', $data);

		return TRUE;
	}

	//Used in preview screen
	// Returns details of latest version of doc
	//docs_id can be the ID of the document or hash of the file.
	function get_detail($hash_or_id) {
		//since the id field in MySQL is only 10 characters long, we can assume it's a hash if it's 32 characters long.

		if (strlen($hash_or_id)==32) {
			$query = $this->db->select()
				->from('a_docs_ver')
				->join('a_docs', 'a_docs_ver.docs_id=a_docs.id')
				->where('a_docs_ver.hash', $hash_or_id)
				->where('deleted', 0)
				->limit(1)
				->get();
		} else {
			$query = $this->db->select()
				->from('a_docs')
				->join('a_docs_ver', 'a_docs.current_version_id = a_docs_ver.id')
				->where('a_docs.id', $hash_or_id)
				->where('deleted', 0)
				->limit(1)
				->get();
		}

		return $query->row_array();
	}

	function get_all_versions($docs_id) {
		$query = $this->db->select()
			->from('a_docs_ver')
			->where('docs_id', $docs_id)
			->where('deleted', 0)
			->order_by('version', 'asc')
			->get();

		if ($query->num_rows() == 0) return array();

		return $query->result_array();
	}
}
