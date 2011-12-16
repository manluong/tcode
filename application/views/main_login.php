<?php

if (isset($status) && $status == 3) {
echo '
	<div class="ui-widget message closeable">
		<div class="ui-state-error ui-corner-all">
			<p>
				<span class="ui-icon ui-icon-alert"></span>
				<strong>'.lang('accesslogin_failure').'</strong>
			</p>
		</div>
	</div>
';
}
?>
<form class="form" id="formid_divaccess_login" name="formid_divaccess_login" style="margin: -2px;" method="post" action="<?php echo base_url("main/login");?>">
<div style="padding: 5px;"></div>
<div class="clearfix">
	<label for="form_access_user_username" class="form-label"><?=lang('accessaccess_user_username')?></label>
	<div class="form-input"><input type="text" id="form_access_user_username" name="access_user_username" maxlength="50" /></div>
</div>
<div class="clearfix">
	<label for="form_access_user_pw" class="form-label"><?=lang('accessaccess_user_pw')?></label>
	<div class="form-input"><input type="password" id="form_access_user_pw" name="access_user_pw" maxlength="50" /></div>
</div><div class="clearfix"></div><div class="bu-div bu-formview"><span class="fr"><button type="submit" class="button" data-icon-primary="ui-icon-locked"><?=lang('accesselementbu_buttonlogin')?></button></span></div>
</form>