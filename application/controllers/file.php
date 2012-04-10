<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends MY_Controller{

	function __construct() {
		parent::__construct();

		$this->load->library('fileL');
	}

	function read($hash_or_id) {
		$file = $this->filel->read($hash_or_id);

		$this->output->set_header('Content-type: '.$file['mime'].';');
		$this->output->set_header('Content-Disposition:inline; filename="'.$file['file_name'].$file['file_ext'].'";');
		$this->output->set_output($file['contents']);
	}
}