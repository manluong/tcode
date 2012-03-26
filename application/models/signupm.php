<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class RegisterM extends CI_Model {
	function __construct() {
		parent::__construct();


	}

	//TODO: Setup transactions?
	function setup_account($name, $email, $domain, $username, $password) {
		$this->create_tenant($name, $email, $domain, $username, $password);
		$this->create_tenant_database($domain);
		$this->create_tenant_account($name, $email, $domain, $username, $password);

		return TRUE;
	}

	function create_tenant($name, $email, $domain, $username, $password) {
		$name = explode(' ', $name);
		$data = array(
			'card_nick' => '',
			'card_title' => 0,
			'card_fname' => $name[0],
			'card_mname' => '',
			'card_lname' => ( isset($name[1]) ) ? $name[1] : '',
			'card_formatname' => '',
			'card_orgname' => $domain,
			'card_orgnum' => '',
			'card_orgtitle' => '',
			'card_timezone' => 0,
			'card_deflang' => '',
		);
		$this->db->insert('card', $data);
		$card_id = $this->db->insert_id();

		$data = array(
			'access_user_username' => $username,
			'access_user_cardid' => $card_id,
			'access_user_status' => 1,
			'access_user_datecreate' => get_current_stamp(),
			'access_user_dateactive' => get_current_stamp(),
		);
		$this->db->insert('access_user', $data);
		$user_id = $this->db->insert_id();

		$data = array(
			'access_link_cardid' => $card_id,
			'access_link_gpmaster' => 3,
		);
		$this->db->insert('access_link', $data);

		$data = array(
			'access_p_uid' => $user_id,
			'access_p_p' => f_password_encrypt($password),
		);
		$this->db->insert('access_p', $data);
	}

	function create_tenant_database($domain) {
		$db_name = 't_'.$domain;

		//create tenant's database
		$this->db->query('CREATE DATABASE '.$db_name.';');

		//copy table structure from the current database which should be t_my
		$rs = $this->db->query('SHOW TABLES');
		foreach($rs->result_array() AS $r) {
			$t = $r['Tables_in_t_my'];
			$this->db->query("CREATE TABLE $db_name.$t LIKE t_my.$t");
		}

		//create tenant's db user account
		$this->db->query("CREATE USER '$db_name'@'%' IDENTIFIED BY '721hewX';");

		//grant db privileges
		$sql = "GRANT SELECT, INSERT, UPDATE, DELETE ON $db_name.* TO '$db_name'@'%' ";
		$sql .= " WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;";
		$this->db->query($sql);

		//grant db privileges to global_setting db
		$sql = "GRANT SELECT, INSERT, UPDATE, DELETE ON global_setting.* TO '$db_name'@'%' ";
		$sql .= " WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;";
		$this->db->query($sql);
	}

	//sets up tenant admin's card id and login account in the tenant's db
	function create_tenant_account($name, $email, $domain, $username, $password) {
		$db_name = 't_'.$domain;

		$name = explode(' ', $name);
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
		$this->db->insert($db_name.'.card', $data);
		$card_id = $this->db->insert_id();

		$data = array(
			'access_user_username' => $username,
			'access_user_cardid' => $card_id,
			'access_user_status' => 1,
			'access_user_datecreate' => get_current_stamp(),
			'access_user_dateactive' => get_current_stamp(),
		);
		$this->db->insert($db_name.'.access_user', $data);
		$user_id = $this->db->insert_id();

		$data = array(
			'access_link_cardid' => $card_id,
			'access_link_gpmaster' => 1,
		);
		$this->db->insert($db_name.'.access_link', $data);

		$data = array(
			'access_p_uid' => $user_id,
			'access_p_p' => f_password_encrypt($password),
		);
		$this->db->insert($db_name.'.access_p', $data);
	}
}