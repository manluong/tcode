<?php

if (isset($status) && $status == 3) {
echo '
	<div class="alert alert-error">
		<i class="icon-exclamation-sign"></i>
		<strong>'.lang('access_login_failure').'</strong>
	</div>
';
}
?>
<form class="form" id="formid_divaccess_login" name="formid_divaccess_login" style="margin: -2px;" method="post" action="<?php echo base_url('access/login');?>">
	<?=reset_return_url_form();?>
<div style="padding: 5px;"></div>
<div class="clearfix">
	<label for="form_access_user_username" class="form-label"><?=lang('access_access_user_username')?></label>
	<div class="form-input"><input type="text" id="form_access_user_username" name="access_user_username" maxlength="50" /></div>
</div>
<div class="clearfix">
	<label for="form_access_user_pw" class="form-label"><?=lang('access_access_user_pw')?></label>
	<div class="form-input"><input type="password" id="form_access_user_pw" name="access_user_pw" maxlength="50" /></div>
</div><div class="clearfix"></div><div class="bu-div bu-formview"><span class="fr"><button type="submit" class="button" data-icon-primary="ui-icon-locked"><?=lang('access_button_login')?></button></span></div>
</form>