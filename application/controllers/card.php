<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('UserM');

		$this->model =& $this->UserM;
	}

	function x_card() {
		$this->DatasetM->load('info');

		$details = array(
			'columns' => $this->DatasetM->get_datatable_fields(),
			'data' => $this->DatasetM->get_datatable_data(),
			'ids' => $this->DatasetM->get_list_ids(),
			'setting' => array(
				'hidetitle' => 0,
			),
		);

		$this->RespM->set_message($this->DatasetM->sql)
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Contacts List')
				->set_details($details)
				->output_json();
	}

}