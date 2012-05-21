<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CardM');
	}

	function index() {
		$view_data = array(
			'list' => $this->CardM->get_list(),
		);

		$this->data['content'] = $this->load->view(get_template().'/card/index', $view_data, TRUE);
		$this->_do_output();
	}

	function view($id) {
		$view_data = $this->CardM->get($id);

		$this->data['content'] = $this->load->view(get_template().'/card/view', $view_data, TRUE);
		$this->_do_output();
	}

	function edit($id) {
		$view_data = $this->CardM->get($id);

		$this->data['content'] = $this->load->view(get_template().'/card/edit', $view_data, TRUE);
		$this->_do_output();
	}

	function save() {
		//$id = $this->CardM->save();
		echo '<pre>', print_r($_POST, TRUE), '</pre>';
		die();
		if ($id == FALSE) {
			echo 'error saving.';
			echo '<pre>', print_r($this->CardM->errors, TRUE), '</pre>';
		} else {
			redirect('/card/view'.$id);
		}
	}

	function ajax_get_list() {
		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');

		$this->CardM->limit = $limit;
		$this->CardM->offset = $offset;

		$list = $this->CardM->get_list();

		$this->RespM->set_message()
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Contacts List')
				->set_details($list)
				->output_json();
	}

	function ajax_edit() {
		$id = $this->input->post('id');

		if ($id === FALSE) {
			$this->RespM->set_message('Invalid ID')
				->set_type('form')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success(False)
				->set_title('Card Info Dataset')
				->set_details()
				->output_json();
			return;
		}

		$details['data'] = $this->DS_Card->set_subaction('v')
				->set_id($id)
				->get_fields_with_data();

		$details['links'][] = array(
			'type' => 'submit',
			'url' => '/card/ajax_edit_save',
			'target' => 'form',
			'style' => 'primary',
			'icon' => 'ok',
			'text' => 'Save',
		);

		$this->RespM->set_message()
				->set_type('form')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success(true)
				->set_title('Card Info Dataset')
				->set_details($details)
				->output_json();
	}

	function ajax_save() {
		$id = $this->CardM->save();

		$success = ($id !== FALSE);

		$message = '';
		$details = '';
		if (!$success) {
			$message = $this->CardM->get_error_string();
			$details = $this->CardM->error_fields;
		}

		$this->RespM->set_message($message)
				->set_type('form')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success($success)
				->set_title('Card Info Dataset')
				->set_details($details)
				->output_json();
	}
}