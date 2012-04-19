<?php
	$fields = array('name', 'email', 'domain', 'password');
	foreach($fields AS $f) {
		if (isset($errors[$f]) && $errors[$f]!='') {
			${$f.'_class'} = 'error';
			${$f.'_message'} = '<span class="help-inline">'.$errors[$f].'</span>';
		} else {
			${$f.'_class'} = '';
			${$f.'_message'} = '';
		}
	}
?>

<?=form_open('signup/step2')?>
	<fieldset>
		<legend>Sign Up</legend>

		<div class="control-group <?=$name_class?>">
			<label class="control-label">Name</label>
			<div class="controls">
				<input type="text" name="name" value="<?=(isset($signup['name'])?$signup['name']:'')?>" />
				<?=$name_message?>
			</div>
		</div>

		<div class="control-group <?=$email_class?>">
			<label class="control-label">Email</label>
			<div class="controls">
				<input type="text" name="email" value="<?=(isset($signup['email'])?$signup['email']:'')?>" />
				<?=$email_message?>
			</div>
		</div>
		
		<div class="control-group <?=$password_class?>">
			<label class="control-label">Password</label>
			<div class="controls">
				<input type="text" name="password" value="<?=(isset($signup['password'])?$signup['password']:'')?>" />
				<?=$password_message?>
			</div>
		</div>

		<div class="control-group <?=$domain_class?>">
			<label class="control-label">Domain</label>
			<div class="controls">
				<input type="text" name="domain" value="<?=(isset($signup['domain'])?$signup['domain']:'')?>" />.8force.net
				<?=$domain_message?>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Create My Account!</button>
		</div>
	</fieldset>
</form>