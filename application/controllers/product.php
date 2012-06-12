<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('ProductM');
		$this->load->model('Product_CategoryM');

		$this->data['app_menu'] = array(
			array('url' => '/product', 'extra' => '', 'title' => 'List'),
			array('url' => '/product/add', 'extra' => '', 'title' => 'New')
		);
	}

	function index() {
		$this->data['breadcrumb'] = array(array('title' => 'List'));
		$this->data['content'] = $this->load->view(get_template().'/product/index', NULL, TRUE);
		$this->_do_output();
	}

	function search() {
		$data = array();
		foreach ($this->Product_CategoryM->get_list() as $r) {
			$data[] = array(
				'type' => 'category',
				'id' => $r['id'],
				'name' => $r['name'],
				'price' => ''
			);
		}

		foreach ($this->ProductM->get_list() as $r) {
			$data[] = array(
				'type' => 'product',
				'id' => $r['id'],
				'name' => $r['name'],
				'price' => ''
			);
		}

		$this->RespM->set_success(TRUE)
			->set_details($data)
			->output_json();
	}

	function view() {
		$this->data['breadcrumb'] = array(array('title' => 'View'));
		$this->data['content'] = $this->load->view(get_template().'/product/view', NULL, TRUE);
		$this->_do_output();
	}

	function add() {
		$this->data['breadcrumb'] = array(array('title' => 'New'));
		$this->data['content'] = $this->load->view(get_template().'/product/new', NULL, TRUE);
		$this->_do_output();
	}

	function edit() {
		$this->data['breadcrumb'] = array(array('title' => 'Edit'));
		$this->data['content'] = $this->load->view(get_template().'/product/edit', NULL, TRUE);
		$this->_do_output();
	}

	function save() {
		$data = $this->ProductM->get_form_data();

		if ($this->ProductM->is_valid($data) == FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->ProductM->get_error_string())
				->set_details($this->ProductM->field_errors)
				->output_json();
			exit;
		}

		$this->ProductM->sett_skip_validation = TRUE;
		$product_id = $this->ProductM->save($data);
		if ($product_id === FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->ProductM->get_error_string())
				->set_details($this->ProductM->field_errors)
				->output_json();
			exit;
		}

		$this->RespM->set_success(TRUE)
			->set_details('/product/view/'.$product_id)
			->output_json();
	}

	function category_save() {
		$data = $this->Product_CategoryM->get_form_data();

		if ($this->Product_CategoryM->is_valid($data) == FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->Product_CategoryM->get_error_string())
				->set_details($this->Product_CategoryM->field_errors)
				->output_json();
			exit;
		}

		$this->Product_CategoryM->sett_skip_validation = TRUE;
		$category_id = $this->Product_CategoryM->save($data);
		if ($category_id === FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->Product_CategoryM->get_error_string())
				->set_details($this->Product_CategoryM->field_errors)
				->output_json();
			exit;
		}

		$this->RespM->set_success(TRUE)
			->set_details('/product/category_view/'.$category_id)
			->output_json();
	}
}