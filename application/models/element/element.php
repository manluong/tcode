<?php


function element_fdata_from_dgroup($dgroup_name,$fdata_name,$thisid,$thisapp=0,$thisaved=0){

    global $DOCUMENT_ROOT,$app;

    if (!$thisapp) $thisapp = $app;
    if (!$thisaved) $thisaved = "v";

    //print_r($thisapp);print_r($thisid);

    include_once $DOCUMENT_ROOT.'/includes/core/element/element_dgroup_structure.inc';
    $dgroup_structure = core_element_dgroup_structure($thisapp,$thisaved,$dgroup_name,$thisid,0);

    //print_r($dgroup_structure);

    $dgroup_value = core_element_dgroup_value($dgroup_structure);
    include_once $DOCUMENT_ROOT.'/includes/core/element/element_fdata.inc';
    $f_element_fdata = element_fdata($fdata_name,$dgroup_value);
    //print_r($f_element_fdata);

return($f_element_fdata);
}







?>