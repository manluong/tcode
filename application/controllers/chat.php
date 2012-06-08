<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Chat extends MY_Controller {
        function __construct() {
		parent::__construct();
                $this->load->model('ChatM');
	}
        function forward(){
           $this->ChatM->forward();
        }
        function index(){
            $this->ChatM->add_user();
        }
    }

?>
