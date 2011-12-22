<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CommentsM');
	}

	function index() {
		$this->test();
	}

	function test() {
		$data = array();
		$data['html'] = $this->commentsl->get_list_html();
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Comments';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	function ajax_save_reply() {
		$data = array(
			'app_id' => $this->input->post('app_id'),
			'app_data_id' => $this->input->post('app_data_id'),
			'parent_id' => $this->input->post('parent_id'),
			'text' => $this->input->post('text'),
		);

		$this->CommentsM->save_reply($data);

		$response = array(
			'success' => TRUE,
			'results' => '',
		);

		echo json_encode($response);
	}

	function ajax_save_comment() {
		$data = array(
			'app_id' => $this->input->post('app_id'),
			'app_data_id' => $this->input->post('app_data_id'),
			'parent_id' => 0,
			'text' => $this->input->post('text'),
		);

		$this->CommentsM->save_reply($data);

		$response = array(
			'success' => TRUE,
			'results' => '',
		);

		echo json_encode($response);
	}

	function ajax_load_more_replies() {
		$parent_id = $this->input->post('parent_id');

		$results = $this->CommentsM->get_replies($parent_id, 0);

		$response = array(
			'success' => TRUE,
			'resultss' => $results,
		);

		echo json_encode($response);
	}


}