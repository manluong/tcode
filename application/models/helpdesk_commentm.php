<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Helpdesk_CommentM extends MY_Model {
	public $data_fields = array(
		'id' => array(
			'type' => 'id'
		),
		'helpdesk_id' => array(
			'type' => 'id'
		),
        'private' => array(
			'type' => 'id'
		),
		 'log_id' => array(
			'type' => 'id'
		),
		 'group' => array(
			'type' => 'id'
		),
		 'status' => array(
			'type' => 'id'
		),
		 'priority' => array(
			'type' => 'id'
		),
		'type' => array(
			'type' => 'id'
		),
		'active' => array(
			'type' => 'id'
		),
	);
	
	public $sett_fill_card = TRUE;
	public $sett_fill_helpdesk = TRUE;
	public $sett_fill_file = TRUE;
	
	private $addons = array(
		'card' => 'CardM',
		'helpdesk' => 'HelpdeskM',
		'comment_file' => 'Comment_FileM',
	);
	
	function __construct() {
		parent::__construct();

		$this->app = 'helpdesk';
		$this->table = 'a_helpdesk_comment';
		$this->cache_enabled = TRUE;
        $this->sett_filter_deleted = FALSE;
	}
	
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
		if (count($data) == 0 || $data === FALSE) return FALSE;

		if ($mode == SINGLE_DATA) {
			$data = array($data);
		}

		$helpdesk_id = get_distinct('helpdesk_id', $data);
		$comment_id = get_distinct('id_comment', $data);

		foreach($this->addons AS $name=>$model) {
			$sett_var = 'sett_fill_'.$name;
			if ($this->$sett_var == FALSE) continue;

			if(!empty($helpdesk_id)){
				$addons = $this->$model
						->set_where('id IN ('.implode(',', $helpdesk_id).')')
						->get_list();

				if ($addons !== FALSE && count($addons) > 0) {
					foreach($data AS $k=>$v) {
						foreach($addons AS $addon) {
							if ($addon['id'] != $v['helpdesk_id']) continue;
							$data[$k]['addon_'.$name][] = $addon;
						}
					}
				}
			}
			$this->$model->reset();
		}
		//Get addon file
		
		if ($mode == SINGLE_DATA) {
			$data = $data[0];
		}
	}
	
	function get_assign() {
		$this->db->select('id,display_name');
		$query = $this->db->get('card');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function get_comment_list($id){
		$this->db->select('a_helpdesk_comment.id, a_helpdesk_comment.comment, a_helpdesk_comment.created_stamp, a_helpdesk_comment.priority , card.display_name, card.organization_name, a_helpdesk.subject, a_helpdesk.cc_email');
		$this->db->from('a_helpdesk_comment');
		$this->db->join('card', 'card.id = a_helpdesk_comment.created_card_id');
		$this->db->join('a_helpdesk', 'a_helpdesk.id = a_helpdesk_comment.helpdesk_id');
		//$this->db->join('a_comment_file', 'a_comment_file.id_comment= a_helpdesk_comment.id');
		//$this->db->join('a_priority', 'a_priority.id = a_helpdesk_comment.priority');
		$this->db->where('helpdesk_id',$id);
		$this->db->where('a_helpdesk_comment.active',0);
		$this->db->order_by('created_stamp','DESC');
		
		$query = $this->db->get();
		
		if ($query->result_array()) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
	function get_content($id) {
		$this->db->select('*');
		$this->db->where('helpdesk_id',$id);
		$this->db->where('active',0);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_content_helpdesk($id) {
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get('a_helpdesk');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_assigname($id) {
		$this->db->select('first_name, last_name');
		$this->db->where('id',$id);
		$query = $this->db->get('card');

		if ($query->result()) {
			$tmp = $query->result();
			if (!empty($tmp)) {
				foreach($tmp as $k) {
					return $k->first_name.' '.$k->last_name;
				}
			}
		} else {
			return false;
		}
	}

	function get_priority_type($id) {
		$this->db->select('name');
		$this->db->where('id',$id);
		$query = $this->db->get('a_priority');

		if ($query->result()) {
			$tmp = $query->result();
			if (!empty($tmp)) {
				foreach($tmp as $k) {
					return $k->name;
				}
			}
		} else {
			return false;
		}
	}

    function get_comment_not_use() {
		$this->db->select('id');
		$this->db->where('active', 1);
		$query = $this->db->get('a_helpdesk_comment');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_comment_files($id) {
		$this->db->select('*');
		$this->db->where('id_comment',$id);
		$query = $this->db->get('a_comment_file');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

    function delete_files_not_use($id){
		$this->db->where('id', $id);
		$this->db->delete('a_comment_file');
	}

	function search_comment($search_string) {
		$this->select_fields = array('helpdesk_id', 'comment');
		$this->search_fields = array(
			array('comment'),
		);

		return parent::search($search_string);
	}
}