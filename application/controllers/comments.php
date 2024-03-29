<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CommentsM');
		$this->load->library('CommentsL');
	}


	function test() {
		$this->commentsl->app_id = 1000;
		$this->commentsl->app_data_id = 1;

		$this->data['content'] = $this->commentsl->get_lite_html();

		$this->_do_output();
	}

	function save() {
		$data = array(
			'app_id' => $this->input->post('app_id'),
			'app_data_id' => $this->input->post('app_data_id'),
			'parent_id' => $this->input->post('parent_id'),
			'text' => $this->input->post('text'),
		);

		$this->CommentsM->save($data);

		execute_return_url();
	}

	function ajax_load($app_id, $data_id) {
		$this->commentsl->app_id = $app_id;
		$this->commentsl->app_data_id = $data_id;

		echo $this->commentsl->get_lite_html(1);
	}

	function ajax_save_comment() {
		$app_id = $this->input->post('app_id');
		$app_data_id = $this->input->post('app_data_id');

		$data = array(
			'app_id' => $app_id,
			'app_data_id' => $app_data_id,
			'parent_id' => $this->input->post('parent_id'),
			'text' => $this->input->post('text'),
		);

		$this->CommentsM->save($data);
		$this->UserM->fill_card_info($data);
		$this->CommentsM->insert_timeago_stamp($data);
		if ($data['parent_id'] != 0) {
			$data['in_reply_to']['name'] = $this->CommentsM->get_reply_to_name($data['parent_id']);
		}

		//if it's activity feed app
		if  ($app_id == 1) {
			$this->load->model('ActivityM');
			$this->ActivityM->touch($app_data_id);
		}

		$this->RespM->set_message('')
				->set_type('list')
				->set_template('post')
				->set_success(TRUE)
				->set_title('Comments')
				->set_details($data)
				->output_json();
	}

	function ajax_load_more_replies() {
		$parent_id = $this->input->post('parent_id');
		$show_replies = $this->input->post('show_replies');

		$results = $this->CommentsM->get_more_replies($parent_id, $show_replies);

		$this->RespM->set_message('')
				->set_type('list')
				->set_template('post')
				->set_success(TRUE)
				->set_title('Comments')
				->set_details($results)
				->output_json();
	}

	function ajax_load_more_comments() {
		$last_id = $this->input->post('last_id');
		$threaded = $this->input->post('threaded');
		$app_id = $this->input->post('app_id');
		$app_data_id = $this->input->post('app_data_id');

		//string value false was treated as boolean true so we need to confirm
		$threaded = ($threaded === 'true') ? TRUE : FALSE;

		$this->CommentsM->threaded = $threaded;
		$this->CommentsM->get_all = TRUE;
		$this->CommentsM->older_comments_top = TRUE;
		$results = $this->CommentsM->get_remaining($app_id, $app_data_id, $last_id);

		$this->RespM->set_message('')
				->set_type('list')
				->set_template('post')
				->set_success(TRUE)
				->set_title('Comments')
				->set_details($results)
				->output_json();
	}

}