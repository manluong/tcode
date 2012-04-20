<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ACL extends MY_Controller {

	function __construct() {
		parent::__construct();
	}


	public function index($app='', $action='', $app_data_id='')	{
		if ($app == '') $app = $this->url['app'];
		if ($action == '') $action = $this->AppM->get_group($this->url['app'], $this->url['action']);
		if ($app_data_id == '') $app_data_id = $this->url['id_plain'];

		$app = 'client';
		$action = 'search';
		$app_data_id = 12;

		$html_data = array();
		//$html_data['acl'] = $this->ACLM->get_acl($app, $action, array($app_data_id));
		$html_data['app'] = $app;
		$html_data['action'] = $action;
		$html_data['app_data_id'] = $app_data_id;

		//$this->ACLM->fill_acl_details($html_data['acl']);

		$data = array();
		$data['html'] = $this->load->view(get_template().'/acl/view', $html_data, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Permissions';
		$data['div']['element_name'] = 'winpermissions';
		$data['div']['element_id'] = 'divpermissions';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}


	function ajax_get_acl($app, $action, $app_data_id) {
		$html_data = array();
		$html_data['app'] = $app;
		$html_data['action'] = $action;
		$html_data['app_data_id'] = $app_data_id;

		$acl = $this->ACLM->get_acl($app, $action, array($app_data_id));
		$this->ACLM->fill_acl_details($acl);

		$yn = array(
			0 => 'No',
			1 => 'Yes'
		);

		foreach($acl AS $k=>$v) {
			$acl[$k]['admin_display'] = $yn[$v['admin']];
			$acl[$k]['read_display'] = $yn[$v['read']];
			$acl[$k]['list_display'] = $yn[$v['list']];
			$acl[$k]['search_display'] = $yn[$v['search']];
			$acl[$k]['copy_display'] = $yn[$v['copy']];
			$acl[$k]['download_display'] = $yn[$v['download']];
			$acl[$k]['write_display'] = $yn[$v['write']];
			$acl[$k]['add_display'] = $yn[$v['add']];
			$acl[$k]['move_display'] = $yn[$v['move']];
			$acl[$k]['rename_display'] = $yn[$v['rename']];
			$acl[$k]['delete_display'] = $yn[$v['delete']];
		}

		$html_data['acl'] = $acl;

		echo json_encode($html_data);
	}


	function ajax_get_groups() {
		$groups = $this->ACLM->get_gp_list();

		$response = array(
			'data' => $groups,
		);

		echo json_encode($response);
	}

	function ajax_get_roles() {
		$gp_id = $this->input->post('gp_id');

		$roles = $this->ACLM->get_subgp($gp_id);

		$response = array(
			'data' => $roles,
		);

		echo json_encode($response);
	}

	function ajax_get_users() {
		$gp_id = $this->input->post('gp_id');

		$roles = $this->ACLM->get_users($gp_id);

		$response = array(
			'data' => $roles,
		);

		echo json_encode($response);
	}


	function ajax_save_acl() {
		$acl_type = $this->input->post('acl_type');
		$data = array();
		$message = 'Permission added';
		$has_error = FALSE;

		$data['app'] = $this->input->post('app');
		$data['action'] = $this->input->post('action');
		$data['app_data_id'] = $this->input->post('app_data_id');

		if ($data['app_data_id'] === FALSE) $data['app_data_id'] = 0;

		switch($acl_type) {
			case 'groups':
				$data['role_type'] = 3;
				$data['role_id'] = $this->input->post('groups');
				break;
			case 'roles':
				$data['role_type'] = 2;
				$data['role_id'] = $this->input->post('roles');
				if (!is_numeric($data['role_id'])) {
					$message = 'You must select a role.';
					$has_error = true;
				}
				break;
			case 'users':
				$data['role_type'] = 1;
				$data['role_id'] = $this->input->post('users');
				if (!is_numeric($data['role_id'])) {
					$message = 'You must select a user.';
					$has_error = true;
				}
				break;
		}

		$data['admin'] = $this->input->post('admin');
		$data['write'] = $this->input->post('write');
		$data['read'] = $this->input->post('read');

		if ($data['admin'] != 1) $data['admin'] = 0;
		if ($data['write'] != 1) $data['write'] = 0;
		if ($data['read'] != 1) $data['read'] = 0;

		if (!$has_error) $this->ACLM->save_acl($data);

		$response = array(
			'success' => !$has_error,
			'message' => $message
		);

		echo json_encode($response);
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
