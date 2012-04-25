<?php

class MY_Controller extends CI_Controller {
	var $domain = '';
	var $language = 'english';

	var $url = array(
		'app' => '',
		'app_id' => '',

		'action' => '',
		'actiongp' => '',

		'id_plain' => 0,
		'id_encrypted' => 0,
		'id' => 0,	//original ID passed in by request

		'subaction' => '',
	);
	var $re_url = array(
		'app' => '',
		'action' => '',
		'id' => 0,
		'subaction' => '',
	);
	var $has_return = FALSE;

	var $data = array(
		//filled in by system automatically
		'title' => '8Force', //HTML <title>
		'company_name' => '',
		'company_logo' => '',
		'current_user' => array(),
		'sidebar' => '',
		'footer' => '',
		'app_list' => '',

		//to be filled in by the app
		'app_menu' => array(),
		'breadcrumb' => array(),
		'content' => '',
		'content_left' => '',
		'content_right' => '',
	);
	var $layout = array(
		'type' => 'full',	// full left right tmode split
		'javascript_src' => array(),
	);


	var $system_messages = array();

	// Needed for logM
	var $log_data = array(
		'start_time' => 0,
		'insert_id' => 0,
		'log_type' => array(),
		'saveid' => 0,
	);

	var $model = '';

	var $is_pjax = FALSE;
	var $is_mobile_app = FALSE;
	var $allow_unauthed_access = FALSE;

	var $eightforce_config = array();

	var $debug = array();

	//valid subactions
	var $subactions = array('a','v','e','d','l','s','as','es','ds');

	function __construct() {
		parent::__construct();

		if ($this->input->get_request_header('X-PJAX', TRUE) !== FALSE) $this->is_pjax = TRUE;

		$this->load->config('eightforce', TRUE);
		$this->eightforce_config = $this->config->item('eightforce');

		$this->setup_url();
		$this->setup_db();

		$this->check_mobile_app();

		$this->load->library('session');

		$this->load->model('UserM');
		$this->load->model('ACLM');
		$this->load->model('AppM');
		$this->load->model('LogM');
		$this->load->model('LicenseM');
		$this->load->model('RespM');

		$this->url['app_id'] = $this->AppM->get_id($this->url['app']);
		$this->url['actiongp'] = $this->AppM->get_group($this->url['app'], $this->url['action']);

		$this->UserM->setup();
		$this->setup_language();
		$this->LogM->start_log();
		$this->AppM->setup();
		$this->LicenseM->setup();

		if ($this->AppM->must_disable_plain_id()) $this->ACLM->check_id_encryption();
		$this->ACLM->check_app_access('basic');
		if (APP_ROLE == 'TSUB') {
			if ($this->LicenseM->has_restriction($this->url['app_id'], $this->url['actiongp'], 'access')) {
				$access = $this->LicenseM->get_restriction($this->url['app_id'], $this->url['actiongp'], 'access');
				if ($access == 0) die('Your license does not permit you to use this application.');
			}
		}


		//if (!$this->is_pjax) $this->output->enable_profiler(true);
	}

	function output() {
		//tasks to be performed before the app's controller file
		$this->data['current_user'] = $this->UserM->info;
		$this->data['app_list'] = array();
		$this->data['title'] = '8Force';

		$this->data['layout'] = $this->layout;

		$html = array();
		$html['content'] = $this->load->view(get_template().'/layout/'.$this->layout['type'], $this->data, TRUE);
		$html['debug'] = $this->debug;
		$html['sidebar'] = '';
		$html['breadcrumb'] = '';
		$html['app_menu'] = '';

		if ($this->is_pjax) {
			$this->load->view(get_template().'/page_ajax', $html);
		} else {
			if ($this->UserM->is_logged_in()) {
				$html['sidebar'] = $this->load->view(get_template().'/sidebar', $this->data, TRUE);
				$html['breadcrumb'] = $this->load->view(get_template().'/breadcrumb', $this->data, TRUE);
				$html['app_menu'] = $this->load->view(get_template().'/app_menu', $this->data, TRUE);
			}

			$this->load->view(get_template().'/page_full', $html);
		}
	}

