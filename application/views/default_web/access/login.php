<h2>8force</h2>

<div class="widget span6 offset3">
	<div class="widget-header">
		<h4><?=$company_name?></h4>
	</div>
	<div class="widget-body">
		<?php
		if (isset($status) && in_array($status, array(3,6,7))) {
		echo '
			<div class="alert alert-error">
				<i class="icon-exclamation-sign"></i>
				<strong>'.lang('access_login_status_'.$status).'</strong>
			</div>
		';
		}
		?>
		<form id="formid_divaccess_login" name="formid_divaccess_login" method="post" action="<?php echo base_url('access/login');?>">
			<?=reset_return_url_form();?>

			<div class="control-group">
				<label class="control-label" for="form_access_user_username"><?=lang('access_access_user_username')?></label>
				<input type="text" id="form_access_user_username" name="access_user_username" maxlength="50" />
			</div>

			<div class="control-group">
				<label class="control-label" for="form_access_user_pw"><?=lang('access_access_user_pw')?></label>
				<input type="password" id="form_access_user_pw" name="access_user_pw" maxlength="50" />
			</div>

			<button type="submit" class="btn"><i class="icon-lock"></i> <?=lang('access_button_login')?></button>
		</form>
	</div>
</div>