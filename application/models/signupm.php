<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class SignupM extends CI_Model {
	var $messages = array();

	function __construct() {
		parent::__construct();
	}

	function setup_account($info) {
		$this->load->model('CardM');
		$this->load->model('AclM');
		$this->load->model('TenantM');

		$this->db->trans_start();

	//create a tenant record in the t_my database
		//save card details
		//TODO: save domain to another table

		$name = explode(' ', $info['name']);
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

		$card['addon_email'][] = array(
			'email' => $info['email'],
			'is_default' => 1
		);

		$card['addon_access_user'][] = array(
			'password' => $info['password'],
			'status' => 1
		);

		$card_id = $this->CardM->save($card);

		//Assign Client role
		$this->AclM->assign_role($card_id, 'DEFAULT/Client');

		$data = array(
			'card_id' => $card_id,
			'domain' => $info['domain']
		);
		$tenant_id = $this->TenantM->save($data);

	//create database and db user accounts, grant neccessary permissions.
		$db_name = 't_'.$info['domain'];

		//create tenant's database
		if ( ! $this->db->query('CREATE DATABASE '.$db_name.';') ) $this->messages[] = 'Unable to create database';

		//copy table structure and data from the 8force_template DB
		$rs = $this->db->query('SHOW TABLES FROM 8force_template');
		foreach($rs->result_array() AS $r) {
			$t = $r['Tables_in_8force_template'];
			if ( ! $this->db->query("CREATE TABLE $db_name.$t LIKE 8force_template.$t") ) $this->messages[] = 'Unable to create db table: '.$t;
			if ( ! $this->db->query("INSERT INTO $db_name.$t SELECT * FROM 8force_template.$t") ) $this->messages[] = 'Unable to copy table data: '.$t;
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
		$this->CardM->set_database($db_name);
		$this->AclM->set_database($db_name);

		$tenant_card_id = $this->CardM->save($card);

		//Setup RoCo and Assign Role
		$this->AclM->install();
		$this->AclM->assign_role($tenant_card_id, 'DEFAULT/Staff/Administrators');


		//Install Basic license, expiring in 30 days.
		$current_date = get_current_stamp();
		$current_timestamp = strtotime($current_date);
		$end_timestamp = $current_timestamp + (30*24*60*60);
		$end_stamp = parse_stamp($end_timestamp, 'MYSQL');

		$this->LicenseM->assign_license('Basic', $tenant_id, 0, get_current_stamp(), $end_stamp);
		$this->LicenseM->export_license_rules($tenant_id);

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

	function domain_exists($domain) {
		$rs = $this->db->select('domain')
				->from('tenant')
				->where('domain', $domain)
				->limit(1)
				->get();

		return ( $rs->num_rows() == 1 );
	}

	function get_messages() {
		return implode('<br />', $this->messages);
	}
}