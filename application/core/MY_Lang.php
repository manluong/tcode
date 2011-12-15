<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MY_Lang extends CI_Lang {
	var $lang_use = "";
	var $langinfo = array();
	
	function __construct() {
		parent::__construct();
		
	}

	function line($line)
	{	
		if (isset($this->language[$line])) return $this->language[$line];
	}
	
	function loadarray($this_array){	
		$this->language = array_merge($this->language, $this_array);
	}
	
	function initialise($langsetting, $app = ""){
		
		foreach ($langsetting as $field) {
		    $this->langinfo['langlist'][$field['lang_use_code']] = $field['lang_use_name'];
		    if ($field['lang_use_default']) {
		        $this->langinfo['use'] = $this->langinfo['default'] = $this->lang_use = $field['lang_use_code'];
		    }
		}
		
		//if there is a request send by _GET or _POST with specified LANG, change the ['use'] to the request LANG
		//if ($CGET['thislang']) { $langinfo['thislang'] = $CGET['thislang']; $langinfo['thislang_get'] = 1; } else { $langinfo['thislang'] = $langinfo['use']; }
		
	}

	function f_layout_listlang($this_list,$app){
	     $this_count = 0;
	     $this_array = explode(",",$this_list);
	     while ($this_array[$this_count]){
	         if($newlist)$newlist.=",";
	         if (!$lang[$app]) $lang[$app] = f_layout_lang_load($app);
	         $newlist.=$lang[$app][$this_array[$this_count]];
	         $this_count++;
	     }
	     //print_r($lang);echo "123";exit;
	     //echo $lang[$app]['core_apps_id'];
	     //echo "xx".$app."xx".$newlist;exit;
	return($newlist);
	}


}