<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class HelpdeskM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_helpdesk';
		$this->cache_enabled = TRUE;
		$this->sett_filter_deleted = FALSE;
		$this->sett_fill_card_info = TRUE;
	}
	public $sett_fill_card = TRUE;
		
	private $addons = array(
		'card' => 'CardM',
	);
	
	public $data_fields = array(
		'subject' => array(
		),
		'group' => array(
		),
        'status' => array(
		),
        'type' => array(
		),
        'rate' => array(
		),
        'in_charge_card_id' => array(
		),
        'cc_email' => array(
		),
        'assign_id' => array(
		),
        'active' => array(
		),
	);
	
	function get($id) {
		$result = parent::get($id);

		$this->fill_addons($result);

		return $result;
	}
	
	function get_list() {
		$result = parent::get_list();
		
		$this->fill_addons($result, MULTIPLE_DATA);
		
		return $result;
	}
	
	private function fill_addons(&$data, $mode=SINGLE_DATA) {
		foreach($this->addons AS $name=>$model) {
			$sett_var = 'sett_fill_'.$name;
			if ($this->$sett_var == FALSE) continue;

			if ($mode == SINGLE_DATA) {
				$data = array($data);
			}
			$assign_id = get_distinct('assign_id', $data);
			
			if(!empty($assign_id)){
				$addons = $this->$model
						->set_where('id IN ('.implode(',', $assign_id).')')
						->get_list();
			}
			
			if ($addons !== FALSE && count($addons) > 0) {
				foreach($data AS $k=>$v) {
					foreach($addons AS $addon) {
						if ($addon['id'] != $v['assign_id']) continue;

						$data[$k]['addon_'.$name][] = $addon;
					}
				}
			}

			$this->$model->reset();

			if ($mode == SINGLE_DATA) {
				$data = $data[0];
			}
		}
	}
	
	function get_content($id) {
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function insert_upload_file($filename , $comment_id) {
		$data = array (
			'filename' => $filename,
			'id_comment' => $comment_id
		);
		if ($this->db->insert('a_comment_file',$data)) {
			return $this->db->insert_id();
		} else {
			return 0;
		}
	}

	function get_helpdesk_not_use() {
		$this->db->select('id');
		$this->db->where('active', 1);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
        }

}