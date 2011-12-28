<?php

class TagsL {
	var $CI = '';

	var $app_id = '';
	var $app_data_id = '';

	var $TagsM = '';

	function __construct() {
		$this->CI =& get_instance();

		$this->app_id = $this->CI->url['app_id'];
		$this->app_data_id = $this->CI->url['id_plain'];

		$this->CI->load->model('TagsM');
		$this->TagsM =& $this->CI->TagsM;
	}




	//this should be the default function to call
	function get_html() {
		$data = array();
		$data['tags'] = $this->TagsM->get($this->app_id, $this->app_data_id);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		return $this->CI->load->view(get_template().'/tags/view', $data, TRUE);
	}


	function search($tag) {
		return $this->TagsM->search($tag, $this->app_id);
	}


	function get_cloud_html($limit=15, $app_id='') {
		$data = array();
		$data['tags'] = $this->TagsM->get_top($limit, $app_id);

		return $this->CI->load->view(get_template().'/tags/cloud', $data, TRUE);
	}





}
?>
