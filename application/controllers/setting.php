<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends MY_Controller {
	var $app_list = array(
		array(
			'core_apps_name' => 'general'
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
			if (file_exists(APPPATH.'/views/'.get_template().'/setting/configure_'.$a['core_apps_name'].'.php')) {
				$this->app_list[] = $a;
			}
		}

		$this->load->model('SettingM');
	}

	function index() {
		$html = array();

		$app_list['list'] = $this->app_list;
		$html['col_2'] = $this->load->view(get_template().'/setting/app_list', $app_list, TRUE);

		$html['col_1'] = 'Begin by selecting an application from the list on the left.';

		$data = array();
		$data['html'] = $this->load->view(get_template().'/element/content_layout_left', $html, TRUE);
		if ($this->is_ajax) {
			echo $data['html'];
			return;
		}
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Comments';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	function configure($app_name) {
		$this->verify_app($app_name);

		$html = array();

		$app_list['list'] = $this->app_list;
		$app_list['selected'] = $app_name;
		$html['col_2'] = $this->load->view(get_template().'/setting/app_list', $app_list, TRUE);

		//load the view file that has the configuration options
		$data_configure['app_name'] = $app_name;
		$data_configure['is_admin'] = $this->UserM->is_admin();
		$data_configure['settings'] = $this->SettingM->get_for_configuration($app_name);
		$data_configure['override_options'] = $this->override;
		$html['col_1'] = $this->load->view(get_template().'/setting/configure_'.$app_name, $data_configure, TRUE);

		$data = array();
		$data['html'] = $this->load->view(get_template().'/element/content_layout_left', $html, TRUE);
		if ($this->is_ajax) {
			echo $data['html'];
			return;
		}
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Comments';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
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
			$app_list[] = $app['core_apps_name'];
		}

		if (!in_array($app_name, $app_list)) redirect('/setting');
	}
}