<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('TaskM');
	}

	function ajax_get_list() {
		//$tasks = array_merge(array(array('id'=>'', 'name'=>'')), $this->TasksM->get_list());
		$tasks = array();

		$response = array(
			'success' => TRUE,
			'data' => $tasks,
		);

		echo json_encode($response);
	}


}