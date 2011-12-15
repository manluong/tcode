<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Log extends CI_Model {
	var $start_time = 0;
	var $log_type = array();

	function __construct() {
		parent::__construct();
		$this->load->helper('date');
	}

	public function start_log($app, $action, $subaction, $id) {
		$this->start_timer();
		$this->log_type = $this->get_log_type($app, $action, $subaction, $id);
		if ( ! $this->log_type['nolog']) {
			$this->do_log();
		}
	}

	public function stop_log() {
		$this->insert_log_eventsub();
		$this->insert_log_history();
		$this->end_timer();
		$log_id = $this->insert_log();


	}
	public function get_history() {
		$query = $this->db->select('furi, text')
				->from('log_history')
				->where('id', $this->User->info['cardid'])
				->order_by('stamp','desc')
				->limit(10);
		return $db->row_array($sql1);
	}

	private function get_log_type() {
		$query = $this->db->get_where('log_type', array('app'=> $this->app, 'an'=>$this->action, 'aved'=>$this->subaction),1);
		return $query->row_array();
	}

	private function do_log() {
		($this->get_log_session_id()) ? : $this->insert_log_session();
	}

	private function get_log_session_id() {
		$query = $this->db->select('id')
				->from('log_session')
				->where('loguid', $this->User->get_loguid())
				->limi(1);
		return $query->row;
	}

	private function insert_log_session() {
		$data = array(
			'stamp' => mdate("%Y-%n-%j %H:%i:%s"),
			'loguid' => $this->User->get_loguid(),
			'agent' => $_SERVER['HTTP_USER_AGENT'],
			'dev' => '0',
			'ip' => inet_pton($_SERVER['REMOTE_ADDR'])
		);
		$this->db->insert('log_session', $data);
		return $this->db->insert_id();
	}

	private function insert_log() {
		$url = explode("?", $_SERVER['REQUEST_URI'],2);
        $log['furi'] = $uri[1];
        $uri = preg_replace("/app=".$app."/", "", $uri[1]);
        $uri = preg_replace("/&an=".$an."/", "", $uri);
        $uri = preg_replace("/&aved=".$aved."/", "", $uri);
        $uri = preg_replace("/&thisid=".$thisid."/", "", $uri);
        if ($uri == "&") $uri = "";
        $log['uri'] = $uri;

        $data = array(
            'stamp' => mdate("%Y-%n-%j %H:%i:%s"),
            'cardid' => $this->User->info['cardid'],
            'gpmid' => $this->User->info['gpmid'],
            'app' => $app,
            'an' => $an,
            'aved' => $aved,
            'tidorg' => $thisid,
            'uri' => $uri,
            'loguid' => $this->User->get_loguid(),
            'load' => 1
        );
        $this->db->insert('log', $data);
	}

	private function insert_log_eventsub() {
		if ($log['eventfield']){
            foreach (array_keys($this->log_type['eventfield']) as $field){
                 if (($aved == "as" && $this->log_type['eventfield'][$field]['new'])||
                     ($aved == "es" && $this->log_type['eventfield'][$field]['cur'] != $this->log_type['eventfield'][$field]['new'])) {
					$data = array(
						'eventid' => $logeventid,
						'field' => $field,
						'fr' => $$this->log_type['eventfield'][$field]['cur'],
						'to' => $this->log_type['eventfield'][$field]['new']
					);
					$this->db->insert('log_eventsub', $data);
				}
            }
        }
	}

	private function update_log() {
		$data = array(
			'tid' => $thisid[0],
			'tid2' => $thisid[1],
			'tid3' => $thisid[2],
			'tid4' => $thisid[3],
			'saveid' => $this->log_type['saveid'], //fix
			'ms' => $total_time,
			'load' => 0,
			'xmsgid' => 0
		);
		$this->db->update('log', $data)
				->where('id',$log_id);
	}

	private function insert_log_history() {
		if ($log['history']) {
        //check if this link already exist in history
        $sql = "SELECT id FROM log_history WHERE furi = '".$log['furi']."' LIMIT 1";
        $id_existhis = $db->fetchOne($sql, 2);

        //if exist, just update the timestamp so it will push to the top of history list
        if ($id_existhis) $db->delete('log_history', 'id = '.$id_existhis);

        //    $data = array(
        //        'stamp' => date("Y-n-j H:i:s")
        //    );
        //    $db->update('log_history', $data, 'id = '.$id_existhis);

        //if do not exist, insert
        //} else {

            if ($log['history_msg']){

                $patterns = array();
                $patterns[0] = '/#thisid#/';
                $patterns[1] = '/#app#/';
                $replacements = array();
                $replacements[0] = $thisid[0];
                $replacements[1] = $lang['core']['apptitle_'.$app];
                $text = preg_replace($patterns, $replacements, $log['history_msg']);

            } else {

                switch($shortaved){
                      case "a": $text = $lang['core']['his_add']; break;
                      case "v": $text = $lang['core']['his_view']; break;
                      case "e": $text = $lang['core']['his_edit']; break;
                      case "d": $text = $lang['core']['his_delete']; break;
                      case "l": $text = $lang['core']['his_list']; break;
                      case "s": $text = $lang['core']['his_search']; break;
                }

                $text .= " ".$lang['core']['apptitle_'.$app]." - ";

                if ($app == "card" || $app == "client" || $app == "vendor" || $app == "staff" || $app == "invoice" || $app == "product"){
                    //$text .= core_d_element_dgroup_select_getopt("",$app,$thisid[0]);
                    $text .= core_app_id2name($app,$thisid[0],1);
                } else {
                    $text .= $thisid[0];
                }

            }

            $data = array(
                'stamp' => date("Y-n-j H:i:s"),
                'typeid' => $log['typeid'],
                'cardid' => $id['cardid'],
                'text' => $text,
                'furi' => $log['furi']
            );
            $db->insert('log_history', $data);

            //only keep 10 history for each card
            //since new history inserted
            $sql = "SELECT id FROM log_history WHERE cardid = '".$id['cardid']."' ORDER BY stamp DESC LIMIT 10,10";
            $result_oldhis = $db->fetchAll($sql, 2);
            if ($result_oldhis){
                foreach ($result_oldhis as $this_oldhis) {
                  $db->delete('log_history', 'id = '.$this_oldhis['id']);
                }
            }
		}
	}

	private function check_log_history() {
		
	}

	private function start_timer() {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$this->start_time = $time;
	}

	private function end_timer() {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $this->start), 4);
		return($total_time);
	}
}
