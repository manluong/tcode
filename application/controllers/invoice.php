<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('DS_Invoice');
	}

	function index() {
		$this->data['content'] = $this->load->view(get_template().'/invoice/index', '', TRUE);

		$this->_do_output();
	}

	function sendjson_list() {
		$this->DS_Invoice->set_subaction('l');

		$details = array(
			'columns' => $this->DS_Invoice->get_datatable_fields(),
			'data' => $this->DS_Invoice->get_datatable_data(),
			'ids' => $this->DS_Invoice->get_list_ids(),
			'setting' => array(
				'hidetitle' => 0,
			),
		);

		$this->RespM->set_message('sendjson_list')
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Invoice List')
				->set_details($details)
				->output_json();
	}

	function sendjson_view($id) {
		$data = $this->DS_Invoice->set_subaction('v')
					->set_id($id)
					->get_view_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Edit',
					'type' => 'ajax',
					'url' => '/invoice/sendjson_edit/'.$id,
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			),
		);

		$this->RespM->set_message('sendjson_view')
				->set_type('view')
				->set_template('')
				->set_success(true)
				->set_title('Invoice View')
				->set_details($details)
				->output_json();
	}

	function sendjson_edit($id) {
		$data = $this->DS_Invoice->set_subaction('e')
					->set_id($id)
					->get_form_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Submit',
					'type' => 'submit',
					'url' => '/invoice/sendjson_save_edit/'.$id,
					'style' => 'default',
					'icon' => '',
				),
				array(
					'target' => '',
					'text' => 'Cancel',
					'type' => 'ajax',
					'url' => '/invoice/sendjson_view/'.$id,
					'style' => 'warning',
					'icon' => 'trash',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			)
		);

		$this->RespM->set_message('sendjson_edit')
				->set_type('form')
				->set_template('')
				->set_success(true)
				->set_title('Invoice Edit')
				->set_details($details)
				->output_json();
	}

	function sendjson_save_edit($id) {
		$success = $this->DS_Invoice->set_subaction('e')
						->set_id($id)->save();

		if ($success) {
			$details['links'] = array(
				array(
					'type' => 'ajax',
					'url' => '/invoice/sendjson_view/'.$id,
					'target' => '',
					'text' => ''
				)
			);
			$message = 'Data saved.';
		} else {
			$details['data'] = $this->DS_Invoice->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Invoice Save')
				->set_details($details)
				->output_json();
	}

	function sendjson_new() {
		$data = $this->DS_Invoice->set_subaction('a')
					->get_form_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Submit',
					'type' => 'submit',
					'url' => '/invoice/sendjson_save_new',
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			)
		);

		$this->RespM->set_message('sendjson_new')
				->set_type('form')
				->set_template('')
				->set_success(true)
				->set_title('Invoice Insert')
				->set_details($details)
				->output_json();
	}

	function sendjson_save_new() {
		$success = $this->DS_Invoice->set_subaction('a')
						->save();

		if ($success) {
			$details['links'] = array(
				array(
					'type' => 'ajax',
					'url' => '/invoice/sendjson_list',
					'target' => '',
					'text' => ''
				)
			);
			$message = 'Data saved.';
		} else {
			$details['data'] = $this->DS_Invoice->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Invoice Save')
				->set_details($details)
				->output_json();
	}
}