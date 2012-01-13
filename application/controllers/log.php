<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class log extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function add_follow() {
		$values = array(
			'following_app_data_id' => $this->input->get('following_app_data_id'),
			'following_card_id' => $this->UserM->info['cardid'],
			'following_log_type_id' => $this->input->get('following_log_type_id'),
		);
		$i = $this->LogM->insert_follow($values);
		if ($i) {
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(array('message' => 'following')));
		} else {
			$this->output->set_header('HTTP/1.1 500');
		}
	}

	function add_favorite() {
		$values = array(
			'favorite_cardid' => $this->UserM->info['cardid'],
			'favorite_name' => $this->input->get('fav_name'),
			'favorite_furi' => $this->input->get('url'),
		);
		$i = $this->LogM->insert_favorite($values);
		if ($i) {
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(array('message' => 'favorited')));
		} else {
			$this->output->set_header('HTTP/1.1 500');
		}
	}
}
