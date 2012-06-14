<?php

class FileL {
	private $CI = '';

	private $filesystem = '';
	private $domain = '';

	private $bucket = '';
	private $s3_path = '';

	private $temp_dir = '';

	var $error_messages = array();

	function __construct() {
		$this->CI = & get_instance();
		$this->CI->load->library('s3');
		$this->CI->load->Model('DocsM');

		$this->filesystem = $this->CI->eightforce_config['file_storage_system'];
		$this->domain = $this->CI->domain;

		if ($this->filesystem == 'S3') {
			$this->bucket = $this->CI->eightforce_config['s3_bucket'];
			$this->s3_path = 'tenants/'.$this->domain.'/';
		}

		$this->temp_dir = $this->CI->eightforce_config['temp_folder'].$this->domain.'/';
		if ( ! file_exists($this->temp_dir)) {
			mkdir($this->temp_dir, 0777, true);
		}
	}

	function read($hash_or_id) {
		if ($hash_or_id == '') return FALSE;

		$file = $this->CI->DocsM->get_detail($hash_or_id);

		if ($file === FALSE) return FALSE;

		if (file_exists($this->temp_dir.$file['hash'])) {
			$file['contents'] = file_get_contents($this->temp_dir.$file['hash']);
		}

		if ($this->filesystem === 'S3') {
			$file['contents'] = $this->read_from_s3($file['hash']);
		}

		if (strlen($file['contents']) == 0) return FALSE;

		return $file;
	}

	function get_url($hash_or_id, $lifetime=3600) {
		if ($hash_or_id == '') return FALSE;

		$file = $this->CI->DocsM->get_detail($hash_or_id);

		if ($this->filesystem === 'S3') {
			$url = $this->url_from_s3($file['hash'], $lifetime);
		}

		if ($this->filesystem === 'local') {

		}

		return $url;
	}

	//all apps should use this function to store uploads from users
	function save($form_field='userfile', $dir_id_or_name=0, $docs_id=FALSE, $overwrite='') {
		$this->CI->load->library('Upload');

		//Configure CI's file upload
		$config = array(
			'upload_path' => $this->temp_dir,
			'allowed_types' => '*',
			'encrypt_name' => TRUE,
		);

		//if Docs ID provided, load the existing file info
		if ($docs_id !== FALSE) {
			$existing_file_info = $this->CI->DocsM->get_detail($docs_id);
		}

		//If overwrite not specified, get from the target directory's setting
		if ($overwrite === '') {
			if ($docs_id !== FALSE) $dir_id_or_name = $existing_file_info['dir_id'];

			$dir_info = $this->CI->DocsM->get_dir_detail($dir_id_or_name);
			$overwrite = ($dir_info['has_versioning'] == 0);
		}

		//update CI file upload configuration
		$config['overwrite'] = $overwrite;

		//if Docs ID given and want to Overwrite, update CI file upload config to use filename same one has the existing file
		if ($overwrite && $docs_id !== FALSE) {
			$config['file_name'] = $existing_file_info['hash'];
		}

		$this->CI->upload->initialize($config);

		//perform the upload
		$result = $this->CI->upload->do_upload($form_field);
		if ($result === FALSE) {
			$this->error_messages = $this->CI->upload->display_errors();
			return FALSE;
		}

		//data of the newly uploaded file
		$new_file_data = $this->CI->upload->data();

		//upload to S3 if needed
		if ($this->filesystem === 'S3') {
			$this->upload_to_s3($new_file_data['full_path'], $new_file_data['raw_name']);
		} elseif ($this->filesystem === 'local') {

		}

		//strip extension of file in the cache folder
		rename($new_file_data['full_path'], $new_file_data['file_path'].$new_file_data['raw_name']);

		//create DB entry
		if ($docs_id !== FALSE) {
			return $this->CI->DocsM->overwrite_file($docs_id, $new_file_data, $overwrite);
		} else {
			return $this->CI->DocsM->new_file_in_dir($new_file_data, $dir_id_or_name);
		}
	}

