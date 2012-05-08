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

	function view($id) {
		$data = array(
			'invoice' => $this->InvoiceM->get($id),
			'invoice_items' => $this->InvoiceItemM->getByInvoiceId($id)
		);

		$this->data['content'] = $this->load->view(get_template().'/invoice/view', $data, TRUE);

		$this->_do_output();
	}

	function edit($id) {
		$data = array(
			'invoice' => $this->InvoiceM->get($id),
			'invoice_items' => $this->InvoiceItemM->getByInvoiceId($id),
			'customer' => $this->InvoiceM->getCustomer(),
			'tax' => $this->InvoiceM->getTax()
		);

		$this->data['content'] = $this->load->view(get_template().'/invoice/edit', $data, TRUE);

		$this->_do_output();
	}

	function edit_save() {
		$invoice_id = $this->input->post('invoice_id');
		$data = array(
			'id' => $invoice_id,
			'customer_card_id' => $this->input->post('customer_id'),
			'invoice_stamp' => date('Y-m-d', strtotime($this->input->post('issue_date'))),
			'payment_due_stamp' => date('Y-m-d', strtotime($this->input->post('due_date'))),
			'custpo' => $this->input->post('po_number'),
			'tax_id' => $this->input->post('tax_id'),
			'currency' => $this->input->post('currency')
		);

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
					'subscription_start_stamp' => date('Y-m-d', strtotime($from[$index])),
					'subscription_end_stamp' => date('Y-m-d', strtotime($to[$index])),
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
			'tax' => $this->InvoiceM->getTax()
		);

		$this->data['content'] = $this->load->view(get_template().'/invoice/new', $data, TRUE);

		$this->_do_output();
	}

	function add_save() {
		$data = array(
			'customer_card_id' => $this->input->post('customer_id'),
			'invoice_stamp' => date('Y-m-d', strtotime($this->input->post('issue_date'))),
			'payment_due_stamp' => date('Y-m-d', strtotime($this->input->post('due_date'))),
			'custpo' => $this->input->post('po_number'),
			'tax_id' => $this->input->post('tax_id'),
			'currency' => $this->input->post('currency')
		);

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
					'subscription_start_stamp' => date('Y-m-d', strtotime($from[$index])),
					'subscription_end_stamp' => date('Y-m-d', strtotime($to[$index])),
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
}