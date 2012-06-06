<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('InvoiceM');
		$this->load->model('Invoice_TermsM');
		$this->load->model('TaxM');
		$this->load->model('Tax_UseM');
	}

	function index() {
		$is_client = $this->UserM->is_client();

		$total = $this->InvoiceM->get_min_max_invoice_total();
		$data = array(
			'is_client' => $is_client,
			'total_min' => floor($total['min']),
			'total_max' => ceil($total['max'])
		);
		$this->data['content'] = $this->load->view(get_template().'/invoice/index', $data, true);

		$this->_do_output();
	}

	function card($id) {
		$is_client = $this->UserM->is_client();

		$this->load->model('CardM');
		$customer_card_name = $this->CardM->get_name($id);

		$total = $this->InvoiceM->get_min_max_invoice_total();
		$data = array(
			'is_client' => $is_client,
			'customer_card_id' => $id,
			'customer_card_name' => $customer_card_name,
			'total_min' => floor($total['min']),
			'total_max' => ceil($total['max'])
		);
		$this->data['content'] = $this->load->view(get_template().'/invoice/index', $data, true);

		$this->_do_output();
	}

	function search() {
		//$page = $this->input->post('page') ? $this->input->post('page') : 1;
		//$row_per_page = $this->input->post('row_per_page') ? $this->input->post('row_per_page') : 10;

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
			//'page' => $page,
			//'row_per_page' => $row_per_page
		);

		if ($this->UserM->is_client()) {
			$search_param['customer_id'] = $this->UserM->get_card_id();
			$search_param['customer_name'] = '';
		}

		//$total_record = $this->InvoiceM->search($search_param, true);
		//$data = array(
			//'invoice_list' => $this->InvoiceM->search($search_param),
			//'total_record' => $total_record,
			//'current_page' => $page,
			//'row_per_page' => $row_per_page,
			//'max_page' => ($row_per_page == -1) ? 1 : ceil($total_record/$row_per_page)
		//);

		//$content = $this->load->view(get_template().'/invoice/search', $data, true);
		//echo $content;

		echo json_encode($this->InvoiceM->search($search_param));

		//$result = array();
		//foreach ($this->InvoiceM->search($search_param) as $invoice) {
		//	$result[] = array('cb', $invoice->display_name, $invoice->id, date('Y-m-d', strtotime($invoice->payment_due_stamp)), '$'.number_format($invoice->total, 2), '', $invoice->id);
		//}

		//echo json_encode($result);
	}

	function view($id) {
		$this->InvoiceM->sett_fill_pay_item = TRUE;
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			//'customer_name' => '',
			'tax' => $this->TaxM->get_list(),
			'invoice_terms' => ''
		);

		//if ($invoice['customer_card_id']) {
		//	$customer = $this->InvoiceM->get_customer_by_id($invoice['customer_card_id']);
		//	if ($customer) {
		//		$data['customer_name'] = $customer->display_name;
		//	}
		//}
		$this->load->model('CardM');
		$card = $this->CardM->get_quickjump($invoice['customer_card_id']);
		$data['quickjump'] = $this->load->view(get_template().'/card/quickjump', $card, TRUE);

		if ($invoice['terms_id']) {
			$terms = $this->Invoice_TermsM->get($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms['content'];
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
			//'customer_name' => '',
			'tax' => $this->TaxM->get_list(),
			'invoice_terms' => ''
		);

		//if ($invoice['customer_card_id']) {
		//	$customer = $this->InvoiceM->get_customer_by_id($invoice['customer_card_id']);
		//	if ($customer) {
		//		$data['customer_name'] = $customer->display_name;
		//	}
		//}

		if ($invoice['terms_id']) {
			$terms = $this->Invoice_TermsM->get($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms['content'];
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$content = $this->load->view(get_template().'/invoice/print', $data, true);
		output_pdf($content);
	}

	function print_invoice($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			//'customer_name' => '',
			'tax' => $this->TaxM->get_list(),
			'invoice_terms' => ''
		);

		//if ($invoice['customer_card_id']) {
		//	$customer = $this->InvoiceM->get_customer_by_id($invoice['customer_card_id']);
		//	if ($customer) {
		//		$data['customer_name'] = $customer->display_name;
		//	}
		//}

		if ($invoice['terms_id']) {
			$terms = $this->Invoice_TermsM->get($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms['content'];
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$content = $this->load->view(get_template().'/invoice/print', $data, true);
		echo $content;
	}

	function edit($id) {
		$invoice = $this->InvoiceM->get($id);
		$data = array(
			'invoice' => $invoice,
			'customer' => $this->InvoiceM->get_customer(),
			'price_type' => $this->InvoiceM->get_price_type(),
			'duration_type' => $this->InvoiceM->get_duration_type(),
			'tax' => $this->TaxM->get_list(),
			'tax_use' => $this->Tax_UseM->get_list(),
			'terms' => $this->Invoice_TermsM->get_list(),
			'invoice_terms' => ''
		);

		if ($invoice['terms_id']) {
			$terms = $this->Invoice_TermsM->get($invoice['terms_id']);
			if ($terms) {
				$data['invoice_terms'] = $terms['content'];
			}
		} else {
			$data['invoice_terms'] = $invoice['terms_content'];
		}

		$this->data['content'] = $this->load->view(get_template().'/invoice/edit', $data, true);

		$this->_do_output();
	}

	function add() {
		$data = array(
			'customer' => $this->InvoiceM->get_customer(),
			'price_type' => $this->InvoiceM->get_price_type(),
			'duration_type' => $this->InvoiceM->get_duration_type(),
			'tax' => $this->TaxM->get_list(),
			'tax_use' => $this->Tax_UseM->get_list(),
			'terms' => $this->Invoice_TermsM->get_list()
		);

		$this->data['content'] = $this->load->view(get_template().'/invoice/new', $data, true);

		$this->_do_output();
	}

	function save() {
		$data = $this->InvoiceM->get_form_data();

		if ($this->InvoiceM->is_valid($data) == FALSE) {
			$this->RespM
				->set_success(FALSE)
				->set_message($this->InvoiceM->get_error_string())
				->set_details($this->InvoiceM->field_errors)
				->output_json();
			exit;
		}

		$this->InvoiceM->sett_skip_validation = TRUE;
		$invoice_id = $this->InvoiceM->save($data);
		if ($invoice_id === FALSE) {
			echo '<pre>'.print_r($this->InvoiceM->errors).'</pre>';die;
		}

		echo json_encode(array(
			'success' => true,
			'url' => '/invoice/view/'.$invoice_id
		));
		exit;
	}

	function pay($id) {
		$data = array (
			'invoice_id' => $id,
			'invoice_total' => $this->InvoiceM->get_invoice_total($id)
		);

		$this->data['content'] = $this->load->view(get_template().'/invoice/pay', $data, true);

		$this->_do_output();
	}

	function pay_save() {
		$this->load->model('Invoice_PayM');

		$data = $this->Invoice_PayM->get_form_data();

		if ($this->Invoice_PayM->is_valid($data) == FALSE) {
			$this->RespM
				->set_success(FALSE)
				->set_message($this->Invoice_PayM->get_error_string())
				->set_details($this->Invoice_PayM->field_errors)
				->output_json();
			exit;
		}

		$this->Invoice_PayM->sett_skip_validation = TRUE;
		$invoice_pay_id = $this->Invoice_PayM->save($data);
		if ($invoice_pay_id === FALSE) {
			echo '<pre>'.print_r($this->Invoice_PayM->errors).'</pre>';die;
		}

		$item = $data['addon_item'];
		if (count($item) == 1) {
			$url = '/invoice/view/'.$item[0]['invoice_id'];
		} else {
			$invoice = $this->InvoiceM->get($item[0]['invoice_id']);
			$url = '/invoice/card/'.$invoice['customer_card_id'];
		}

		echo json_encode(array(
			'success' => true,
			'url' => $url
		));
		exit;
	}

	function get_terms($id) {
		$content = '';
		$terms = $this->Invoice_TermsM->get($id);
		if ($terms) {
			$content = $terms['content'];
		}
		echo $content;
	}

	function get_product() {
		$term = $this->input->get('term');
		$product_list = $this->InvoiceM->get_product_by_name($term);

		$content = array();
		if ($product_list) {
			foreach ($product_list as $product) {
				$content[] = array(
					'product' => array(
						'id' => $product->id,
						'price' => (float)$product->amount
					),
					'label' => $product->name,
					'value' => $product->name
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