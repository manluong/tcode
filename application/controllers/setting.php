<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends MY_Controller {
	var $app_list = array();

	function __construct() {
		parent::__construct();

		$this->app_list = $this->AppM->get_apps();
	}

	function index() {
		$data_app_list['list'] = $this->app_list;
		$data['col_2'] = $this->load->view(get_template().'/setting/app_list', $data_app_list, TRUE);

		$data['col_1'] = 'Begin by selecting an application from the list on the left.';

		$this->load->view(get_template().'/element/content_layout_left', $data);
	}

	function configure($app_name) {
		$data_app_list['list'] = $this->app_list;
		$data_app_list['selected'] = $app_name;
		$data['col_2'] = $this->load->view(get_template().'/setting/app_list', $data_app_list, TRUE);

		//make sure the $app_name is valid
		$app_list = array();
		foreach($this->app_list AS $app) {
			$app_list[] = $app['core_apps_name'];
		}
		if (!in_array($app_name, $app_list)) {
			redirect('/setting');
		}

		//load the view file that has the configuration options
		$data_configure['app_name'] = $app_name;
		$data_configure['is_admin'] = $this->UserM->is_admin();
		//$data_configure['saved'] = $this->SettingM->get_saved_values($app_name);
		$data['col_1'] = $this->load->view(get_template().'/setting/configure_'.$app_name, $data_configure, TRUE);

		$this->load->view(get_template().'/element/content_layout_left', $data);
	}

	function save($app_name) {
		$app_list = array();
		foreach($this->app_list AS $app) {
			$app_list[] = $app['core_apps_name'];
		}
		if (!in_array($app_name, $app_list)) {
			redirect('/setting');
		}

		//save form data
	}
}