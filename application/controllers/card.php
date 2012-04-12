<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('UserM');
		$this->load->model('DS_Card');

		$this->model =& $this->UserM;
	}

	function ajax_get_list() {
		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');

		$list = $this->DS_Card->set_subaction('l')
					->set_limit($limit)
					->set_offset($offset)
					->get_data();

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

	function x_info() {
		$details = array(
			'data' => $this->DS_Card->get_view_data(),
			'links' => array(
				array(
					'target' => '',
					'text' => 'Edit',
					'type' => 'ajax',
					'url' => '/card/x_info/'.$this->url['id_plain'].'/e',
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			),
		);

		$this->RespM->set_message()
				->set_type('view')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success(true)
				->set_title('Card Info Dataset')
				->set_details($details)
				->output_json();
	}

	function ajax_edit() {
		$id = $this->input->post('id');

		$details['data'] = $this->DS_Card->set_subaction('v')
				->set_id($id)
				->get_fields_with_data();

		$this->RespM->set_message()
				->set_type('view')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success(true)
				->set_title('Card Info Dataset')
				->set_details($details)
				->output_json();
	}


























}