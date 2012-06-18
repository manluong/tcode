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
		$content = array(
			'parent_id' => 0
		);

		$this->data['content'] = $this->load->view(get_template().'/product/index', $content, TRUE);
		$this->_do_output();
	}

	function category($parent_id) {
		$content = array(
			'parent_id' => $parent_id
		);

		$this->data['breadcrumb'] = $this->get_breadcrumb($parent_id);
		$this->data['app_menu'][0]['url'] = '/product/category/'.$parent_id;
		$this->data['app_menu'][1]['url'] = '/product/add/category/'.$parent_id;
		$this->data['content'] = $this->load->view(get_template().'/product/index', $content, TRUE);
		$this->_do_output();
	}

	function search() {
		$parent_id = $this->input->post('parent_id');

		$data = array();
		if ($category_list = $this->Product_CategoryM->get_sub_category($parent_id)) {
			foreach ($category_list as $r) {
				$data[] = array(
					'type' => 'category',
					'id' => $r['id'],
					'name' => $r['name'],
					'list_url' => '/product/category/'.$r['id'],
					'view_url' => '/product/category_view/'.$r['id']
				);
			}
		}

		if ($product_list = $this->ProductM->get_product_in_category($parent_id)) {
			foreach ($product_list as $r) {
				$data[] = array(
					'type' => 'product',
					'id' => $r['id'],
					'name' => $r['name'],
					'price' => isset($r['addon_price']) ? $r['addon_price'][0]['amount'] : '',
					'view_url' => '/product/view/'.$r['id']
				);
			}
		}

		$this->RespM->set_success(TRUE)
			->set_details($data)
			->output_json();
	}

	function view($id) {
		$product = $this->ProductM->get($id);

		$breadcrumb = $this->get_breadcrumb($product['category_id']);
		$parent_name = '';
		foreach ($breadcrumb as $bc) {
			$parent_name .= $bc['title'].' > ';
		}

		$content = array(
			'product' => $product,
			'parent_name' => rtrim($parent_name, ' >')
		);

		$this->data['breadcrumb'] = array_merge($breadcrumb, array(array('title' => $product['name'])));
		$this->data['app_menu'][0]['url'] = '/product'.($product['category_id'] ? '/category/'.$product['category_id'] : '');
		$this->data['app_menu'][1]['url'] = '/product/add'.($product['category_id'] ? '/category/'.$product['category_id'] : '');
		$this->data['content'] = $this->load->view(get_template().'/product/view', $content, TRUE);
		$this->_do_output();
	}

	function category_view($id) {
		$category = $this->Product_CategoryM->get($id);

		$breadcrumb = $this->get_breadcrumb($category['parent_id']);
		$parent_name = '';
		foreach ($breadcrumb as $bc) {
			$parent_name .= $bc['title'].' > ';
		}

		$content = array(
			'category' => $category,
			'parent_name' => rtrim($parent_name, ' >')
		);

		$this->data['breadcrumb'] = array_merge($breadcrumb, array(array('title' => $category['name'])));
		$this->data['app_menu'][0]['url'] = '/product'.($category['parent_id'] ? '/category/'.$category['parent_id'] : '');
		$this->data['app_menu'][1]['url'] = '/product/add'.($category['parent_id'] ? '/category/'.$category['parent_id'] : '');
		$this->data['content'] = $this->load->view(get_template().'/product/category_view', $content, TRUE);
		$this->_do_output();
	}

	function add() {
		$parent_id = 0;
		if ($this->uri->segment(3) == 'category') {
			$parent_id = $this->uri->segment(4);
		}

		$breadcrumb = $this->get_breadcrumb($parent_id);
		$parent_name = '';
		foreach ($breadcrumb as $bc) {
			$parent_name .= $bc['title'].' > ';
		}

		$content = array(
			'parent_id' => $parent_id,
			'parent_name' => rtrim($parent_name, ' >')
		);

		$this->data['breadcrumb'] = array_merge($breadcrumb, array(array('title' => 'New')));
		$this->data['app_menu'][0]['url'] = '/product'.($parent_id ? '/category/'.$parent_id : '');
		$this->data['app_menu'][1]['url'] = '/product/add'.($parent_id ? '/category/'.$parent_id : '');
		$this->data['content'] = $this->load->view(get_template().'/product/new', $content, TRUE);
		$this->_do_output();
	}

	function edit($id) {
		$product = $this->ProductM->get($id);

		$breadcrumb = $this->get_breadcrumb($product['category_id']);
		$parent_name = '';
		foreach ($breadcrumb as $bc) {
			$parent_name .= $bc['title'].' > ';
		}

		$content = array(
			'product' => $product,
			'parent_name' => rtrim($parent_name, ' >')
		);

		echo $this->load->view(get_template().'/product/edit', $content, TRUE);
	}

	function category_edit($id) {
		$category = $this->Product_CategoryM->get($id);

		$breadcrumb = $this->get_breadcrumb($category['parent_id']);
		$parent_name = '';
		foreach ($breadcrumb as $bc) {
			$parent_name .= $bc['title'].' > ';
		}

		$content = array(
			'category' => $category,
			'parent_name' => rtrim($parent_name, ' >')
		);

		echo $this->load->view(get_template().'/product/category_edit', $content, TRUE);
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

	private function get_breadcrumb($category_id) {
		$breadcrumb = array();

		while ($category_id) {
			$category = $this->Product_CategoryM->get($category_id);
			if ($category) {
				$breadcrumb[] = array('title' => $category['name'], 'url' => '/product/category/'.$category['id']);
				$category_id = $category['parent_id'];
			} else {
				break;
			}
		}

		return array_reverse($breadcrumb);
	}
}