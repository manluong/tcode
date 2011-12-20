<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docs extends MY_Controller {
	
	function __construct() {
		parent::__construct();

	}
	
	function after_docs_dir(){
		
		echo "just a demo. Uncheck the 2nd action element in Docs > x_listdis. So I will not be run."; exit;
		//there should be a setting to check when this method to be run
		//for example, only run when subsction = as (add save)
		
	}
	
}