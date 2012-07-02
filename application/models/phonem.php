<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class PhoneM extends MY_Model {
        function __construct() {
		parent::__construct();

		$this->app = 'phone';
		$this->table = 'did_cdr';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
    
        function search($param,$count = FALSE){
                if (array_key_exists('date_range_from', $param) && $param['date_range_from']) {
			$this->db->where('did_cdr_start >=', $param['date_range_from']);
		}
		if (array_key_exists('date_range_to', $param) && $param['date_range_to']) {
			$this->db->where('did_cdr_start <=', $param['date_range_to']);
		}
                if (array_key_exists('inout', $param) && $param['inout'] != -1) {
			$this->db->where('did_cdr_inout =', $param['inout']);
		}
                
                $this->db->select('did_cdr.*',FALSE);
                $this->db->from('did_cdr');
                
                if ($count) {
			return $this->db->count_all_results();
		} else {
			if (array_key_exists('row_per_page', $param) && $param['row_per_page']) {
				if ($param['row_per_page'] != '-1') {
					$this->db->limit($param['row_per_page'], ($param['page']-1)*$param['row_per_page']);
				}
			}

			$query = $this->db->get();

			return $query->result();
		}
        }
}