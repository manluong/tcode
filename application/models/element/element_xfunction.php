<?php

function f_element_xfunction($this_element_name,$an,$app,$aved,$thisid,$thisid_en,
$this_element_aved,$this_element_id,$this_element_add,$this_element_view,$this_element_edit,$this_element_del,$this_element_list,$this_element_search){

global $db,$DOCUMENT_ROOT,$lang,$id,$langinfo;



$sql1 = "SELECT * FROM core_e_xfunction WHERE core_e_xfunction_name = '$this_element_name' and core_e_xfunction_app = '$app' LIMIT 1";
$result1 = $db->fetchRow($sql1, 2);
//echo $this_element_name;

if ($result1){

    $xfunction['found'] = 1;

    if ($result1['core_e_xfunction_aved'] && $result1['core_e_xfunction_aved'] == $aved){

    $xfunction['checking'] = 1;

    } elseif (!$result1['core_e_xfunction_aved']){

    $xfunction['checking'] = 1;

    }

    if ($result1['core_e_xfunction_incfile']){

        include_once $DOCUMENT_ROOT.'/includes/apps/'.$app.'/'.$result1['core_e_xfunction_incfile'];
        //ini_set('display_errors','Off');
    }

    if ($result1['core_e_xfunction_callfunction']) $thisresult = call_user_func($result1['core_e_xfunction_callfunction']);




}else{
   $xfunction['found'] = 0;
   //$h_apps_html[$count_element]['html'] .= "<br>No Such mFunction Name: ".$table;
}

	
  //$xfunction = array_merge($xfunction,$xfunction_result);
  //print_r($xfunction);
  //echo $xfunction['html'];

  //if ($this_result['html']) $this_result['html'] = $this_result['html'];

	if ($result1['core_e_xfunction_useoutput']){
		$thisresult['app_output_file'] = $result1['core_e_xfunction_incfile'];	
	}
	
return($thisresult);
}







?>