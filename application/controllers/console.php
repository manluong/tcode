<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Console extends MY_Controller {

	function __construct() {
		$this->allow_unauthed_access = TRUE;

		parent::__construct();

		if (!$this->is_cli) die('This is for console use only.');
	}

	function _remap($method, $params=array()) {
		$this->_setup_db($params[0]);

		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $params);
		}

		echo 'invalid command';
	}

	function test() {
		echo 'success';
	}

	function install_basic_acl() {
		$this->load->model('AclM');

		$apps = array(
			'card' => array(
				'card',
				'card_address',
				'card_associate',
				'card_bank',
				'card_email',
				'card_extra',
				'card_name',
				'card_notes',
				'card_social',
				'card_tel',
			),
			'client' => array(
				'client',
				'client_more'
			),
			'staff' => array(
				'staff',
				'staff_dept',
				'staff_deptlist',
			),
			'product' => array(
			),
			'invoice' => array(
				'a_invoice',
				'a_invoice_info',
				'a_invoice_item',
				'a_invoice_pay',
				'a_invoice_paybatch',
				'a_invoice_payitem',
				'a_invoice_paynotice',
				'a_invoice_quote',
				'a_invoice_quotetemplate',
				'a_invoice_quote_item',
			),
			'vendor' => array(
				'vendor'
			),
			'docs' => array(
				'a_docs',
				'a_docs_dir',
			),
			'helpdesk' => array(
				'a_helpdesk',
				'a_helpdesk_comment',
				'a_helpdesk_re',
			)
		);

		$roles = array(
			'Admin' => array(),
			'Staff' => array('Sales Department', 'Human Resources', 'IT'),
			'Client' => array(),
			'Client+' => array(),
			'Vendor' => array(),
			'Member' => array(),
			'Public' => array(),
		);

		foreach($apps AS $app=>$tables) {
			$data = array(
				'name' => $app,
			);
			$parent_id = $this->AclM->create_node(1, $data, 'ro');

			$data = array();
			foreach($tables AS $t) {
				$data[] = array(
					'name' => $t,
				);
			}
			if (count($data) > 0) $this->AclM->create_nodes($parent_id, $data, 'ro');
		}

		foreach($roles AS $role=>$sub_roles) {
			$data = array(
				'name' => $role,
			);
			$parent_id = $this->AclM->create_node(1, $data, 'co');

			$data = array();
			foreach($sub_roles AS $t) {
				$data[] = array(
					'name' => $t,
				);
			}
			if (count($data) > 0) $this->AclM->create_nodes($parent_id, $data, 'co');
		}

		echo 'done';
	}
}