	function save_raw($content, $filename='', $dir_id_or_name=0, $docs_id=FALSE, $overwrite='') {
		$filehash = generate_hash();

		//if Docs ID provided, load the existing file info
		if ($docs_id !== FALSE) {
			$existing_file_info = $this->CI->DocsM->get_detail($docs_id);
		}

		//If overwrite not specified, get from the target directory's setting
		if ($overwrite === '') {
			if ($docs_id !== FALSE) $dir_id_or_name = $existing_file_info['dir_id'];

			$dir_info = $this->CI->DocsM->get_dir_detail($dir_id_or_name);
			$overwrite = ($dir_info['has_versioning'] == 0);
		}

		//if Docs ID given and want to Overwrite, update CI file upload config to use filename same one has the existing file
		if ($overwrite && $docs_id !== FALSE) {
			$filehash = $existing_file_info['hash'];
		}

		//save to temp
		$filepath = $this->write_to_temp($content, $filehash);

		$this->load->helper('file');
		//data of the newly uploaded file
		if ($filename == '') $filename = $filehash;
		$new_file_data = array(
			'file_name' => $filename,
			'file_type' => get_mime_by_extension($filename),
			'file_path' => $this->temp_dir,
			'full_path' => $filepath,
			'raw_name' => $filehash,
			'orig_name' => $filename,
			'client_name' => $filename,
			'file_ext' => '.'.get_file_extension($filename),
			'file_size' => strlen($content)/1000
		);

		//upload to S3 if needed
		if ($this->filesystem === 'S3') {
			$this->upload_to_s3($new_file_data['full_path'], $new_file_data['raw_name']);
		}

		//create DB entry
		if ($docs_id !== FALSE) {
			return $this->CI->DocsM->overwrite_file($docs_id, $new_file_data, $overwrite);
		} else {
			return $this->CI->DocsM->new_file_in_dir($new_file_data, $dir_id_or_name);
		}
	}

	function delete($hash_or_id) {
		return $this->CI->DocsM->delete($hash_or_id);
	}

	function delete_dir($dir_id) {
		return $this->CI->DocsM->delete_dir_by_id($dir_id);
	}

	function create_physical_folder($folder='') {
		if ($this->filesystem == 'S3') {
			//ensure a / at the end to create a folder in AWS
			if (substr($folder,-1) !== '/') $folder = $folder.'/';

			return S3::putObject('', $this->CI->eightforce_config['s3_bucket'], $folder);
		}

		if ($this->filesystem == 'local') {
		}
	}

	function delete_temp_file($filepath) {
		return unlink($filepath);
	}

	function upload_to_s3($path_to_source, $filename) {
		if ( S3::putObject(S3::inputFile($path_to_source), $this->bucket, $this->s3_path.$filename, S3::ACL_PRIVATE) ) {
			log_message('debug', 'FileL: S3 Upload: '.$path_to_source);
			return TRUE;
		} else {
			log_message('error', 'Unable to upload to S3');
			return FALSE;
		}
	}

	function read_from_s3($filename) {
		$object = S3::getObject($this->bucket, $this->s3_path.$filename, FALSE);
		return $object->body;
	}

	function url_from_s3($filename, $lifetime=3600) {
		return S3::getAuthenticatedURL($this->bucket, $this->s3_path.$filename, $lifetime);
	}

	function delete_from_s3($filename) {
		if ( S3::deleteObject($this->bucket, $this->s3_path.$filename) ) {
			log_message('debug', 'FileL: S3 Delete: '.$this->s3_path.$filename);
			return TRUE;
		} else {
			log_message('error', 'Unable to delete from S3');
			return FALSE;
		}
	}

	private function write_to_temp(&$content, $filename) {
		$filepath = $this->_temp_dir.$filename;
		$fp = fopen($filepath, 'wb');

		if ( ! fwrite($fp, $content)) {
			fclose($fp);
			log_message('debug', 'Error saving content to '.$filepath);
			return FALSE;
		}
		fclose($fp);

		return $filepath;
	}
}