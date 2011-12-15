<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	
	
	public function index()	{
		//if ($this->User->is_logged_in()) redirect('/dashboard');
		
		$this->lang->load('sample', 'english');
		
		echo $this->lang->line('edward');

		
		$this->load->view('dashboard','');
	}

	public function about($pa2, $pa3) {
		
		echo "dashboard about";
		echo $pa2;
		echo $pa3;
		//$this->html['body'] = $this->load->view('main_about','',true);
		//$this->output();
	}
	
		
}