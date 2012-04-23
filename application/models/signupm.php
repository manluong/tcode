<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class SignupM extends CI_Model {
	var $messages = array();

	function __construct() {
		parent::__construct();
	}

	//TODO: Setup transactions?
	function setup_account($info) {

		$this->db->trans_start();

	//create a tenant record in the t_my database
		//save card details
		//TODO: save domain to another table
		$name = explode(' ', $info['name']);
		$data = array(
			'card_nick' => '',
			'card_title' => 0,
			'card_fname' => $name[0],
			'card_mname' => '',
			'card_lname' => ( isset($name[1]) ) ? $name[1] : '',
			'card_formatname' => '',
			'card_orgname' => $info['domain'],
			'card_orgnum' => '',
			'card_orgtitle' => '',
			'card_timezone' => 0,
			'card_deflang' => '',
		);
		if ( ! $this->db->insert('card', $data) ) $this->messages[] = 'Unable to create tenant card record.';
		$card_id = $this->db->insert_id();

		//allow login
		$data = array(
			'card_id' => $card_id,
			'password' => f_password_encrypt($info['password']),
			'status' => 1,
			'created_stamp' => get_current_stamp(),
			'active_stamp' => get_current_stamp(),
		);
		if ( ! $this->db->insert('access_user', $data) ) $this->messages[] = 'Unable to create tenant login record';
		$user_id = $this->db->insert_id();

		//assign to client group
		$data = array(
			'card_id' => $card_id,
			'role_id' => 3,
		);
		if ( ! $this->db->insert('access_user_role', $data) ) $this->messages[] = 'Unable to assign to client group';

	//create database and db user accounts, grant neccessary permissions.
		$db_name = 't_'.$info['domain'];

		//create tenant's database
		if ( ! $this->db->query('CREATE DATABASE '.$db_name.';') ) $this->messages[] = 'Unable to create database';

		//copy table structure from the 8force_template DB
		$rs = $this->db->query('SHOW TABLES FROM 8force_template');
		foreach($rs->result_array() AS $r) {
			$t = $r['Tables_in_8force_template'];
			if ( ! $this->db->query("CREATE TABLE $db_name.$t LIKE 8force_template.$t") ) $this->messages[] = 'Unable to create db table: '.$t;
		}

		//create tenant's db user account
		if ( ! $this->db->query("CREATE USER '$db_name'@'%' IDENTIFIED BY '721hewX';")) $this->messages[] = 'Unable to create db user account';

		//grant db privileges
		$sql = "GRANT SELECT, INSERT, UPDATE, DELETE ON $db_name.* TO '$db_name'@'%'";
		$sql .= " WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;";
		if ( ! $this->db->query($sql) ) $this->messages[] = 'Unable to grant privileges to db';

		//grant db privileges to global_setting db
		$sql = "GRANT SELECT, INSERT, UPDATE, DELETE ON global_setting.* TO '$db_name'@'%'";
		$sql .= " WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;";
		if ( ! $this->db->query($sql) ) $this->messages[] = 'Unable to grant privileges to global_setting db';

	//sets up tenant admin's card id and login account in the tenant's db
		$db_name = 't_'.$info['domain'];

		//save card details
		$data = array(
			'card_nick' => '',
			'card_title' => 0,
			'card_fname' => $name[0],
			'card_mname' => '',
			'card_lname' => ( isset($name[1]) ) ? $name[1] : '',
			'card_formatname' => '',
			'card_orgname' => '',
			'card_orgnum' => '',
			'card_orgtitle' => '',
			'card_timezone' => 0,
			'card_deflang' => '',
		);
		if ( ! $this->db->insert($db_name.'.card', $data) ) $this->messages[] = 'Unable to create card in tenant\'s DB';
		$card_id = $this->db->insert_id();

		//allow login
		$data = array(
			'card_id' => $card_id,
			'password' => f_password_encrypt($info['password']),
			'status' => 1,
			'created_stamp' => get_current_stamp(),
			'active_stamp' => get_current_stamp(),
		);
		if ( ! $this->db->insert($db_name.'.access_user', $data) ) $this->messages[] = 'Unable to create login record';
		$user_id = $this->db->insert_id();

		//assign to admin group
		$data = array(
			'card_id' => $card_id,
			'role_id' => 1,
		);
		if ( ! $this->db->insert($db_name.'.access_link', $data) ) $this->messages[] = 'Unable to assign to admin group';

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function validate_details($info) {
		$result = TRUE;

		$fields = array('name', 'email', 'domain', 'password');

		foreach($fields AS $f) {
			if (!isset($info[$f]) || $info[$f] === FALSE || strlen($info[$f])==0) {
				$this->messages[$f] = ucfirst($f).' must not be blank';
				$result = FALSE;
			}
		}

		if (!isset($this->messages['domain']) && $this->domain_exists($info['domain'])) {
			$this->messages['domain'] = 'This domain has already been taken up.';
			$result = FALSE;
		}

		return $result;
	}

	//TODO: there's a table for this.
	function domain_exists($domain) {
		$rs = $this->db->select('card_orgname')
				->from('card')
				->where('card_orgname', $domain)
				->limit(1)
				->get();

		return ( $rs->num_rows() == 1 );
	}

	function get_messages() {
		return implode('<br />', $this->messages);
	}
}