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

		if ($app_name == 'helpdesk') {
			$data_configure['priority_options'] = $this->SettingM->get_options_for_configuration($app_name, 'priority');
			$data_configure['case_type_options'] = $this->SettingM->get_options_for_configuration($app_name, 'case_type');
		}

		$this->load->view(get_template().'/setting/configure_'.$app_name, $data_configure);
	}

	function ajax_save($app_name) {
		$this->verify_app($app_name);

		$this->SettingM->save($app_name);

		$this->RespM->set_success(TRUE)
				->output_json();
	}

	//Each app's controller will have a function like this for editing options
	/*
	function ajax_configure_options($app_name, $name) {
		$this->verify_app($app_name);

		$data_configure['app_name'] = $app_name;
		$data_configure['is_admin'] = $this->UserM->is_admin();
		$data_configure['settings'] = $this->SettingM->get_options_for_configuration($app_name, $name);

		$this->load->view(get_template().'/setting/edit_options', $data_configure);
	}
	 */

	//Save only the value and sort_order
	//FORM POST: value[id], sort_order[id]
	function ajax_save_options($app_name, $name) {
		$this->verify_app($app_name);

		$this->SettingM->save_options($app_name, $name);

		$results_array = $this->db->select('id, value, sort_order, language_key')
					->from('core_select')
					->where('id', $this->input->post('id'))
					->get()
					->result_array();

		$this->RespM->set_success(TRUE)
				->set_details(isset($results_array) ? $results_array : array())
				->output_json();
	}

	//FORM POST: app_name, name, value, sort_order
	function ajax_add_option() {
		$success = FALSE;

// 		if ($this->UserM->is_admin()) {
			$app_id = $this->AppM->get_id($this->input->post('app_name'));

			$data = array(
				'app_id' => $app_id,
				'name' => $this->input->post('name'),
				'value' => $this->input->post('value'),
				'sort_order' => $this->input->post('sort_order'),
			);

			$new_id = $this->SettingM->add_option($data);

			$results_array = $this->db->select('id, value, sort_order, language_key')
					->from('core_select')
					->where('id', $new_id)
					->get()
					->result_array();
// 		}

		$success = ($new_id !== FALSE);

		$this->RespM->set_success($success)
				->set_details(isset($results_array) ? $results_array : array())
				->output_json();
	}

	// Deleting options will be handled by each app's controller: <app_controller>/ajax_delete_option/<id>
	// The function in that controller will check for data that's using the option
	// If there's no data, then call the SettingM->delete_option() function to delete it
	/*
	function ajax_delete_option($id) {
		$result = FALSE;

		if ($this->UserM->is_admin()) {
			$result = $this->SettingM->delete_option($id);
		}

		$this->RespM->set_success($result)
				->output_json();
	}
	 */

	//make sure the $app_name is valid
	private function verify_app($app_name) {
		$app_list = array();
		foreach($this->app_list AS $app) {
			$app_list[] = $app['name'];
		}

		if (!in_array($app_name, $app_list)) redirect('/setting');
	}
}