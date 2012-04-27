<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('TagsM');
	}

	function index() {
		$this->load->library('TagsL');

		$html = array();
		$html['search'] = $this->input->get('search');

		$this->data['content'] = $this->load->view(get_template().'/tags/search', $html, TRUE);

		$this->_do_output();
	}


	function test() {
		$this->load->library('TagsL');

		$this->tagsl->app_id = 10;
		$this->tagsl->app_data_id = 1;

		$this->data['content'] = $this->tagsl->get_html();

		$this->_do_output();
	}

	function test_cloud() {
		$this->load->library('TagsL');

		$this->tagsl->app_id = 1001;
		$this->tagsl->app_data_id = 1;

		$this->data['content'] = $this->tagsl->get_cloud_html();

		$this->_do_output();
	}

	function ajax_add() {
		$app_id = $this->input->post('app_id');
		$app_data_id = $this->input->post('app_data_id');
		$tag = $this->input->post('tag');

		$this->TagsM->add($app_id, $app_data_id, $tag);

		$response = array(
			'success' => TRUE,
			'data' => $data,
		);

		echo json_encode($response);
	}

	function ajax_remove() {
		$app_id = $this->input->post('app_id');
		$app_data_id = $this->input->post('app_data_id');
		$tag = $this->input->post('tag');

		$this->TagsM->remove($app_id, $app_data_id, $tag);

		$response = array(
			'success' => TRUE,
			'data' => $data,
		);

		echo json_encode($response);
	}


	function ajax_search() {
		$tag = $this->input->post('tag');
		$tag = explode(',', $tag);

		$app_keys = $this->TagsM->search($tag);
		$data = $this->TagsM->get_details($app_keys);

		$response = array(
			'success' => TRUE,
			'data' => $data,
		);

		echo json_encode($response);
	}
}