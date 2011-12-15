<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends MY_Controller {

	public function index()	{

	}

	function access_login(){
	
	    global $_GET;
		$thisresult['html'] = "";
	
	    if (isset($this->User->id['hasid'])){
	
	    if ($_GET['re_app']) $re_url = '&app='.$_GET['re_app'].'&an='.$_GET['re_an'].'&aved='.$_GET['re_aved'].'&thisid='.$_GET['re_thisid'];
	
	    header( 'Location: index.php?'.$re_url);
	    exit;
	
	    } else {
	
	    if ($_GET['re_app']) $re_url = '&re_app='.$_GET['re_app'].'&re_an='.$_GET['re_an'].'&re_aved='.$_GET['re_aved'].'&re_thisid='.$_GET['re_thisid'];
	
	    if (isset($this->User->id['status']) && $this->User->id['status'] == 3){
	    $thisresult['html'] = '
	                                <div class="ui-widget message closeable">
	
	                                    <div class="ui-state-error ui-corner-all">
	
	                                        <p>
	
	                                            <span class="ui-icon ui-icon-alert"></span>
	
	                                            <strong>'.$lang['access']['login_failure'].'</strong>
	
	                                        </p>
	
	                                    </div>
	
	                                </div>
	    ';
	    }
	
	    $thisresult['html'] .= '
	    <form class="form" id="formid_divaccess_login" name="formid_divaccess_login" style="margin: -2px;" method="post" action="?app=access&an=login'.$re_url.'">
	    <div style="padding: 5px;"></div>
	    <div class="clearfix">
	        <label for="form_access_user_username" class="form-label">'.$this->lang->line('accessaccess_user_username').'</label>
	        <div class="form-input"><input type="text" id="form_access_user_username" name="access_user_username" maxlength="50" /></div>
	    </div>
	    <div class="clearfix">
	        <label for="form_access_user_pw" class="form-label">'.$this->lang->line('accessaccess_user_pw').'</label>
	        <div class="form-input"><input type="password" id="form_access_user_pw" name="access_user_pw" maxlength="50" /></div>
	    </div><div class="clearfix"></div><div class="bu-div bu-formview"><span class="fr"><button type="submit" class="button" data-icon-primary="ui-icon-locked">'.$this->lang->line('accesselementbu_buttonlogin').'</button></span></div>
	    </form>
	    ';
	
	    }
	
		$thisresult['outputdiv'] = 1;
		
	return($thisresult);
	}
	
}