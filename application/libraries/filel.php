<?php

class FileL {
	private $CI = '';

	private $filesystem = '';
	private $domain = '';

	private $bucket = '';
	private $s3_path = '';

	private $temp_dir = '';

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

		$this->temp_dir = $this->CI->eightforce_config['temp_folder'].$this->domain.'/docs/files/upload/';

		if ( ! file_exists($this->temp_dir)) {
			mkdir($this->temp_dir, 0777, true);
		}
	}

	function read($hash_or_id) {
		$file = $this->CI->DocsM->get_detail($hash_or_id);

		if ($this->filesystem === 'S3') {
			$file['contents'] = $this->read_from_s3($file['hash']);
		}

		if ($this->filesystem === 'local') {

		}

		return $file;
	}

	// Saves new content, creates a new docs_id
	// return: $file_id = array('id','hash');
	function save_new($content, $path, $filename, $overwrite=FALSE, $via='') {
		$file_info = $this->save_temp_file($content, $filename);

		if ($file_info === FALSE) {
			log_message('error', 'Unable to save to temp folder');
			return FALSE;
		}

		$file_id = $this->CI->DocsM->new_file($file_info, $path);

		if ($this->filesystem === 'S3') {
			$this->upload_to_s3($file_info['filepath'], $file_id['hash']);
		}

		if ($this->filesystem === 'local') {

		}

		$this->delete_temp_file($file_info['filepath']);

		return $file_id;
	}

	// Saves content over existing docs_id or creates a new ver for it.
	// if $versioning is not set to TRUE or FALSE, it will take the versioning info from the directory it is saved to
	function save_existing($content, $hash_or_id, $filename, $versioning='', $via='') {
		$file_info = $this->save_temp_file($content, $filename);

		if ($file_info === FALSE) {
			log_message('error', 'Unable to save to temp folder');
			return FALSE;
		}

		$file_id = $this->CI->DocsM->overwrite_file($hash_or_id, $file_info, $versioning);

		if ($this->filesystem === 'S3') {
			$this->upload_to_s3($file_info['filepath'], $file_id['hash']);
		}

		if ($this->filesystem === 'local') {

		}

		$this->delete_temp_file($file_info['filepath']);

		return $file_id;
	}

	function delete($hash_or_id) {
		$this->CI->DocsM->delete($hash_or_id);
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

	function save_temp_file(&$content, $filename) {
		$filepath = $this->temp_dir.$filename;

		$fp = fopen($filepath, 'wb');
		if ($fp === FALSE) {
			log_message('debug', 'Error saving to temp folder '.$filepath);
			return FALSE;
		}

		if ( ! fwrite($fp, $content)) {
			fclose($fp);
			log_message('debug', 'Error saving content to '.$filepath);
			return FALSE;
		}
		fclose($fp);

		$f = finfo_open(FILEINFO_MIME_TYPE);
		$mime_type = finfo_file($f, $filepath);
		finfo_close($f);

		$extension = get_file_extension($filename);

		$fileinfo = array(
			'filename' => str_replace($extension, '', $filename),
			'filepath' => $filepath,
			'mime' => $mime_type,
			'extension' => $extension,
			'filesize' => filesize($filepath)
		);

		return $fileinfo;
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

	function delete_from_s3($filename) {
		if ( S3::deleteObject($this->bucket, $this->s3_path.$filename) ) {
			log_message('debug', 'FileL: S3 Delete: '.$this->s3_path.$filename);
			return TRUE;
		} else {
			log_message('error', 'Unable to delete from S3');
			return FALSE;
		}
	}
}