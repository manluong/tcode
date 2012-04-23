<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_Email extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('UserM');
		$this->load->model('DS_Card_Email');
		
		$this->model =& $this->UserM;
	}

	function ajax_get_list() {
		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');

		$list = $this->DS_Card_Email->set_subaction('l')
					->set_limit($limit)
					->set_offset($offset)
					->get_data();

		$this->RespM->set_message()
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Card Email List')
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
				->set_title('Card Email Dataset')
				->set_details()
				->output_json();
			return;
		}

		$details['data'] = $this->DS_Card_Email->set_subaction('v')
				->set_id($id)
				->get_fields_with_data();

		$details['links'][] = array(
			'type' => 'submit',
			'url' => '/card_email/ajax_edit_save',
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
				->set_title('Card Email Dataset')
				->set_details($details)
				->output_json();
	}

	function ajax_edit_save() {
		$result = $this->DS_Card_Email->set_subaction('e')
					->save();

		$message = $this->DS_Card_Email->get_save_errors();

		$this->RespM->set_message($message)
				->set_type('form')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success($result)
				->set_title('Card Email Dataset')
				->set_details()
				->output_json();
	}
}