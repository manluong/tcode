<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helloworld_new extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('HelloworldM');
	}

	function index() {
		$this->HelloworldM->limit = 5;
		$view_data['list'] = $this->HelloworldM->get_list();
		$this->data['content'] = $this->load->view(get_template().'/helloworld_new/index', $view_data, TRUE);

		$this->_do_output();
	}

	function show_list() {
		$view_data['list'] = $this->HelloworldM->get_list();
		$this->data['content'] = $this->load->view(get_template().'/helloworld_new/list', $view_data, TRUE);

		$this->_do_output();
	}

	function show_five() {
		$this->HelloworldM->limit = 5;
		$view_data['list'] = $this->HelloworldM->get_list();
		$this->data['content'] = $this->load->view(get_template().'/helloworld_new/list', $view_data, TRUE);

		$this->_do_output();
	}



}
