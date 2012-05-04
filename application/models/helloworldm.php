<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class HelloworldM extends MY_Model {
	public $data_fields = array(
		'avatar' => array(
		),
		'nickname' => array(
		),
		'title' => array(
			'type'=>'selection',
			'default'=>0,
			'options'=>array(
				0 => '',
				1 => 'core_select-select-cardtitle-Mr.',
				2 => 'core_select-select-cardtitle-Miss.',
				3 => 'core_select-select-cardtitle-Mrs.',
				4 => 'core_select-select-cardtitle-Dr.',
			),
		),
		'first_name' => array(
			'required'=>true,
			'allow_blank' => false,
		),
		'middle_name' => array(
		),
		'last_name' => array(
			'required'=>true,
			'allow_blank' => false,
		),

	);


	function __construct() {
		$this->table = 'card';
		$this->cache_enabled = TRUE;

		parent::__construct();
	}

	public function get_name($id) {
		$result = $this->get($id);
		return $result['first_name'].' '.$result['last_name'];
	}

	//$type = primary | secondary | all
	public function get_email($card_id, $type='primary') {
		$this->db->select('email')
			->from('card_email')
			->where('card_id', $card_id);

		if ($type == 'primary') {
			$this->db->where('is_default', 1)
					->limit(1);
		} elseif ($type == 'secondary') {
			$this->db->where('is_default', 0);
		}

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return FALSE;

		if ($type == 'primary') {
			$r = $rs->row_array();
			$results = $r['email'];
		} else {
			$results = array();
			foreach($rs->result_array() AS $r) {
				$results[] = $r['email'];
			}
		}

		return $results;
	}

}