<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$this->data['content'] = $this->load->view(get_template().'/dashboard/index', '', TRUE);

		$this->_do_output();
	}
}