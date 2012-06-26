<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$this->data['content'] = $this->load->view(get_template().'/activity/index', '', TRUE);

		$this->_do_output();
	}

	function ajax_wall($id='') {
		$this->load->model('ActivityM');
		$wall = $this->ActivityM->get_wall($id, 5);

		$this->RespM->set_message('')
				->set_type('list')
				->set_template('wall')
				->set_success(true)
				->set_title('Wall')
				->set_details($wall)
				->output_json();
	}

	function new_post() {
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