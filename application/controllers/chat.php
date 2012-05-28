<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Chat extends MY_Controller {
        function __construct() {
		$this->load->model('chatm');

		parent::__construct();
	}
        function forward(){
            $this->ChatM->forward();
        }
    }

?>
