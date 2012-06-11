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
           echo 'chat screen';
            
        }
        function get(){
            $domain = explode('.',$_SERVER['HTTP_HOST']);
            $domain = $domain[0];
            $data['domain'] = $domain;
            $data['id'] = $this->UserM->get_id();
            echo json_encode($data);
            
            
        }
    }

?>
