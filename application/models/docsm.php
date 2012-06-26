<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class docsM extends MY_Model {

	var $upload_source = 'web';

	function __construct() {
		$this->table = 'a_docs';

		parent::__construct();
	}


	function does_folder_exists($id) {
		$query = $this->db->select()
			->from('a_docs_dir')
			->where('id', $id)
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

	function get_docs_id_from_path($path, $filename) {
		// Similiar to does_file_exist
		$i = $this->does_file_exist($path, $filename);
		return ($i) ? $i['docs_id'] : FALSE;
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
			->join('card', 'a_docs_ver.a_docs_ver_cardid = card.id')
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



	function get_root_dir() {
		$query = $this->db->select()
			->from('a_docs')
			->join('a_docs_dir', 'a_docs_dir.a_docs_dir_docs_id = a_docs.a_docs_id')
			->where(array('a_docs_dir_cardid'=>$this->UserM->info['cardid'], 'a_docs_parentid'=>0))
			->get();
		return $query->row_array();
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

	function new_file($file_info, $dir_path='') {
		$path = explode('/', $dir_path);

		if (count($path) == 1 && $path[0]==='') {
			$dir_id = 0;
		} else {
			$dir_id = $this->create_dir($dir_path);
		}

		return $this->new_file_in_dir($file_info, $dir_id);
	}

	function new_file_in_dir($file_info, $dir_id_or_name) {
		if (!is_numeric($dir_id_or_name)) {
			$dir_id = $this->get_dir_id_by_name ($dir_id_or_name);
		} else {
			$dir_id = $dir_id_or_name;
		}

		$this->db->trans_start();

		$filename_without_extension = get_filename_without_extension($file_info['orig_name']);

		$data = array(
			'dir_id' => $dir_id,
			'display_name' => $filename_without_extension,
			'created_card_id' => $this->UserM->get_card_id(),
			'created_stamp' => get_current_stamp()
		);
		$this->db->insert('a_docs', $data);
		$docs_id = $this->db->insert_id();

		$data = array(
			'docs_id' => $docs_id,
			'hash' => $file_info['raw_name'],
			'file_name' => $filename_without_extension,
			'file_ext' => $file_info['file_ext'],
			'file_size' => ($file_info['file_size']*1000),
			'mime' => $file_info['file_type'],
			'upload_source' => $this->upload_source,
			'created_card_id' => $this->UserM->get_card_id(),
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
			'hash' => $file_info['raw_name'],
		);
	}

	function overwrite_file($hash_or_id, $file_info, $versioning='') {
		$existing_info = $this->get_detail($hash_or_id);

		if ($versioning == '') {
			$dir_info = $this->get_dir_detail($existing_info['dir_id']);
			$versioning = ($dir_info['has_versioning'] == 1);
		}

		$filename_without_extension = get_filename_without_extension($file_info['orig_name']);

		$this->db->trans_start();

		if ($versioning) {
			$docs_id = $existing_info['docs_id'];

			$data = array(
				'docs_id' => $docs_id,
				'version' => $this->get_next_version($docs_id),
				'hash' => $file_info['raw_name'],
				'file_name' => $filename_without_extension,
				'file_ext' => $file_info['file_ext'],
				'file_size' => ($file_info['file_size']*1000),
				'mime' => $file_info['file_type'],
				'created_card_id' => $this->UserM->get_card_id(),
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
				'file_name' => $filename_without_extension,
				'file_ext' => $file_info['file_ext'],
				'file_size' => ($file_info['file_size']*1000),
				'mime' => $file_info['file_type'],
				'modified_card_id' => $this->UserM->get_card_id(),
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

	//get directory ID by path
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

	function get_dir_id_by_name($name, $parent_folder_id=1) {
		$rs = $this->db->select('id')
				->from('a_docs_dir')
				->where('name', $name)
				->where('parent_id', $parent_folder_id)
				->where('deleted', 0)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();
		return $result['id'];
	}

	// Get directory path by ID
	function get_dir_path($dir_id=0) {
		if ($dir_id == 0) return '';

		$rs = $this->db->select('name, parent_id')
			->from('a_docs_dir')
			->where('id', $dir_id)
			->where('deleted', 0)
			->limit(1)
			->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();

		$path = $result['name'];

		if ($result['parent_id'] !== 0) {
			$path = $this->get_dir_path($result['parent_id']).'/'.$path;
		}

		return $path;
	}

	function get_subdir_ids($dir_ids, $recurse=FALSE, $include_hidden=FALSE) {
		if (!is_array($dir_ids)) $dir_ids = array($dir_ids);
		$results = array();

		$this->db->select('id')
			->from('a_docs_dir')
			->where_in('parent_id', $dir_id);

		if (!$include_hidden) $this->db->where('hidden', 0);
		$this->db->where('deleted', 0);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		foreach($rs->result_array() AS $r) {
			$results[] = $r['id'];
		}

		if ($recurse) {
			$results = array_merge($results, $this->get_subdir_ids($results, $recurse, $include_hidden));
		}

		return $results;
	}

	function get_subdir($dir_id=0, $recurse=FALSE, $include_hidden=FALSE) {
		$results = array();

		$this->db->select('id, parent_id, name')
				->from('a_docs_dir')
				->where('parent_id', $dir_id);

		if (!$include_hidden) $this->db->where('hidden', 0);
		$this->db->where('deleted', 0);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		foreach($rs->result_array() AS $r) {
			$results[] = $r;
		}

		if ($recurse) {
			foreach($results AS $key=>$r) {
				$subdir = $this->get_subdir($r['id'], $recurse, $include_hidden);
				if (count($subdir) > 0) $results[$key]['child'] = $subdir;
			}
		}

		return $results;
	}

	function get_dir_contents($dir_id) {
		$rs = $this->db->select('d.id, d.dir_id, d.display_name, d.description, d.status, d.created_card_id, d.created_stamp,
				v.version, v.hash, v.file_name, v.file_ext, v.file_size, v.mime, v.download_hits, v.has_ocr, v.has_preview')
			->from('a_docs AS d')
			->join('a_docs_ver AS v', 'v.docs_id=d.id')
			->where('d.dir_id', $dir_id)
			->where('d.deleted', 0)
			->order_by('d.display_name')
			->get();

		return $rs->result_array();
	}

	function get_dir_detail($dir_id_or_name) {
		if (is_numeric($dir_id_or_name)) {
			$rs = $this->db->select()
					->from('a_docs_dir')
					->where('id', $dir_id_or_name)
					->where('deleted', 0)
					->limit(1)
					->get();
		} else {
			$rs = $this->db->select()
					->from('a_docs_dir')
					->where('name', $dir_id_or_name)
					->where('parent_id', 1)	//APPS folder
					->where('deleted', 0)
					->limit(1)
					->get();
		}

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->row_array();
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
				'created_card_id' => $this->UserM->get_card_id(),
				'created_stamp' => get_current_stamp()
			);
			$this->db->insert('a_docs_dir', $data);
			return $this->db->insert_id();
		}

		foreach($path AS $i=>$p) {
			if ($i==0 && $p=='') continue;

			$dir_id = $this->get_dir_id($p, $parent_dir_id);

			if ($dir_id !== FALSE) $parent_dir_id = $dir_id;

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

	//Used when we delete a current version and want to automatically use the next latest version
	function get_next_latest_version($docs_id) {
		$rs = $this->db->select()
				->from('a_docs_ver')
				->order_by('version', 'DESC')
				->where('docs_id', $docs_id)
				->where('deleted', 0)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->row_array();
	}

	function get_version_id_from_hash($hash) {
		$rs = $this->db->select('id')
				->from('a_docs_ver')
				->where('hash', $hash)
				->where('deleted', 0)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();
		return $result['id'];
	}

	// $actual_delete parameter is not used, but added in order to suppress
	// errors saying that it should be similar to MY_Model's delete()
	function delete($hash_or_id, $actual_delete=FALSE) {
		$data = array(
			'modified_card_id' => $this->UserM->get_card_id(),
			'modified_stamp' => get_current_stamp(),
			'deleted' => 1,
		);

		if (strlen($hash_or_id)==32) {
			//get docs version hash
			$docs = $this->get_detail($hash_or_id);

			//get versions of this doc
			$versions = $this->get_all_versions($docs['id']);

			//delete the hash
			$this->db->where('hash', $hash_or_id)
				->update('a_docs_ver', $data);

			if (count($versions) == 1) {
				//if there's only 1 version, delete the doc as well

				$this->db->where('id', $docs['id'])
					->update('a_docs', $data);

			} elseif (count($versions) > 1) {
				//if there's more than 1 version
				//
				//if the deleted version is the same as the current version, find the next current version and update the doc
				if ($docs['current_version_id'] == $this->get_version_id_from_hash($hash_or_id)) {
					$next_version = $this->get_next_latest_version($docs['id']);

					$update = array(
						'current_version_id' => $next_version['id'],
						'modified_card_id' => $this->UserM->get_card_id(),
						'modified_stamp' => get_current_stamp(),
					);

					$this->db->where('id', $docs['id'])
							->update('a_docs', $update);
				}
			}


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
			'modified_card_id' => $this->UserM->get_card_id(),
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

		$this->db->select('d.id, d.dir_id, d.display_name, d.description, d.status, d.created_card_id, d.created_stamp,
			v.version, v.hash, v.file_name, v.file_ext, v.file_size, v.mime, v.download_hits, v.has_ocr, v.has_preview');

		if (strlen($hash_or_id)==32) {
			$this->db->from('a_docs_ver AS v')
				->join('a_docs AS d', 'v.docs_id=d.id')
				->where('v.hash', $hash_or_id)
				->where('v.deleted', 0);
		} else {
			$this->db->from('a_docs AS d')
				->join('a_docs_ver AS v', 'd.current_version_id = v.id')
				->where('d.id', $hash_or_id)
				->where('d.deleted', 0);
		}

		$query = $this->db->limit(1)
					->get();

		if ($query->num_rows() == 0) return FALSE;

		$result = $query->row_array();

		$this->fill_card_info($result);

		return $result;
	}

	function update_display_name($docs_id, $display_name) {
		$data = array(
			'display_name' => $display_name,
			'modified_card_id' => $this->UserM->get_card_id(),
			'modified_stamp' => get_current_stamp()
		);
		return $this->db->where('id', $docs_id)
				->update('a_docs', $data);
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

	function move_file($docs_id, $dir_id) {
		$data = array(
			'dir_id' => $dir_id,
			'modified_card_id' => $this->UserM->get_card_id(),
			'modified_stamp' => get_current_stamp()
		);

		$this->db->where('id', $docs_id)
			->update('a_docs', $data);
	}

	function get_doc_parent_id($id) {
		$rs = $this->db->select('parent_id')
					->from('a_docs')
					->where('id', $id)
					->where('deleted', 0)
					->limit(1)
					->get();

		if ($rs->num_rows() == 0) return FALSE;

		$row = $rs->row_array();
		return $row['parent_id'];
	}

	function get_dir_parent_id($id) {
		$rs = $this->db->select('parent_id')
					->from('a_docs_dir')
					->where('id', $id)
					->where('deleted', 0)
					->limit(1)
					->get();

		if ($rs->num_rows() == 0) return FALSE;

		$row = $rs->row_array();
		return $row['parent_id'];
	}
}
