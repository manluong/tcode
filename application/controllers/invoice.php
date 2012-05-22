<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('InvoiceM');
		$this->load->model('InvoiceItemM');
	}

	function index() {
		$total = $this->InvoiceM->get_min_max_invoice_total();
		$data = array(
			'total_min' => floor($total['min']),
			'total_max' => ceil($total['max'])
		);
		$this->data['content'] = $this->load->view(get_template().'/invoice/index', $data, TRUE);

		$this->_do_output();
	}

	function search() {
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$row_per_page = $this->input->post('row_per_page') ? $this->input->post('row_per_page') : 10;

		$search_param = array(
			'customer_id' => $this->input->post('customer_id'),
			'customer_name' => $this->input->post('customer_name'),
			'date_range' => $this->input->post('date_range'),
			'date_range_from' => $this->format_date($this->input->post('date_range_from')),
			'date_range_to' => $this->format_date($this->input->post('date_range_to')),
			'total_min' => $this->input->post('total_min'),
			'total_max' => $this->input->post('total_max'),
			'invoice_id' => $this->input->post('invoice_id'),
			'po_number' => $this->input->post('po_number'),
			'notes' => $this->input->post('notes'),
			'page' => $page,
			'row_per_page' => $row_per_page
		);

		$total_record = $this->InvoiceM->search($search_param, true);
		$data = array(
			'invoice_list' => $this->InvoiceM->search($search_param),
			'total_record' => $total_record,
			'current_page' => $page,
			'row_per_page' => $row_per_page,
			'max_page' => ($row_per_page == -1) ? 1 : ceil($total_record/$row_per_page)
		);

		$content = $this->load->view(get_template().'/invoice/search', $data, TRUE);
		echo $content;
	}

	function view($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			'invoice_items' => $this->InvoiceItemM->get_by_invoice_id($id),
			'customer_name' => '',
			'invoice_terms' => ''
		);

		if ($invoice['customer_card_id']) {
			$customer = $this->InvoiceM->get_customer_by_id($invoice['customer_card_id']);
			if ($customer) {
				$data['customer_name'] = $customer->nickname;
			}
		}

		if ($invoice['terms_id']) {
			$terms = $this->InvoiceM->get_terms_by_id($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms->content;
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$this->data['content'] = $this->load->view(get_template().'/invoice/view', $data, TRUE);

		$this->_do_output();
	}

	function pdf($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			'invoice_items' => $this->InvoiceItemM->get_by_invoice_id($id),
			'customer_name' => '',
			'invoice_terms' => ''
		);

		if ($invoice['customer_card_id']) {
			$customer = $this->InvoiceM->get_customer_by_id($invoice['customer_card_id']);
			if ($customer) {
				$data['customer_name'] = $customer->nickname;
			}
		}

		if ($invoice['terms_id']) {
			$terms = $this->InvoiceM->get_terms_by_id($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms->content;
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$content = $this->load->view(get_template().'/invoice/print', $data, TRUE);
		output_pdf($content);
	}

	function print_invoice($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			'invoice_items' => $this->InvoiceItemM->get_by_invoice_id($id),
			'customer_name' => '',
			'invoice_terms' => ''
		);

		if ($invoice['customer_card_id']) {
			$customer = $this->InvoiceM->get_customer_by_id($invoice['customer_card_id']);
			if ($customer) {
				$data['customer_name'] = $customer->nickname;
			}
		}

		if ($invoice['terms_id']) {
			$terms = $this->InvoiceM->get_terms_by_id($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms->content;
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$content = $this->load->view(get_template().'/invoice/print', $data, TRUE);
		echo $content;
	}

	function edit($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			'invoice_items' => $this->InvoiceItemM->get_by_invoice_id($id),
			'customer' => $this->InvoiceM->get_customer(),
			'price_type' => $this->InvoiceM->get_price_type(),
			'duration_type' => $this->InvoiceM->get_duration_type(),
			'terms' => $this->InvoiceM->get_terms(),
			'invoice_terms' => ''
		);

		if ($invoice['terms_id']) {
			$terms = $this->InvoiceM->get_terms_by_id($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms->content;
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$this->data['content'] = $this->load->view(get_template().'/invoice/edit', $data, TRUE);

		$this->_do_output();
	}

	function edit_save() {
		// get input data
		$post = $this->input->post();
		$invoice_id = $post['invoice_id'];

		$invoice_data = array(
			'id' => $invoice_id,
			'customer_card_id' => $post['customer_id'],
			'invoice_stamp' => $this->format_date($post['issue_date']),
			'payment_due_stamp' => $this->format_date($post['due_date']),
			'purchase_order_number' => $post['po_number'],
			'terms_id' => $post['terms_id'] ? $post['terms_id'] : null,
			'terms_content' => $post['terms_id'] ? null : $post['terms_content'],
			'memo' => $post['notes']
		);

		$invoice_item_data = array();
		foreach ($post['product_name'] as $index => $value) {
			if ($value) {
				$invoice_item_data[] = array(
					'invoice_id' => $invoice_id,
					'product_id' => $post['product_id'][$index],
					'description' => $post['description'][$index],
					'unit_price' => $post['unit_price'][$index],
					'quantity' => $post['qty'][$index],
					'discount' => $post['discount'][$index],
					'tax_id' => 0,
					'total' => $post['total'][$index],
					'price_type' => $post['price_type'][$index],
					'subscription_start_stamp' => $this->format_date($post['from'][$index]),
					'subscription_end_stamp' => $this->format_date($post['to'][$index]),
					'duration_type' => $post['duration'][$index]
				);
			}
		}

		// validate data
		$is_valid = true;
		$error = array();

		$is_valid = $is_valid && $this->InvoiceM->is_valid($invoice_data);
		$error = array_merge($error, $this->InvoiceM->field_errors);

		foreach ($invoice_item_data as $data) {
			$is_valid = $is_valid && $this->InvoiceItemM->is_valid($data);
			$error = array_merge($error, $this->InvoiceItemM->field_errors);
		}

		if (!$is_valid) {
			echo json_encode(array(
				'success' => false,
				'error' => $error
			));
			exit;
		}

		// save db
		$this->InvoiceM->save($invoice_data);
		$this->InvoiceItemM->delete_by_invoice_id($invoice_id);
		foreach ($invoice_item_data as $data) {
			$this->InvoiceItemM->save($data);
		}

		echo json_encode(array(
			'success' => true,
			'url' => '/invoice/view/'.$invoice_id
		));
		exit;
	}

	function add() {
		$data = array(
			'customer' => $this->InvoiceM->get_customer(),
			'price_type' => $this->InvoiceM->get_price_type(),
			'duration_type' => $this->InvoiceM->get_duration_type(),
			'terms' => $this->InvoiceM->get_terms()
		);

		$this->data['content'] = $this->load->view(get_template().'/invoice/new', $data, TRUE);

		$this->_do_output();
	}

	function add_save() {
		// get input data
		$post = $this->input->post();

		$invoice_data = array(
			'customer_card_id' => $post['customer_id'],
			'invoice_stamp' => $this->format_date($post['issue_date']),
			'payment_due_stamp' => $this->format_date($post['due_date']),
			'purchase_order_number' => $post['po_number'],
			'terms_id' => $post['terms_id'] ? $post['terms_id'] : null,
			'terms_content' => $post['terms_id'] ? null : $post['terms_content'],
			'memo' => $post['notes']
		);

		$invoice_item_data = array();
		foreach ($post['product_name'] as $index => $value) {
			if ($value) {
				$invoice_item_data[] = array(
					'product_id' => $post['product_id'][$index],
					'description' => $post['description'][$index],
					'unit_price' => $post['unit_price'][$index],
					'quantity' => $post['qty'][$index],
					'discount' => $post['discount'][$index],
					'tax_id' => 0,
					'total' => $post['total'][$index],
					'price_type' => $post['price_type'][$index],
					'subscription_start_stamp' => $this->format_date($post['from'][$index]),
					'subscription_end_stamp' => $this->format_date($post['to'][$index]),
					'duration_type' => $post['duration'][$index]
				);
			}
		}

		// validate data
		$is_valid = true;
		$error = array();

		$is_valid = $is_valid && $this->InvoiceM->is_valid($invoice_data);
		$error = array_merge($error, $this->InvoiceM->field_errors);

		foreach ($invoice_item_data as $data) {
			$is_valid = $is_valid && $this->InvoiceItemM->is_valid($data);
			$error = array_merge($error, $this->InvoiceItemM->field_errors);
		}

		if (!$is_valid) {
			echo json_encode(array(
				'success' => false,
				'error' => $error
			));
			exit;
		}

		// save db
		$invoice_id = $this->InvoiceM->save($invoice_data);
		foreach ($invoice_item_data as $data) {
			$data['invoice_id'] = $invoice_id;
			$this->InvoiceItemM->save($data);
		}

		echo json_encode(array(
			'success' => true,
			'url' => '/invoice/view/'.$invoice_id
		));
		exit;
	}

	function get_terms($id) {
		$content = '';
		$terms = $this->InvoiceM->get_terms_by_id($id);
		if ($terms) {
			$content = $terms->content;
		}
		echo $content;
	}

	function get_customer() {
		$term = $this->input->get('term');
		$customer_list = $this->InvoiceM->get_customer_by_name($term);

		$content = array();
		if ($customer_list) {
			foreach ($customer_list as $customer) {
				$content[] = array(
					'id' => $customer->id,
					'label' => $customer->nickname,
					'value' => $customer->nickname,
				);
			}
		}

		echo json_encode($content);
	}

	function get_product() {
		$term = $this->input->get('term');
		$product_list = $this->InvoiceM->get_product_by_name($term);

		$content = array();
		if ($product_list) {
			foreach ($product_list as $product) {
				$content[] = array(
					'product' => array(
						'id' => $product->a_product_id,
						'price' => (float)$product->a_product_price_price
					),
					'label' => $product->a_product_name,
					'value' => $product->a_product_name
				);
			}
		}

		echo json_encode($content);
	}

	private function format_date($date) {
		if (empty($date)) {
			return '';
		} else {
			return date('Y-m-d', strtotime($date));
		}
	}
}