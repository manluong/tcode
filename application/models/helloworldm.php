<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class HelloworldM extends MY_Model {
	public $data_fields = array(
		'avatar' => array(
			'type'=>'text',
			'required'=>false,
			'default'=>'',
		),
		'nickname' => array(
			'type'=>'text',
			'required'=>false,
			'default'=>'',
		),
		'title' => array(
			'type'=>'selection',
			'required'=>false,
			'default'=>0,
			'options'=>array(
				0 => '',
				1 => 'Mr.',
				2 => 'Ms.',
				3 => 'Mrs.',
				4 => 'Dr.',
			),
		),
		'first_name' => array(
			'type'=>'text',
			'required'=>true,
			'default'=>'',
		),
		'middle_name' => array(
			'type'=>'text',
			'required'=>false,
			'default'=>'',
		),
		'last_name' => array(
			'type'=>'text',
			'required'=>true,
			'default'=>'',
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