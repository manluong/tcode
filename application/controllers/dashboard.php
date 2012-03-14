<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$html = array();

		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/dashboard/index', $html, TRUE);
		$data['outputdiv'] = 0;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Wall';
		$data['div']['element_name'] = 'wall';
		$data['div']['element_id'] = 'wall';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->layout['type'] = 'full';

		$this->output();
	}

	function ajax_wall($id='') {
		$wall = $this->LogM->get_wall($id, 10);

		$this->load->model('CommentsM');
		foreach($wall AS $k=>$v) {
			$wall[$k]['comment_count'] = $this->CommentsM->get_comment_count(18, $v['id']);
		}

		$this->RespM->set_message('')
				->set_type('list')
				->set_template('wall')
				->set_success(true)
				->set_title('Wall')
				->set_details($wall)
				->output_json();
	}
}