<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function get_list($name) {
		$ds = $this->get_dataset($name);

		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');

		$list = $ds->set_subaction('l')
					->set_limit($limit)
					->set_offset($offset)
					->get_data();

		$this->RespM->set_message()
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('List')
				->set_details($list)
				->output_json();
	}

	function view($name) {
		$ds = $this->get_dataset($name);

		$id = $this->input->post('id');

		if ($id === false) {
			$this->RespM->set_message('Invalid ID')
				->set_type('view')
				->set_template('')
				->set_success(false)
				->set_title('Dataset')
				->set_details()
				->output_json();
			return;
		}

		$details['data'] = $ds->set_subaction('v')
				->set_id($id)
				->get_view_data();

		$this->RespM->set_message()
				->set_type('view')
				->set_template('')
				->set_success(true)
				->set_title('Dataset')
				->set_details($details)
				->output_json();
	}

	function add($name) {
		$ds = $this->get_dataset($name);

		$details['data'] = $ds->set_subaction('a')
				->get_form_data();

		$details['links'][] = array(
			'type' => 'submit',
			'url' => '/api/add_save/'.$name,
			'target' => 'form',
			'style' => 'primary',
			'icon' => 'ok',
			'text' => $this->lang->line('button_save'),
		);

		$this->RespM->set_message()
				->set_type('form')
				->set_template('')
				->set_success(true)
				->set_title('Dataset')
				->set_details($details)
				->output_json();
	}

	function add_save($name) {
		$ds = $this->get_dataset($name);

		$result = $ds->set_subaction('a')
					->save();

		$message = $ds->get_save_errors();

		$this->RespM->set_message($message)
				->set_type('form')
				->set_template('')
				->set_success($result)
				->set_title('Dataset')
				->set_details()
				->output_json();
	}

	function edit($name) {
		$ds = $this->get_dataset($name);

		$id = $this->input->post('id');

		if ($id === false) {
			$this->RespM->set_message('Invalid ID')
				->set_type('form')
				->set_template('')
				->set_success(false)
				->set_title('Dataset')
				->set_details()
				->output_json();
			return;
		}

		$details['data'] = $ds->set_subaction('e')
				->set_id($id)
				->get_fields_with_data();

		$details['links'][] = array(
			'type' => 'submit',
			'url' => '/api/edit_save/'.$name,
			'target' => 'form',
			'style' => 'primary',
			'icon' => 'ok',
			'text' => $this->lang->line('button_save'),
		);

		$this->RespM->set_message()
				->set_type('form')
				->set_template('')
				->set_success(true)
				->set_title('Dataset')
				->set_details($details)
				->output_json();
	}

	function edit_save($name) {
		$ds = $this->get_dataset($name);

		$result = $ds->set_subaction('e')
					->save();

		$message = $ds->get_save_errors();

		$this->RespM->set_message($message)
				->set_type('form')
				->set_template('')
				->set_success($result)
				->set_title('Dataset')
				->set_details()
				->output_json();
	}

	private function get_dataset($name) {
		$ds_name = 'DS_'.$name;
		$this->load->model($ds_name);
		return $this->$ds_name;
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