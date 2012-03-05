<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_button extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function Element_button_getbutton($app, $an, $sql_aved){

	    $sql = "SELECT * FROM global_setting.core_apps_action_element_button WHERE core_apps_action_element_button_app = '$app' AND core_apps_action_element_button_an = '$an' AND core_apps_action_element_button_usein = '$sql_aved' ORDER BY core_apps_action_element_button_position,core_apps_action_element_button_sort ASC";
	    $rs = $this->db->query($sql);

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->result_array();
	}

function core_element_button($app,$an,$this_element_aved,$this_element_id,$this_element_add,$this_element_view,$this_element_edit,$this_element_del,$this_element_list,$this_element_search,$dgourp_value_notfound,$basetype=0,$formadd_allowforceid=0,$button_e_xtra=0){

    $sql_aved = $this_element_aved;
    if (substr($sql_aved,1,1) == "d") $sql_aved = substr($sql_aved,0,1);

	$result1 = $this->Element_button_getbutton($app, $an, $sql_aved);

    $totalbutton = count($result1);
    $countbutton = 0;
	$buttonallicon = 0;

    if ($basetype=="list"){
        $form_or_list = "l";
        $form_or_list_id = "listid";
        $form_or_list_id_e = "listid";
        $form_or_list_id_a = "thisid";
        $form_or_list_id_as = "listid";
    } else {
        $form_or_list = "v";
        $form_or_list_id = "thisid";
        $form_or_list_id_e = "thisid";

            if ($formadd_allowforceid){
            $form_or_list_id_as = "thisid";
            $form_or_list_id_a = "thisid";
            } else {
            $form_or_list_id_as = "rspid";
            $form_or_list_id_a = "";
            }
    }
    //set position
    if ($result1){
        if ($totalbutton == 1 && !$result1[0]['core_apps_action_element_button_type']){
            $buttonposition = $result1[0]['core_apps_action_element_button_position'];
            $buttonallicon = $result1[0]['core_apps_action_element_button_icononly'];
            $buttonlangapp = "core";
        }else{
            foreach ($result1 as $field1) {
            $button[$countbutton]['type'] = $field1['core_apps_action_element_button_type'];
            if ($button[$countbutton]['type'] == "listtitle") $element_button['listtitle'] = 1;

            $button[$countbutton]['targetaved'] = $field1['core_apps_action_element_button_targetaved'];
            $button[$countbutton]['targetapp'] = $this_targetapp = $field1['core_apps_action_element_button_targetapp'];
            $button[$countbutton]['targetan'] = $this_targetan = $field1['core_apps_action_element_button_targetan'];
            $button[$countbutton]['targetid'] = $field1['core_apps_action_element_button_targetid'];
            $button[$countbutton]['targeturl'] = $field1['core_apps_action_element_button_targeturl'];
            $button[$countbutton]['targetvalue'] = $field1['core_apps_action_element_button_targetvalue'];
            $button[$countbutton]['div'] = $field1['core_apps_action_element_button_div'];
            $button[$countbutton]['lang'] = $this->lang->line($app."elementbu_".$field1['core_apps_action_element_button_lang']);
            if (!$button[$countbutton]['lang']) $button[$countbutton]['lang'] = $this->lang->line('core'.$field1['core_apps_action_element_button_lang']);
            $button[$countbutton]['position'] = $field1['core_apps_action_element_button_position'];
            $button[$countbutton]['align'] = $field1['core_apps_action_element_button_align'];
            $button[$countbutton]['icononly'] = $field1['core_apps_action_element_button_icononly'];

            if (!$button[$countbutton]['targetapp']) $button[$countbutton]['targetapp'] = $this_targetapp = $app;
            if (!$button[$countbutton]['targetan']) $button[$countbutton]['targetan'] = $this_targetan = $an;
            if (!$button[$countbutton]['targetaved']) $button[$countbutton]['targetaved'] = $button[$countbutton]['type'];
            if (!$button[$countbutton]['targetid']) $button[$countbutton]['targetid'] = "thisid";
            if (!$button[$countbutton]['position']) $button[$countbutton]['position'] = "bottom";

                if (!$button[$countbutton]['div'] && $button[$countbutton]['targetapp'] && $button[$countbutton]['targetan']){
                $sql2 = "SELECT core_apps_action_element_name,core_apps_action_element_divname FROM global_setting.core_apps_action_element WHERE core_apps_action_element_x_core_apps_name = '$this_targetapp' AND core_apps_action_element_x_core_apps_action_name = '$this_targetan' AND core_apps_action_element_active = '1' LIMIT 1";
				$result2 = $this->db->query($sql2);
				$result2 = $result2->row_array(0);

                    if ($result2['core_apps_action_element_divname']){
                    $button[$countbutton]['div'] = $result2['core_apps_action_element_divname'];
                    }else{
                    $button[$countbutton]['div'] = "div".$result2['core_apps_action_element_name'];
                    }
                }

            if (!$button[$countbutton]['lang']){
                $this_buttonavedtype = $field1['core_apps_action_element_button_type'];
                substr($this_buttonavedtype, 0, 1);
                switch($this_buttonavedtype){
                    case "a": $button[$countbutton]['lang']=$this->lang->line('corebutton_add'); break;
                    case "as": $button[$countbutton]['lang']=$this->lang->line('corebutton_add'); break;
                    case "v": $button[$countbutton]['lang']=$this->lang->line('corebutton_view'); break;
                    case "e": $button[$countbutton]['lang']=$this->lang->line('corebutton_edit'); break;
                    case "d": $button[$countbutton]['lang']=$this->lang->line('corebutton_del'); break;
                    case "c": $button[$countbutton]['lang']=$this->lang->line('corebutton_cancel'); break;
                    case "l": $button[$countbutton]['lang']=$this->lang->line('corebutton_list'); break;
                    case "s": $button[$countbutton]['lang']=$this->lang->line('corebutton_search'); break;
                }
            }

            $countbutton++;
            }
        }
    } else {
        $buttonposition = "bottom";
        $buttonlangapp = "core";

    }

    if ($countbutton==0 && $buttonposition != "none"){
    //not button defined, use default
        $element_button['autobutton'] = "1";
		$buttonposition_list = "rowend";

        if ($this_element_aved == "v"){

              if ($this_element_edit && !$dgourp_value_notfound) {
              $button[$countbutton]['type']="e";$button[$countbutton]['targetaved']="e";
              $button[$countbutton]['targetid']="thisid";$button[$countbutton]['lang']=$this->lang->line('corebutton_edit');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              }
              if ($this_element_del && !$dgourp_value_notfound) {
              $button[$countbutton]['type']="d";$button[$countbutton]['targetaved']="d";
              $button[$countbutton]['targetid']="thisid";$button[$countbutton]['lang']=$this->lang->line('corebutton_del');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              }
              if ($this_element_add && $dgourp_value_notfound) {
              $button[$countbutton]['type']="a";$button[$countbutton]['targetaved']="a";
              $button[$countbutton]['targetid']=$form_or_list_id_a;$button[$countbutton]['lang']=$this->lang->line('corebutton_add');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

        }elseif ($this_element_aved == "l" || $this_element_aved == "ld" || $this_element_aved == "ss" || $this_element_aved == "sq"){

              if ($this_element_add) {
              $button[$countbutton]['type']="a";$button[$countbutton]['targetaved']="a";$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']=$form_or_list_id_a;$button[$countbutton]['lang']=$this->lang->line('corebutton_add');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['autobutton']="1";
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              }
              if ($this_element_view) {
              $button[$countbutton]['type']="v";$button[$countbutton]['targetaved']="v";$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']="rid";$button[$countbutton]['lang']=$this->lang->line('corebutton_view');
              $button[$countbutton]['position']=$buttonposition_list;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['autobutton']="1";
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              }
              if ($this_element_edit) {
              $button[$countbutton]['type']="e";$button[$countbutton]['targetaved']="e";$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']="rid";$button[$countbutton]['lang']=$this->lang->line('corebutton_edit');
              $button[$countbutton]['position']=$buttonposition_list;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['autobutton']="1";
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              }
              if ($this_element_del) {
              $button[$countbutton]['type']="d";$button[$countbutton]['targetaved']="d";$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']="rid";$button[$countbutton]['lang']=$this->lang->line('corebutton_del');
              $button[$countbutton]['position']=$buttonposition_list;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['autobutton']="1";
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

        }elseif ($this_element_aved == "s" || $this_element_aved == "sd"){

              if ($this_element_search) {
              $button[$countbutton]['type']="ss";$button[$countbutton]['targetaved']="ss";$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']="thisid";$button[$countbutton]['lang']=$this->lang->line('corebutton_search');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

         }elseif ($this_element_aved == "a" || $this_element_aved == "ad"){

			/*
              if ($this_element_add) {
              $button[$countbutton]['type']="as";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']=$form_or_list_id_as;$button[$countbutton]['lang']=$lang['core']['button_save'];
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              $countbutton++;
              $button[$countbutton]['type']="c";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']=$form_or_list_id;$button[$countbutton]['lang']=$lang['core']['button_cancel'];
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }
			*/

              if ($this_element_add) {
              $button[$countbutton]['type']="as";$button[$countbutton]['targetaved']="as";
              $button[$countbutton]['targetid']=$form_or_list_id_as;$button[$countbutton]['lang']=$this->lang->line('corebutton_save');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              $countbutton++;
              $button[$countbutton]['type']="c";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']="thisid";$button[$countbutton]['lang']=$this->lang->line('corebutton_cancel');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

        }elseif ($this_element_aved == "as"){

              if ($this_element_add) {
              $button[$countbutton]['type']="as";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']=$form_or_list_id_as;$button[$countbutton]['lang']=$this->lang->line('corebutton_save');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

        }elseif ($this_element_aved == "e" || $this_element_aved == "ed"){

			/*
              if ($this_element_edit) {
              $button[$countbutton]['type']="es";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']=$form_or_list_id_e;$button[$countbutton]['lang']=$lang['core']['button_save'];
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              $button[$countbutton]['type']="c";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']=$form_or_list_id;$button[$countbutton]['lang']=$lang['core']['button_cancel'];
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }
			*/
              if ($this_element_edit) {
              $button[$countbutton]['type']="es";$button[$countbutton]['targetaved']="es";
              $button[$countbutton]['targetid']="thisid";$button[$countbutton]['lang']=$this->lang->line('corebutton_save');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if (isset($buttonallicon)) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              $button[$countbutton]['type']="c";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']=$form_or_list_id;$button[$countbutton]['lang']=$this->lang->line('corebutton_cancel');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if (isset($buttonallicon)) $button[$countbutton]['icononly'] = '1';
              }

        }elseif ($this_element_aved == "es"){

              if ($this_element_edit) {
              $button[$countbutton]['type']="es";$button[$countbutton]['targetaved']=$form_or_list;
              $button[$countbutton]['targetid']=$form_or_list_id_e;$button[$countbutton]['lang']=$this->lang->line('corebutton_edit');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

        }elseif ($this_element_aved == "d"){

              if ($this_element_del) {
              $button[$countbutton]['type']="ds";$button[$countbutton]['targetaved']="ds";$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']="thisid";$button[$countbutton]['lang']=$this->lang->line('corebutton_del');
              $button[$countbutton]['lang__d']=$this->lang->line('corebutton_del__d');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              $countbutton++;
              $button[$countbutton]['type']="c";$button[$countbutton]['targetaved']=$form_or_list;$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']=$form_or_list_id;$button[$countbutton]['lang']=$this->lang->line('corebutton_cancel');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

        }elseif ($this_element_aved == "ds"){

              if ($this_element_list || $this_element_view) {
              $button[$countbutton]['type']="v";$button[$countbutton]['targetaved']=$form_or_list;$button[$countbutton]['position']=$buttonposition;
              $button[$countbutton]['targetid']=$form_or_list_id;$button[$countbutton]['lang']=$this->lang->line('corebutton_cancel');
              $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
              $button[$countbutton]['align'] = '1';
              if ($buttonallicon) $button[$countbutton]['icononly'] = '1';
              }

        }


    }


    //add button for $button_e_xtra so can switch between language
    if ($button_e_xtra){
        $element_button['button_e_xtra'] = "1";
        $buttonposition = "top";
        $buttonlangapp = "core";

        if ($this_element_aved == "v" && $this_element_view){

              foreach (array_keys($this->lang->langinfo['langlist']) as $thislangcode){
                  if ($thislangcode != $this->lang->langinfo['use']) {
                  $button[$countbutton]['type']="v";$button[$countbutton]['targetaved']="v";$button[$countbutton]['position']=$buttonposition;
                  $button[$countbutton]['targetid']="thisid";$button[$countbutton]['lang']=$this->lang->langinfo['langlist'][$thislangcode];
                  $button[$countbutton]['position']=$buttonposition;$button[$countbutton]['div']=$this_element_id;$button[$countbutton]['targetapp']=$app;$button[$countbutton]['targetan']=$an;
                  $button[$countbutton]['button_e_xtra']="1";
                  $button[$countbutton]['thislang']=$thislangcode;
                  $button[$countbutton]['align'] = '1';
                  $countbutton++;
                  }
              }

        }

    }

    $element_button['buttons'] = $button;

return($element_button);
}

}



?>