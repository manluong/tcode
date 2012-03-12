<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class LogM extends CI_Model {

	private $_url = array();
	private $_core_apps = array();
	private $_log_data = array();

	function __construct() {
		parent::__construct();
		$CI =& get_instance();
		$this->_url =& $CI->url;
		$this->_log_data =&  $CI->log_data; //element_dgroup_save access the same data
		$this->_core_apps = array('card', 'client', 'vendor', 'staff', 'invoice', 'product');
	}

	public function start_log() {
		$this->_insert_log();
		$this->_start_timer();
		$this->_log_data['log_type'] = $this->_get_log_type();
		if (isset($this->_log_data['log_type']['eventfield'])) {
			$this->_log_data['log_type']['eventfield'] = explode(",", $this->_log_data['log_type']['eventfield']);
			foreach ($this->_log_data['log_type']['eventfield'] as $thisfield) {
				$this->_log_data['log_type']['eventfield'][$thisfield]['log'] = 1;
			}
		}
		if ( ! empty($this->_log_data['log_type'])) {
			if (isset($this->_log_data['log_type']['nolog'])) {
				$this->_do_log();
			}
		}
	}

	public function stop_log() {
		$total_time = $this->_end_timer();
		$this->_update_log($total_time);
		if ($this->_log_data['log_type']) {
			if ($this->_log_data['log_type']['event']) {
				$log_event_insert_id = $this->_insert_log_event();
				$this->_insert_log_eventsub($log_event_insert_id);
			}

			if ($this->_log_data['log_type']['history']) {
				$this->_insert_log_history();
				$this->_remove_extra_history();
			}
		}
		log_message('debug', 'Log ended.');
	}

	private function _insert_log() {
        $data = array(
            'cardid' => isset($this->UserM->info['cardid']) ? $this->UserM->info['cardid'] : '',
            'gpmid' => isset($this->UserM->info['accessgp']) ? $this->UserM->info['accessgp'] : '',
            'app' => $this->url['app'],
            'action' => $this->url['action'],
            'subaction' => $this->url['subaction'],
			'stamp' => get_current_stamp(),
            'app_data_id' => $this->_url['id_plain'],
			'app_data_id_uri' => $this->uri->segment(3),
			'uri' => $_SERVER['REQUEST_URI'],
			'saveid' => $this->_log_data['saveid'],
			'ms' => '',
			'stamp' => get_current_stamp(),
			'load' => 1,
			'xmsgid' => $_SERVER['REQUEST_URI'],
            'loguid' => $this->UserM->get_loguid(),
        );
        $this->db->insert('log', $data);
		$this->_log_data['insert_id']= $this->db->insert_id();
	}

	function _get_log_type() {
		$query = $this->db->select()
				->from('global_setting.log_type')
				->where(array('app'=>$this->_url['app'],'action'=>$this->_url['action'],
						'subaction'=>$this->_url['subaction']
					))
				->limit(1)
				->get();
		return $query->row_array();
	}

	private function _do_log() {
		$i = $this->_get_log_session_id();
		if (empty($i)) $this->_insert_log_session();
	}

	private function _get_log_session_id() {
		$query = $this->db->select('id')
				->from('log_session')
				->where('loguid', $this->UserM->get_loguid())
				->limit(1)
				->get();
		return $query->row_array();
	}

	private function _insert_log_session() {
		$data = array(
			'stamp' => get_current_stamp(),
			'loguid' => $this->UserM->get_loguid(),
			'agent' => $_SERVER['HTTP_USER_AGENT'],
			'dev' => '0',
			'ip' => inet_pton($_SERVER['REMOTE_ADDR'])
		);
		$this->db->insert('log_session', $data);
	}

	private function _update_log($total_time) {
		$data = array(
			'app_data_id' => $this->_url['id_plain'],
			'saveid' => $this->_log_data['saveid'],
			'ms' => $total_time,
			'load' => 0,
			'xmsgid' => 0
		);
		$this->db->where('id', $this->_log_data['insert_id'])
				->update('log', $data);
	}

	private function _insert_log_event() {
		$data = array(
			'log_type_id' => $this->_log_data['log_type']['id'],
			'app_id' => $this->_url['app_id'],
			'app_data_id' => $this->url['id_plain'],
			'saveid' => $this->_log_data['saveid'],
			'card_id' => $this->UserM->info['cardid'],
			'gpmid' => $this->UserM->info['accessgp'],
			'timeline' => $this->_log_data['log_type']['timeline'],
			'stamp' => get_current_stamp(),
		);
		$this->db->insert('log_event', $data);
		return $this->db->insert_id();
	}

	private function _insert_log_eventsub($log_event_insert_id) {
		if ($this->_log_data['log_type']['eventfield']) {
            foreach (array_keys($this->_log_data['log_type']['eventfield']) as $field){
				log_message('debug', 'Field: '.$field);
                 if (($this->_url['subaction'] == "as" && $this->_log_data['log_type']['eventfield'][$field]['new'])||
                     ($this->_url['subaction'] == "es" && $this->_log_data['log_type']['eventfield'][$field]['cur'] != $this->_log_data['log_type']['eventfield'][$field]['new'])) {
					$data = array(
						'log_id' => $this->_log_data['insert_id'],
						'field' => $field,
						'from' => $this->_log_data['log_type']['eventfield'][$field]['cur'],
						'to' => $this->_log_data['log_type']['eventfield'][$field]['new']
					);
					$this->db->insert('log_eventsub', $data);
				}
            }
        }
		return;
	}

	private function _insert_log_history() {
		if ($this->_log_data['log_type']['history']) {
			//check if this link already exist in history
			$i = $this->_get_old_history();
			if ( ! empty($i)) {
				$this->_update_log_history($i['id']);
				return;
			}

			if ($this->_log_data['log_type']['msg_history'] !== '') {
				$text = $this->_get_custom_msg($this->lang->line('core'.$this->_log_data['log_type']['msg_history']));
			} else {
				$text = $this->_get_default_msg($this->_url['subaction'], $this->_url['app'], $this->_url['id_plain']);
			}
			$data = array(
				'log_type_id' => $this->_log_data['log_type']['id'],
				'cardid' => $this->UserM->info['cardid'],
				'text' => (isset($text)) ? $text : '',
				'furi' => $_SERVER['REQUEST_URI'],
				'stamp' => get_current_stamp(),
			);
			$this->db->insert('log_history', $data);
		}
	}

	private function _update_log_history($id) {
		/* for testing text update so dont have to empty the table everytime.
		if ($this->_log_data['log_type']['msg_history'] !== '') {
			$text = $this->_get_custom_msg($this->lang->line('core'.$this->_log_data['log_type']['msg_history']));
		} else {
			$text = $this->_get_default_msg();
		}*/
		$this->db->where('id', $id)
			->update('log_history', array('stamp'=>  get_current_stamp()));
		return;
	}

	private function _get_custom_msg($msg) {
		$data = array();
		$patterns = array();

		$patterns[0] = '/#thisid#/';
		$patterns[1] = '/#app#/';
		$patterns[2] = '/#cardid#/';
		$patterns[3] = '/#card_emailid#/';
		$patterns[4] = '/#when#/';
		$replacements = array();
		$replacements[0] = $this->_url['id_plain'];
		$replacements[1] = $this->lang->line('coreapptitle_'.$this->_url['app']);
		$replacements[2] = $this->App_generalM->core_app_id2name("card",$this->UserM->info['cardid'],0);
		$replacements[3] = '';//$this->App_generalM->core_app_id2name("card",app_convertid("emailid","cardid",$field1['tid']),0);
		$replacements[4] = parse_stamp(get_current_stamp());
		return preg_replace($patterns, $replacements, $msg);
	}

	private function _remove_extra_history() {
		$query = $this->db->select()
				->from('log_history')
				->where('cardid', $this->UserM->info['cardid'])
				->order_by('stamp', 'desc')
				->limit(10,10)
				->get();
		foreach ($query->result_array() as $row) {
			$this->db->delete('log_history', array('id'=>$row['id']));
		}
	}

	private function _get_default_msg($subaction, $app, $url_id) {
		switch($subaction) {
			  case "a": $text = $this->lang->line('corehis_add'); break;
			  case "v": $text = $this->lang->line('corehis_view'); break;
			  case "e": $text = $this->lang->line('corehis_edit'); break;
			  case "d": $text = $this->lang->line('corehis_delete'); break;
			  case "l": $text = $this->lang->line('corehis_list'); break;
			  case "s": $text = $this->lang->line('corehis_search'); break;
		}
		$text .= ' '.$this->lang->line('coreapptitle_'.$app).' - ';
		if (in_array($app, $this->_core_apps)) {
			$text .= $this->App_generalM->core_app_id2name($app, $url_id,1);
		} else {
			$text .= $_url['id_plain'];
		}
		return $text;
	}

	private function _get_old_history() {
		$query = $this->db->select('id, furi, text')
				->from('log_history')
				->where(array('id'=>$this->UserM->info['cardid'],
						'furi'=>$_SERVER['REQUEST_URI'],
					))
				->get();
		return $query->row_array();
	}

	private function _start_timer() {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$this->_log_data['start_time'] = $time;
	}

	private function _end_timer() {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $this->_log_data['start_time']), 2);
		return $total_time;
	}

	public function get_wall($stamp='', $limit=10) {
		$this->db->select()
			->from('log_event')
			->join('global_setting.log_type', 'log_type.id = log_event.log_type_id')
			->order_by('log_event.lastupdate', 'desc')
			->limit($limit);

		if ($stamp != '') $this->db->where('log_event.lastupdate <', $stamp);

		$query = $this->db->get();

		$i = $query->result_array();
		if (empty($i)) {
			return 'No events';
		}

		$result = array();
		foreach ($i as $event) {
			$furi = '/'.$event['app'].'/'.$event['action'].'/'.$event['app_data_id'].'/'.$event['subaction'];
			if ($event['msg'] !== '') {
				$text = $this->_get_custom_msg($event['msg']);
			} else {
				$text = $this->_get_default_msg($event['subaction'], $event['app'], $event['app_data_id']);
				$text .= $event['stamp'];
			}
			$result[$event['stamp']]['text'] = $text;
			$result[$event['stamp']]['furi'] = $furi;
			$result[$event['stamp']]['app'] = $event['app'];
			$result[$event['stamp']]['tag'] = $event['tag'];
		}


		return $result;
	}

	function get_history(&$limit = 10) {
		$query = $this->db->select()
			->from('log_history')
			->where('cardid', $this->UserM->info['cardid'])
			->limit($limit)
			->order_by('stamp', 'desc')
			->get();
		return $query->result_array();
	}

	function insert_follow($values) {
		$data = array(
			'following_app_data_id' => $values['following_app_data_id'],
			'following_card_id' => $values['following_card_id'],
			'following_log_type_id' => $values['following_log_type_id'],
		);
		$this->db->insert('following', $data);
		return $this->db->insert_id();
	}
	function insert_favorite($values) {
		$data = array(
			'favorite_cardid' => $values['favorite_cardid'],
			'favorite_name' => $values['favorite_name'],
			'favorite_furi' => $values['favorite_furi'],
		);
		$this->db->insert('favorite', $data);
		return $this->db->insert_id();
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
