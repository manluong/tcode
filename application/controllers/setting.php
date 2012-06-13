<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends MY_Controller {
	var $app_list = array(
		array(
			'name' => 'general',
		),
	);

	//apps here use their own controller to manage setting configurations
	var $custom_app_list = array(
		'tax' => array(
			'name' => 'tax',
			'url' => '/tax/ajax_setting'
		),
	);

	var $override = array(
		0 => 'User cannot override',
		1 => 'User can override',
	);

	function __construct() {
		parent::__construct();

		//App list, ACL controlled
		$apps = $this->AppM->get_apps();

		//add app to the list only if there's a configuration view file created for it.
		foreach($apps AS $a) {
			if (file_exists(APPPATH.'/views/'.get_template().'/setting/configure_'.$a['name'].'.php')) {
				$this->app_list[] = $a;
			}
		}

		//loop again but checking if the app will manage the settings in it's own controller
		foreach($apps AS $a) {
			if (isset($this->custom_app_list[$a['name']])) $this->app_list[] = $this->custom_app_list[$a['name']];
		}

		$this->load->model('SettingM');
	}

	function ajax_index() {
		$app_list['list'] = $this->app_list;
		$this->load->view(get_template().'/setting/app_list', $app_list);
	}

	function ajax_configure($app_name) {
		$this->verify_app($app_name);

		//load the view file that has the configuration options
		$data_configure['app_name'] = $app_name;
		$data_configure['is_admin'] = $this->UserM->is_admin();
		$data_configure['settings'] = $this->SettingM->get_for_configuration($app_name);
		$data_configure['override_options'] = $this->override;

		$this->load->view(get_template().'/setting/configure_'.$app_name, $data_configure);
	}

	function ajax_save($app_name) {
		$this->verify_app($app_name);

		$this->SettingM->save($app_name);

		$this->RespM->set_success(TRUE)
				->output_json();
	}

	//make sure the $app_name is valid
	private function verify_app($app_name) {
		$app_list = array();
		foreach($this->app_list AS $app) {
			$app_list[] = $app['name'];
		}

		if (!in_array($app_name, $app_list)) redirect('/setting');
	}
}