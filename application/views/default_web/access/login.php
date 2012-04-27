<h2>8force</h2>

<div class="widget span6 offset3">
	<div class="widget-header">
		<h4>Login</h4>
	</div>
	<div class="widget-body">
		<?php
		if (isset($status) && in_array($status, array(3,6,7,8))) {
		echo '
			<div class="alert alert-error">
				<i class="icon-exclamation-sign"></i>
				<strong>'.lang('access-login_status-'.$status).'</strong>
			</div>
		';
		}
		?>
		<form method="post" action="<?php echo base_url('access/login');?>">
			<?=reset_return_url_form();?>

			<div class="control-group">
				<label class="control-label" for="login_email"><?=lang('login_email')?></label>
				<input type="text" id="login_email" name="login_email" maxlength="50" />
			</div>

			<div class="control-group">
				<label class="control-label" for="login_password"><?=lang('login_password')?></label>
				<input type="password" id="login_password" name="login_password" maxlength="50" />
			</div>

			<button type="submit" class="btn btn-primary"><i class="icon-lock icon-white"></i> <?=lang('button_login')?></button>
		</form>
	</div>
</div>