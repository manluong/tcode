<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tax extends MY_Controller{

	function __construct() {
		parent::__construct();
	}

	function ajax_setting() {
		$this->load->model('Tax_UseM');

		$data_configure = array(
			'is_admin' => $this->UserM->is_admin(),
			'tax_use' => $this->Tax_UseM->get_list(),
		);

		$this->load->view(get_template().'/tax/setting', $data_configure);
	}

	function ajax_save_setting() {
		$this->load->model('Tax_UseM');
		
		$tax_use = $this->input->post('disabled');
		$result = $this->Tax_UseM->save_disabled_setting($tax_use);

		$this->RespM->set_success($result)
				->output_json();
	}
}