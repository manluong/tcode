<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_dgroup_form extends Element_dgroup {

	function __construct() {
		parent::__construct();
	}


function dgroup_form($dgroup_structure,$dgroup_value,$element_button,$aved){

	$count = 0;
	$previous_fnobr = 0;
	
	foreach (array_keys($dgroup_structure['fieldsort']) as $this_fieldname){

		$this_field = $dgroup_structure['table'][$dgroup_structure['fieldsort'][$this_fieldname]['tablenum']]['fields'][$dgroup_structure['fieldsort'][$this_fieldname]['fieldnum']];
		
		if (!$this_field['hide']){
		
		if ($this_field['fhidden']) $this_field['form_fieldtype'] = "19";
		
		$this_field['core_db_fields_name'] = $this_fieldname;

		
		//if this field form viewing is set to not break to a new line/tr(table), add the the this_field array of the next field
		if ($previous_fnobr) {
		  $this_field['fnobr_pre'] = 1;
		  $previous_fnobr = 0;
		  if (!$this_field['fnobr']) $this_field['fnobr_stop'] = 1;
		}
		if ($this_field['fnobr']) $previous_fnobr = 1;
		
		if (isset($dgroup_structure['fieldsort'][$this_fieldname]['lang_name'])) $this_field['form_name_lang'] = $this_field['form_name_lang'] . " (".$dgroup_structure['fieldsort'][$this_fieldname]['lang_name'].")";
		
		//$this_value
		//$this_value = $dgroup_value[$this_fieldname];
		//echo $this_fieldname.$dgroup_value[$this_fieldname];
		//print_r($dgroup_value);
        if ($aved == "a" && $this_field['db_defaultvalue']) {
        	$this_value = htmlspecialchars($field['db_defaultvalue'], ENT_QUOTES);
		} elseif (isset($dgroup_value[$this_fieldname])){
			$this_value = htmlspecialchars($dgroup_value[$this_fieldname], ENT_QUOTES);
		} else{
			$this_value = "";
		}
		
		$result['form']['field'][$count] = $this_field;
		$result['form']['field'][$count]['value'] = $this_value;
		$count++;
		}
	
	}

	//thisid is needed for cancel button
	$listid = "";
	if ($aved == "e" && $dgroup_structure['basetype'] == "list" && $dgroup_structure['thisidlist'][1]){
	$list_key = explode(".", $dgroup_structure['thisidlist'][1],2);
	$listid_sql = "SELECT ".$list_key[1]." FROM ".$dgroup_structure['table'][0]['table']." WHERE ".$dgroup_structure['table'][0]['index']." = '".$this->url['id_plain']."' LIMIT 1";
	$listid = $this->db->query($listid_sql);
	$listid = $listid->row_array(0);
	$listid = $listid[$list_key[1]];
	}
	
	$result['dgrouptype'] = "form";
	$result['element_button'] = $element_button;
	$result['element_button']['listid'] = $listid;
	//$result['autocomplete'] = $dgroup_structure['autocomplete']; 
	
return($result);	
}
	
function dgroup_view($dgroup_structure,$dgroup_value,$element_button,$aved){
	
		global $getthisid;
		
		$this_allfvnolabel = 0;
		$this_fvnolabel = 0;
		$previous_fvnobr = 0;
	
        if ($dgroup_structure['fieldsort']){

        if ($dgroup_structure['fvnolabel']) { $this_allfvnolabel = 1; $this_fvnolabel = 1; }

		$count = 0;
        foreach (array_keys($dgroup_structure['fieldsort']) as $this_fieldname){

            //core_db_fields_name
            $this_field = $dgroup_structure['table'][$dgroup_structure['fieldsort'][$this_fieldname]['tablenum']]['fields'][$dgroup_structure['fieldsort'][$this_fieldname]['fieldnum']];
			
			//because is viewing, ignore hidden field
            if (!$this_field['hide']){

			//set the logic for joining fields
            if ($previous_fvnobr) { $this_fvnobr['pre'] = 1; $previous_fvnobr = 0; } else { $this_fvnobr['pre'] = 0; }
            if ($this_field['fvnobr']) { $previous_fvnobr = 1; $this_fvnobr['this'] = 1; } else { $this_fvnobr['this'] = 0; }
            $this_fvnobr['pretxt'] = $this_field['fvnobrpre'];
            $this_fvnobr['subtxt'] = $this_field['fvnobrsub'];
			
			//field title
            if (isset($dgroup_structure['fieldsort'][$this_fieldname]['lang_name'])) $this_field['form_name_lang'] = $this_field['form_name_lang'] . " (".$dgroup_structure['fieldsort'][$this_fieldname]['lang_name'].")";

            //img
            if (isset($this_field['imguse'])){
                if ($this_fieldname == 'card_avatar'){
                    if ($dgroup_value[$this_fieldname] == "1"){
                    //$dgroup_value[$this_fieldname] = '<img class="img_'.$this_fieldname.'" src="'.$this_field['imgpath'].'/avatar_'.$getthisid['cardid'].'.jpg" />';
                    $form[$count]['imgvalue_src'] = $this_field['imgpath'].'/avatar_'.$getthisid['cardid'].'.jpg';
					}else{
                    //$dgroup_value[$this_fieldname] = '<img class="img_'.$this_fieldname.'" src="'.$this_field['imgpath'].'/preload/'.$dgroup_value[$this_fieldname].'.png" />';
                    $form[$count]['imgvalue_src'] = $this_field['imgpath'].'/preload/'.$dgroup_value[$this_fieldname].'.png';
					}
                } else {
                //$dgroup_value[$this_fieldname] = '<img class="img_'.$this_fieldname.'" src="'.$this_field['imgpath'].'/'.$this_field['imgpre'].$dgroup_value[$this_fieldname].$this_field['imgsub'].'" />';
                $form[$count]['imgvalue_src'] = $this_field['imgpath'].'/'.$this_field['imgpre'].$dgroup_value[$this_fieldname].$this_field['imgsub'];
                }
				$form[$count]['imgvalue'] = 1;
            }

            if (!$this_allfvnolabel) $this_fvnolabel = $this_field['fvnolabel'];

            //set fieldtype
            $form[$count]['type'] = $this_field['form_fieldtype'];
			
            switch($this_field['form_fieldtype']){

            //input
            case "1":
			$form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $dgroup_value[$this_fieldname];
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			
            ; break;

            //select
            case "2":
            $this_value = $this->Element_dgroup->core_element_dgroup_select_show($this_field['sellist'],$dgroup_value[$this_fieldname]);
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $this_value;
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			
			; break;

            //checkbox
            case "3":

                if ($dgroup_value[$this_fieldname]){
                //    if ($this_field['name_lang_chkname1_icon']){
                //    $this_value = '<img src="html/images/icons/16/'.$this_field['name_lang_chkname1_icon'].'" alt="'.$this_field['name_lang_chkname1'].'" title="'.$this_field['name_lang_chkname1'].'" width="16" height="16">';
                //    $this_field['name_lang_chkname1_icon'];
                //    } else {
                    $this_value = $this_field['name_lang_chkname1'];
                //    }
                } else {
                //    if ($this_field['name_lang_chkname0_icon']){
                //    $this_value = $this_field['name_lang_chkname0_icon'];
                //    } else {
                    $this_value = $this_field['name_lang_chkname0'];
                //    }
                }

            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $this_value;
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			
            ; break;

            //textarea
            case "4":
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = nl2br($dgroup_value[$this_fieldname]);
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			
            ; break;

            //radio
            case "5":
            ; break;

            //date
            case "6":
            //if ($dgroup_value[$this_fieldname]) $this_value = core_date_convert($this_field['date'],$this_field['date_showformat'],$dgroup_value[$this_fieldname]);
            if ($dgroup_value[$this_fieldname]) $this_value = parse_stamp_user($dgroup_value[$this_fieldname], $this_field['date_showformat']);
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $this_value;
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;			
			
            ; break;

            //datetime
            case "18":
            //if ($dgroup_value[$this_fieldname]) $this_value = core_date_convert($this_field['date'],$this_field['date_showformat'],$dgroup_value[$this_fieldname]);
			if ($dgroup_value[$this_fieldname]) $this_value = parse_stamp_user($dgroup_value[$this_fieldname], $this_field['date_showformat']);
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $this_value;
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			
			; break;

            //time
            case "12":
            //if ($dgroup_value[$this_fieldname]) $this_value = core_date_convert($this_field['date'],$this_field['date_showformat'],$dgroup_value[$this_fieldname]);
			if ($dgroup_value[$this_fieldname]) $this_value = parse_stamp_user($dgroup_value[$this_fieldname], $this_field['date_showformat']);
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $this_value;
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			
			; break;

            //password
            case "7":
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = "******";
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;			
            ; break;

            case "8":
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $dgroup_value[$this_fieldname];
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			; break;

            case "9":
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $dgroup_value[$this_fieldname];
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			; break;

            case "10":
            $form[$count]['title'] = $this_field['form_name_lang'];
			$form[$count]['value'] = $dgroup_value[$this_fieldname];
			$form[$count]['name'] = $this_fieldname;
			$form[$count]['fvnolabel'] = $this_fvnolabel;
			$form[$count]['fvnobr'] = $this_fvnobr;
			; break;
			
            }

        }

		$count++;
        }

        //for button that is using a FIELDxx replace (use a field value in a button),
        //check if match this foreach fieldname value, change the button is so
    
    	$count = 0;
		while (isset($element_button['buttons'][$count])){

			if (substr($element_button['buttons'][$count]['targetid'],0,5) == "FIELD") {
				$element_button['buttons'][$count]['thisid'] = encode_id($dgroup_value[substr($element_button['buttons'][$count]['targetid'],5)]);
			}
			
			if (isset($element_button['buttons'][$count]['targetvalue']) && preg_match("/FIELD/",$element_button['buttons'][$count]['targetvalue'])){
	        	$this_targetvalue_array = explode("FIELD", $element_button['buttons'][$count]['targetvalue']);
	            foreach ($this_targetvalue_array as $this_targetvalue) {
	            	$this_targetvalue_name = explode("XX", $this_targetvalue);
					$element_button['buttons'][$count]['targetvalue'] = preg_replace("/FIELD".$this_targetvalue_name[0]."/XX",$dgroup_value[$this_targetvalue_name[0]],$element_button['buttons'][$count]['targetvalue']);
				}	
						
			}
				

		$count++;		
		}
			

        }//end if fieldsort

	//thisid is needed for cancel button
	$listid = "";
	if ($aved == "d" && $dgroup_structure['basetype'] == "list" && $dgroup_structure['thisidlist'][1]){
	$list_key = explode(".", $dgroup_structure['thisidlist'][1],2);
	$listid_sql = "SELECT ".$list_key[1]." FROM ".$dgroup_structure['table'][0]['table']." WHERE ".$dgroup_structure['table'][0]['index']." = '".$this->url['id_plain']."' LIMIT 1";
	$listid = $this->db->query($listid_sql);
	$listid = $listid->row_array(0);
	$listid = $listid[$list_key[1]];
	}
	
	$result['dgrouptype'] = "view";	
	$result['element_button'] = $element_button;
	$result['element_button']['listid'] = $listid;
	$result['form']['field'] = $form;
	if ($this_allfvnolabel) $result['form']['fvnolabel'] = 1;
	
return ($result);
}        



}