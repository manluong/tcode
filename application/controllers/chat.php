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
            //$this->ChatM->add_user('','apple','');
        }
        function get(){
            $domain = explode('.',$_SERVER['HTTP_HOST']);
            $domain = $domain[0];
            echo $this->UserM->get_id().'|'.$domain;
            
            
        }
    }

?>
