<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CardM');
	}

	function index() {
		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;

		$view_data = array(
			'list' => $this->CardM->get_list(),
		);

		$this->data['content'] = $this->load->view(get_template().'/card/index', $view_data, TRUE);
		$this->_do_output();
	}

	function view($id) {
		//$view_data['data'] = $this->CardM->get($id);
		$view_data = array(
			'data' => $this->CardM->get($id),
			'card_email' => $this->CardM->get_card_email($id),
			'card_social' => $this->CardM->get_card_social($id),
			'card_phone' => $this->CardM->get_card_phone($id),
			'card_address' => $this->CardM->get_card_address($id),
		);
		$this->data['content'] = $this->load->view(get_template().'/card/view', $view_data, TRUE);
		$this->_do_output();
	}

	function edit($id) {
		$view_data['data'] = $this->CardM->get($id);
		$view_data['is_new'] = FALSE;

		$this->data['content'] = $this->load->view(get_template().'/card/edit', $view_data, TRUE);
		$this->_do_output();
	}
	
	function card_ajax_edit() {
		$id = $this->input->post('id');
		
		$view_data['data'] = $this->CardM->get($id);
		$view_data['is_new'] = FALSE;

		$content = $this->load->view(get_template().'/card/edit', $view_data, TRUE);
		echo $content;
	}
	
	function save() {
		$id = $this->CardM->save();
		if ($id == FALSE) {
			echo 'error saving.';
			echo '<pre>', print_r($this->CardM->errors, TRUE), '</pre>';
		} else {
			redirect('/card/view/'.$id);
		}
	}

	function ajax_get_list() {
		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');

		$this->CardM->limit = $limit;
		$this->CardM->offset = $offset;

		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$list = $this->CardM->get_list();

		$this->RespM->set_message()
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Contacts List')
				->set_details($list)
				->output_json();
	}

	function ajax_get() {
		$id = $this->input->post('id');
		$result = $this->CardM->get($id);

		$this->RespM->set_message('')
			->set_type('')
			->set_template('')
			->set_success(TRUE)
			->set_title('Contact Info')
			->set_details($result)
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

		$result['data'] = $this->CardM->get($id);
		$result['is_new'] = FALSE;

		$this->RespM->set_message()
				->set_type('form')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success(true)
				->set_title('Card Info Dataset')
				->set_details($result)
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