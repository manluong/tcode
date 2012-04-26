<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$this->data['content'] = $this->load->view(get_template().'/dashboard/index', '', TRUE);

		$this->_do_output();
	}

	function ajax_wall($id='') {
		$wall = $this->LogM->get_wall($id, 10);

		$this->load->model('CommentsM');
		$this->CommentsM->results_per_page = 2;
		$this->CommentsM->older_comments_top = TRUE;
		$this->CommentsM->threaded = FALSE;

		foreach($wall AS $k=>$v) {
			$wall[$k]['comments'] = $this->CommentsM->get_page(18, $v['id'], 1, 0);

			$comment_count = $this->CommentsM->get_comment_count(18, $v['id']);
			$wall[$k]['comments_more'] = ($comment_count > 2);
		}

		$this->RespM->set_message('')
				->set_type('list')
				->set_template('wall')
				->set_success(true)
				->set_title('Wall')
				->set_details($wall)
				->output_json();
	}

	function new_post() {
		$this->url['subaction'] = 'a';
		//because the subaction was changed, we need to reload the logtype
		$this->LogM->update_log_type();

		$text = $this->input->post('text');

		$post = $this->LogM->insert_wall_post($text);
		$post['comment_count'] = 0;

		if ($this->input->is_ajax_request()) {
			$this->RespM->set_message('')
				->set_type('list')
				->set_template('wall')
				->set_success(true)
				->set_title('Wall')
				->set_details($post)
				->output_json();
		} else {
			redirect('/dashboard');
		}
	}
}