	private function setup_url() {
		$this->url['app'] = $this->router->fetch_class();

		//if method is not pass in, action is set to "index" by CI
		$this->url['action'] = $this->router->fetch_method();

		$this->url['subaction'] = $this->uri->segment(4, '');
		if (!in_array($this->url['subaction'], $this->subactions)) $this->url['subaction'] = 'v';

		$this->url['id'] = $id = $this->uri->segment(3, 0);

		if (id_is_encrypted($id)) {
			$this->url['id_plain'] = decode_id($id);
			$this->url['id_encrypted'] = $id;
		} else {
			$this->url['id_plain'] = $id;
			$this->url['id_encrypted'] = encode_id($id);
		}

		$this->re_url['app'] = $this->input->get_post('re_app', TRUE);
		$this->re_url['action'] = $this->input->get_post('re_action', TRUE);
		$this->re_url['subaction'] = $this->input->get_post('re_subaction', TRUE);
		$this->re_url['id'] = $this->input->get_post('re_id', TRUE);

		if ($this->re_url['app'] !== FALSE && $this->re_url['action'] !== FALSE) $this->has_return = TRUE;

		if ($this->input->is_cli_request()) {
			$this->domain = 'my';
		} else {
			if (ENVIRONMENT != 'development') {
				$domain = explode('.', $_SERVER['SERVER_NAME']);
				//if ($domain[1]!=='8force' || $domain[2]!=='net') die('There is a problem with the domain name.');
				$this->domain = $domain[0];
			}
		}

		$this->debug['environment'] = ENVIRONMENT;

		//TBOSS - For internal use
		//TSUB - Tenant software
		if ($this->domain === 'my') {
			define('APP_ROLE', 'TBOSS');
			$this->debug['app_role'] = 'TBOSS';
		} else {
			define('APP_ROLE', 'TSUB');
			$this->debug['app_role'] = 'TSUB';
		}
	}

	private function setup_db() {
		if (ENVIRONMENT == 'development') return NULL;

		//load the default db settings in the configuration files
		include(APPPATH.'config/'.ENVIRONMENT.'/database.php');
		$config = $db['default'];

		//subdomain defines database table to use
		$config['database'] = 't_'.$this->domain;

		if (APP_ROLE == 'TSUB') {
			$config['username'] = 't_'.$this->domain;
		}

		if (APP_ROLE == 'TBOSS' && ENVIRONMENT == 'testing') {
			$config['database'] = 't_'.$this->domain.'2';
		}

		$this->debug['database'] = $config['database'];
		$this->debug['database_username'] = $config['username'];

		$this->load->database($config);
	}



	private function setup_language() {
		/*
		$this->load->model('LangM');
		$this->lang->initialise($this->LangM->active);
		$this->lang->load($this->LangM->get_array('core', $this->lang->lang_use));
		$this->lang->load($this->LangM->get_array($this->url['app'], $this->lang->lang_use));
		 */

		//Notes
		//1 Default english
		//2 if user is logged out check if there's a language setting cookie and use that lang
		//
		//3 if user is logged in, load his preferences and use that lang setting
		//
		//4 if the lang preferences differ from the setting in step2, update/create lang cookie
		//
		//5 load the language based on the final setting after going through the above steps
		//6 if user is logged in, check for any custom labels and load them over the default loaded language labels

		//get the default language specified in the config/config.php file
		$this->language = $this->config->item('language');

		//load the language file based on the final setting
		$this->lang->load('eightforce', $this->language);

	}

	private function check_mobile_app() {
		if ($this->input->is_cli_request()) return NULL;

		//detect mobile app or not
		$agent = trim($_SERVER['HTTP_USER_AGENT']);
		$mobile_app_user_agents = array('8force-ios', '8force-and');
		foreach($mobile_app_user_agents AS $maua) {
			if (strpos($agent, $maua) !== FALSE) $this->is_mobile_app = TRUE;
		}

		//change session timeout based on whether the requesting party is a mobile app or not
		if ($this->is_mobile_app) {
			$this->session->sess_expiration = 60*60*24*365*2;
			$this->session->sess_expire_on_close = FALSE;
		}
	}


	//basic API functions for all controllers
	function api_get() {
		$data = $this->model->get($this->url['id_plain']);

		$resp = array(
			'success' => TRUE,
			'data' => $data
		);

		echo json_encode($resp);
	}


	function api_list() {
		$data = $this->model->get_list();

		$resp = array(
			'success' => TRUE,
			'data' => $data
		);

		echo json_encode($resp);
	}



}