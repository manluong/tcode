<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {

	public function _remap($method, $params = array())
	{
		echo "client remap";
		echo $method;
		print_r($params);
		exit;
	    // Some code here...
	}

	public function index($pa2, $pa3, $pa4)	{
		//if ($this->User->is_logged_in()) redirect('/dashboard');
		
		echo "client index";
		echo $pa2;
		echo $pa3;
		echo $pa4;
		
		
		


		//$this->html['body'] = $this->load->view('main_login','',true);
		$this->output_do();
	}
	
	public function about() {
		
		echo "about client";
		//$this->html['body'] = $this->load->view('main_about','',true);
		//$this->output();
	}
	
	
}