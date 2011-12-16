<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class App extends CI_Model {
	var $url = array();
	var $actions = array();

	var $public_apps = array('main', 'dashboard');

	function __construct() {
		parent::__construct();

		$CI =& get_instance();
		$this->url = $CI->url;
	}

	function setup() {
		if ($this->get_status($this->url['app'])) $this->actions = $this->get_actions($this->url['app'], $this->url['action']);
	}

	function has_actions() {
		return (count($this->actions)>0);
	}

	function must_disable_plain_id() {
		if (!$this->has_actions()) return FALSE;
		return ($this->actions['core_apps_action_disableplainid'] == 1);
	}

	function has_public_access() {
		if (in_array($this->url['app'], $this->public_apps)) return TRUE;
		
		return ($this->actions['core_apps_action_public'] == 1);
	}

	function get_status($app) {
		$rs = $this->db->select('core_apps_status')
				->where('core_apps_name', $app)
				->where('core_apps_status', 1)
				->get('core_apps', 1);

		if ($rs->num_rows()==0) return 0;

		$result = $rs->row_array();
		return $result['core_apps_status'];
	}

	function get_actions($app, $action) {
		$rs = $this->db->select()
				->where('core_apps_action_x_core_apps_name', $app)
				->where('core_apps_action_name', $action)
				->where('core_apps_action_active', 1)
				->get('core_apps_action', 1);

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->row_array();
	}

	function get_default_actions($app) {
		$rs = $this->db->select()
				->where('core_apps_action_x_core_apps_name', $app)
				->where('core_apps_action_default', 1)
				->where('core_apps_action_active', 1)
				->get('core_apps_action');

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->result_array();
	}

	function get_action_element($app, $action){

		$sql = "SELECT * FROM core_apps_action_element WHERE core_apps_action_element_x_core_apps_action_name = '$action' AND core_apps_action_element_active = '1' AND core_apps_action_element_x_core_apps_name = '$app' ORDER BY core_apps_action_element_sort ASC";
		$rs = $this->db->query($sql);

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->result_array();

	}

	function get_this_element($this_element, $mfunction, $apps_action){

        $rs['ajax'] = $this_element['core_apps_action_element_ajax'];
        $rs['subaction'] = $this_element['core_apps_action_element_aved'];
		if (!$rs['subaction']) $rs['subaction'] =  $this->url['subaction'];
        $rs['thisid'] = $this_element['core_apps_action_element_thisid'];

        $rs['divstyle'] = $this_element['core_apps_action_element_actlayout'];
        $rs['dgroupextend'] = $this_element['core_apps_action_element_dgroupextend'];
        $rs['langname'] = $this_element['core_apps_action_element_langname'];

        $rs['divwh'] = $this_element['core_apps_action_element_divwh'];
        $rs['tab'] = $this_element['core_apps_action_element_tab'];
        $rs['colnum'] = $this_element['core_apps_action_element_colnum'];

        if (!$rs['ajax']){

			//if this is not a ajax call
			//use the aved setting from the current AN
            $rs['target_an'] = $apps_action['core_apps_action_name'];
            $rs['add'] = $apps_action['core_apps_action_add'];
            $rs['view'] = $apps_action['core_apps_action_view'];
            $rs['edit'] = $apps_action['core_apps_action_edit'];
            $rs['del'] = $apps_action['core_apps_action_del'];
            $rs['list'] = $apps_action['core_apps_action_list'];
            $rs['search'] = $apps_action['core_apps_action_search'];
            $rs['form'] = $apps_action['core_apps_action_form'];
            $rs['type'] = $this_element['core_apps_action_element_type'];
            $rs['name'] = $this_element['core_apps_action_element_name'];

        } else {

			//if this is a ajax call
			//get the aved from the target AN
            if (!isset($mfunction['target_an'])) {
            $rs['target_an'] = $this_element['core_apps_action_element_target_an'];
            } else {
            $rs['target_an'] = $mfunction['target_an'];
            $rs['subaction'] = $mfunction['element_aved'];
            }

			$this_ajax_an = $this->get_actions($this->url['app'], $rs['target_an']);
			if (isset($this_ajax_an[0])) $this_ajax_an = $this_ajax_an[0];

			//echo "<br>"; print_r($get_actions);
            $rs['add'] = $this_ajax_an['core_apps_action_add'];
            $rs['view'] = $this_ajax_an['core_apps_action_view'];
            $rs['edit'] = $this_ajax_an['core_apps_action_edit'];
            $rs['del'] = $this_ajax_an['core_apps_action_del'];
            $rs['list'] = $this_ajax_an['core_apps_action_list'];
            $rs['search'] = $this_ajax_an['core_apps_action_search'];
            $rs['from'] = $this_ajax_an['core_apps_action_form'];

			$this_ajax_element = $this->get_action_element($this->url['app'], $rs['target_an']);
			if (isset($this_ajax_element[0])) $this_ajax_element = $this_ajax_element[0];

			$rs['type'] = $this_ajax_element['core_apps_action_element_type'];
            $rs['name'] = $this_ajax_element['core_apps_action_element_name'];
            if (!$rs['langname']) $rs['langname'] = $this_ajax_element['core_apps_action_element_langname'];

        }

        if ($this_element['core_apps_action_element_divname']) {
            $rs['element_id'] = $this_element['core_apps_action_element_divname'];
        } else {
            $rs['element_id'] = "div".$rs['name'];
        }

		return $rs;
	}

	/*
	function get_thisid($thisid, $disableplainid=0){

		//returning
		//[0] = decoded/real plain id
		//[encode] = encoded id to be used in url
		//some old code ported over still use $thisid_en for encoded thisid
		if(substr($thisid, 0, 1) == "n"){
		    $rs['encode'] = $thisid;
		    $rs['thisid'] = decode_id($thisid);
		}else{
		    if ($disableplainid == 1){
			//this action is stopped becasue this an do not allow passing in of unencrypted thisid
			//to replace this with proper message/alert
		    //meg(102,"Operation Ignored.");
		    }else{
		    $rs['encode'] = encode_id($thisid);
		    //$rs['thisid'] = explode("|", $thisid);
			$rs[0] = $thisid;
		    }
		}
		return $rs;
	}
	*/
}