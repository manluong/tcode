<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpdesk extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('DS_Helpdesk');
		$this->load->model('Helpdesk_CommentM');
		$this->load->model('Helpdesk_NodatasetM');
	}

	function index(){
		$content = array(
			'result' => $this->Helpdesk_NodatasetM->get_list(),
		);
		$this->data['content'] = $this->load->view(get_template().'/helpdesk/index',$content, TRUE);
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
		$id = $this->input->post('id');
		$result[0] = array();
		if($id!=0){
			$result = $this->Helpdesk_CommentM->get_content_helpdesk($id);

		}
		$data = array(
			'id' => $id,
			'group' =>  $this->Helpdesk_CommentM->get_group(),
			'status' => $this->Helpdesk_CommentM->get_status(),
			'priority' => $this->Helpdesk_CommentM->get_priority(),
			'type' => $this->Helpdesk_CommentM->get_type(),
			'comment' => $this->Helpdesk_CommentM->get_content($id),
			'result' => $result[0],
			'assign' => $this->Helpdesk_CommentM->get_assign(),

		);	
		$content = $this->load->view(get_template().'/helpdesk/comment',$data ,true);
		echo $content;
	}
	
	function insert_helpdesk_form() {
		$data = array(
			'group' =>  $this->Helpdesk_CommentM->get_group(),
			'status' => $this->Helpdesk_CommentM->get_status(),
			'priority' => $this->Helpdesk_CommentM->get_priority(),
			'type' => $this->Helpdesk_CommentM->get_type(),
			'assign' => $this->Helpdesk_CommentM->get_assign(),
		);	
		$content = $this->load->view(get_template().'/helpdesk/helpdesk_insert',$data ,true);
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
			'created_stamp' => date('Y-m-d H:i:s',time()),

		);
		$insert_id = $this->Helpdesk_CommentM->save($data);

		
		$data_ajax['comment'] = $this->Helpdesk_CommentM->get_content($id_helpdesk);

		$ajax_content = $this->load->view(get_template().'/helpdesk/ajax_updateComment',$data_ajax ,true);
		echo $ajax_content;
	}
	
	function save_insert_helpdesk(){
		$this->CI->load->library('FileL');
		$this->filel->save('attach_file', 'Helpdesk');
		$data = array(
			'subject' => $this->input->post('subject'),
			'assign_id' => $this->input->post('assign'),
			'cc_email' => $this->input->post('cc_email'),
			'group' => $this->input->post('group'),
			'status' => $this->input->post('status'),
			'type' => $this->input->post('type'),
			'priority' => $this->input->post('priority'),
		);
		$insert_id = $this->Helpdesk_NodatasetM->save($data);

		if($insert_id !=''){
			echo $insert_id;
			//Header("Location: /helpdesk/index");
		}
	}
	
	function ajaxChangeInfoHelpDesk(){
		$id = $this->input->post('id');
		$data = array(
			'id'=>$id,
			'subject' => $this->input->post('subject'),
			'assign_id' => $this->input->post('assign'),
			'cc_email' => $this->input->post('cc_email'),
		);
		$edit_id = $this->Helpdesk_NodatasetM->save($data);
		$content = array (
			'info' => $this->Helpdesk_NodatasetM->get_content($id),

		);

		$ajax_content = $this->load->view(get_template().'/helpdesk/ajax_updateInfoHelpdesk',$content ,true);
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
