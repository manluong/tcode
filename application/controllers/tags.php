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

		$data = array();
		$data['html'] = $this->load->view(get_template().'/tags/search', $html, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Tags';
		$data['div']['element_name'] = 'tagwin';
		$data['div']['element_id'] = 'divtag';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}


	function test() {
		$this->load->library('TagsL');

		$this->tagsl->app_id = 10;
		$this->tagsl->app_data_id = 1;

		$data = array();
		$data['html'] = $this->tagsl->get_html();
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Tags';
		$data['div']['element_name'] = 'tagwin';
		$data['div']['element_id'] = 'divtag';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	function test_cloud() {
		$this->load->library('TagsL');

		$this->tagsl->app_id = 1001;
		$this->tagsl->app_data_id = 1;

		$data = array();
		$data['html'] = $this->tagsl->get_cloud_html();
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Tag Cloud';
		$data['div']['element_name'] = 'tagwin';
		$data['div']['element_id'] = 'divtag';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
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