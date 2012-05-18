<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CardM');
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

	function x_card() {
		$details = array(
			'columns' => $this->DS_Card->get_datatable_fields(),
			'data' => $this->DS_Card->get_datatable_data(),
			'ids' => $this->DS_Card->get_list_ids(),
			'setting' => array(
				'hidetitle' => 0,
			),
			'listlinks' => array(
				array(
					'target' => '',
					'text' => 'View',
					'type' => 'page',
					'url' => '/card/view/{{id}}/v',
					'style' => 'default',
					'icon' => '',
				)
			),
		);

		$this->RespM->set_message()
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Contacts List')
				->set_details($details)
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

	function ajax_edit_save() {
		$result = $this->DS_Card->set_subaction('e')
					->save();

		$message = $this->DS_Card->get_save_errors();

		$this->RespM->set_message($message)
				->set_type('form')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success($result)
				->set_title('Card Info Dataset')
				->set_details()
				->output_json();
	}
}