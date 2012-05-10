<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('DS_Invoice');
		$this->load->model('InvoiceM');
		$this->load->model('InvoiceItemM');
	}

	function index() {
		$this->data['content'] = $this->load->view(get_template().'/invoice/index', '', TRUE);

		$this->_do_output();
	}

	function search() {
		$search_param = array(
			'customer_id' => $this->input->post('customer_id'),
			'customer_name' => $this->input->post('customer_name'),
			'date_range' => $this->input->post('date_range'),
			'date_range_from' => $this->format_date($this->input->post('date_range_from')),
			'date_range_to' => $this->format_date($this->input->post('date_range_to')),
			'invoice_id' => $this->input->post('invoice_id'),
			'po_number' => $this->input->post('po_number'),
			'notes' => $this->input->post('notes')
		);

		$data['invoice_list'] = $this->InvoiceM->search($search_param);
		$content = $this->load->view(get_template().'/invoice/search', $data, TRUE);
		echo $content;
	}

	function view($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			'invoice_items' => $this->InvoiceItemM->getByInvoiceId($id),
			'customer_name' => '',
			'invoice_terms' => ''
		);

		if ($invoice['customer_card_id']) {
			$customer = $this->InvoiceM->getCustomerById($invoice['customer_card_id']);
			if ($customer) {
				$data['customer_name'] = $customer->nickname;
			}
		}

		if ($invoice['terms_id']) {
			$terms = $this->InvoiceM->getTermsById($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms->content;
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$this->data['content'] = $this->load->view(get_template().'/invoice/view', $data, TRUE);

		$this->_do_output();
	}

	function edit($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			'invoice_items' => $this->InvoiceItemM->getByInvoiceId($id),
			'customer' => $this->InvoiceM->getCustomer(),
			'tax' => $this->InvoiceM->getTax(),
			'terms' => $this->InvoiceM->getTerms(),
			'invoice_terms' => ''
		);

		if ($invoice['terms_id']) {
			$terms = $this->InvoiceM->getTermsById($invoice['terms_id']);
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
		$invoice_id = $this->input->post('invoice_id');
		$data = array(
			'id' => $invoice_id,
			'customer_card_id' => $this->input->post('customer_id'),
			'invoice_stamp' => $this->format_date($this->input->post('issue_date')),
			'payment_due_stamp' => $this->format_date($this->input->post('due_date')),
			'purchase_order_number' => $this->input->post('po_number'),
			'tax_id' => $this->input->post('tax_id'),
			'currency' => $this->input->post('currency'),
			'memo' => $this->input->post('notes')
		);

		if ($this->input->post('terms_id')) {
			$data['terms_id'] = $this->input->post('terms_id');
			$data['terms_content'] = null;
		} else {
			$data['terms_id'] = null;
			$data['terms_content'] = $this->input->post('terms_content');
		}

		$this->InvoiceM->save($data);

		$product = $this->input->post('product');
		$description = $this->input->post('description');
		$unit_price = $this->input->post('unit_price');
		$qty = $this->input->post('qty');
		$discount = $this->input->post('discount');
		$tax = $this->input->post('tax');
		$total = $this->input->post('total');
		$price_type = $this->input->post('price_type');
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		$duration = $this->input->post('duration');

		$this->InvoiceItemM->deleteByInvoiceId($invoice_id);

		foreach ($product as $index => $value) {
			if ($product[$index]) {
				$data = array(
					'invoice_id' => $invoice_id,
					'product_id' => $product[$index],
					'description' => $description[$index],
					'unit_price' => $unit_price[$index],
					'quantity' => $qty[$index],
					'discount' => $discount[$index],
					'tax_id' => $tax[$index],
					'total' => $total[$index],
					'price_type' => $price_type[$index],
					'subscription_start_stamp' => $this->format_date($from[$index]),
					'subscription_end_stamp' => $this->format_date($to[$index]),
					'duration_type' => $duration[$index]
				);

				$invoice_item_id = $this->InvoiceItemM->save($data);
			}
		}

		if ($invoice_id) {
			redirect('/invoice/view/'.$invoice_id);
		} else {
			$details['data'] = $this->InvoiceM->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Invoice Save')
				->set_details($details)
				->output_json();
	}

	function add() {
		$data = array(
			'customer' => $this->InvoiceM->getCustomer(),
			'tax' => $this->InvoiceM->getTax(),
			'terms' => $this->InvoiceM->getTerms()
		);

		$this->data['content'] = $this->load->view(get_template().'/invoice/new', $data, TRUE);

		$this->_do_output();
	}

	function add_save() {
		$data = array(
			'customer_card_id' => $this->input->post('customer_id'),
			'invoice_stamp' => $this->format_date($this->input->post('issue_date')),
			'payment_due_stamp' => $this->format_date($this->input->post('due_date')),
			'purchase_order_number' => $this->input->post('po_number'),
			'tax_id' => $this->input->post('tax_id'),
			'currency' => $this->input->post('currency'),
			'memo' => $this->input->post('notes')
		);

		if ($this->input->post('terms_id')) {
			$data['terms_id'] = $this->input->post('terms_id');
			$data['terms_content'] = null;
		} else {
			$data['terms_id'] = null;
			$data['terms_content'] = $this->input->post('terms_content');
		}

		$invoice_id = $this->InvoiceM->save($data);

		$product = $this->input->post('product');
		$description = $this->input->post('description');
		$unit_price = $this->input->post('unit_price');
		$qty = $this->input->post('qty');
		$discount = $this->input->post('discount');
		$tax = $this->input->post('tax');
		$total = $this->input->post('total');
		$price_type = $this->input->post('price_type');
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		$duration = $this->input->post('duration');

		foreach ($product as $index => $value) {
			if ($product[$index]) {
				$data = array(
					'invoice_id' => $invoice_id,
					'product_id' => $product[$index],
					'description' => $description[$index],
					'unit_price' => $unit_price[$index],
					'quantity' => $qty[$index],
					'discount' => $discount[$index],
					'tax_id' => $tax[$index],
					'total' => $total[$index],
					'price_type' => $price_type[$index],
					'subscription_start_stamp' => $this->format_date($from[$index]),
					'subscription_end_stamp' => $this->format_date($to[$index]),
					'duration_type' => $duration[$index]
				);

				$invoice_item_id = $this->InvoiceItemM->save($data);
			}
		}

		if ($invoice_id) {
			redirect('/invoice/view/'.$invoice_id);
		} else {
			$details['data'] = $this->InvoiceM->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Invoice Save')
				->set_details($details)
				->output_json();
	}

	function sendjson_list() {
		$this->DS_Invoice->set_subaction('l');

		$details = array(
			'columns' => $this->DS_Invoice->get_datatable_fields(),
			'data' => $this->DS_Invoice->get_datatable_data(),
			'ids' => $this->DS_Invoice->get_list_ids(),
			'setting' => array(
				'hidetitle' => 0,
			),
		);

		$this->RespM->set_message('sendjson_list')
				->set_type('list')
				->set_template('')
				->set_success(true)
				->set_title('Invoice List')
				->set_details($details)
				->output_json();
	}

	function sendjson_view($id) {
		$data = $this->DS_Invoice->set_subaction('v')
					->set_id($id)
					->get_view_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Edit',
					'type' => 'ajax',
					'url' => '/invoice/sendjson_edit/'.$id,
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			),
		);

		$this->RespM->set_message('sendjson_view')
				->set_type('view')
				->set_template('')
				->set_success(true)
				->set_title('Invoice View')
				->set_details($details)
				->output_json();
	}

	function sendjson_edit($id) {
		$data = $this->DS_Invoice->set_subaction('e')
					->set_id($id)
					->get_form_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Submit',
					'type' => 'submit',
					'url' => '/invoice/sendjson_save_edit/'.$id,
					'style' => 'default',
					'icon' => '',
				),
				array(
					'target' => '',
					'text' => 'Cancel',
					'type' => 'ajax',
					'url' => '/invoice/sendjson_view/'.$id,
					'style' => 'warning',
					'icon' => 'trash',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			)
		);

		$this->RespM->set_message('sendjson_edit')
				->set_type('form')
				->set_template('')
				->set_success(true)
				->set_title('Invoice Edit')
				->set_details($details)
				->output_json();
	}

	function sendjson_save_edit($id) {
		$success = $this->DS_Invoice->set_subaction('e')
						->set_id($id)->save();

		if ($success) {
			$details['links'] = array(
				array(
					'type' => 'ajax',
					'url' => '/invoice/sendjson_view/'.$id,
					'target' => '',
					'text' => ''
				)
			);
			$message = 'Data saved.';
		} else {
			$details['data'] = $this->DS_Invoice->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Invoice Save')
				->set_details($details)
				->output_json();
	}

	function sendjson_new() {
		$data = $this->DS_Invoice->set_subaction('a')
					->get_form_data();

		$details = array(
			'data' => $data,
			'links' => array(
				array(
					'target' => '',
					'text' => 'Submit',
					'type' => 'submit',
					'url' => '/invoice/sendjson_save_new',
					'style' => 'default',
					'icon' => '',
				)
			),
			'setting' => array(
				'hidelabel' => 0,
			)
		);

		$this->RespM->set_message('sendjson_new')
				->set_type('form')
				->set_template('')
				->set_success(true)
				->set_title('Invoice Insert')
				->set_details($details)
				->output_json();
	}

	function sendjson_save_new() {
		$success = $this->DS_Invoice->set_subaction('a')
						->save();

		if ($success) {
			$details['links'] = array(
				array(
					'type' => 'ajax',
					'url' => '/invoice/sendjson_list',
					'target' => '',
					'text' => ''
				)
			);
			$message = 'Data saved.';
		} else {
			$details['data'] = $this->DS_Invoice->get_save_errors();
			$message = 'There was an error saving your data';
		}

		$this->RespM->set_message($message)
				->set_type('')
				->set_template('')
				->set_success($success)
				->set_title('Invoice Save')
				->set_details($details)
				->output_json();
	}

	function get_terms($id) {
		$content = '';
		$terms = $this->InvoiceM->getTermsById($id);
		if ($terms) {
			$content = $terms->content;
		}
		echo $content;
	}

	function get_customer() {
		$term = $this->input->get('term');
		$customer_list = $this->InvoiceM->getCustomerByName($term);

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

	private function format_date($date) {
		if (empty($date)) {
			return '';
		} else {
			return date('Y-m-d', strtotime($date));
		}
	}
}