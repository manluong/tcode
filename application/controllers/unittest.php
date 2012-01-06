<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unittest extends MY_Controller {

	function __construct() {
		parent::__construct();

		if (ENVIRONMENT == 'production') exit;
	}

	function index() {
		echo 'Test Options';
		echo '<p><a href="/unittest/acl">ACL</a></p>';
	}

	function acl() {
		$sett_randomize_tests = TRUE;

		$cuid = $this->UserM->get_cardid();
		echo 'Current User Card ID: '.$cuid.'<br />';
		echo 'Current SubGroup: <pre>', print_r($this->UserM->info['subgp'], TRUE), '</pre><br />';
		echo 'Current Master Group: <pre>', print_r($this->UserM->info['accessgp'], TRUE), '</pre><br />';

		$tests = array(
			1 => array(
				'admin_client_search_1' => 'no',
				'write_client_search_12' => 'no',
				'read_client_search_12' => 'yes',
				'admin_client_search_88' => 'no',
				'admin_client_list_12' => 'no',
				'admin_client_list_88' => 'yes',
			)
		);

		echo '<p>ACL Tests:</p>';

		$test_set = $tests[$cuid];
		if ($sett_randomize_tests) $test_set = shuffle_assoc($test_set);

		echo '<table border="1" cellpadding="5">';
		echo '<tr><th>Action</th><th>App</th><th>ActionGroup</th><th>Data ID</th><th>Expected Result</th><th>Result</th>';
		foreach($test_set AS $t => $answer) {
			$params = explode('_', $t);
			$test_result = ($this->ACLM->check($params[0], $params[1], $params[2], $params[3])) ? 'yes' : 'no';

			echo ($test_result == $answer) ? '<tr style="background-color:#CFC;">' : '<tr style="background-color:#FCC;">';
			echo '<td>',$params[0],'</td><td>',$params[1],'</td><td>',$params[2],'</td><td>',$params[3];
			echo '</td><td>',$answer,'</td><td>',$test_result,'</td></tr>';
		}


	}
}


function shuffle_assoc( $array ) {
   $keys = array_keys( $array );
   shuffle( $keys );
   return array_merge( array_flip( $keys ) , $array );
}