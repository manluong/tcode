<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_mfunction extends CI_Model {

	function __construct() {
		parent::__construct();
	}
	
function f_element_mfunction($this_element_name){

global $db,$DOCUMENT_ROOT,$thisid,$thisid_en,$an,$app,$aved;

$sql1 = "SELECT * FROM core_e_mfunction WHERE core_e_mfunction_name = '$this_element_name' LIMIT 1";
$result1 = $db->fetchRow($sql1, 2);
//echo $this_element_name;

if ($result1){

    $mfunction['found'] = 1;

    if ($result1['core_e_mfunction_aved'] && $result1['core_e_mfunction_aved'] == $this->url['subaction']){

    $mfunction['checking'] = 1;

    } elseif (!$result1['core_e_mfunction_aved']){

    $mfunction['checking'] = 1;

    }

    //if sql is found, run SQL for this mFuntion, else, check next for function
    if ($mfunction['checking'] && $result1['core_e_mfunction_sql']){

         //$h_apps_html[$count_element] = $result1['core_e_mfunction_sql'];
         //echo $result1['core_e_mfunction_sql'];
         //$h_apps_html[$count_element]['html'] = "123";


        //replace the XXid
        $result1['core_e_mfunction_sql'] = preg_replace("/XXthisid/",$thisid[0], $result1['core_e_mfunction_sql']);
        //echo $result1['core_e_mfunction_sql'];

        $mfunction['checkusing'] = "sql";

        $passresult = 0;
        $passmethod = $result1['core_e_mfunction_sqlpass'];

        if ($passmethod == 3 || $passmethod == 4 || $passmethod == 5 || $passmethod == 11){

              $result2 = $db->fetchOne($result1['core_e_mfunction_sql'], 2);
              if ($result2){
                  if ($passmethod == 11 && $result1['core_e_mfunction_switchthisid']){
                  $mfunction['thisid'] = $result2;
					
				  //switching thisid
				  //thisid and thisid_en already global
				  
	              $thisid_en = f_thisid_encode($mfunction['thisid']);
	              $thisid = explode("|", $mfunction['thisid']);
					  
                  $passresult = 1;
                  }elseif ($passmethod == 5 && $result2 == $result1['core_e_mfunction_sqlvalue']){
                  $passresult = 1;
                  }elseif ($passmethod == 4 && $result2 > $result1['core_e_mfunction_sqlvalue']){
                  $passresult = 1;
                  }elseif ($passmethod == 3 && $result2 < $result1['core_e_mfunction_sqlvalue']){
                  $passresult = 1;
                  }
              }
        }

        if ($passmethod == 1 || $passmethod == 2 || $passmethod == 9 || $passmethod == 10){

            $result2 = $db->fetchAll($result1['core_e_mfunction_sql'], 2);
            if ($result2){

                if ($passmethod == 1) {
                      $passresult = 1;
                }elseif ($passmethod == 9 || $passmethod == 10){

                      $totalcount = count($result2);
                      if ($passmethod == 9 && $totalcount < $result1['core_e_mfunction_sqlvalue']){
                      $passresult = 1;
                      }elseif ($passmethod == 10 && $totalcount > $result1['core_e_mfunction_sqlvalue']){
                      $passresult = 1;
                      }

                }

            }else{
               if ($passmethod == 2) $passresult = 1;
            }
        }

        if ($passmethod == 6) $passresult = 1;

        if ($passmethod == 7 || $passmethod == 8){
        //perform the statement

        }

        $mfunction['pass'] = $passresult;

    //print_r($mfunction);
    }elseif ($mfunction['checking'] && $result1['core_e_mfunction_function']){

        include_once $DOCUMENT_ROOT.'/includes/apps/'.$app.'/mfunction.inc';
        ini_set('display_errors','Off');
        $call_user_func_result = call_user_func($result1['core_e_mfunction_function']);

        if ($call_user_func_result == 1 || $call_user_func_result == 0) {
          $mfunction['pass'] = $call_user_func_result;
        }elseif($call_user_func_result['extra']){
          $mfunction = array_merge($mfunction, $call_user_func_result['extra']);
          $mfunction['pass'] = 1;
        }

        //echo $mfunction['pass'];

    }


}else{
   $mfunction['found'] = 0;
   //$html['html'] .= "<br>No Such mFunction Name: ".$table;
}




global $lang;
if ($mfunction['checking']){
    if (!$mfunction['found']) {

        $mfunction['stop'] = 1;
        $mfunction['error'] = "system: notpasshandle not set";

    } elseif ($mfunction['pass']) {

        $passhandle = $result1['core_e_mfunction_passhandle'];
        $passlang_define = $lang[$app][$result1['core_e_mfunction_passlang']];

        if($passhandle == 1){
            $mfunction['passmsg'] = $passlang_define;
        }elseif($passhandle == 2){
            $mfunction['log'] = $passlang_define;
        }

    } else {
        $notpasshandle = $result1['core_e_mfunction_notpasshandle'];
        $error_jsfunction = "error_".$result1['core_e_mfunction_errorlang'];

        if (!$notpasshandle) {
            $mfunction['stop'] = 1;
            $mfunction['xmsgname'] = "system: notpasshandle not set";
            $mfunction['log'] = "system: notpasshandle not set";
        }elseif($notpasshandle == 1){
            $mfunction['stop'] = 1;
            $mfunction['xmsgname'] = $result1['core_e_mfunction_passxmsg'];
        }elseif($notpasshandle == 2){
            $mfunction['xmsgname'] = $result1['core_e_mfunction_passxmsg'];
        }elseif($notpasshandle == 3){
            $mfunction['stop'] = 1;
            $mfunction['log'] = $result1['core_e_mfunction_passxmsg'];
        }elseif($notpasshandle == 4){
            $mfunction['log'] = $result1['core_e_mfunction_passxmsg'];
        }elseif($notpasshandle == 5){
            //skip remaining elements
            $mfunction['stop'] = 1;
        }
    }
}

//print_r($mfunction);

if ($mfunction['checking'] && !$mfunction['pass'] && $notpasshandle != 5 && $passmethod != 11 && !$call_user_func_result['extra']){

  /*
  $h_apps_html[$count_element]['html'] = '
                                <div class="ui-widget message closeable">

                                    <div class="ui-state-error ui-corner-all">

                                        <p>

                                            <span class="ui-icon ui-icon-alert"></span>

                                            <strong>Alert:</strong> '.$mfunction['error'].'

                                        </p>

                                    </div>

                                </div>

                            ';

    $this_jsfunction = $error_jsfunction;
    function '.$this_jsfunction.'() {
    notification_show(\'section_app\',\''.$mfunction['error'].'\',\'error\',1)
    };
    */

    $mfunction['xmsg'] = $mfunction['xmsgname'];

} elseif (!$mfunction['found']) {
	
	//$mfunction['xmsg'] = $mfunction['xmsgname'];
		
}

return($mfunction);
}



}