<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CardM');
		$this->load->model('Card_AddressM');
		$this->load->model('Card_EmailM');
		$this->load->model('Card_SocialM');
		$this->load->model('Card_TelM');
		$this->load->model('InvoiceM');
		$this->load->model('HelpdeskM');
	}

	function index(){
		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$this->CardM->sett_fill_roles = TRUE;

		$this->CardM->order_by[] = 'first_name ASC';
		$view_data = array(
			'list' => $this->CardM->get_list(),
		);

		$this->data['content'] = $this->load->view(get_template().'/card/index',$view_data, TRUE);
		$this->_do_output();
	}

	function upload_crop(){
		$this->load->view(get_template().'/card/upload_crop');
	}

	function contact_list_json() {
		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$this->CardM->sett_fill_roles = TRUE;

		$this->CardM->order_by[] = 'first_name ASC';
		$list = $this->CardM->get_list();
		echo json_encode($list);
	}

	function upload($comment_id){
	   $this->load->library('filel');
	   $file = $this->filel->save('file', 'Helpdesk');
		if($comment_id != 0){
		   $insert_id = $this->HelpdeskM->insert_upload_file($file['hash'],$comment_id);
		   echo $file['hash'];
		}
	}

	function ajax_contact_info(){
		$card_id = $this->input->post('id');
		$view_data = array(
			'detail' => $this->CardM->get($card_id),
		);
		$this->load->view(get_template().'/card/ajax_contact_info',$view_data);
	}
	
	function ajax_change_status(){
		$id = $this->input->post('id');
		$this->load->model('aclM');
		$role = $this->AclM->get_user_role_info($id);
		$data = array (
			'id' => $id ,
            'active' => $this->input->post('active'),
		);
		
		$save_id = $this->CardM->save($data);
		$view_data['role'] = $role['name'];
		$view_data['data'] = $this->CardM->get($save_id);
		$this->load->view(get_template().'/card/ajax_status',$view_data);
	}
	
	function ajax_change_pass(){
		$this->load->model('Access_UserM');
		$date = $this->input->post('expiry_date');
		$date = split('/',$date);
		$data = array (
			'id' => $this->input->post('id'),
            'password' => md5($this->input->post('pass')),
			'expire_stamp' => $date[2].'-'.$date[1].'-'.$date[0].' 00:00:00',
		);
		$this->Access_UserM->save($data);
		$view_data['data'] = $this->Access_UserM->get($this->input->post('id'));
		$this->load->view(get_template().'/card/ajax_change_pass',$view_data);
	}
	
	function contact_fillter(){
		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$this->CardM->sett_fill_roles = TRUE;

		$role_id = $this->input->post('role_id');
		if (is_array($role_id)) {
			$this->Card_RoleM->set_where('role_id IN ('.implode(',', $role_id).')');
		} else if ($role_id == -1) {
			$this->Card_RoleM->set_where('role_id > 4');
		} else if ($role_id != 0) {
			$this->Card_RoleM->set_where('role_id = '.$role_id);
		}

		$result = $this->Card_RoleM->get_list();

		$list_id = '';
		for($i = 0 ; $i < count($result) ; $i++){
			if($i == 0 ){
				$list_id = $result[$i]['card_id'];
			}
			$list_id .= ','.$result[$i]['card_id'];
		}
		$this->db->order_by('first_name ASC');
		$list_id = explode(',',$list_id);
		$list = $this->CardM->get_batch($list_id);

		$view_data = array(
			'list' => $list,
		);
		$this->load->view(get_template().'/card/ajax_contact_list',$view_data);
	}

	function view($id) {
		$this->load->model('aclM');
		$role = $this->AclM->get_user_role_info($id);
		
		$view_data = array(
			'user_role' => $role,
			'title' => 'Contact View',
			'data' => $this->CardM->get($id),
			'card_email' => $this->CardM->get_card_email($id),
			'card_social' => $this->CardM->get_card_social($id),
			'card_phone' => $this->CardM->get_card_phone($id),
			'card_address' => $this->CardM->get_card_address($id),
		);
		//$result = $this->CardM->get($id);
		//echo '<pre>';
		//print_r($result);
		//echo '</pre>';
		//die;
		$where = array();
		$where[] = "created_card_id='$id'";
		$this->HelpdeskM->where = $where;

		$view_data['invoice_summary'] = $this->InvoiceM->get_invoice_summary($id);
		$view_data['helpdesk_summary'] = $this->HelpdeskM->get_list($id);
		$view_data['role'] = array(
			0 => 'None',
			1 => 'Staff',
			2 => 'Customer',
			4 => 'Vendor',
		);
		$this->data['content'] = $this->load->view(get_template().'/card/view', $view_data, TRUE);
		
		$this->data['breadcrumb'][] = array(
			'title' => $view_data['data']['first_name'],
			'url' => '/card/view/'.$id,
		);
		$this->data['breadcrumb'][] = array(
			'title' => 'Contact Information',
			'url' => '',
		);

		$this->_do_output();
	}

	function view_json($id){
		$view_data = array(
			'data' => $this->CardM->get($id),
			'card_email' => $this->CardM->get_card_email($id),
			'card_social' => $this->CardM->get_card_social($id),
			'card_phone' => $this->CardM->get_card_phone($id),
			'card_address' => $this->CardM->get_card_address($id),
		);
		echo json_encode($view_data);
	}
	
	function save_role(){
		$this->load->model('Card_roleM');
		$data = array(
			'card_id' => $this->input->post('id'),
			'role_id' => $this->input->post('role'),
		);
		$id_save = $this->Card_roleM->save($data);
		echo $id_save;
	}
	
	function add($id) {
		//if (!$this->AclM->check('card', $id, 'edit')) die('you cannot edit this data');

		$view_data['title_option'] = array(
			0 => '',
			1 => 'Mr.',
			2 => 'Miss.',
			3 => 'Mrs.',
			4 => 'Dr.',
		);
		$view_data['gender'] = array(
			0 => 'Female',
			1 => 'Male'
		);
		$view_data['role'] = array(
			0 => 'None',
			1 => 'Staff',
			2 => 'Customer',
			4 => 'Vendor',
		);
		$view_data['title'] = $data_fields['title'];
		$view_data['is_new'] = TRUE;
		$view_data['countries'] = $this->Card_AddressM->get_country_list();

		$view_data['tel_label'] = $this->Card_TelM->get_label('number');
		$view_data['tel_type_options'] = $this->Card_TelM->get_options('type');

		$view_data['email_label'] = $this->Card_EmailM->get_label('email');
		$view_data['email_type_options'] = $this->Card_EmailM->get_options('type');

		$view_data['address_label'] = 'Address';
		$view_data['address_type_options'] = $this->Card_AddressM->get_options('type');

		$view_data['social_label'] = 'Social';
		$view_data['social_type_options'] = $this->Card_SocialM->get_options('type');
		$this->data['content'] = $this->load->view(get_template().'/card/add', $view_data, TRUE);
		$this->_do_output();
	}

	function edit($id) {
		//if (!$this->AclM->check('card', $id, 'edit')) die('you cannot edit this data');

		$view_data['data'] = $this->CardM->get($id);
		$data = $this->CardM->get($id);
		$view_data['role'] = array(
			0 => 'None',
			1 => 'Staff',
			2 => 'Customer',
			4 => 'Vendor',
		);
		
		$view_data['is_new'] = FALSE;
		$view_data['countries'] = $this->Card_AddressM->get_country_list();

		$view_data['tel_label'] = $this->Card_TelM->get_label('number');
		$view_data['tel_type_options'] = $this->Card_TelM->get_options('type');

		$view_data['email_label'] = $this->Card_EmailM->get_label('email');
		$view_data['email_type_options'] = $this->Card_EmailM->get_options('type');

		$view_data['address_label'] = 'Address';
		$view_data['address_type_options'] = $this->Card_AddressM->get_options('type');

		$view_data['social_label'] = 'Social';
		$view_data['social_type_options'] = $this->Card_SocialM->get_options('type');

		$this->load->view(get_template().'/card/edit', $view_data);
		//$this->data['content'] = $this->load->view(get_template().'/card/edit', $view_data, TRUE);
		//$this->_do_output();
	}

	function save() {
		$id = $this->CardM->save();
		if ($id == FALSE) {
			echo 'error saving.';
			echo '<pre>', print_r($this->CardM->errors, TRUE), '</pre>';
		} else {
			redirect('/card/view/'.$id);
		}
	}

	function confirm_delete($card_id) {
		//$staff_id = $this->UserM->get_card_id();
		$per = $this->AclM->check('card',0,'delete');
		$data = array(
			'per' => $per,
			'card_id' => $card_id,
		);
		$this->load->view(get_template().'/card/confirm_delete', $data);
	}

	function delete($card_id){
		$per_helpdesk = $this->AclM->check('helpdesk',0,'delete');
		$per_invoice = $this->AclM->check('invoice',0,'delete');
		if($per_helpdesk == TRUE && $per_invoice == TRUE){
			$this->CardM->delete($card_id);
			$per = TRUE;
		}else{
			$per = FALSE;
		}
		$data['per'] = $per;
		$this->load->view(get_template().'/card/delete', $data);
	}

	function ajax_get_list() {
		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');

		$this->CardM->limit = $limit;
		$this->CardM->offset = $offset;

		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$list = $this->CardM->get_list();

		$this->RespM->set_message()
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Contacts List')
				->set_details($list)
				->output_json();
	}

	function ajax_get() {
		$id = $this->input->post('id');
		$result = $this->CardM->get($id);

		$this->RespM->set_message('')
			->set_type('')
			->set_template('')
			->set_success(TRUE)
			->set_title('Contact Info')
			->set_details($result)
			->output_json();
	}

	function ajax_save() {
		/*
		//test code for client-side validation
		$errors = array(
			'first_name' => 'test error first name'
		);
		$this->RespM->set_success(FALSE)
				->set_details($errors)
				->output_json();
		return;
		*/
            
            
                /*
                 * Leo Fix
                 */
                 $date = explode('/',$_POST['addon_extra'][0]['birth_date']);
                 $date = $date[2].'-'.$date[1].'-'.$date[0];
                 $_POST['addon_extra'][0]['birth_date'] = $date;
                 /*
                  * End Fix
                  */
		$id = $this->CardM->save();

		$success = ($id !== FALSE);

		$message = '';
		$details = '/card/view/'.$id;
		if (!$success) {
			$message = $this->CardM->get_error_string();

			$errors = array();
			foreach($this->CardM->field_errors AS $field => $message) {
				$errors[$field] = implode('<br />', $message);
			}
			$details = $errors;
		}

		$this->RespM->set_message($message)
				->set_success($success)
				->set_details($details)
				->output_json();
	}

	function ajax_search_staff() {
		$search_string = $this->input->get_post('search_string');
		$list = $this->CardM->search_staff($search_string);

		$this->RespM->set_success(TRUE)
				->set_details($list)
				->output_json();
	}

	function ajax_auto_staff() {
		$term = $this->input->get('term');
		$list = $this->CardM->search_staff($term);

		$data = array();
		if (count($list)) {
			foreach ($list as $v) {
				if (strlen($v['display_name'])) {
					$name = $v['display_name'];
				} else {
					$name = $v['first_name'].' '.$v['last_name'];
				}

				$data[] = array(
					'id' => $v['id'],
					'label' => $name,
					'value' => $name
				);
			}
		}

		echo json_encode($data);
		exit;
	}

	function ajax_auto_customer() {
		$term = $this->input->get('term');
		$list = $this->CardM->search_customer($term);

		$data = array();
		if (count($list)) {
			foreach ($list as $v) {
				if (strlen($v['display_name'])) {
					$name = $v['display_name'];
				} else {
					$name = $v['first_name'].' '.$v['last_name'];
				}

				$data[] = array(
					'id' => $v['id'],
					'label' => $name,
					'value' => $name
				);
			}
		}

		echo json_encode($data);
		exit;
	}
