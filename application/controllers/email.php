<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('Email_TemplateM');
	}

	function ajax_get($id) {
		$this->RespM->set_success(TRUE)
			->set_details($this->Email_TemplateM->get($id))
			->output_json();
	}

	function ajax_save() {
		$template_id = $this->Email_TemplateM->save();
		if ($template_id === FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->Email_TemplateM->get_error_string())
				->set_details($this->Email_TemplateM->field_errors)
				->output_json();
			exit;
		}

		$this->RespM->set_success(TRUE)
			->output_json();
	}
}