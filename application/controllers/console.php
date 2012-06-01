<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Console extends MY_Controller {

	function __construct() {
		$this->allow_unauthed_access = TRUE;

		parent::__construct();

		if (!$this->is_cli) die('This is for console use only.');
	}

	function _remap($method, $params=array()) {
		if ($method == 'help') return call_user_func_array(array($this, $method), $params);

		$domain = array_shift($params);
//		$this->load->dbutil();
//		if ( ! $this->dbutil->database_exists('t_'.$domain) ) {
//			die('Database does not exist for '.$domain."\n");
//		}

		$this->_setup_db($domain);


		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $params);
		}

		echo 'invalid command';
	}

	function test($p1, $p2) {
		echo "P1: $p1\n";
		echo "P2: $p2\n";
	}

	function help() {
		echo "Available commands:\n\n";

		echo " acl_install<domain>\n";
		echo "  - installs the basic ACL request objects and control objects and rules\n\n";

		echo " acl_rebuild <domain> <type=co|ro, default=co> <parent_id=1> <left=1>\n";
		echo "  - rebuilds the lft and rght values\n\n";

		echo " acl_display <domain> <type=co|ro, default=co>\n";
		echo "  - shows the objects in selected type in heirarchy\n\n";

		echo " acl_reset <domain>\n";
		echo "  - clears all the acl tables except for the default objects, resets autonumber and rght value.\n\n";

		echo " acl_reinstall <domain>\n";
		echo "  - Runs reset_acl and install_basic_acl.\n\n";

		echo " acl_check <domain> <name> <foreign_key> <card_id> <action_type=access|create|read|update|delete>\n";
		echo "  - Checks the ACL rules to see if action is granted or not.\n\n";

		echo " acl_path_to_id <domain> <path> <type=ro|co>\n";
		echo "  - Get the RO/CO ID of a path. Separate paths with -(hyphen) instead of /(forward slash) \n\n";

		echo " acl_path_to_foreign_id <domain> <path> <type=ro|co>\n";
		echo "  - Get the Foreign ID of a path. Separate paths with -(hyphen) instead of /(forward slash) \n\n";

		echo " acl_test <domain>\n";
		echo "  - Runs an ACL test.\n\n";

		echo " user_add <domain> <name> <email> <password> <role>\n";
		echo "  - Add a user.\n\n";
	}

	function acl_install() {
		$this->load->model('AclM');

		$this->AclM->install();

		echo "Done installing basic ACL\n";
	}

	function acl_display($type='co') {
		$rs = $this->db->select()
				->from('access_'.$type)
				->order_by('lft', 'ASC')
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$results = $rs->result_array();

		$right = array();
		$output = array();
		$card_ids = array();
		foreach($results AS $r) {
			if (count($right) > 0) {
				while(isset($right[count($right)-1]) && $right[count($right)-1] < $r['rght']) {
					array_pop($right);
				}
			}

			$output[] = array(
				'spacing' => count($right),
				'text' => '['.$r['id'].'] '.$r['name'].' - '.$r['foreign_key'],
				'table' => $r['name'],
				'foreign_key' => $r['foreign_key'],
			);
			if ($r['name'] == 'card') $card_ids[] = $r['foreign_key'];

			$right[] = $r['rght'];
		}

		if (count($card_ids) > 0) {
			$this->load->model('UserM');
			$users = $this->UserM->get_batch($card_ids, TRUE);
			foreach($output AS $k=>$v) {
				if ($v['table'] != 'card') continue;

				$output[$k]['text'] .= ' - '.$users[$v['foreign_key']]['first_name'].' '.$users[$v['foreign_key']]['last_name'];
			}
		}

		foreach($output AS $k=>$v) {
			echo str_repeat(' ',$v['spacing']).$v['text']."\n";
		}
	}

	function acl_rebuild($type='co', $parent_id=1, $left='1') {
		$this->load->model('AclM');
		$this->AclM->rebuild($type, $parent_id, $left);
		echo "ACL Rebuilding done.\n";
	}

	function acl_reset() {
		$this->load->model('AclM');
		$this->AclM->reset();
		echo "Done resetting ACL tables.\n";
	}

	function acl_reinstall() {
		$this->acl_reset();
		$this->acl_install();

		echo "ACL Reinstallation done.\n";
	}


	function acl_path_to_id($path, $type) {
		$this->load->model('AclM');

		$path = str_replace('-', '/', $path);

		$id = $this->AclM->get_id_by_path($path, $type);
		echo "ID: $id\n";
	}

	function acl_path_to_foreign_id($path, $type) {
		$this->load->model('AclM');

		$path = str_replace('-', '/', $path);

		$id = $this->AclM->get_foreign_id_by_path($path, $type);
		echo "Foreign ID: $id\n";
	}

	function acl_check($name, $foreign_key, $type, $card_id) {
		$this->load->model('AclM');
		$result = $this->AclM->check($name, $foreign_key, $type, $card_id);
		echo ($result) ? 'granted' : 'denied';
		echo "\n";
	}

	function acl_test() {
		$this->load->model('AclM');

		echo 'Access for Erik Yeoh to card: ';
		echo ($this->AclM->check('card', 0, 'access', 1))?'granted':'denied';
		echo "\n\n";

		echo 'Access for Staff One to invoice: ';
		echo ($this->AclM->check('invoice', 0, 'access', 191))?'granted':'denied';
		echo "\n";
		echo 'Access for Staff Three to invoice: ';
		echo ($this->AclM->check('invoice', 0, 'access', 193))?'granted':'denied';
		echo "\n\n";

		echo 'Access for Staff One to docs: ';
		echo ($this->AclM->check('docs', 0, 'access', 191))?'granted':'denied';
		echo "\n";
		echo 'Access for Staff Two to docs: ';
		echo ($this->AclM->check('docs', 0, 'access', 192))?'granted':'denied';
		echo "\n\n";

		echo 'Access for Staff Three to product: ';
		echo ($this->AclM->check('product', 0, 'access', 193))?'granted':'denied';
		echo "\n\n";

		echo 'Access for Staff One to helpdesk: ';
		echo ($this->AclM->check('helpdesk', 0, 'access', 191))?'granted':'denied';
		echo "\n";
		echo 'Access for Staff Two to helpdesk: ';
		echo ($this->AclM->check('helpdesk', 0, 'access', 192))?'granted':'denied';
		echo "\n\n";

		echo 'Access for Erik Yeoh on invoice row 10: ';
		echo ($this->AclM->check('a_invoice', 10, 'access', 1))?'granted':'denied';
		echo "\n";
		echo 'Access for Staff One on invoice row 10: ';
		echo ($this->AclM->check('a_invoice', 10, 'access', 191))?'granted':'denied';
		echo "\n";
		echo 'Access for Staff Two on invoice row 10: ';
		echo ($this->AclM->check('a_invoice', 10, 'access', 192))?'granted':'denied';
		echo "\n";
		echo 'Access for Staff Three on invoice row 10: ';
		echo ($this->AclM->check('a_invoice', 10, 'access', 193))?'granted':'denied';
		echo "\n\n";
	}

	function user_add($name, $email, $password, $role) {
		$this->load->model('CardM');
		$this->load->model('AclM');

		$name = explode(' ', $name);
		$card = array();
		switch(count($name)) {
			case 1:
				$card['first_name'] = $name[0];
				break;
			case 2:
				$card['first_name'] = $name[0];
				$card['last_name'] = $name[1];
				break;
			case 3:
				$card['first_name'] = $name[0];
				$card['middle_name'] = $name[1];
				$card['last_name'] = $name[2];
				break;
		}

		$email = str_replace(':', '@', $email);
		$card['addon_email'][] = array(
			'email' => $email,
			'is_default' => 1
		);

		$card['addon_access_user'][] = array(
			'password' => $password,
			'status' => 1
		);

		$card_id = $this->CardM->save($card);
		if ($card_id === FALSE) {
			echo $this->CardM->get_error_string();
			return;
		} else {
			echo "New user created\n";
		}

		$role = str_replace('-', '/', $role);
		$result = $this->AclM->assign_role($card_id, $role);

		if ($result) {
			echo "Role Assigned.\n";
		} else {
			echo "Error assigning role: ".$this->AclM->get_error_string()."\n";
		}
	}

	function user_delete($card_id) {
		$rs = $this->db->query("SELECT id FROM access_ro WHERE name='card' AND foreign_key=$card_id");
		$ro_ids = array();
		foreach($rs->result_array() AS $r) {
			$ro_ids[] = $r['id'];
		}
		if (count($ro_ids) > 0) {
			$this->db->query("DELETE FROM access_ro WHERE id IN (".implode(',', $ro_ids).")");
			$this->db->query("DELETE FROM access_ro_co WHERE ro_id IN (".implode(',', $ro_ids).")");
		}

		$this->db->query("DELETE FROM access_user WHERE card_id=$card_id");
		$this->db->query("DELETE FROM access_user_role WHERE card_id=$card_id");
		$this->db->query("DELETE FROM access_user_role_sub WHERE card_id=$card_id");
		$this->db->query("DELETE FROM card WHERE id=$card_id");
		$this->db->query("DELETE FROM card_email WHERE card_id=$card_id");
		echo "Deleted user from DB.\n";
		$this->acl_rebuild('ro');
	}
}