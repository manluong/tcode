<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends MY_Controller{

	function __construct() {
		parent::__construct();

		$this->load->library('fileL');
	}

	function read($hash_or_id) {
		//TODO: Check logged-in user's credentials
		$file = $this->filel->read($hash_or_id);

		$this->output->set_header('Content-type: '.$file['mime'].';');
		$this->output->set_header('Content-Disposition:inline; filename="'.$file['file_name'].$file['file_ext'].'";');
		$this->output->set_output($file['contents']);
	}

	function ajax_upload() {
		$success = TRUE;
		$message = '';

		$dir_id = $this->input->post('dir_id');
		$docs_id = $this->input->post('docs_id');
		$overwrite = $this->input->post('overwrite');
		if ($overwrite === FALSE) $overwrite = '';

		$file_info = $this->filel->save('file', $dir_id, $docs_id, $overwrite);

		if ($file_info === FALSE) {
			$success = FALSE;
			$message = $this->filel->error_messages;
		}

		$this->RespM->set_success($success)
			->set_message($message)
			->set_details($file_info)
			->output_json();
	}
}