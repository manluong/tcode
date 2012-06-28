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
		//
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

	function upload(){
	   $this->load->library('filel');
//	   $file = $this->filel->save('file', 'Contact');
//	   echo $file['hash'];
	    if ((($_FILES["file"]["type"] == "image/gif")
	    || ($_FILES["file"]["type"] == "image/jpeg")
	    || ($_FILES["file"]["type"] == "image/pjpeg")
	    || ($_FILES["file"]["type"] == "image/png")))
	    {
		$name = date('d-m-Y-h-i-s');
		$file = pathinfo($_FILES["file"]["name"]);
		$file = $name.'.'.$file['extension'];
		$path = $this->filel->write_to_temp(file_get_contents($_FILES["file"]["tmp_name"]),$file);
		echo $file;
	    }
	    else
	    {
		echo "error";
	    }
	}
	function get_image($name){
	    $this->load->library('filel');
	    echo $temp_dir;
	    $file = file_get_contents($this->filel->get_temp_dir().'/'.$name);
	    echo $file;
	}
	function ajax_contact_info(){
		$card_id = $this->input->post('id');
		$data = $this->CardM->get($card_id);
		echo json_encode($data);
		/*
		$view_data = array(
			'detail' => $this->CardM->get($card_id),
		);
        $this->load->view(get_template().'/card/ajax_contact_info',$view_data);
		*/
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
		$id = $this->input->post('id');
		if($id != ''){
			$data['id'] = $id ;
		}
		if($this->input->post('card_id') != ''){
			$data['card_id'] = $this->input->post('card_id');
		}

//		$data = array (
//			'password' => $this->input->post('pass'),
//			'expire_stamp' => $date[2].'-'.$date[1].'-'.$date[0].' 00:00:00',
//		);
		// Leo fix
		    $data['password'] = $this->input->post('pass');
		    $data['expire_stamp'] = $date[2].'-'.$date[0].'-'.$date[1].' 00:00:00';
		// End fix
		$save_id = $this->Access_UserM->save($data);
//		$view_data['data'] = $this->Access_UserM->get($save_id);
//		$this->load->view(get_template().'/card/ajax_change_pass',$view_data);
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

		echo json_encode($list);
		/*
		$view_data = array(
			'list' => $list,
		);
		$this->load->view(get_template().'/card/ajax_contact_list',$view_data);
		*/
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

	function post_value(){
		if($_POST){
			$data = array(
				'addon_email[0][email]' => $_POST['addon_email[0][email]'],
				'addon_email[0][type]' => $_POST['addon_email[0][type]'],
				'addon_tel[0][type]' => $_POST['addon_tel[0][type]'],
				'addon_tel[0][number]' => $_POST['addon_tel[0][number]'],
				'addon_socia[0][type]' => $_POST['addon_socia[0][type]'],
				'addon_socia[0][name_id]' => $_POST['addon_socia[0][name_id]'],
				'addon_notes[0][note]' => $_POST['addon_notes[0][note]'],
				'addon_extra[0][gender]' => $_POST['addon_extra[0][gender]'],
				'addon_extra[0][birth_date]' => $_POST['addon_extra[0][birth_date]'],
				'addon_address[0][type]' => $_POST['addon_address[0][type]'],
				'addon_address[0][line_1]' => $_POST['addon_address[0][line_1]'],
				'addon_address[0][line_2]' => $_POST['addon_address[0][line_2]'],

			);
			echo json_encode($data);
		}
	}

	function save_role(){
		$this->load->model('Card_roleM');
		$data = array(
			'card_id' => $this->input->post('id'),
			'role_id' => $this->input->post('role'),
		);
		if($this->input->post('id_role') != ''){
			$data['id'] = $this->input->post('id_role');
		}
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
		$per = $this->AclM->check('card', 0, 'delete');
		$data = array(
			'per' => $per,
			'card_id' => $card_id,
		);
		$this->load->view(get_template().'/card/confirm_delete', $data);
	}

	function delete($card_id){
		$per = $this->CardM->delete($card_id, TRUE);
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

	function ajax_auto_all_contact() {
		$term = $this->input->get('term');
		$list = $this->CardM->search_all_contact($term);

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

	/*--Iphone--*/

	function iphone_save(){
		if(isset($_POST)){

			echo 'hello world';
			/*--Save email--*/
			if(isset($_POST['addon_email'])){
				$addon_email = $_POST['addon_email'];
				$addon_email = explode(';',$addon_email);
				for($i=0 ; $i<count($addon_email) ; $i++){
					$email = explode(',',$addon_email[$i]);
						$_POST['addon_email'][$i]['id'] = trim($email[0]);
						$_POST['addon_email'][$i]['email'] = trim($email[1]);
						$_POST['addon_email'][$i]['type'] = trim($email[2]);
						$_POST['addon_email'][$i]['is_default'] = trim($email[3]);
				}
			}
			print_r($_POST);
			/*--Save phone--*/
			if($_POST['addon_tel']){
				$addon_tel = $_POST['addon_tel'];
				$addon_tel = explode(';',$addon_tel);
				for($i=0 ; $i<count($addon_tel) ; $i++){
					$tel = explode(',',$addon_tel[$i]);
						$_POST['addon_tel'][$i]['id'] = trim($tel[0]);
						$_POST['addon_tel'][$i]['type'] = trim($tel[1]);
						$_POST['addon_tel'][$i]['number'] = trim($tel[2]);
						$_POST['addon_tel'][$i]['country'] = trim($tel[3]);
						$_POST['addon_tel'][$i]['area'] = trim($tel[4]);
						$_POST['addon_tel'][$i]['extension'] = trim($tel[5]);
						$_POST['addon_tel'][$i]['is_default'] = trim($tel[6]);
				}
			}
			/*--Save address--*/
			if($_POST['addon_address']){
				$addon_address = $_POST['addon_address'];
				$addon_address = explode(';',$addon_address);
				for($i=0 ; $i<count($addon_address) ; $i++){
					$add = explode(',',$addon_address[$i]);
						$_POST['addon_address'][$i]['id'] = trim($add[0]);
						$_POST['addon_address'][$i]['type'] = trim($add[1]);
						$_POST['addon_address'][$i]['line_1'] = trim($add[2]);
						$_POST['addon_address'][$i]['line_2'] = trim($add[3]);
						$_POST['addon_address'][$i]['is_default'] = trim($add[4]);
				}
			}
			/*--Save social--*/
			if($_POST['addon_social']){
				$addon_social = $_POST['addon_social'];
				$addon_social = explode(';',$addon_social);
				for($i=0 ; $i<count($addon_social) ; $i++){
					$social = explode(',',$addon_social[$i]);
						$_POST['addon_social'][$i]['id'] = trim($social[0]);
						$_POST['addon_social'][$i]['type'] = trim($social[1]);
						$_POST['addon_social'][$i]['name_id'] = trim($social[2]);
				}
			}
			/*--Save notes--*/
			if($_POST['addon_notes']){
				$addon_notes = $_POST['addon_notes'];
				$addon_notes = explode(';',$addon_notes);
				for($i=0 ; $i<count($addon_notes) ; $i++){
					$note = explode(',',$addon_notes[$i]);
						$_POST['addon_notes'][$i]['id'] = trim($note[0]);
						$_POST['addon_notes'][$i]['note'] = trim($note[1]);
				}
			}
			/*--Save extra--*/
			if($_POST['addon_extra']){
				$addon_extra = $_POST['addon_extra'];
				$addon_extra = explode(';',$addon_extra);
				for($i=0 ; $i<count($addon_extra) ; $i++){
					$extra = explode(',',$addon_extra[$i]);
						$_POST['addon_extra'][$i]['id'] = trim($extra[0]);
						$_POST['addon_extra'][$i]['gender'] = trim($extra[1]);
						$_POST['addon_extra'][$i]['birth_date'] = trim($extra[2]);
				}
			}

			$id_save = $this->CardM->save();
			echo 'success';
		}
		//echo '<pre>';
		//print_r($_POST);
		//echo '</pre>';
	}

	function test_iphone(){
		$addon_email = '110, abc@ymail.com, 0, 0; ,xyz@abc.com, 1, 1;';
		$addon_tel = ' , 0, 11, 22, 33, 44, 0; , 2, 111, 222, 333, 444, 1';
		$addon_address = ' , 1, Dien Bien Phu, Dinh Bo Linh, 1;  , 0, Nguyen Van Dau, Nguyen Cong Tru, 0';
		$addon_social = ', 1, nva@gmail.com; , 2, nvb@gmail.com; , 3, nvc@gmail.com';
		$addon_notes = '126, hehehe';
		$addon_extra = ' , 1, 2012-06-20';
		$this->iphone_save(1, $addon_email, $addon_tel, $addon_address, $addon_social, $addon_notes, $addon_extra);
	}
}