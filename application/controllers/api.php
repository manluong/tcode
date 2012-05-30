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

	function ajax_get_staff_list() {
		$this->load->model('CardM');

		$this->CardM->select_fields = array(
			'id', 'display_name', 'first_name', 'last_name', 'avatar'
		);

		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;

		$results = $this->CardM->get_staff_list();

		$this->RespM->set_success(TRUE)
				->set_details($results)
				->output_json();
	}
}