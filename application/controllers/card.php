<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CardM');
		$this->load->model('Card_AddressM');
		$this->load->model('Card_EmailM');
		$this->load->model('Card_SocialM');
		$this->load->model('Card_TelM');
	}

	function index() {
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

		$this->data['content'] = $this->load->view(get_template().'/card/index', $view_data, TRUE);
		$this->_do_output();
	}

	function view($id) {
		$view_data = array(
			'title' => 'Contact View',
			'data' => $this->CardM->get($id),
			'card_email' => $this->CardM->get_card_email($id),
			'card_social' => $this->CardM->get_card_social($id),
			'card_phone' => $this->CardM->get_card_phone($id),
			'card_address' => $this->CardM->get_card_address($id),
		);

		$this->load->model('InvoiceM');
		$view_data['invoice_summary'] = $this->InvoiceM->get_invoice_summary($id);

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

	function edit($id) {
		//if (!$this->AclM->check('card', $id, 'edit')) die('you cannot edit this data');

		$view_data['data'] = $this->CardM->get($id);
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

		//$content = $this->load->view(get_template().'/card/edit', $view_data, TRUE);
		//echo $content;
		$this->data['content'] = $this->load->view(get_template().'/card/edit', $view_data, TRUE);
		$this->_do_output();
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

	function upload($card_id){
	   $this->load->library('filel');
	   $file = $this->filel->save('file', 'CardM');
		if($card_id != 0){
		   $insert_id = $this->CardM->insert_upload_file($file['hash'],$card_id);
		   echo $file['hash'];
		}
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