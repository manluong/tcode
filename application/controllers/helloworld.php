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

		$this->RespM->set_message($this->DatasetM->sql)
				->set_type('list')
				->set_template('list_template')
				->set_success(true)
				->set_title('Hello Dataset')
				->set_details($details)
				->output_json();
	}

	function sendjson_view() {
		$this->url['subaction'] = 'v';
		$this->url['id_plain'] = '150';

		$this->DatasetM->load('ds_helloworld');

		$details = array(
			'data' => $this->DatasetM->get_view_data(),
			'setting' => array(
				'hidelabel' => 0,
			),
		);

		$this->RespM->set_message($this->DatasetM->sql)
				->set_type('view')
				->set_template('view_template')
				->set_success(true)
				->set_title('Hello Dataset')
				->set_details($details)
				->output_json();
	}

	function sendjson_form() {

		$data = array();

		$links = ',"links":[{"type":"submit","url":"/helloworld/contact/1/as","target":"","text":"Submit"},{"type":"ajax","url":"/helloworld/contact/1/v","target":"","text":"Cancel"}]';

		$the_dataarray = '{"label":"First Name","value":"Anthony","form_type":"text","name":"firstname","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Last Name","value":"Andy","form_type":"text","name":"lastname","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Email","value":"ea@abc.com","form_type":"email","name":"email","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Number","value":"32.10","form_type":"number","name":"number","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Phone","value":"65-0-96369636","form_type":"phone","name":"phone","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"URL","value":"http://www.telcson.com","form_type":"url","name":"url","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Checkbox","value":"1","form_type":"checkbox","name":"chkbox","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"No","chk_name1":"Yes","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Date","value":"2012-02-01","form_type":"date","name":"date","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"ISO_DATE","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"DateTime","value":"2011-01-01 12:00:00","form_type":"datetime","name":"datetime","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"ISO","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Time","value":"13:00:00","form_type":"time","name":"tine","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"ISO_TIME","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Select","value":"2","form_type":"select","name":"select","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 2","value":"2"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Select Switch","2":"Anthony","form_type":"select_switch","name":"selectswitch","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 2","value":"2"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Password","value":"***","form_type":"password","name":"password","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"File","value":"","form_type":"file","name":"file","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Text Area","value":"Something to go here!","form_type":"textarea","name":"textarea","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';
		$the_dataarray .= ',{"label":"Hidden","value":"secret hidden text","form_type":"hidden","name":"hidden","required":"","min":"","max":"","validate":"","pattern":"","chk_name0":"","chk_name1":"","date_showformat":"","sel_multiple":"","select_options":[{"key":"Option 1","value":"1"},{"key":"Option 1","value":"1"}],"helptext":"help me text"}';

		$data['json'] = '{"success":"1","message":"1","template":"1","title":"Title","type":"form","details":{"setting":{"hidelabel":"0"},"data":['.$the_dataarray.']'.$links.'}}';

		$data['isoutput'] = 1;
		$data['isdiv'] = 0;

		return($data);

	}

	function show_data() {

		$this->DatasetM->load('ds_helloworld');

		$data = $this->DatasetM->get_view_data();

		echo '<pre>', print_r($data, TRUE), '</pre>';
	}

	
}
