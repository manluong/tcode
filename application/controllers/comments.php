<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CommentsM');
	}


	function test() {
		$data = array();
		$data['html'] = $this->commentsl->get_page_html();
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Comments';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	function save() {
		$data = array(
			'app_id' => $this->input->post('app_id'),
			'app_data_id' => $this->input->post('app_data_id'),
			'parent_id' => $this->input->post('parent_id'),
			'text' => $this->input->post('text'),
		);

		$this->CommentsM->save_reply($data);

		execute_return_url();
	}

	function ajax_save_reply() {
		$data = array(
			'app_id' => $this->input->post('app_id'),
			'app_data_id' => $this->input->post('app_data_id'),
			'parent_id' => $this->input->post('parent_id'),
			'text' => $this->input->post('text'),
		);

		$this->CommentsM->save_reply($data);
		$this->UserM->fill_card_info($data);

		$data['created_stamp_iso8601'] = parse_stamp_user($data['created_stamp'], 'ISO_8601');
		$data['created_stamp_iso'] = parse_stamp_user($data['created_stamp'], 'ISO_DATE');

		$response = array(
			'success' => TRUE,
			'data' => $data,
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
		$this->UserM->fill_card_info($data);

		$data['created_stamp_iso8601'] = parse_stamp_user($data['created_stamp'], 'ISO_8601');
		$data['created_stamp_iso'] = parse_stamp_user($data['created_stamp'], 'ISO_DATE');

		$response = array(
			'success' => TRUE,
			'data' => $data,
		);

		echo json_encode($response);
	}

	function ajax_load_more_replies() {
		$parent_id = $this->input->post('parent_id');

		$results = $this->CommentsM->get_more_replies($parent_id);

		foreach($results AS $k=>$v) {
			$results[$k]['created_stamp_iso8601'] = parse_stamp_user($v['created_stamp'], 'ISO_8601');
			$results[$k]['created_stamp_iso'] = parse_stamp_user($v['created_stamp'], 'ISO_DATE');
		}

		$response = array(
			'success' => TRUE,
			'data' => $results,
		);

		echo json_encode($response);
	}

	function ajax_load_more_comments() {
		$app_id = $this->input->post('app_id');
		$app_data_id = $this->input->post('app_data_id');
		$page = $this->input->post('page');

		$results = $this->CommentsM->get_page($app_id, $app_data_id, $page);

		foreach($results AS $k=>$v) {
			$results[$k]['created_stamp_iso8601'] = parse_stamp_user($v['created_stamp'], 'ISO_8601');
			$results[$k]['created_stamp_iso'] = parse_stamp_user($v['created_stamp'], 'ISO_DATE');

			foreach($results[$k]['replies'] AS $rk=>$rv) {
				$results[$k]['replies'][$rk]['created_stamp_iso8601'] = parse_stamp_user($rv['created_stamp'], 'ISO_8601');
				$results[$k]['replies'][$rk]['created_stamp_iso'] = parse_stamp_user($rv['created_stamp'], 'ISO_DATE');
			}
		}

		$response = array(
			'success' => TRUE,
			'data' => $results,
		);

		echo json_encode($response);
	}

}