<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phone extends MY_Controller {

        function __construct() {
                parent::__construct();
                
                $this->load->model('PhoneM');
        }

        function index() {
                $content = 'phone list';

                $this->data['app_menu'] = array(
                    array('url' => '/phone','extra' => '', 'title' => 'List'),
                    array('url' => '/phone/call_log', 'extra' => '', 'title' => 'Call Log'),
                    array('url' => '/phone/send_fax', 'extra' => '', 'title' => 'Send Fax'),
                    array('url' => '/phone/make_call', 'extra' => '', 'title' => 'Make Call'),
                    array('url' => '/phone/settings', 'extra' => '', 'title' => 'Settings')
                );
                $this->data['breadcrumb'] = array(array('title' => 'List'));
                $this->data['content'] = $this->load->view(get_template() . '/phone/index', $content, TRUE);
                $this->_do_output();
        }

        function search(){
            $search_param = array(
                'date_range' => $this->input->post('date_range'),
		'date_range_from' => $this->format_date($this->input->post('date_range_from')),
		'date_range_to' => $this->format_date($this->input->post('date_range_to')),
                'inout' => $this->input->post('inout'),    
                'calltype' => $this->input->post('calltype'),
            );

            $this->RespM->set_success(TRUE)
			->set_details($this->PhoneM->search($search_param))
			->output_json();
        }
        
        private function format_date($date) {
		if (empty($date)) {
			return '';
		} else {
			return date('Y-m-d', strtotime($date));
		}
	}
}