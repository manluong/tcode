<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends MY_Controller {
	var $app_list = array(
		array(
			'name' => 'general'
		),
	);

	var $override = array(
		0 => 'User cannot override',
		1 => 'User can override',
	);

	function __construct() {
		parent::__construct();

		$apps = $this->AppM->get_apps();

		//add app to the list only if there's a configuration view file created for it.
		foreach($apps AS $a) {
			if (file_exists(APPPATH.'/views/'.get_template().'/setting/configure_'.$a['name'].'.php')) {
				$this->app_list[] = $a;
			}
		}

		$this->load->model('SettingM');
	}

	function index() {
		$app_list['list'] = $this->app_list;
		$this->data['content_left'] = $this->load->view(get_template().'/setting/app_list', $app_list, TRUE);
		$this->data['content_right'] = 'Begin by selecting an application from the list on the left.';

		$this->layout['type'] = 'right';

		$this->_do_output();
	}

	function configure($app_name) {
		$this->verify_app($app_name);

		$app_list['list'] = $this->app_list;
		$app_list['selected'] = $app_name;
		$this->data['content_left'] = $this->load->view(get_template().'/setting/app_list', $app_list, TRUE);

		//load the view file that has the configuration options
		$data_configure['app_name'] = $app_name;
		$data_configure['is_admin'] = $this->UserM->is_admin();
		$data_configure['settings'] = $this->SettingM->get_for_configuration($app_name);
		$data_configure['override_options'] = $this->override;
		$this->data['content_right'] = $this->load->view(get_template().'/setting/configure_'.$app_name, $data_configure, TRUE);

		$this->layout['type'] = 'right';

		$this->_do_output();
	}

	function save($app_name) {
		$this->verify_app($app_name);

		$this->SettingM->save($app_name);

		redirect('/setting/configure/'.$app_name);
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