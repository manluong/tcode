<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpdesk extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('Helpdesk_CommentM');
		$this->load->model('HelpdeskM');
		$this->load->model('CardM');
	}

	function index() {
		$content = array(
			'total' => $this->HelpdeskM->get_total_records(),
			'result' => json_encode($this->HelpdeskM->get_list()),
			'group' =>  $this->Helpdesk_CommentM->get_group(),
			'status' => $this->Helpdesk_CommentM->get_status(),
			'priority' => $this->Helpdesk_CommentM->get_priority(),
			'type' => $this->Helpdesk_CommentM->get_type(),
		);
		$this->data['content'] = $this->load->view(get_template().'/helpdesk/index',$content, TRUE);
		$this->_do_output();
	}
	
	function ajax_status_fillter(){
		$value = $this->input->post('value');
		$where = array(
			'status' => $value,
		);
		$this->HelpdeskM->set_where($where);
		
		$result = $this->HelpdeskM->get_list();
		$data = json_encode($result);
		echo $data;
	}
	
	function ajax_group_fillter(){
		$value = $this->input->post('value');
		$where = array(
			'group' => $value,
		);
		$this->HelpdeskM->set_where($where);
		
		$result = $this->HelpdeskM->get_list();
		$data = json_encode($result);
		echo $data;
	}
	
	function ajax_type_fillter(){
		$value = $this->input->post('value');
		$where = array(
			'type' => $value,
		);
		$this->HelpdeskM->set_where($where);
		
		$result = $this->HelpdeskM->get_list();
		$data = json_encode($result);
		echo $data;
	}
	
	function ajax_priority_fillter(){
		$value = $this->input->post('value');
		$where = array(
			'priority' => $value,
		);
		$this->HelpdeskM->set_where($where);
		
		$result = $this->HelpdeskM->get_list();
		$data = json_encode($result);
		echo $data;
	}
	
	function add() {
		//Delete NULL HELPDESK
		$this->delete_helpdesk();

		//Delete NULL COMMENT
		$this->delete_comment();

		//Create NULL HELPDESK
		$helpdesk_data = array(
			'subject' => '',
			'assign_id' => '',
			'cc_email' => '',
			'group' => '',
			'status' => '',
			'type' => '',
			'priority' => '',
			'active' => 1,
		);

	   $helpdesk_id = $this->HelpdeskM->save($helpdesk_data);
	   //Create NULL COMMENT
	   $comment_data = array (
			'group' => '',
			'status' => '',
			'type' => '',
			'priority' => '',
			'comment' => '',
			'private' => '',
			'helpdesk_id' => $helpdesk_id ,
            'active' => 1,
		);
		$insert_comment_id = $this->Helpdesk_CommentM->save($comment_data);

		//get data for add new helpdesk
		$content = array(
			'comment_id' => $insert_comment_id,
			'helpdesk_id' => $helpdesk_id,
			'group' =>  $this->Helpdesk_CommentM->get_group(),
			'status' => $this->Helpdesk_CommentM->get_status(),
			'priority' => $this->Helpdesk_CommentM->get_priority(),
			'type' => $this->Helpdesk_CommentM->get_type(),
			'assign' => $this->Helpdesk_CommentM->get_assign(),
		);

		$this->data['content'] = $this->load->view(get_template().'/helpdesk/add',$content, TRUE);
		$this->_do_output();
	}

	function edit(){
		//Delete NULL COMMENT
		//$this->delete_comment() ;
		
		$id = $this->uri->segment(3);	
		//Create NULL COMMENT for upload attach file
	   $comment_data = array (
			'group' => '',
			'status' => '',
			'type' => '',
			'priority' => '',
			'comment' => '',
			'private' => '',
			'helpdesk_id' => $id ,
            'active' => 1,
		);
		$comment_id = $this->Helpdesk_CommentM->save($comment_data);
			
		$result[0] = array();
		if($id!=0){
			$result = $this->Helpdesk_CommentM->get_content_helpdesk($id);
		}
		
		$content = array(
			'id' => $id,
			'comment_id' => $comment_id,
			'group' =>  $this->Helpdesk_CommentM->get_group(),
			'status' => $this->Helpdesk_CommentM->get_status(),
			'priority' => $this->Helpdesk_CommentM->get_priority(),
			'type' => $this->Helpdesk_CommentM->get_type(),
			'comment' => $this->Helpdesk_CommentM->get_content($id),
			'result' => $this->HelpdeskM->get($id),
			'assign' => $this->Helpdesk_CommentM->get_assign(),
			'file_attach' => $this->Helpdesk_CommentM->get_comment_files($id),
		);

		$this->data['content'] = $this->load->view(get_template().'/helpdesk/edit',$content, TRUE);
		$this->_do_output();
	}
	
	function out_put_pdf($id) {
		$result[0] = array();
		if($id!=0){
			$result = $this->Helpdesk_CommentM->get_content_helpdesk($id);
		}
		$content = array(
			'id' => $id,
			'group' =>  $this->Helpdesk_CommentM->get_group(),
			'status' => $this->Helpdesk_CommentM->get_status(),
			'priority' => $this->Helpdesk_CommentM->get_priority(),
			'type' => $this->Helpdesk_CommentM->get_type(),
			'comment' => $this->Helpdesk_CommentM->get_content($id),
			'result' => $result[0],
			'assign' => $this->Helpdesk_CommentM->get_assign(),
			'file_attach' => $this->HelpdeskM->get_helpdesk_files($id),
		);

		$html = $this->load->view(get_template().'/helpdesk/pdf_comment',$content, TRUE);
		output_pdf($html);
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

	function save_comment(){
		$id_helpdesk = $this->input->post('id');
		$data = array(
            'id' => $this->input->post('id_comment'),
			'group' => $this->input->post('group'),
			'status' => $this->input->post('status'),
			'type' => $this->input->post('type'),
			'priority' => $this->input->post('priority'),
			'comment' => $this->input->post('comment'),
			'private' => $this->input->post('pri'),
			'helpdesk_id' => $id_helpdesk ,
            'active' => 0,
		);
		$this->Helpdesk_CommentM->save($data);
		
		$result[0] = array();
		if($id_helpdesk!=0){
			$result = $this->Helpdesk_CommentM->get_content_helpdesk($id_helpdesk);
		}
		$data_ajax = array(
           'comment' => $this->Helpdesk_CommentM->get_content($id_helpdesk),
		   'result' => $result[0],
		);

		$ajax_content = $this->load->view(get_template().'/helpdesk/ajax_updateComment',$data_ajax ,true);
		echo $ajax_content  ;
	}


	function save_insert_helpdesk(){
		$data = array(
			'id' => $this->input->post('id'),
			'subject' => $this->input->post('subject'),
			'assign_id' => $this->input->post('assign'),
			'cc_email' => $this->input->post('cc_email'),
			'group' => $this->input->post('group'),
			'status' => $this->input->post('status'),
			'type' => $this->input->post('type'),
			'priority' => $this->input->post('priority'),
			'active' => 0,
		);
		$insert_id = $this->HelpdeskM->save($data);
		echo $insert_id;
	}

	function ajaxChangeInfoHelpDesk() {
		$id = $this->input->post('id');
		$data = array(
			'id'=>$id,
			'subject' => $this->input->post('subject'),
			'assign_id' => $this->input->post('assign'),
			'cc_email' => $this->input->post('cc_email'),
		);
		$this->HelpdeskM->save($data);
		
		$content = array (
			'info' => $this->HelpdeskM->get_content($id),
		);

		$ajax_content = $this->load->view(get_template().'/helpdesk/ajax_updateInfoHelpdesk',$content ,true);
		echo $ajax_content;
	}

	function upload($comment_id){
	   $this->load->library('filel');
	   $file = $this->filel->save('file', 'Helpdesk');
		if($comment_id != 0){
		   $insert_id = $this->HelpdeskM->insert_upload_file($file['hash'],$comment_id);
		   echo $file['hash'];
		}
	}

	function delete_helpdesk() {
		$result = $this->HelpdeskM->get_helpdesk_not_use();
		if (!empty($result)) {
			foreach($result as $k) {
				$this->HelpdeskM->delete($k->id,TRUE);
			}
		}
	}

	function delete_comment() {
		$this->load->library('filel');
		$result = $this->Helpdesk_CommentM->get_comment_not_use();
		if (!empty($result)) {
			foreach ($result as $k) {
				$this->Helpdesk_CommentM->delete($k->id,TRUE);
				$file = $this->Helpdesk_CommentM->get_comment_files($k->id);
				if (!empty($file)) {
					foreach ($file as $v) {
						$this->filel->delete($v->filename);
						$this->Helpdesk_CommentM->delete_files_not_use($v->id);
					}
				}
			}
		}
	}

}
