<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function ajax_get_apps() {
		$results = $this->AppM->acl_app_list;

		$this->RespM->set_message('')
				->set_type('form')
				->set_template('')
				->set_success(TRUE)
				->set_title('Dataset')
				->set_details($results)
				->output_json();
	}
}