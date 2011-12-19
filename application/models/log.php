<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Log extends CI_Model {
	private $_start_time = 0;
	private $_curr_log_id = 0;
	private $_log_type = array();
	private $_lang = array();
	private $_url = array();
	private $_langinfo = array();
	private $_core_apps = array();

	function __construct() {
		parent::__construct();
		$this->load->helper('date');
		$CI =& get_instance();
		$this->_url =& $CI->url;
		$this->_langinfo = $this->lang->langinfo;
		$this->_core_apps = array('card', 'client', 'vendor', 'staff', 'invoice', 'product');
	}

	public function start_log() {
		$this->_insert_log();
		$this->_start_timer();
		$this->_log_type = $this->_get_log_type();
		if ($this->_log_type) {
			if (! $this->_log_type['nolog']) {
				$this->_do_log();
			}
		}
	}

	public function stop_log() {
		$total_time = $this->_end_timer();
		$this->_update_log($total_time);
		$this->_insert_log_eventsub();
		$this->_insert_log_history();
		$this->_remove_extra_history();
	}

	private function _insert_log() {
        $data = array(
            'stamp' => mdate("%Y-%n-%j %H:%i:%s"),
            'cardid' => isset($this->User->info['cardid'])? $this->User->info['cardid'] : NULL,
            'gpmid' => isset($this->User->info['gpmid'])? $this->User->info['gpmid'] : NULL,
            'app' => $this->_url['app'],
            'an' => $this->_url['action'],
            'aved' => $this->_url['subaction'],
            'tidorg' => $this->_url['id_plain'],
            'uri' => $_SERVER['REQUEST_URI'],
            'loguid' => $this->User->get_loguid(),
            'load' => 1
        );
        $this->db->insert('log', $data);
		$this->_curr_log_id = $this->db->insert_id();
	}

	private function _get_log_type() {
		$query = $this->db->select()
				->from('log_type')
				->where(array('app'=>$this->_url['app'],'an'=>$this->_url['action'],
						'aved'=>$this->_url['subaction']
					))
				->limit(1)
				->get();
		return $query->row_array();
	}

	private function _do_log() {
		($this->_get_log_session_id()) ? : $this->_insert_log_session();
	}

	private function _get_log_session_id() {
		$query = $this->db->select('id')
				->from('log_session')
				->where('loguid', $this->User->get_loguid())
				->limit(1)
				->get();
		return $query->row();
	}

	private function _insert_log_session() {
		$data = array(
			'stamp' => mdate("%Y-%n-%j %H:%i:%s"),
			'loguid' => $this->User->get_loguid(),
			'agent' => $_SERVER['HTTP_USER_AGENT'],
			'dev' => '0',
			'ip' => inet_pton($_SERVER['REMOTE_ADDR'])
		);
		$this->db->insert('log_session', $data);
	}

	private function _update_log($total_time) {
		$data = array(
			'tid' => $this->_url['id_plain'],
			'saveid' => $this->_log_type['saveid'],
			'ms' => $total_time,
			'load' => 0,
			'xmsgid' => 0
		);
		$this->db->update('log', $data)
				->where('id',$this->_curr_log_id);
	}

	private function _insert_log_eventsub() {
		if ($this->_log_type['eventfield']){
            foreach (array_keys($this->_log_type['eventfield']) as $field){
                 if (($this->_url['subaction'] == "as" && $this->_log_type['eventfield'][$field]['new'])||
                     ($$this->_url['subaction'] == "es" && $this->_log_type['eventfield'][$field]['cur'] != $this->log_type['eventfield'][$field]['new'])) {
					$data = array(
						'eventid' => $logeventid,
						'field' => $field,
						'fr' => $$this->_log_type['eventfield'][$field]['cur'],
						'to' => $this->_log_type['eventfield'][$field]['new']
					);
					$this->db->insert('log_eventsub', $data);
				}
            }
        }
	}

	private function _insert_log_history() {
		if ($this->_log_type['history']) {
			//check if this link already exist in history
			if ($this->_get_old_history()) {
				$this->_update_log_history();
				return;
			}

			if ($this->_log_type['history_msg']){
				$text = $this->_get_custom_history_msg();
			} else {
				$text = $this->_get_default_history_msg();
			}
			$data = array(
				'stamp' => mdate("%Y-%n-%j %H:%i:%s"),
				'typeid' => $this->_log_type['id'],
				'cardid' => $this->User->info['cardid'],
				'text' => $text,
				'furi' => $_SERVER['REQUEST_URI'],
			);
			$this->db->insert('log_history', $data);
		}
	}

	private function _update_log_history() {
		$query = $this->db->select('id')
				->from('log_history')
				->where('furi', $_SERVER['REQUEST_URI'])
				->limit(1)
				->get();
		$log_history_id =  $query->row();
		if ($this->_log_type['msghis_'.$this->langinfo['thislang']]) {

		}
	}

	private function _get_custom_history_msg() {
		$patterns = array();
		$patterns[0] = '/#thisid#/';
		$patterns[1] = '/#app#/';
		$replacements = array();
		$replacements[0] = $thisid[0];
		$replacements[1] = $_lang['core']['apptitle_'.$app];
		return preg_replace($patterns, $replacements, $log['history_msg']);
	}

	private function _remove_extra_history() {
		$query = $this->db->select()
				->from('log_history')
				->where('cardid', $this->User->info['cardid'])
				->order_by('stamp', 'desc')
				->limit(10,10)
				->get();
		foreach ($query->row_array() as $row) {
			$this->db->delete('log_history', array('id'=>$row['id']));
		}
	}

	private function _get_default_history_msg() {
		switch($this->_url['subaction']){
			  case "a": $text = $_lang['core']['his_add']; break;
			  case "v": $text = $_lang['core']['his_view']; break;
			  case "e": $text = $_lang['core']['his_edit']; break;
			  case "d": $text = $_lang['core']['his_delete']; break;
			  case "l": $text = $_lang['core']['his_list']; break;
			  case "s": $text = $_lang['core']['his_search']; break;
		}
		$text .= " ".$_lang['core']['apptitle_'.$app]." - ";
		if (in_array($this->_core_apps)) {
			$text .= core_app_id2name($app,$_url['id_plain'],1);
		} else {
			$text .= $_url['id_plain'];
		}
	}

	private function _get_old_history() {
		$query = $this->db->select('furi, text')
				->from('log_history')
				->where(array('id'=>$this->User->info['cardid'],
						'furi'=>$_SERVER['REQUEST_URI'],
					))
				->order_by('stamp','desc')
				->limit(1)
				->get();
		return $this->query->row_array($query);
	}

	private function _start_timer() {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$this->_start_time = $time;
	}

	private function _end_timer() {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $this->start), 4);
		return($total_time);
	}
	//	private function _get_request_uri() {
	//		$_url = explode("?", $_SERVER['REQUEST_URI'],2);
	//        $log['furi'] = $uri[1];
	//        $uri = preg_replace("/app=".$this->_url['app']."/", "", $uri[1]);
	//        $uri = preg_replace("/&an=".$this->_url['action']."/", "", $uri);
	//        $uri = preg_replace("/&aved=".$this->_url['subaction']."/", "", $uri);
	//        $uri = preg_replace("/&thisid=".$this->_url['id_encrpted']."/", "", $uri);
	//        if ($uri == "&") $uri = "";
	//        $this->_request_uri = $uri;
	//	}
}
