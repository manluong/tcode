<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('InvoiceM');
		$this->load->model('Invoice_TermsM');
		$this->load->model('TaxM');
		$this->load->model('Tax_UseM');
		$this->load->model('CardM');

		$this->data['app_menu'] = array(
			array('url' => '/invoice', 'extra' => '', 'title' => 'List'),
			array('url' => '/invoice/add', 'extra' => '', 'title' => 'New')
		);
	}

	function index() {
		$total = $this->InvoiceM->get_min_max_invoice_total();
		$content = array(
			'is_client' => $this->UserM->is_client(),
			'total_min' => floor($total['min']),
			'total_max' => ceil($total['max']),
			'quickjump' => $this->get_quickjump()
		);

		$this->data['breadcrumb'] = array(array('title' => 'List'));
		$this->data['content'] = $this->load->view(get_template().'/invoice/index', $content, TRUE);
		$this->_do_output();
	}

	function card($id) {
		$customer_card_name = $this->CardM->get_name($id);

		$total = $this->InvoiceM->get_min_max_invoice_total();
		$content = array(
			'is_client' => $this->UserM->is_client(),
			'total_min' => floor($total['min']),
			'total_max' => ceil($total['max']),
			'customer_card_id' => $id,
			'customer_card_name' => $customer_card_name,
			'quickjump' => $this->get_quickjump()
		);

		$this->data['breadcrumb'] = array(array('title' => 'List'));
		$this->data['content'] = $this->load->view(get_template().'/invoice/index', $content, TRUE);
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
			'status' => $this->input->post('status'),
			'total_min' => $this->input->post('total_min'),
			'total_max' => $this->input->post('total_max'),
			'invoice_id' => $this->input->post('invoice_id'),
			'po_number' => $this->input->post('po_number'),
			'notes' => $this->input->post('notes'),
			//'page' => $page,
			//'row_per_page' => $row_per_page
		);

		$this->RespM->set_success(TRUE)
			->set_details($this->InvoiceM->search($search_param))
			->output_json();
	}

	function view($id) {
		$this->InvoiceM->sett_fill_pay_item = TRUE;
		$invoice = $this->InvoiceM->get($id);
		if ($invoice === FALSE) die('404 Not found');

		$content = array(
			'invoice' => $invoice,
			//'customer_name' => '',
			'tax' => $this->TaxM->get_list(),
			'terms_content' => $invoice['terms_id'] ? $this->Invoice_TermsM->get_content($invoice['terms_id']) : '',
			'quickjump' => $this->get_quickjump()
		);

		$this->data['breadcrumb'] = array(array('title' => 'View'));
		$this->data['content'] = $this->load->view(get_template().'/invoice/view', $content, TRUE);
		$this->_do_output();
	}

	function pdf($id) {
		$invoice = $this->InvoiceM->get($id);
		if ($invoice === FALSE) die('404 Not found');

		$content = array(
			'invoice' => $invoice,
			//'customer_name' => '',
			'tax' => $this->TaxM->get_list(),
			'terms_content' => $invoice['terms_id'] ? $this->Invoice_TermsM->get_content($invoice['terms_id']) : ''
		);

		$html = $this->load->view(get_template().'/invoice/pdf', $content, TRUE);
		output_pdf2($html, 'invoice-'.$invoice['id'].'.pdf');
		exit;
	}

	function print_invoice($id) {
		$invoice = $this->InvoiceM->get($id);
		if ($invoice === FALSE) die('404 Not found');

		$content = array(
			'invoice' => $invoice,
			//'customer_name' => '',
			'tax' => $this->TaxM->get_list(),
			'terms_content' => $invoice['terms_id'] ? $this->Invoice_TermsM->get_content($invoice['terms_id']) : ''
		);

		$html = $this->load->view(get_template().'/invoice/print', $content, TRUE);
		echo $html;
	}

	function add() {
		$content = array(
// 			'price_type' => $this->InvoiceM->get_price_type(),
// 			'duration_type' => $this->InvoiceM->get_duration_type(),
			'tax' => $this->TaxM->get_list(),
			'tax_use' => $this->Tax_UseM->get_list(),
			'terms' => $this->Invoice_TermsM->get_list(),
			'quickjump' => $this->get_quickjump()
		);

		$this->data['breadcrumb'] = array(array('title' => 'New'));
		$this->data['content'] = $this->load->view(get_template().'/invoice/new', $content, TRUE);
		$this->_do_output();
	}

	function edit($id) {
		$invoice = $this->InvoiceM->get($id);
		if ($invoice === FALSE) die('404 Not found');

		$content = array(
			'invoice' => $invoice,
// 			'price_type' => $this->InvoiceM->get_price_type(),
// 			'duration_type' => $this->InvoiceM->get_duration_type(),
			'tax' => $this->TaxM->get_list(),
			'tax_use' => $this->Tax_UseM->get_list(),
			'terms' => $this->Invoice_TermsM->get_list(),
			'terms_content' => $invoice['terms_id'] ? $this->Invoice_TermsM->get_content($invoice['terms_id']) : '',
			'quickjump' => $this->get_quickjump()
		);

		$this->data['breadcrumb'] = array(array('title' => 'Edit'));
		$this->data['content'] = $this->load->view(get_template().'/invoice/edit', $content, TRUE);
		$this->_do_output();
	}

	function save() {
		$data = $this->InvoiceM->get_form_data();

		if ($this->InvoiceM->is_valid($data) == FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->InvoiceM->get_error_string())
				->set_details($this->InvoiceM->field_errors)
				->output_json();
			exit;
		}

		$this->InvoiceM->sett_skip_validation = TRUE;
		$invoice_id = $this->InvoiceM->save($data);
		if ($invoice_id === FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->InvoiceM->get_error_string())
				->set_details($this->InvoiceM->field_errors)
				->output_json();
			exit;
		}

		$this->RespM->set_success(TRUE)
			->set_details('/invoice/view/'.$invoice_id)
			->output_json();
	}

	function pay($id) {
		$this->InvoiceM->sett_fill_pay_item = TRUE;
		$invoice = $this->InvoiceM->get($id);
		if ($invoice === FALSE) die('404 Not found');

		$content = array (
			'invoice' => $invoice,
			'quickjump' => $this->get_quickjump()
		);

		$this->data['breadcrumb'] = array(array('title' => 'Pay'));
		$this->data['content'] = $this->load->view(get_template().'/invoice/pay', $content, TRUE);
		$this->_do_output();
	}

	function pay_save() {
		$this->load->model('Invoice_PayM');
		$data = $this->Invoice_PayM->get_form_data();

		if ($this->Invoice_PayM->is_valid($data) == FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->Invoice_PayM->get_error_string())
				->set_details($this->Invoice_PayM->field_errors)
				->output_json();
			exit;
		}

		$this->Invoice_PayM->sett_skip_validation = TRUE;
		$invoice_pay_id = $this->Invoice_PayM->save($data);
		if ($invoice_pay_id === FALSE) {
			$this->RespM->set_success(FALSE)
				->set_message($this->Invoice_PayM->get_error_string())
				->set_details($this->Invoice_PayM->field_errors)
				->output_json();
			exit;
		}

		$item = $data['addon_item'];
		if (count($item) == 1) {
			$url = '/invoice/view/'.$item[0]['invoice_id'];
		} else {
			$invoice = $this->InvoiceM->get($item[0]['invoice_id']);
			$url = '/invoice/card/'.$invoice['customer_card_id'];
		}

		$this->RespM->set_success(TRUE)
			->set_details($url)
			->output_json();
	}

	function get_terms($id) {
		$this->RespM->set_success(TRUE)
			->set_details($this->Invoice_TermsM->get_content($id))
			->output_json();
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

	private function get_quickjump() {
		$quickjump_card_id = $this->session->userdata('quickjump_card_id');
		if ($quickjump_card_id !== FALSE) {
			$card = $this->CardM->get_quickjump($quickjump_card_id);
			return $this->load->view(get_template().'/card/quickjump', $card, TRUE);
		} else {
			return '';
		}
	}
}