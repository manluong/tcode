<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Status extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('StatusM');
	}

	function ajax_get_status() {
		$status = $this->StatusM->get_user_current();

		$response = array(
			'success' => TRUE,
			'data' => $status,
		);

		echo json_encode($response);
	}

	function ajax_get_availability_list() {
		$status = array_merge(array(array('id'=>'', 'name'=>'')), $this->StatusM->get_status_types());

		$response = array(
			'success' => TRUE,
			'data' => $status,
		);

		echo json_encode($response);
	}

	function ajax_update() {
		$status = array(
			'card_id' => $this->UserM->get_cardid(),
			'status_type_id' => $this->input->post('status_type_id'),
			'message' => $this->input->post('message'),
			'task_id' => $this->input->post('task_id'),
			'location_id' => $this->input->post('location_id'),
			'geo_lat' => $this->input->post('geo_lat'),
			'geo_lng' => $this->input->post('geo_lng'),
			'created_stamp' => get_current_stamp(),
		);

		foreach($status AS $k=>$v) {
			if ($v === FALSE) unset($status[$k]);
		}

		$this->StatusM->save($status);

		$response = array(
			'success' => TRUE,
			'data' => '',
		);

		echo json_encode($response);
	}

	function ajax_delete() {
		$status_id = $this->input->post('status_id');
		$card_id = $this->UserM->get_cardid();

		$this->StatusM->delete($status_id, $card_id);

		$new_status = $this->StatusM->get_user_current($card_id);

		$response = array(
			'success' => TRUE,
			'data' => $new_status,
		);

		echo json_encode($response);
	}


}