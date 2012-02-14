<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helloworld extends MY_Controller {

	function __construct() {
		parent::__construct();

	}

	function sendhtml() {
		$data = array();
		$data['data'] = "I am a some HTML";
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);
	}

	function sendjson() {
		$data = array();
		$data['json'] = '{"element_type":"helloworld","message":"Hello World!"}';
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);
	}

	function sendjs() {
		$data = array();
		$data['json'] = '//js

		';//alert("Hello JS");
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);
	}

	function sendjson_list_x(){

		$data = array();
		$data['json'] = '{"success":"1","message":"1","template":"1","title":"Title","type":"list","details":{"columns":[{"sTitle":null},{"sTitle":null},{"sTitle":null},{"sTitle":null},{"sTitle":null},{"sTitle":""}],"data":[["3","Willson","W.","Willson","Intern7","<div class=\"ar bu-div\"><button type=\"button\" class=\"btn\" onclick=\"ajax_content(\'\/staff\/viewstaff\/nttpw%3D%3D\/v\',\'page\');\"><\/button><\/div>"],["4","John","J.","Jo","","<div class=\"ar bu-div\"><button type=\"button\" class=\"btn\" onclick=\"apps_action_pageload(\'\/staff\/viewstaff\/nttqA%3D%3D\/v\');\"><\/button><\/div>"]],"setting":{"hidetitle":"0"}}}';
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);

	}

	function sendjson_view(){

		$data = array();
		$data['json'] = '{"success":"1","message":"1","template":"1","title":"Title","type":"view","details":{"setting":{"hidelabel":"0"},"data":[{"fieldname":"firstname","label":"First Name","value":"Anthony"},{"fieldname":"lastname","label":"Last Name","value":"Andy"}]}}';

		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);

	}

	function sendjson_form(){

		$data = array();

		$the_dataarray = '{"label":"First Name","value":"Anthony","form_type":"text","name":"firstname","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"First Name","value":"Anthony","form_type":"text","name":"firstname","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"First Name","value":"Anthony","form_type":"text","name":"firstname","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';

		$data['json'] = '{"success":"1","message":"1","template":"1","title":"Title","type":"form","details":{"setting":{"hidelabel":"0"},"data":['.$the_dataarray.']}}';

		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);

	}

	function sendjson_list() {
		$this->url['subaction'] = 'l';

		$this->DatasetM->load('ds_helloworld');

		$details = array(
			'columns' => $this->DatasetM->get_datatable_fields(),
			'data' => $this->DatasetM->get_datatable_data(),
			'setting' => array(
				'hidetitle' => 0,
			),
		);

		$this->RespM->set_message('Message')
				->set_type('list')
				->set_template('list_template')
				->set_success(true)
				->set_title('Hello Dataset')
				->set_details($details)
				->output();

	}

}