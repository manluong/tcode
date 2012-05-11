<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpdesk extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('DS_Helpdesk');
		$this->load->model('Helpdesk_CommentM');
		$this->load->model('HelpdeskM');
	}

	function index(){
		$this->HelpdeskM->offset = 0;
		$this->HelpdeskM->limit = 10;
		
		$content = array(
			'total' => $this->HelpdeskM->getTotalRecord(),
			'result' => $this->HelpdeskM->get_list(),
			'group' =>  $this->Helpdesk_CommentM->get_group(),
			'status' => $this->Helpdesk_CommentM->get_status(),
			'priority' => $this->Helpdesk_CommentM->get_priority(),
			'type' => $this->Helpdesk_CommentM->get_type(),
		);
		$this->data['content'] = $this->load->view(get_template().'/helpdesk/index',$content, TRUE);
		$this->_do_output();
	}
	
	function ajax_pagination(){
		$this->HelpdeskM->offset = $this->input->post('offset');
		$this->HelpdeskM->limit = 10;
		
		$data = array(
			'total' => $this->HelpdeskM->getTotalRecord(),
			'result' => $this->HelpdeskM->get_list(),
		);
		$this->load->view(get_template().'/helpdesk/ajax_fillter_list',$data);
	}
	
	function fillter_record(){
		$this->HelpdeskM->limit = $this->input->post('value');
		$data = array(
			'result' =>  $this->HelpdeskM->get_list(),
		);
		$this->load->view(get_template().'/helpdesk/ajax_fillter_list',$data);
	}
	
	//ajax_search
	function ajax_search(){
		$value = $this->input->post('value');
		$data = array(
			'result' => $this->HelpdeskM->search_content($value),
		);
		$this->load->view(get_template().'/helpdesk/ajax_helpdesk_list',$data);
	}
	
	//ajax_fillter
	function group_fillter(){
		$value = $this->input->post('value');
		$data = array(
			'result' => $this->HelpdeskM->group_fillter($value),
		);
		$this->load->view(get_template().'/helpdesk/ajax_helpdesk_list',$data);
	}
	
	function status_fillter(){
		$value = $this->input->post('value');
		$data = array(
			'result' => $this->HelpdeskM->status_fillter($value),
		);
		$this->load->view(get_template().'/helpdesk/ajax_helpdesk_list',$data);
	}
	
	function type_fillter(){
		$value = $this->input->post('value');
		$data = array(
			'result' => $this->HelpdeskM->type_fillter($value),
		);
		$this->load->view(get_template().'/helpdesk/ajax_helpdesk_list',$data);
	}
	
	function priority_fillter(){
		$value = $this->input->post('value');
		$data = array(
			'result' => $this->HelpdeskM->priority_fillter($value),
		);
		$this->load->view(get_template().'/helpdesk/ajax_helpdesk_list',$data);
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
		$data = array(
			'subject' => $this->input->post('subject'),
			'assign_id' => $this->input->post('assign'),
			'cc_email' => $this->input->post('cc_email'),
			'group' => $this->input->post('group'),
			'status' => $this->input->post('status'),
			'type' => $this->input->post('type'),
			'priority' => $this->input->post('priority'),
		);
		$insert_id = $this->HelpdeskM->save($data);

		if($insert_id !=''){
			echo $insert_id;
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
		$edit_id = $this->HelpdeskM->save($data);
		$content = array (
			'info' => $this->HelpdeskM->get_content($id),

		);

		$ajax_content = $this->load->view(get_template().'/helpdesk/ajax_updateInfoHelpdesk',$content ,true);
		echo $ajax_content;
	}
	
	function upload(){
	   $this->load->library('fileL');
	   $file = $this->filel->save('file', 'Helpdesk');
	   echo 'http://apple.8force.net/file/read/'.$file['hash'];
	}
	
	function uploada(){
	   $this->load->library('filel');
	   $file = $this->filel->save('file', 'Helpdesk');
	   
	   $data = array(
			'subject' => '',
			'assign_id' => '',
			'cc_email' => '',
			'group' => '',
			'status' => '',
			'type' => '',
			'priority' => '',
		);
		
	   $helpdesk_id = $this->HelpdeskM->save($data);
	   $insert_id = $this->HelpdeskM->insert_upload_file($file['hash'],$helpdesk_id);
		echo $insert_id ;
	}
	
	function delete($hash){
		$this->filel->delete($hash);
	}
	
}
