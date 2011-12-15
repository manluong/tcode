<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Staff extends MY_Controller {



	public function index($pa2, $pa3)	{
		//if ($this->User->is_logged_in()) redirect('/dashboard');
		
		echo "staff index";
		echo $pa2;
		echo $pa3;
		
		//$this->html['body'] = $this->load->view('main_login','',true);
		//$this->output_do();
	}
	
	public function about2() {
		
		echo "1";	

	}
		
	public function about() {
		
		$thisresult['html'] = "This is about us";
		$thisresult['outputdiv'] = 1;
		return($thisresult);		

	}
	
	
}