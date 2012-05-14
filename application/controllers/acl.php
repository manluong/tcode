<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ACL extends MY_Controller {

	function __construct() {
		parent::__construct();
	}


	public function index($app='',  $app_data_id='')	{
		if ($app == '') $app = $this->url['app'];
		if ($app_data_id == '') $app_data_id = $this->url['id_plain'];

		$app = 'client';
		$app_data_id = 12;

		$html_data = array();
		$html_data['app'] = $app;
		$html_data['app_data_id'] = $app_data_id;

		$this->data['content'] = $this->load->view(get_template().'/acl/view', $html_data, TRUE);

		$this->_do_output();
	}


	function ajax_get_acl($app, $app_data_id) {

	}


	function ajax_get_subroles() {
		$role_id = $this->input->post('role_id');

		$subroles = $this->ACLM->get_subroles($role_id);

		$response = array(
			'data' => $subroles,
		);

		echo json_encode($response);
	}

	function ajax_get_users() {
		$role_id = $this->input->post('role_id');

		$roles = $this->ACLM->get_users($role_id);

		$response = array(
			'data' => $roles,
		);

		echo json_encode($response);
	}


	function ajax_save_acl() {

	}


	function ajax_delete_acl() {
		$acl_id = $this->input->post('acl_id');
		$has_error = false;
		$message = '';

		//TODO: verify user has access to delete this acl first

		if (!$has_error) $this->ACLM->delete_acl($acl_id);

		$response = array(
			'success' => !$has_error,
			'message' => $message
		);

		echo json_encode($response);
	}


}
