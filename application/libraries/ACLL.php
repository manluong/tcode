<?php

class ACLL {
	var $CI = '';

	var $ACLM = '';

	function __construct() {
		$this->CI =& get_instance();

		$this->ACLM =& $this->CI->ACLM;

		$this->CI->load->helper('form');
	}


	function get_html($app='', $actiongp='', $app_data_id='') {
		if ($app == '') $app = $this->CI->url['app'];
		if ($actiongp == '') $actiongp = $this->AppM->get_group($this->CI->url['app'], $this->CI->url['action']);
		if ($app_data_id == '') $app_data_id = $this->CI->url['id_plain'];

		$data = array();
		$data['acl'] = $this->ACLM->get_acl($app, $actiongp, $app_data_id);

		return $this->CI->load->view(get_template().'/acl/view', $data, TRUE);
	}


/*
	function get_summary($app='', $actiongp='', $app_data_id='') {
		if ($app == '') $app = $this->CI->url['app'];
		if ($actiongp == '') $actiongp = $this->AppM->get_group($this->CI->url['app'], $this->CI->url['action']);
		if ($app_data_id == '') $app_data_id = $this->CI->url['id_plain'];

		$data = array();
		$data['acl'] = $this->ACLM->get_acl($app, $actiongp, $app_data_id);

		return $this->CI->load->view(get_template().'/acl/form_summary', $data, TRUE);
	}



	function get_form_single($app='', $actiongp='', $app_data_id='') {
		if ($app == '') $app = $this->CI->url['app'];
		if ($actiongp == '') $actiongp = $this->AppM->get_group($this->CI->url['app'], $this->CI->url['action']);
		if ($app_data_id == '') $app_data_id = $this->CI->url['id_plain'];

		$data = array();
		$data['acl'] = $this->ACLM->get_acl($app, $actiongp, $app_data_id);

		return $this->CI->load->view(get_template().'/acl/form_single', $data, TRUE);
	}
*/




}