=======
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CardM');
		$this->load->model('Card_AddressM');
		$this->load->model('Card_EmailM');
		$this->load->model('Card_SocialM');
		$this->load->model('Card_TelM');
		$this->load->model('InvoiceM');
		$this->load->model('HelpdeskM');
	}

	function index(){
		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$this->CardM->sett_fill_roles = TRUE;

		$this->CardM->order_by[] = 'first_name ASC';
		$view_data = array(
			'list' => $this->CardM->get_list(),
		);

		$this->data['content'] = $this->load->view(get_template().'/card/index',$view_data, TRUE);
		$this->_do_output();
	}

	function upload_crop(){
		$this->load->view(get_template().'/card/upload_crop');
	}

	function contact_list_json() {
		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$this->CardM->sett_fill_roles = TRUE;

		$this->CardM->order_by[] = 'first_name ASC';
		$list = $this->CardM->get_list();
		echo json_encode($list);
	}

	function upload($comment_id){
	   $this->load->library('filel');
	   $file = $this->filel->save('file', 'Helpdesk');
		if($comment_id != 0){
		   $insert_id = $this->HelpdeskM->insert_upload_file($file['hash'],$comment_id);
		   echo $file['hash'];
		}
	}

	function ajax_contact_info(){
		$card_id = $this->input->post('id');
		$view_data = array(
			'detail' => $this->CardM->get($card_id),
		);
		$this->load->view(get_template().'/card/ajax_contact_info',$view_data);
	}
	
	function ajax_change_status(){
		$id = $this->input->post('id');
		$this->load->model('aclM');
		$role = $this->AclM->get_user_role_info($id);
		$data = array (
			'id' => $id ,
            'active' => $this->input->post('active'),
		);
		
		$save_id = $this->CardM->save($data);
		$view_data['role'] = $role['name'];
		$view_data['data'] = $this->CardM->get($save_id);
		$this->load->view(get_template().'/card/ajax_status',$view_data);
	}
	
	function ajax_change_pass(){
		$this->load->model('Access_UserM');
		
		$data = array (
			'id' => $this->input->post('id'),
            'password' => md5($this->input->post('pass')),
			'expire_stamp' => $this->input->post('expiry_date') ,
		);
		$id_save = $this->Access_UserM->save($data);
		echo $id_save;
		//$view_data['data'] = $this->Access_userM->get($this->input->post('id'));
		//$data = $this->Access_userM->get($this->input->post('id'));
		
	}
	
	function contact_fillter(){
		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$this->CardM->sett_fill_roles = TRUE;

		$role_id = $this->input->post('role_id');
		if (is_array($role_id)) {
			$this->Card_RoleM->set_where('role_id IN ('.implode(',', $role_id).')');
		} else if ($role_id == -1) {
			$this->Card_RoleM->set_where('role_id > 4');
		} else if ($role_id != 0) {
			$this->Card_RoleM->set_where('role_id = '.$role_id);
		}

		$result = $this->Card_RoleM->get_list();

		$list_id = '';
		for($i = 0 ; $i < count($result) ; $i++){
			if($i == 0 ){
				$list_id = $result[$i]['card_id'];
			}
			$list_id .= ','.$result[$i]['card_id'];
		}
		$this->db->order_by('first_name ASC');
		$list_id = explode(',',$list_id);
		$list = $this->CardM->get_batch($list_id);

		$view_data = array(
			'list' => $list,
		);
		$this->load->view(get_template().'/card/ajax_contact_list',$view_data);
	}

	function view($id) {
		$this->load->model('aclM');
		$role = $this->AclM->get_user_role_info($id);
		
		$view_data = array(
			'user_role' => $role,
			'title' => 'Contact View',
			'data' => $this->CardM->get($id),
			'card_email' => $this->CardM->get_card_email($id),
			'card_social' => $this->CardM->get_card_social($id),
			'card_phone' => $this->CardM->get_card_phone($id),
			'card_address' => $this->CardM->get_card_address($id),
		);
		//$result = $this->CardM->get($id);
		//echo '<pre>';
		//print_r($result);
		//echo '</pre>';
		//die;
		$where = array();
		$where[] = "created_card_id='$id'";
		$this->HelpdeskM->where = $where;

		$view_data['invoice_summary'] = $this->InvoiceM->get_invoice_summary($id);
		$view_data['helpdesk_summary'] = $this->HelpdeskM->get_list($id);
		$view_data['role'] = array(
			0 => 'None',
			1 => 'Staff',
			2 => 'Customer',
			4 => 'Vendor',
		);
		$this->data['content'] = $this->load->view(get_template().'/card/view', $view_data, TRUE);
		
		$this->data['breadcrumb'][] = array(
			'title' => $view_data['data']['first_name'],
			'url' => '/card/view/'.$id,
		);
		$this->data['breadcrumb'][] = array(
			'title' => 'Contact Information',
			'url' => '',
		);

		$this->_do_output();
	}

	function view_json($id){
		$view_data = array(
			'data' => $this->CardM->get($id),
			'card_email' => $this->CardM->get_card_email($id),
			'card_social' => $this->CardM->get_card_social($id),
			'card_phone' => $this->CardM->get_card_phone($id),
			'card_address' => $this->CardM->get_card_address($id),
		);
		echo json_encode($view_data);
	}
	
	function save_role(){
		$this->load->model('Card_roleM');
		$data = array(
			'card_id' => $this->input->post('id'),
			'role_id' => $this->input->post('role'),
		);
		$id_save = $this->Card_roleM->save($data);
		echo $id_save;
	}
	
	function add($id) {
		//if (!$this->AclM->check('card', $id, 'edit')) die('you cannot edit this data');

		$view_data['title_option'] = array(
			0 => '',
			1 => 'Mr.',
			2 => 'Miss.',
			3 => 'Mrs.',
			4 => 'Dr.',
		);
		$view_data['gender'] = array(
			0 => 'Female',
			1 => 'Male'
		);
		$view_data['role'] = array(
			0 => 'None',
			1 => 'Staff',
			2 => 'Customer',
			4 => 'Vendor',
		);
		$view_data['title'] = $data_fields['title'];
		$view_data['is_new'] = TRUE;
		$view_data['countries'] = $this->Card_AddressM->get_country_list();

		$view_data['tel_label'] = $this->Card_TelM->get_label('number');
		$view_data['tel_type_options'] = $this->Card_TelM->get_options('type');

		$view_data['email_label'] = $this->Card_EmailM->get_label('email');
		$view_data['email_type_options'] = $this->Card_EmailM->get_options('type');

		$view_data['address_label'] = 'Address';
		$view_data['address_type_options'] = $this->Card_AddressM->get_options('type');

		$view_data['social_label'] = 'Social';
		$view_data['social_type_options'] = $this->Card_SocialM->get_options('type');
		$this->data['content'] = $this->load->view(get_template().'/card/add', $view_data, TRUE);
		$this->_do_output();
	}

	function edit($id) {
		//if (!$this->AclM->check('card', $id, 'edit')) die('you cannot edit this data');

		$view_data['data'] = $this->CardM->get($id);
		$data = $this->CardM->get($id);
		$view_data['role'] = array(
			0 => 'None',
			1 => 'Staff',
			2 => 'Customer',
			4 => 'Vendor',
		);
		
		$view_data['is_new'] = FALSE;
		$view_data['countries'] = $this->Card_AddressM->get_country_list();

		$view_data['tel_label'] = $this->Card_TelM->get_label('number');
		$view_data['tel_type_options'] = $this->Card_TelM->get_options('type');

		$view_data['email_label'] = $this->Card_EmailM->get_label('email');
		$view_data['email_type_options'] = $this->Card_EmailM->get_options('type');

		$view_data['address_label'] = 'Address';
		$view_data['address_type_options'] = $this->Card_AddressM->get_options('type');

		$view_data['social_label'] = 'Social';
		$view_data['social_type_options'] = $this->Card_SocialM->get_options('type');

		$this->load->view(get_template().'/card/edit', $view_data);
		//$this->data['content'] = $this->load->view(get_template().'/card/edit', $view_data, TRUE);
		//$this->_do_output();
	}

	function save() {
		$id = $this->CardM->save();
		if ($id == FALSE) {
			echo 'error saving.';
			echo '<pre>', print_r($this->CardM->errors, TRUE), '</pre>';
		} else {
			redirect('/card/view/'.$id);
		}
	}

	function confirm_delete($card_id) {
		//$staff_id = $this->UserM->get_card_id();
		$per = $this->AclM->check('card',0,'delete');
		$data = array(
			'per' => $per,
			'card_id' => $card_id,
		);
		$this->load->view(get_template().'/card/confirm_delete', $data);
	}

	function delete($card_id){
		$per_helpdesk = $this->AclM->check('helpdesk',0,'delete');
		$per_invoice = $this->AclM->check('invoice',0,'delete');
		if($per_helpdesk == TRUE && $per_invoice == TRUE){
			$this->CardM->delete($card_id);
			$per = TRUE;
		}else{
			$per = FALSE;
		}
		$data['per'] = $per;
		$this->load->view(get_template().'/card/delete', $data);
	}

	function ajax_get_list() {
		$limit = $this->input->post('limit');
		$offset = $this->input->post('offset');

		$this->CardM->limit = $limit;
		$this->CardM->offset = $offset;

		$this->CardM->sett_fill_address = FALSE;
		$this->CardM->sett_fill_bank = FALSE;
		$this->CardM->sett_fill_email = FALSE;
		$this->CardM->sett_fill_extra = FALSE;
		$this->CardM->sett_fill_notes = FALSE;
		$this->CardM->sett_fill_social = FALSE;
		$this->CardM->sett_fill_tel = FALSE;
		$list = $this->CardM->get_list();

		$this->RespM->set_message()
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Contacts List')
				->set_details($list)
				->output_json();
	}

	function ajax_get() {
		$id = $this->input->post('id');
		$result = $this->CardM->get($id);

		$this->RespM->set_message('')
			->set_type('')
			->set_template('')
			->set_success(TRUE)
			->set_title('Contact Info')
			->set_details($result)
			->output_json();
	}

	function ajax_save() {
		/*
		//test code for client-side validation
		$errors = array(
			'first_name' => 'test error first name'
		);
		$this->RespM->set_success(FALSE)
				->set_details($errors)
				->output_json();
		return;
		*/
            
            
                /*
                 * Leo Fix
                 */
                 $date = explode('/',$_POST['addon_extra'][0]['birth_date']);
                 $date = $date[2].'-'.$date[0].'-'.$date[1];
                 $_POST['addon_extra'][0]['birth_date'] = $date;
                 /*
                  * End Fix
                  */
		$id = $this->CardM->save();

		$success = ($id !== FALSE);

		$message = '';
		$details = '/card/view/'.$id;
		if (!$success) {
			$message = $this->CardM->get_error_string();

			$errors = array();
			foreach($this->CardM->field_errors AS $field => $message) {
				$errors[$field] = implode('<br />', $message);
			}
			$details = $errors;
		}

		$this->RespM->set_message($message)
				->set_success($success)
				->set_details($details)
				->output_json();
	}

	function ajax_search_staff() {
		$search_string = $this->input->get_post('search_string');
		$list = $this->CardM->search_staff($search_string);

		$this->RespM->set_success(TRUE)
				->set_details($list)
				->output_json();
	}

	function ajax_auto_staff() {
		$term = $this->input->get('term');
		$list = $this->CardM->search_staff($term);

		$data = array();
		if (count($list)) {
			foreach ($list as $v) {
				if (strlen($v['display_name'])) {
					$name = $v['display_name'];
				} else {
					$name = $v['first_name'].' '.$v['last_name'];
				}

				$data[] = array(
					'id' => $v['id'],
					'label' => $name,
					'value' => $name
				);
			}
		}

		echo json_encode($data);
		exit;
	}

	function ajax_auto_customer() {
		$term = $this->input->get('term');
		$list = $this->CardM->search_customer($term);

		$data = array();
		if (count($list)) {
			foreach ($list as $v) {
				if (strlen($v['display_name'])) {
					$name = $v['display_name'];
				} else {
					$name = $v['first_name'].' '.$v['last_name'];
				}

				$data[] = array(
					'id' => $v['id'],
					'label' => $name,
					'value' => $name
				);
			}
		}

		echo json_encode($data);
		exit;
	}

}