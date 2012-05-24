<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class LogM extends CI_Model {

	private $_core_apps = array();
	private $_log_data = array();

	function __construct() {
		parent::__construct();

		$this->_log_data =  $this->log_data; //element_dgroup_save access the same data
		$this->_core_apps = array('card', 'client', 'vendor', 'staff', 'invoice', 'product');
	}

	public function start_log() {
		$this->_insert_log();
		$this->_start_timer();
		$this->_log_data['log_type'] = $this->_get_log_type();
		if (isset($this->_log_data['log_type']['auditfield'])) {
			$this->_log_data['log_type']['auditfield'] = explode(',', $this->_log_data['log_type']['auditfield']);
			foreach ($this->_log_data['log_type']['auditfield'] as $thisfield) {
				$this->_log_data['log_type']['auditfield'][$thisfield]['log'] = 1;
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
			}

			if ($this->_log_data['log_type']['history']) {
				$this->_insert_log_history();
				$this->_remove_extra_history();
			}
		}
		log_message('debug', 'Log ended.');
	}

	private function _insert_log() {
		$server_request_uri = ($this->input->is_cli_request())
			? 'cli_request'
			: $_SERVER['REQUEST_URI'];


        $data = array(
            'cardid' => $this->UserM->get_card_id(),
            'gpmid' => isset($this->UserM->info['role']['role_id']) ? $this->UserM->info['role']['role_id'] : '',
            'app' => $this->url['app'],
            'action' => $this->url['action'],
            'subaction' => $this->url['subaction'],
			'stamp' => get_current_stamp(),
            'app_data_id' => $this->url['id_plain'],
			'app_data_id_uri' => $this->uri->segment(3),
			'uri' => $server_request_uri,
			'saveid' => $this->_log_data['saveid'],
			'ms' => '',
			'stamp' => get_current_stamp(),
			'load' => 1,
			'xmsgid' => $server_request_uri,
            'loguid' => $this->UserM->get_loguid(),
        );
        $this->db->insert('log', $data);
		$this->_log_data['insert_id']= $this->db->insert_id();
	}

	function _get_log_type() {
		$query = $this->db->select()
				->from('global_setting.log_type')
				->where(array('app'=>$this->url['app'],'action'=>$this->url['action'],
						'subaction'=>$this->url['subaction']
					))
				->limit(1)
				->get();
		return $query->row_array();
	}

	function update_log_type() {
		$this->_log_data['log_type'] = $this->_get_log_type();
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
		if ($this->is_cli) return;

		$data = array(
			'app_data_id' => $this->url['id_plain'],
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
			'app_id' => $this->url['app_id'],
			'app_data_id' => $this->url['id_plain'],
			'saveid' => $this->_log_data['saveid'],
			'card_id' => $this->UserM->get_card_id(),
			'type' => 'standard',
			'gpmid' => $this->UserM->info['role']['role_id'],
			'timeline' => $this->_log_data['log_type']['timeline'],
			'stamp' => get_current_stamp(),
			'lastupdate' => get_current_stamp(),
		);
		$this->db->insert('log_event', $data);
		return $this->db->insert_id();
	}

	function insert_wall_post($text) {
		$data = array(
			'log_type_id' => $this->_log_data['log_type']['id'],
			'app_id' => $this->url['app_id'],
			'app_data_id' => $this->url['id_plain'],
			'saveid' => $this->_log_data['saveid'],
			'card_id' => $this->UserM->get_card_id(),
			'sub_group' => '',
			'type' => 'text',
			'applink' => '',
			'msg' => $text,
			'html' => '',
			'gpmid' => $this->UserM->info['role']['role_id'],
			'timeline' => $this->_log_data['log_type']['timeline'],
			'stamp' => get_current_stamp(),
			'lastupdate' => get_current_stamp(),
			'priority' => 0,
		);

		$this->db->insert('log_event', $data);
		$data['id'] = $this->db->insert_id();
		$data['card_name'] = $this->UserM->get_data_name($data['card_id']);
		$data['created_stamp_iso8601'] = parse_user_date($data['stamp'], 'ISO_8601');
		$data['created_stamp_iso'] = parse_user_date($data['stamp'], 'ISO');

		return $data;
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
				$text = $this->_get_default_msg($this->url['subaction'], $this->url['app'], $this->url['id_plain']);
			}
			$data = array(
				'log_type_id' => $this->_log_data['log_type']['id'],
				'cardid' => $this->UserM->get_card_id(),
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

	private function _remove_extra_history() {
		$query = $this->db->select()
				->from('log_history')
				->where('cardid', $this->UserM->get_card_id())
				->order_by('stamp', 'desc')
				->limit(10,10)
				->get();

		$ids = array();
		foreach ($query->result_array() as $row) {
			$ids[] = $row['id'];
		}

		$this->db->where_in('id', $ids)
			->delete('log_history');
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
		$replacements[0] = $this->url['id_plain'];
		$replacements[1] = $this->lang->line('coreapptitle_'.$this->url['app']);
		$replacements[2] = '';//TODO: check this function-> $this->App_generalM->core_app_id2name('card',$this->UserM->get_card_id(),0);
		$replacements[3] = '';//$this->App_generalM->core_app_id2name("card",app_convertid("emailid","cardid",$field1['tid']),0);
		$replacements[4] = parse_stamp(get_current_stamp());
		return preg_replace($patterns, $replacements, $msg);
	}

	private function _get_default_msg($subaction, $app, $url_id) {
		$default_lang = array(
			'a' => 'corehis_add',
			'v' => 'corehis_view',
			'e' => 'corehis_edit',
			'd' => 'corehis_delete',
			'l' => 'corehis_list',
			's' => 'corehis_search'
		);

		$text = $this->lang->line($default_lang[$subaction]);
		$text .= ' '.$this->lang->line('coreapptitle_'.$app).' - ';

		if (in_array($app, $this->_core_apps)) {
			$text .= $this->App_generalM->core_app_id2name($app, $url_id,1);
		} else {
			$text .= $url['id_plain'];
		}

		return $text;
	}

	private function _get_old_history() {
		$query = $this->db->select('id, furi, text')
				->from('log_history')
				->where(array('id'=>$this->UserM->get_card_id(),
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

	public function get_wall($id='', $limit=10) {
		$this->db->select('log_event.*, global_setting.log_type.app, global_setting.log_type.tag, global_setting.log_type.action, global_setting.log_type.subaction')
			->from('log_event')
			->join('global_setting.log_type', 'log_type.id = log_event.log_type_id')
			->order_by('log_event.lastupdate', 'desc')
			->limit($limit);

		if ($id != '') $this->db->where('log_event.id <', $id);

		$query = $this->db->get();

		$result = array();
		foreach ($query->result_array() as $event) {
			$furi = '/'.$event['app'].'/'.$event['action'].'/'.$event['app_data_id'].'/'.$event['subaction'];
			if ($event['type'] == 'text') {
				$msg = $event['msg'];
			} else {
				if ($event['msg'] !== '') {
					$msg = $this->_get_custom_msg($event['msg']);
				} else {
					$msg = $this->_get_default_msg($event['subaction'], $event['app'], $event['app_data_id']);
					$msg .= $event['stamp'];
				}
			}

			$result[$event['id']]['id'] = $event['id'];
			$result[$event['id']]['card_name'] = $this->UserM->get_data_name($event['card_id']);
			$result[$event['id']]['msg'] = $msg;
			$result[$event['id']]['furi'] = $furi;
			$result[$event['id']]['type'] = $event['type'];
			$result[$event['id']]['app'] = $event['app'];
			$result[$event['id']]['tag'] = $event['tag'];
			$result[$event['id']]['priority'] = $event['priority'];
			$result[$event['id']]['stamp'] = $event['stamp'];
			$result[$event['id']]['created_stamp_iso8601'] = parse_user_date($event['stamp'], 'ISO_8601');
			$result[$event['id']]['created_stamp_iso'] = parse_user_date($event['stamp'], 'ISO');
			$result[$event['id']]['lastupdate'] = $event['lastupdate'];
		}

		return $result;
	}

	function get_history(&$limit = 10) {
		$query = $this->db->select()
			->from('log_history')
			->where('cardid', $this->UserM->get_card_id())
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
	//		$url = explode("?", $_SERVER['REQUEST_URI'],2);
	//        $log['furi'] = $uri[1];
	//        $uri = preg_replace("/app=".$this->url['app']."/", "", $uri[1]);
	//        $uri = preg_replace("/&an=".$this->url['action']."/", "", $uri);
	//        $uri = preg_replace("/&aved=".$this->url['subaction']."/", "", $uri);
	//        $uri = preg_replace("/&thisid=".$this->url['id_encrpted']."/", "", $uri);
	//        if ($uri == "&") $uri = "";
	//        $this->_request_uri = $uri;
	//	}
}
