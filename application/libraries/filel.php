<?php

class filel {
	private $_fs = 's3';
	private $_ci = '';
	private $_bucket = 'tcs99';
	private $_upload_status = FALSE;

	function __construct() {
		$this->_ci = & get_instance();
		$this->_ci->load->library('s3');
		$this->_ci->load->Model('DocsM');
	}

	function set_fs($fs) {
		$this->_fs = $fs; return $this;
	}

	function read($filepath) {
		if ($this->_fs === 's3') {
			$object = S3::getObject($this->_bucket, $filepath, FALSE);
			return $object;
		}

		if ($this->_fs === 'local') {

		}
	}

	function save($path, $overwrite, $via, $filename='') {
		if ($this->_fs === 's3') {
			$this->_check_folder($path);
			$docs_id_n_path = $this->_upload_files($path, $overwrite, $via);
			return $docs_id_n_path;
			/*
			$this->_ci->output->set_content_type('application/json');
			($docs_id)
				? $this->_ci->output->set_output(json_encode(array('success'=>'1', 'docs_id'=>$docs_id)))
				: $this->_ci->output->set_output(json_encode(array('success'=>'0')));*/
		}

		if ($this->_fs === 'local') {

		}
	}

	function save_current($path, $version, $via, $filename='') {
		if ($this->_fs === 's3') {

		}

		if ($this->_fs === 'local') {

		}
	}

	function delete($path) {
		$filepath = explode('/',$path);
		$filename = array_pop($filepath);
		if ( ! empty($filepath)) {
			$filepath = '/'.implode('/',$filepath);
		}
		else {
			$filepath = '/';
		}

		if ($this->_fs === 's3') {
			$file_exists = $this->_ci->DocsM->does_file_exists($filepath, $filename);
			if ($file_exists) {
				if (S3::deleteObject($this->_bucket, $this->_format_dirpath($filepath, $filename))) {
					log_message('debug', 'FileL: Deleted '. $this->_format_dirpath($filepath, $filename));
					$docs_id = $this->_ci->DocsM->get_docs_id_from_path($filepath, $filename);
					// Check if this is the only version
					$all_ver = $this->_ci->DocsM->get_all_versions($docs_id);
					if (count($all_ver) > 1) {
						// There are other versions, delete only current one.

					}
					else {
						// This is the only version, remove everything including docs_id
						$this->_ci->DocsM->delete_docs($docs_id);
					}
				}
				return TRUE;
			}
			return FALSE;
			/*
			$this->output->set_content_type('application/json');
			($i !== '')
			? $this->output->set_output(json_encode(array('success' => '1')))
			: $this->output->set_output(json_encode(array('success' => '0')));*/
		}

		if ($this->_fs === 'local') {

		}
	}

	private function _check_folder($path) {
		if ( ! $this->_ci->DocsM->does_path_exists($path)) {
			// Create folder
			return;
		}
		return;
	}

	private function _upload_files($path, $overwrite, $via) {
		if (empty($_FILES)) {
			return;
		}
		if ($overwrite === '1') {
			$filename = $_FILES['file']['name'];
		}
		else {
			$filename = $this->_check_filename($_FILES['file']['name']);
		}

		$this->_s3_put_object($path, $filename);
		$values = array();
		if ($this->_upload_status) {
			$dir_id = $this->_ci->DocsM->get_dir_id_from_path($path);
			$values['a_docs_parentid'] = $dir_id;
			$values['a_docs_ver_filename'] = $filename;
			$values['a_docs_ver_uploadvia'] = $via;
			$values['a_docs_ver_filesize'] = $_FILES['file']['size'];
			$values['a_docs_ver_mime'] = $_FILES['file']['type'];
			$values['a_docs_ver_stamp'] = get_current_stamp();
			$file_exists = $this->_ci->DocsM->does_file_exists($path, $filename);
			if ($overwrite === '1') {
				if ($file_exists) {
					// Update current version
					$docs_id = $this->_ci->DocsM->get_docs_id_from_path($path, $filename);
					$ver_id = $this->_ci->DocsM->get_current_ver_id($docs_id);
					$this->_ci->DocsM->update_docs_ver($values);
					return array('docs_id'=>$docs_id, 'path'=>$this->_format_dirpath($path, $filename));
				}
			}
			$docs_id = $this->_ci->DocsM->insert_docs($values);
			return array('docs_id'=>$docs_id, 'path'=>$this->_format_dirpath($path, $filename));
		}
	}

	private function _check_filename($filename) {
		// Break filename into filename and extension
		$_ext = substr(strrchr($filename, '.'), 0);
		$_ext_length = strlen($_ext);
		$old_filename = substr($filename,0,strlen($filename)-$_ext_length);

		if ( ! $this->_does_file_exists($old_filename.$_ext)) {
			return $filename;
		} else {
			$new_filename = $old_filename.'_'.randStr(3).$_ext;
			// Check if the generated name exists
			if ($this->_does_file_exists($new_filename)) {
				$this->_check_filename($filename);
			}
		}
		return $new_filename;
	}

	private function _does_file_exists($filename) {
		$i = $this->_ci->DocsM->search_filename($filename);
		if ( ! empty ($i)) return TRUE;
		return FALSE;
	}

	private function _s3_put_object($path, $filename) {
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		log_message('debug','Content-type:'.$contentType."\n");
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== FALSE) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				if (S3::putObject(S3::inputFile($_FILES['file']['tmp_name']), $this->_bucket,
					$this->_format_dirpath($path, $filename), S3::ACL_PRIVATE)) {
					$this->_upload_status = TRUE;
					log_message('debug', 'FileL: Upload: '.$this->_format_dirpath($path, $filename));
				} else {
					$this->_upload_status = FALSE;
				}
			}
		}
		// ----- end S3 code ---- /
		return;
	}

	private function _format_dirpath($path, $filename) {
		if ($path === '/') {
			return $path.$filename;
		} else {
			return $path.'/'.$filename;
		}
	}
}