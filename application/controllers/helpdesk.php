<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpdesk extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('DS_Helpdesk');
		$this->load->model('DS_Comment');
	}

	function index() {
		$this->data['content'] = $this->load->view(get_template().'/helpdesk/index', '', TRUE);

		$this->_do_output();
	}

	function sendhtml() {
		$data = array();
		$data['html'] = "I am a some HTML";
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;
		echo $data['html'];
		return($data);
	}

	function sendjson() {
		$data = array();
		$data['json'] = '{"element_type":"helloworld","message":"Hello World!"}';
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);
	}

	function sendjs() {
		$data = array();
		$data['json'] = '//js

		';//alert("Hello JS");
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);
	}

	function sendjson_list() {
		$this->DS_Helpdesk->set_subaction('l');

		$details = array(
			'columns' => $this->DS_Helpdesk->get_datatable_fields(),
			'data' => $this->DS_Helpdesk->get_datatable_data(),
			'ids' => $this->DS_Helpdesk->get_list_ids(),
			'setting' => array(
				'hidetitle' => 0,
			),
		);

		$this->RespM->set_message('sendjson_list')
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('HelpDesk List')
				->set_details($details)
				->output_json();
	}

	function sendjson_view() {
		$data = $this->DS_Helpdesk->set_subaction('v')
					->set_id(2)
					->get_view_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Edit',
					'type' => 'ajax',
					'url' => '/helpdesk/sendjson_form/2/as',
					'style' => 'default',
					'icon' => '',
				),
				array(
					'target' => '',
					'text' => 'Cancel',
					'type' => 'ajax',
					'url' => '/helpdesk/contact/1/v',
					'style' => 'warning',
					'icon' => 'trash',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			),
		);

		$this->RespM->set_message('sendjson_view')
				->set_type('view')
				->set_template('')
				//->set_template('custom_viewcard')//custom template
				->set_success(true)
				->set_title('HelpDesk View')
				->set_details($details)
				->output_json();
	}
	
	function sendjson_insert_form() {
		$data = $this->DS_Helpdesk->set_subaction('a')
					 ->get_form_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Submit',
					'type' => 'submit',
					'url' => '/helpdesk/helpdesk_insert/a',
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			)
		);

		$this->RespM->set_message('sendjson_form')
				->set_type('form')
				->set_template('')
				//->set_template('custom_editcard')//custom template
				->set_success(true)
				->set_title('HelpDesk Insert')
				->set_details($details)
				->output_json();
	}
	
	function sendjson_form($id) {
		$data = $this->DS_Helpdesk->set_subaction('e')
					 ->set_id(1)
					 ->get_form_data();
		
		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Submit',
					'type' => 'submit',
					'url' => '/helpdesk/sendjson_save/'.$id.'/es',
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			)
		);

		$this->RespM->set_message('sendjson_form')
				->set_type('form')
				->set_template('')
				//->set_template('custom_editcard')//custom template
				->set_success(true)
				->set_title('HelpDesk Edit')
				->set_details($details)
				->output_json();
	}

	function sendjson_comment_form() {

		$data = array(
			'id' => $this->input->post('id'),
			'group' =>  $this->DS_Comment->getGroup(),
			'status' => $this->DS_Comment->getStatus(),
			'priority' => $this->DS_Comment->getPriority(),
			'type' => $this->DS_Comment->getType(),
			'comment' => $this->DS_Comment->getContent($this->input->post('id')),
		);	
		
		/*
		echo '<pre>';
		print_r($content);
		echo '</pre>';
		exit;
		*/
		$content = $this->load->view(get_template().'/helpdesk/comment',$data ,true);
		echo $content;
			
	}
	
	function save_comment(){
		$id_helpdesk = $this->input->post('id');
		$data = array(
			'group' => $this->input->post('group'),
			'status' => $this->input->post('status'),
			'type' => $this->input->post('type'),
			'priority' => $this->input->post('priority'),
			'comment' => $this->input->post('comment'),
			'private' => $this->input->post('pri'),
			'helpdesk_id' => $id_helpdesk ,
		);
		$insert_id = $this->DS_Comment->save($data);
		
		$data_ajax['comment'] = $this->DS_Comment->getContent($id_helpdesk);
		$ajax_content = $this->load->view(get_template().'/helpdesk/ajax_updateComment',$data_ajax ,true);
		echo $ajax_content;
	}
	
	function helpdesk_insert() {

		$this->DS_Helpdesk->subaction = 'a';
		//$this->DS_Helpdesk->id = 1;
		$success = $this->DS_Helpdesk->save();

		if ($success) {
			$details['links'] = array(
				array(
				'type' => 'ajax',
				'url' => '/helpdesk/sendjson_list',
				'target' => '',
				'text' => ''
				)
			);
			$message = 'Data saved.';
		} else {
			$details['data'] = $this->DS_Helpdesk->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Save Hello Dataset')
				->set_details($details)
				->output_json();
	}
	
	function comment_insert() {

		$this->DS_Comment->subaction = 'a';
		//$this->DS_Helpdesk->id = 1;
		$success = $this->DS_Comment->save();

		if ($success) {
			$details['links'] = array(
				array(
				'type' => 'ajax',
				'url' => '/helpdesk/sendjson_list',
				'target' => '',
				'text' => ''
				)
			);
			$message = 'Data saved.';
		} else {
			$details['data'] = $this->DS_Comment->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Save Hello Dataset')
				->set_details($details)
				->output_json();
	}
	
	/*
	function helpdesk_insert() {
		$data = $this->DS_Helpdesk->set_subaction('a')
					 ->get_form_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Submit',
					'type' => 'submit',
					'url' => '/helpdesk/helpdesk_insert/a',
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			)
		);

		$this->RespM->set_message('sendjson_form')
				->set_type('form')
				->set_template('')
				//->set_template('custom_editcard')//custom template
				->set_success(true)
				->set_title('HelpDesk Insert')
				->set_details($details)
				->output_json();
	}
	*/
	
	function sendjson_save($id) {

		$this->DS_Helpdesk->subaction = 'e';
		$this->DS_Helpdesk->id = $id;
		$success = $this->DS_Helpdesk->save();

		if ($success) {
			$details['links'] = array(
				array(
				'type' => 'ajax',
				'url' => '/helpdesk/sendjson_list',
				'target' => '',
				'text' => ''
				)
			);
			$message = 'Data saved.';
		} else {
			$details['data'] = $this->DS_Helpdesk->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Save Hello Dataset')
				->set_details($details)
				->output_json();
	}

	function show_data() {
		$this->DS_Helpdesk->subaction = 'e';
		$this->DS_Helpdesk->id = 150;

		$data = $this->DS_Helpdesk->get_form_data();

		echo '<pre>', print_r($data, TRUE), '</pre>';
	}

	function sendjson_sample(){

		//sample json respond
		//for testing

		$data = array();
		$links = ',"links":[{"type":"submit","url":"/helloworld/returnjson_save/1/es","target":"","text":"Submit"},{"type":"ajax","url":"/helloworld/contact/1/v","target":"","text":"Cancel","style":"","icon":""}]';

		//for list
		$data['json'] = '{"success":"1","message":"1","template":"1","title":"Title","type":"view","details":{"setting":{"hidelabel":"0"}'.$links.',"data":[{"fieldname":"firstname","label":"First Name","value":"Anthony"},{"fieldname":"lastname","label":"Last Name","value":"Andy"}]}}';

		//for view
		$data['json'] = '{"success":"1","message":"1","template":"1","title":"Title","type":"list","details":{"columns":[{"sTitle":null},{"sTitle":null},{"sTitle":null},{"sTitle":null},{"sTitle":null},{"sTitle":""}],"data":[["3","Willson","W.","Willson","Intern7","<div class=\"ar bu-div\"><button type=\"button\" class=\"btn\" onclick=\"ajax_content(\'\/staff\/viewstaff\/nttpw%3D%3D\/v\',\'page\');\"><\/button><\/div>"],["4","John","J.","Jo","","<div class=\"ar bu-div\"><button type=\"button\" class=\"btn\" onclick=\"apps_action_pageload(\'\/staff\/viewstaff\/nttqA%3D%3D\/v\');\"><\/button><\/div>"]],"setting":{"hidetitle":"0"}'.$links.'}}';

		$data['isoutput'] = 1;
		$data['isdiv'] = 0;
		return($data);

	}

	function test_url() {
		echo '<pre>', print_r($this->url, TRUE), '</pre>';
	}
	
	function comment_view(){
		$this->load->view(get_template().'/helpdesk/comment');
	}

}
