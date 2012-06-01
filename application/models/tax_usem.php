<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Tax_UseM extends MY_Model {

	public $data_fields = array(
		'name' => array(
			'type' => 'text'
		),
		'tax_id_1' => array(
			'type' => 'id'
		),
		'tax_id_2' => array(
			'type' => 'id'
		),
		'tax_id_3' => array(
			'type' => 'id'
		),
		'tax_2_compound' => array(
			'type' => 'boolean'
		),
		'tax_3_compound' => array(
			'type' => 'boolean'
		)
	);

	function __construct() {
		parent::__construct();

		$this->table = 'tax_use';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}

	function calculate_tax($tax_use_id, $amount) {
		$this->load->model('TaxM');

		$tax_use = $this->get($tax_use_id);

		$result = array();
		$tax_1 = $tax_2 = $tax_3 = 0;

		if ($tax_use['tax_id_1']) {
			$tax = $this->TaxM->get($tax_use['tax_id_1']);
			$tax_1 = $amount * $tax['percent'] / 100;
			$result[] = array('id' => $tax['id'], 'name' => $tax['name'], 'amount' => $tax_1);
		}

		if ($tax_use['tax_id_2']) {
			$tax = $this->TaxM->get($tax_use['tax_id_2']);
			if ($tax_use['tax_2_compound'] == 1) {
				$tax_2 = ($amount + $tax_1) * $tax['percent'] / 100;
			} else {
				$tax_2 = $amount * $tax['percent'] / 100;
			}
			$result[] = array('id' => $tax['id'], 'name' => $tax['name'], 'amount' => $tax_2);
		}

		if ($tax_use['tax_id_3']) {
			$tax = $this->TaxM->get($tax_use['tax_id_3']);
			if ($tax_use['tax_3_compound'] == 1) {
				$tax_3 = ($amount + $tax_1 + $tax_2) * $tax['percent'] / 100;
			} else {
				$tax_3 = $amount * $tax['percent'] / 100;
			}
			$result[] = array('id' => $tax['id'], 'name' => $tax['name'], 'amount' => $tax_3);
		}

		$result[] = array('id' => '0', 'name' => 'Total', 'amount' => $tax_1 + $tax_2 + $tax_3);
		return $result;
	}
}
