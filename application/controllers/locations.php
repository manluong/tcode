<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Locations extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('LocationM');
	}

	function ajax_get_list() {
		$locations = array_merge(array(array('id'=>'', 'name'=>'')), $this->LocationM->get_list());

		$response = array(
			'success' => TRUE,
			'data' => $locations,
		);

		echo json_encode($response);
	}


}