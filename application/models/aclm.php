<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class AclM extends MY_Model {
	var $cache_acl = array();
	var $unit_test = array(
		'triggered_rule' => array(),
	);

	function __construct() {
		$this->table = 'access_ro_co';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;

		parent::__construct();
	}

	function check_id_encryption() {
		if (!id_is_encrypted($this->url['id'])) die('id must be encrypted');
	}

	function check_app_access() {
		if (!$this->UserM->is_logged_in() && !$this->allow_unauthed_access) {
			header( 'Location: /access/'.set_return_url(TRUE));
			exit;
		} elseif ($this->UserM->is_logged_in() && !$this->UserM->is_admin() && !$this->allow_unauthed_access) {
			$app = $this->url['app'];
			$acl_app_list = $this->AppM->acl_app_list;
			$has_access = FALSE;
			foreach($acl_app_list AS $app_name) {
				if ($app_name == $app) $has_access = TRUE;
			}
			if (!$has_access) {
				die('You do not have the permissions to access this app');
				//TODO: change to 404 error?
			}
		}
	}

	function get_subroles_batch($ids, $id_as_key=FALSE) {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'access_roles_sub';
		$this->id_field = 'id';

		$results = parent::get_batch($ids, $id_as_key);

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_roles_batch($ids, $id_as_key=FALSE) {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'global_setting.access_roles';
		$this->id_field = 'code';

		$results = parent::get_batch($ids, $id_as_key);

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_roles_list() {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'global_setting.access_roles';
		$this->id_field = 'code';

		$results = parent::get_list();

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_subroles($role_id='') {
		$this->db->select()
			->from('access_roles_sub');

		if ($role_id != '') $this->db->where('role_id', $role_id);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}

	function get_card_ids_in_role($role_name) {
		$card_ids = array();
		$rs = $this->db->select('card_id')
				->from('access_user_role AS ur')
				->join('global_setting.access_roles AS r', 'r.code=ur.role_id', 'left')
				->where('r.name', $role_name)
				->get();

		if ($rs->num_rows() == 0) return array();

		foreach($rs->result_array() AS $r) {
			$card_ids[] = $r['card_id'];
		}

		return $card_ids;
	}

	function get_users($role_id='') {
		$subroles = $this->get_subroles($role_id);

		$subrole_ids = array();

		foreach($subroles AS $r) {
			$subrole_ids[] = $r['id'];
		}

		if (count($subrole_ids) == 0) return array();

		$rs = $this->db->select('DISTINCT ur.card_id, CONCAT(c.card_fname," ",c.card_lname) AS name', false)
				->from('access_user_role AS ur')
				->where_in('ur.roles_sub_id', $subrole_ids)
				->join('card AS c', 'c.id=ur.card_id', 'left')
				->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}



	function check($name='', $foreign_key=0, $action='access', $card_id='', $priority='allow') {
		if ($name == '') $name = $this->url['app'];
		if ($card_id == '') $card_id = $this->UserM->get_card_id();

		$ro = array(
			'name' => 'card',
			'foreign_key' => $card_id,
		);
		$ro_id = $this->get_ro_id($ro);

		$co = array(
			'name' => $name,
			'foreign_key' => $foreign_key,
		);
		$co_id = $this->get_co_id($co);

		if (is_array($ro_id)) {
			//This happens if the card_id is in more than 1 sub role
			$ros = array();
			foreach($ro_id AS $id) {
				$ros[] = $this->get_ro_tiered($id);
			}
		} else {
			$ros = $this->get_ro_tiered($ro_id);
		}

		$cos = $this->get_co_tiered($co_id);

		$co_ids = array();
		foreach($cos AS $c) {
			$co_ids[] = $c['id'];
		}

		if (is_array($ro_id)) {
			//This happens if the card_id is in more than 1 sub role
			foreach($ros AS $x=>$sub_ros) {

				$break = FALSE;
				$result[$x] = 0;

				foreach($sub_ros AS $r) {
					$rules = $this->get_rocos($r['id'], $co_ids);

					if ($rules === FALSE) continue;

					foreach($rules AS $rule) {
						if ($break) continue;

						switch($rule['action_'.$action]) {
							case -1:
								$result[$x] = -1;
								$break = TRUE;
								break;
							case 0:
								continue;
								break;
							case 1:
								$result[$x] = 1;
								$break = TRUE;
								break;
						}
					}
				}
			}

			$result = array_unique($result);
			if (count($result) == 1) {
				return ($result[0] == 1);
			} else {
				return ($priority == 'allow');
			}
		} else {
			foreach($ros AS $r) {
				$rules = $this->get_rocos($r['id'], $co_ids);

				if ($rules === FALSE) continue;

				foreach($rules AS $rule) {
					switch($rule['action_'.$action]) {
						case -1:
							return FALSE;
							break;
						case 0:
							continue;
							break;
						case 1:
							return TRUE;
							break;
					}
				}
			}
		}

		return FALSE;
	}

	function rebuild($type='co', $parent_id=1, $left='1') {
		$right = $left+1;

		$rs = $this->db->select()
				->from('access_'.$type)
				->where('parent_id', $parent_id)
				->get();

		foreach($rs->result_array() AS $r) {
			$right = $this->rebuild($type, $r['id'], $right);
		}

		$this->db->query("UPDATE access_$type SET lft='$left', rght='$right' WHERE id=$parent_id");

		return $right+1;
	}

	function reset() {
		$this->db->query('DELETE FROM access_ro WHERE id!=1');
		$this->db->query('ALTER TABLE access_ro AUTO_INCREMENT=2');
		$this->db->query('UPDATE access_ro SET rght=2 WHERE id=1');

		$this->db->query('DELETE FROM access_co WHERE id!=1');
		$this->db->query('ALTER TABLE access_co AUTO_INCREMENT=2');
		$this->db->query('UPDATE access_co SET rght=2 WHERE id=1');

		$this->db->query('DELETE FROM access_ro_co WHERE id!=1');
		$this->db->query('ALTER TABLE access_ro_co AUTO_INCREMENT=2');
	}

	/*  $nodes = array(
	 *    'name' => '',
	 *    'foreign_key' => ''
	 *  );
	 * assume all nodes belong to a single parent.
	 */
	function create_nodes($parent_id, $nodes, $type) {
		$this->check_type($type);

		$parent_lftrght = $this->get_lftrght($parent_id, $type);
		$rght = $parent_lftrght['rght'];

		foreach($nodes AS $k=>$n) {
			$nodes[$k]['parent_id'] = $parent_id;

			$nodes[$k]['lft'] = $rght;
			$rght++;
			$nodes[$k]['rght'] = $rght;
			$rght++;
		}

		$increment = count($nodes)*2;

		$this->db->trans_start();
		$this->db->query('UPDATE access_'.$type.' SET lft=lft+'.$increment.' WHERE lft>='.$parent_lftrght['rght']);
		$this->db->query('UPDATE access_'.$type.' SET rght=rght+'.$increment.' WHERE rght>='.$parent_lftrght['rght']);

		$this->db->insert_batch('access_'.$type, $nodes);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			log_message('error', 'Unable to save nodes');
		}
	}

	function create_node($parent_id, $node, $type) {
		$this->check_type($type);

		$parent_lftrght = $this->get_lftrght($parent_id, $type);
		$rght = $parent_lftrght['rght'];

		$data = array(
			'parent_id' => $parent_id,
			'name' => $node['name'],
			'lft' => $rght,
			'rght' => $rght+1,
		);

		if (isset($node['foreign_key'])) $data['foreign_key'] = $node['foreign_key'];

		$this->db->trans_start();
		$this->db->query('UPDATE access_'.$type.' SET lft=lft+2 WHERE lft>='.$parent_lftrght['rght']);
		$this->db->query('UPDATE access_'.$type.' SET rght=rght+2 WHERE rght>='.$parent_lftrght['rght']);

		$this->db->insert('access_'.$type, $data);
		$new_id = $this->db->insert_id();
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			log_message('error', 'Unable to save node');
		}

		return $new_id;
	}

	//$type = co|ro
	function get_lftrght($id, $type) {
		$this->check_type($type);

		$rs = $this->db->select('lft, rght')
				->from('access_'.$type)
				->where('id', $id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$row = $rs->row_array();
		return $row;
	}


	function grant($ro, $co, $action='*') {
		$ro_id = $this->get_ro_id($ro);
		$co_id = $this->get_co_id($co);

		if ($ro_id === FALSE) die('Invalid user or user does not have RO created. Card ID: '.$ro['foreign_key']);
		if ($co_id === FALSE) {
			if (is_array($co)) {
				if (!isset($co['parent_id'])) {
					$parent_id = $this->find_co_parent_id($co);
				} else {
					$parent_id = $co['parent_id'];
				}
			} elseif(is_string($co)) {
				$temp = explode('/', $co);
				if (count($temp) == 1) return FALSE;

				$co = array();
				if (is_numeric($temp[count($temp)-1])) {
					$co['foreign_key'] = array_pop($temp);
				}
				$co['name'] = array_pop($temp);

				$parent_co = array(
					'name' => array_pop($temp)
				);
				$parent_id = $this->get_co_id($parent_co);
			}

			$co_id = $this->create_node($parent_id, $co, 'co');
		}

		$fields = $this->db->list_fields('access_ro_co');
		if ($action!='*' && !in_array('action_'.$action, $fields)) die('Invalid Action: '.$action);

		$roco = $this->get_roco($ro_id, $co_id);

		if ($roco !== FALSE) {
			if ($action == '*') {
				foreach($fields AS $f) {
					if (strpos($f, 'action_') === FALSE) continue;
					$roco[$f] = 1;
				}
			} else {
				$roco['action_'.$action] = 1;
			}
			$roco['modified_card_id'] = $this->UserM->get_card_id();
			$roco['modified_stamp'] = get_current_stamp();

			$this->db->where('id', $roco['id'])
				->update('access_ro_co', $roco);
		} else {
			$roco = array(
				'ro_id' => $ro_id,
				'co_id' => $co_id,
				'created_card_id' => $this->UserM->get_card_id(),
				'created_stamp' => get_current_stamp(),
			);

			if ($action == '*') {
				foreach($fields AS $f) {
					if (strpos($f, 'action_') === FALSE) continue;
					$roco[$f] = 1;
				}
			} else {
				$roco['action_'.$action] = 1;
			}

			$this->db->insert('access_ro_co', $roco);
		}

		return TRUE;
	}


	function deny($ro, $co, $action='*') {
		$ro_id = $this->get_ro_id($ro);
		$co_id = $this->get_co_id($co);

		if ($ro_id === FALSE) die('Invalid user or user does not have RO created. Card ID: '.$ro['foreign_key']);
		if ($co_id === FALSE) {
			if (is_array($co)) {
				if (!isset($co['parent_id'])) {
					$parent_id = $this->find_co_parent_id($co);
				} else {
					$parent_id = $co['parent_id'];
				}
			} elseif(is_string($co)) {
				$temp = explode('/', $co);
				if (count($temp) == 1) return FALSE;

				$co = array();
				if (is_numeric($temp[count($temp)-1])) {
					$co['foreign_key'] = array_pop($temp);
				}
				$co['name'] = array_pop($temp);

				$parent_co = array(
					'name' => array_pop($temp)
				);
				$parent_id = $this->get_co_id($parent_co);
			}

			$co_id = $this->create_node($parent_id, $co, 'co');
		}

		$fields = $this->db->list_fields('access_ro_co');
		if ($action!='*' && !in_array('action_'.$action, $fields)) die('Invalid Action: '.$action);

		$roco = $this->get_roco($ro_id, $co_id);

		if ($roco !== FALSE) {
			if ($action == '*') {
				foreach($fields AS $f) {
					if (strpos($f, 'action_') === FALSE) continue;
					$roco[$f] = -1;
				}
			} else {
				$roco['action_'.$action] = -1;
			}
			$roco['modified_card_id'] = $this->UserM->get_card_id();
			$roco['modified_stamp'] = get_current_stamp();

			$this->db->where('id', $roco['id'])
				->update('access_ro_co', $roco);
		} else {
			$roco = array(
				'ro_id' => $ro_id,
				'co_id' => $co_id,
				'created_card_id' => $this->UserM->get_card_id(),
				'created_stamp' => get_current_stamp(),
			);

			if ($action == '*') {
				foreach($fields AS $f) {
					if (strpos($f, 'action_') === FALSE) continue;
					$roco[$f] = -1;
				}
			} else {
				$roco['action_'.$action] = -1;
			}

			$this->db->insert('access_ro_co', $roco);
		}

		return TRUE;
	}

	/*
	 * If assigning by individual person,
	 * $ro = array('name'=>'card', 'foreign_key'=>'<card_id>');
	 *
	 * If assigning by role,
	 * $ro = array('name'=>'<role_name>');
	 */
	function get_ro_id($ro) {
		if (is_numeric($ro)) {
			return $ro;
		} elseif (is_string($ro)) {
			return $this->get_id_by_path($ro, 'ro');
		} elseif (is_array($ro)) {
			if (!isset($ro['foreign_key'])) $ro['foreign_key'] = 0;

			$this->db->select('id')
				->from('access_ro')
				->where('name', $ro['name'])
				->where('foreign_key', $ro['foreign_key']);

			if (isset($ro['parent_id'])) $this->db->where('parent_id', $ro['parent_id']);

			$rs = $this->db->get();

			if ($rs->num_rows() == 0) return FALSE;

			if ($rs->num_rows() == 1) {
				$result = $rs->row_array();
				return $result['id'];
			} else {
				$results = array();
				foreach($rs->result_array() AS $r) {
					$results[] = $r['id'];
				}
				return $results;
			}
		}
	}

	//Get an RO object
	function get_ro($ro_id) {
		$rs = $this->db->select()
				->from('access_ro')
				->where('id', $ro_id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->row_array();
	}

	//Get an RO object with it's parents
	function get_ro_tiered($ro_id) {
		$ro = $this->get_ro($ro_id);

		if ($ro === FALSE) die('Cannot find RO object for: '.$ro_id);

		$rs = $this->db->select()
				->from('access_ro')
				->where('lft <=', $ro['lft'])
				->where('rght >=', $ro['rght'])
				->order_by('lft', 'DESC')
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->result_array();
	}

	/*
	 * If co is an APP
	 * $co = array('name'=>'<app_name>', 'parent_id'=>1);
	 *
	 * If co is a table
	 * $co = array('name'=>'<table_name>');
	 *
	 * If co is an individual record
	 * $co = array('name'=>'<table_name>', foreign_key'=>'123456');
	 */
	function get_co_id($co) {

		if (is_numeric($co)) {
			return $co;
		} elseif (is_string($co)) {
			return $this->get_id_by_path($co, 'co');
		} elseif (is_array($co)) {
			if (!isset($co['foreign_key'])) $co['foreign_key'] = 0;

			if (!isset($co['parent_id']) && $co['foreign_key'] == 0) {
				$co['parent_id'] = 1;
			}

			$this->db->select('id')
				->from('access_co')
				->where('name', $co['name'])
				->limit(1);

			if (!isset($co['parent_id']) && !isset($co['foreign_key'])) {
				$this->db->where('parent_id != 1');
			} else {
				if (isset($co['parent_id'])) $this->db->where('parent_id', $co['parent_id']);
			}

			if (!isset($co['foreign_key'])) {
				$this->db->where('foreign_key', 0);
			} else {
				$this->db->where('foreign_key', $co['foreign_key']);
			}

			$rs = $this->db->get();

			if ($rs->num_rows() == 0) return FALSE;

			$result = $rs->row_array();

			return $result['id'];
		}
	}

	//Get a CO object
	function get_co($co_id) {
		$rs = $this->db->select()
				->from('access_co')
				->where('id', $co_id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->row_array();
	}

	//Get a CO object with it's parents
	function get_co_tiered($co_id) {
		$co = $this->get_co($co_id);

		if ($co === FALSE) die('Cannot find CO object for: '.$co_id);

		$rs = $this->db->select()
				->from('access_co')
				->where('lft <=', $co['lft'])
				->where('rght >=', $co['rght'])
				->order_by('lft', 'DESC')
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->result_array();
	}

	//Fetch a specific RO-CO rule
	function get_roco($ro_id, $co_id) {
		$rs = $this->db->select()
				->from('access_ro_co')
				->where('ro_id', $ro_id)
				->where('co_id', $co_id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->row_array();
	}

	//Fetch rules matching Single RO, Multiple CO
	function get_rocos($ro_id, $co_ids) {
		$rs = $this->db->select('roco.*, co.lft')
				->from('access_ro_co AS roco')
				->join('access_co AS co', 'co.id=roco.co_id', 'LEFT')
				->where('ro_id', $ro_id)
				->where_in('co_id', $co_ids)
				->order_by('co.lft', 'DESC')
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->result_array();
	}


	//Gets the ID of an RO/CO object based on a path.
	//Examples of paths:
	// helpdesk/a_helpdesk
	// helpdesk/a_helpdesk/10 - an item with an ID of 10 in the a_helpdesk table
	function get_id_by_path($path, $type) {
		$this->check_type($type);

		$orig_path = $path;
		$path = explode('/', $path);
		$path_count = count($path);

		if ($path_count == 1) {
			$this->db->select('id')
				->from('access_'.$type)
				->where('name', $path[0]);
		 } else {
			//if the last item in the path is a number
			if (is_numeric($path[$path_count-1])) {
				$foreign_key = array_pop($path);
				$path_count--;
			}

			$this->db->select('tb'.$path_count.'.id')
				->from('access_'.$type.' AS tb1');

			for($x=1; $x<=$path_count; $x++) {
				$this->db->join('access_'.$type.' AS tb'.($x+1),
						'tb'.($x+1).'.parent_id=tb'.($x).'.id',
						'LEFT');
			}

			foreach($path AS $x=>$p) {
				$this->db->where('tb'.($x+1).'.name', $p);
			}

			//if the last item in the path is a number
			if (isset($foreign_key) && is_numeric($foreign_key)) {
				$this->db->where('tb'.($x+1).'.foreign_key', $foreign_key);
			}
		}

		$rs = $this->db->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();
		return $result['id'];
	}

	//Gets the Foreign ID of an RO/CO object based on a path.
	//Examples of paths:
	// helpdesk/a_helpdesk
	// helpdesk/a_helpdesk/10 - an item with an ID of 10 in the a_helpdesk table
	function get_foreign_id_by_path($path, $type) {
		$this->check_type($type);

		$orig_path = $path;
		$path = explode('/', $path);
		$path_count = count($path);

		if ($path_count == 1) {
			$this->db->select('foreign_key')
				->from('access_'.$type)
				->where('name', $path[0]);
		 } else {
			//if the last item in the path is a number
			if (is_numeric($path[$path_count-1])) {
				$foreign_key = array_pop($path);
				$path_count--;
			}

			$this->db->select('tb'.$path_count.'.foreign_key')
				->from('access_'.$type.' AS tb1');

			for($x=1; $x<=$path_count; $x++) {
				$this->db->join('access_'.$type.' AS tb'.($x+1),
						'tb'.($x+1).'.parent_id=tb'.($x).'.id',
						'LEFT');
			}

			foreach($path AS $x=>$p) {
				$this->db->where('tb'.($x+1).'.name', $p);
			}

			//if the last item in the path is a number
			if (isset($foreign_key) && is_numeric($foreign_key)) {
				$this->db->where('tb'.($x+1).'.foreign_key', $foreign_key);
			}
		}

		$rs = $this->db->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();
		return $result['foreign_key'];
	}


	//finds the parent_id of a CO for an individual row of data
	function find_co_parent_id($co) {
		$rs = $this->db->select('id')
				->from('access_co')
				->where('parent_id != 1')
				->where('name', $co['name'])
				->where('foreign_key', 0)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();
		return $result['id'];
	}

	private function check_type($type) {
		$acceptable_types = array('co','ro');
		if (!in_array($type, $acceptable_types)) {
			die('Unacceptable Type. Only "co" or "ro" accepted. Received: '.$type);
		}
	}

	function install() {
		// ===================================================================== INSTALLTING CO
		$tables = array(
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

		$apps = $this->AppM->get_list();

		foreach($apps AS $app) {
			$data = array(
				'name' => $app['name'],
			);
			$parent_id = $this->create_node(1, $data, 'co');

			if (isset($tables[$app['name']])) {
				$data = array();
				foreach($tables[$app['name']] AS $t) {
					$data[] = array(
						'parent_id' => $parent_id,
						'name' => $t,
					);
				}
				if (count($data) > 0) $this->create_nodes($parent_id, $data, 'co');
			}
		}

		// ===================================================================== INSTALLTING RO
		$roles = $this->get_roles_list();

		foreach($roles AS $role) {
			$data = array(
				'name' => $role['name'],
				'foreign_key' => $role['code'],
			);
			$role_parent_id = $this->create_node(1, $data, 'ro');

			//if there are users in this role, create an RO for them
			if ($role['code']!=1) {	//skip the Staff role
				$rs = $this->db->select('card_id')
						->from('access_user_role')
						->where('role_id', $role['code'])
						->get();
				if ($rs->num_rows() > 0) {
					$data = array();
					foreach($rs->result_array() AS $r) {
						$data[] = array(
							'name' => 'card',
							'foreign_key' => $r['card_id']
						);
					}
					$this->create_nodes($role_parent_id, $data, 'ro');
				}
			}

			$subroles = $this->get_subroles($role['code']);
			if (count($subroles) == 0) continue;

			foreach($subroles AS $sr) {
				$data = array(
					'name' => $sr['name'],
					'foreign_key' => $sr['id'],
				);
				$subrole_parent_id = $this->create_node($role_parent_id, $data, 'ro');

				$rs = $this->db->select('card_id')
					->from('access_user_role_sub')
					->where('roles_sub_id', $sr['id'])
					->get();
				if ($rs->num_rows() > 0) {
					$data = array();
					foreach($rs->result_array() AS $r) {
						$data[] = array(
							'name' => 'card',
							'foreign_key' => $r['card_id']
						);
					}
					$this->create_nodes($subrole_parent_id, $data, 'ro');
				}
			}
		}
	}


	function assign_role($card_id, $role) {
		$role_id = $this->get_foreign_id_by_path($role, 'ro');

		$depth = count(explode('/', $role));

		if ($depth >= 2) {
			if ($depth == 3) {
				$arr_role = explode('/', $role);
				array_pop($arr_role);
				$main_role_id = $this->get_foreign_id_by_path(implode('/', $arr_role), 'ro');
			} else {
				$main_role_id = $role_id;
			}

			//is a role
			$existing_role_id = $this->get_card_role_id($card_id);
			if ($existing_role_id !== FALSE) {
				$this->errors[] = $this->lang->line('error-already_has_role');
				return FALSE;
			}
			$data = array(
				'card_id' => $card_id,
				'role_id' => $main_role_id,
			);

			$result = $this->db->insert('access_user_role', $data);
		}

		if ($depth == 3) {
			//is a subrole
			if (!$this->has_sub_role($card_id, $role_id)) {
				$data = array(
					'card_id' => $card_id,
					'roles_sub_id' => $role_id,
				);

				$result = $this->db->insert('access_user_role_sub', $data);
			} else {
				$result = TRUE;
			}
		}

		$parent_ro_id = $this->get_id_by_path($role, 'ro');
		$ro = array(
			'parent_id' => $parent_ro_id,
			'name' => 'card',
			'foreign_key' => $card_id,
		);
		$ro_id = $this->get_ro_id($ro);
		if ($ro_id === FALSE) {
			$this->create_node($parent_ro_id, $ro, 'ro');
		}

		return $result;
	}

	function get_card_role_id($card_id) {
		$rs = $this->db->select('role_id')
				->from('access_user_role')
				->where('card_id', $card_id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();
		return $result['role_id'];
	}

	function has_sub_role($card_id, $role_id) {
		$rs = $this->db->select('id')
				->from('access_user_role_sub')
				->where('card_id', $card_id)
				->where('roles_sub_id', $role_id)
				->limit(1)
				->get();

		return ($rs->num_rows() == 1);
	}


	function get_user_subroles($card_id){
		$rs = $this->db->select('usr.roles_sub_id, sr.name')
				->from('access_user_role_sub AS usr')
				->join('access_roles_sub AS sr', 'sr.id=usr.roles_sub_id', 'left')
				->where('usr.card_id', $card_id)
				->get();

		if ($rs->num_rows() == 0) return array();

		$result = array();
		foreach ($rs->result_array() as $r) {
			$result[$r['roles_sub_id']] = $r['name'];
		}

		return $result;
	}

	//get this user's role ID, name, and the relevant role data id.
	function get_user_role_info($card_id){
		$rs = $this->db->select('ur.role_id, r.name')
				->from('access_user_role AS ur')
				->join('global_setting.access_roles AS r', 'r.code=ur.role_id')
				->where('ur.card_id', $card_id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) {
			if (ENVIRONMENT == 'development') {
				echo $this->db->last_query();
			}

			die('error loading role info.');
		}

		$result = array();
		$temp = $rs->row_array();
		$result['name'] = $temp['name'];
		$result['role_id'] = $temp['role_id'];

		if (in_array($temp['role_id'], array(3,5,6))) return $result;

		$this->db->select();

		switch($temp['role_id']){
			case '1':
				$this->db->from('staff');
				break;
			case '2':
				$this->db->from('client');
				break;
			case '4':
				$this->db->from('vendor');
				break;
		}

		$rs = $this->db->where('card_id', $card_id)
				->limit(1)
				->get();

		if ($rs->num_rows()>0) {
			$temp = $rs->row_array();
			$result['role_data_id'] = $temp['id'];
			$result['role_data_id_encoded'] = encode_id($temp['id']);
			$result['details'] = $temp;
		}

		return $result;
	}







}