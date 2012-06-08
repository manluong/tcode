<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpdesk extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('Helpdesk_CommentM');
		$this->load->model('HelpdeskM');
		$this->load->model('CardM');

		$this->load->model('Helpdesk_StatusM');
		$this->load->model('Helpdesk_GroupM');
		$this->load->model('Helpdesk_TypeM');
		$this->load->model('Helpdesk_PriorityM');
	}

	function index() {
		$content = array(
			'card_id' => $this->UserM->get_card_id(),
			'group' =>  $this->Helpdesk_GroupM->get_list(),
			'status' => $this->Helpdesk_StatusM->get_list(),
			'priority' => $this->Helpdesk_PriorityM->get_list(),
			'type' => $this->Helpdesk_TypeM->get_list(),
		);
		$this->data['content'] = $this->load->view(get_template().'/helpdesk/index',$content, TRUE);

		$this->data['app_menu'] = array(
			array(
				'url' => '/helpdesk',
				'extra' => '',
				'title' => 'List',
			),
			array(
				'url' => '#',
				'extra' => 'onclick="helpdesk_fillter('.$this->UserM->get_card_id().');"',
				'title' => 'My Cases',
			),
			array(
				'url' => '/helpdesk/add',
				'extra' => '',
				'title' => 'New',
			),
		);
		$this->_do_output();
	}
	
	function card() {
		$content = array(
			'helpdesk_card_id' => $this->uri->segment(3),
			'card_id' => $this->UserM->get_card_id(),
			'group' =>  $this->Helpdesk_GroupM->get_list(),
			'status' => $this->Helpdesk_StatusM->get_list(),
			'priority' => $this->Helpdesk_PriorityM->get_list(),
			'type' => $this->Helpdesk_TypeM->get_list(),
		);
		$this->data['content'] = $this->load->view(get_template().'/helpdesk/index',$content, TRUE);
		$this->_do_output();
	}
	
	
	function helpdesk_fillter(){
		//Set order 
		$order_by = 'created_stamp  DESC';
		$this->HelpdeskM->set_order_by($order_by);
		$where = array();	
		//Check Customer
		if($this->UserM->is_client() == TRUE){
			$card_id = $this->UserM->get_card_id();
			if(!empty($card_id)){
				$where[] = "created_card_id='$card_id'";
			}
		}

		//Fillter MY CASE of Staff
		$card_id = $this->input->post('card_id');
		if(!empty($card_id)){
			$where[] = "created_card_id='$card_id'";
		}

		$status = $this->input->post('status');
		if(!empty($status)){
			$where[] = "status='$status'";
		}

		$group = $this->input->post('group');
		if(!empty($group)){
			$where[] = "`group`='$group'";
		}

		$type = $this->input->post('type');
		if(!empty($type)){
			$where[] = "type='$type'";
		}

		$priority = $this->input->post('priority');
		if(!empty($priority)){
			$where[] = "priority='$priority'";
		}

		$this->HelpdeskM->where = $where;

		$result = $this->HelpdeskM->get_list();
		$data = json_encode($result);
		echo $data;
	}

	function helpdesk_fillter_all(){
		$where = array();

		$status = $this->input->post('status');
		if(!empty($status)){
			$where[] = "status='$status'";
		}

		$group = $this->input->post('group');
		if(!empty($group)){
			$where[] = "`group`='$group'";
		}

		$type = $this->input->post('type');
		if(!empty($type)){
			$where[] = "type='$type'";
		}

		$priority = $this->input->post('priority');
		if(!empty($priority)){
			$where[] = "priority='$priority'";
		}

		$customer = $this->input->post('customer');
		if(!empty($customer)){
			$where[] = "created_card_id='$customer'";
		}

		$assigned = $this->input->post('assigned');
		if(!empty($assigned)){
			$where[] = "assign_id='$assigned'";
		}

		$subject = $this->input->post('subject');
		if(!empty($subject)){
			$where[] = "subject LIKE '$subject%'";
		}

		$comments = $this->input->post('comments');
		if(!empty($comments)){
			$results = $this->Helpdesk_CommentM->search_comment($comments);
			$helpdesk_ids = get_distinct('helpdesk_id', $results);
			$where[] = 'id IN ('.implode(',', $helpdesk_ids).')';
		}

		$this->HelpdeskM->where = $where;

		$result = $this->HelpdeskM->get_list();

		$data = json_encode($result);
		echo $data;
	}

	function get_customer() {
		$term = $this->input->get('term');

		$this->load->model('CardM');
		$customer_list = $this->CardM->search_customer($term);

		$content = array();
		if ($customer_list) {
			foreach ($customer_list as $customer) {
				$content[] = array(
					'id' => $customer['id'],
					'label' => trim($customer['first_name'].' '.$customer['last_name']),
					'value' => trim($customer['first_name'].' '.$customer['last_name'])
				);
			}
		}
		echo json_encode($content);
	}

	function get_comment() {
		$term = $this->input->get('term');
		$comment_list = $this->Helpdesk_CommentM->search_comment($term);

		$content = array();
		if ($comment_list) {
			foreach ($comment_list as $k) {
				$content[] = array(
					'id' => $k['id'],
					'label' => $k['comment'],
					'value' =>  $k['comment'],
				);
			}
		}
		echo json_encode($content);
	}

	function get_staff() {
		$term = $this->input->get('term');

		$this->load->model('CardM');
		$customer_list = $this->Card->ajax_search_staff($term);

		$content = array();
		if ($customer_list) {
			foreach ($customer_list as $customer) {
				$content[] = array(
					'id' => $customer['id'],
					'label' => trim($customer['first_name'].' '.$customer['last_name']),
					'value' => trim($customer['first_name'].' '.$customer['last_name'])
				);
			}
		}

		echo json_encode($content);
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
			'card_id' => $this->UserM->get_card_id(),
			'comment_id' => $insert_comment_id,
			'helpdesk_id' => $helpdesk_id,
			'group' =>  $this->Helpdesk_GroupM->get_list(),
			'status' => $this->Helpdesk_StatusM->get_list(),
			'priority' => $this->Helpdesk_PriorityM->get_list(),
			'type' => $this->Helpdesk_TypeM->get_list(),
			'assign' => $this->Helpdesk_CommentM->get_assign(),
		);

		$this->data['content'] = $this->load->view(get_template().'/helpdesk/add',$content, TRUE);
		$this->_do_output();
	}

	function edit(){
		$id = $this->uri->segment(3);
		$result = $this->HelpdeskM->get($id);
		//Check Customer id
		$card_id = $this->UserM->get_card_id();
		
		if($this->UserM->is_client() == TRUE){
			if($result['created_card_id'] != $card_id){
				header("location: /helpdesk");
				die;
			}
		}
		//Delete NULL COMMENT
		$this->delete_comment();
		
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
		$where_comment[] = "active = 0";
		$where_comment[] = "helpdesk_id='$id'";
		$this->Helpdesk_CommentM->where = $where_comment;
		$comment = $this->Helpdesk_CommentM->get_list();
		
		$content = array(
			'card_id' => $card_id,
			'id' => $id,
			'comment_id' => $comment_id,
			'group' =>  $this->Helpdesk_GroupM->get_list(),
			'status' => $this->Helpdesk_StatusM->get_list(),
			'priority' => $this->Helpdesk_PriorityM->get_list(),
			'type' => $this->Helpdesk_TypeM->get_list(),
			'comment' => $comment,
			'result' => $result,
			'assign' => $this->Helpdesk_CommentM->get_assign(),
			'file_attach' => $this->Helpdesk_CommentM->get_comment_files(),
		);
		//$card = $this->CardM->get_quickjump($result['created_card_id']);
		//$content['quickjump'] = $this->load->view(get_template().'/card/quickjump', $card, TRUE);
		

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

		if($id_helpdesk!=0){
			$where_comment[] = "active = 0";
			$where_comment[] = "helpdesk_id='$id_helpdesk'";
			$this->Helpdesk_CommentM->where = $where_comment;
			$result = $this->Helpdesk_CommentM->get_list();
		}
		$data_ajax = array(
           'comment' => $this->Helpdesk_CommentM->get_list($id_helpdesk),
		   'result' => $result,
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
			'`group`' => $this->input->post('group'),
			'status' => $this->input->post('status'),
			'type' => $this->input->post('type'),
			'priority' => $this->input->post('priority'),
			'created_card_id' => $this->input->post('requester'),
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

	function resave_helpdesk_info() {
		$id = $this->input->post('id');
		$data = array(
			'id'=>$id,
			'status' => $this->input->post('status'),
			'priority' => $this->input->post('priority'),
		);
		$id_save = $this->HelpdeskM->save($data);
		return $id_save;
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
		//$this->load->library('filel');
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
