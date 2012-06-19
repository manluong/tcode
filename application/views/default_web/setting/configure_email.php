<div class="title">Settings</div> <span class="arrow"></span> <span class="simple">General</span>

<?php
	echo form_open('/setting/ajax_save/'.$app_name , array('class'=>'form-horizontal'));
?>

<?php if (APP_ROLE == 'TBOSS' && $is_admin) { ?>
<fieldset>
	<legend>Global Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to everyone including tenants.</p></legend>

	<div class="control-group">
		<label class="control-label">Email Domain</label>
		<div class="controls">
			<input type="text" name="global-domain" value="<?=(isset($settings['global']['domain']['value']))?$settings['global']['domain']['value']:''?>" />
			<span class="help-inline">Default email domain name.</span>
			<input type="hidden" name="global-domain-override" value="1" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Default "From" email</label>
		<div class="controls">
			<input type="text" name="global-from_email_default" value="<?=(isset($settings['global']['from_email_default']['value']))?$settings['global']['from_email_default']['value']:''?>" />
			<input type="hidden" name="global-from_email_default-override" value="1" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Default "From" name</label>
		<div class="controls">
			<input type="text" name="global-from_name_default" value="<?=(isset($settings['global']['from_name_default']['value']))?$settings['global']['from_name_default']['value']:''?>" />
			<input type="hidden" name="global-from_name_default-override" value="1" />
		</div>
	</div>
</fieldset>
<?php } ?>

<?php if ($is_admin) { ?>
<fieldset>
	<legend>Admin Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to all users.</p></legend>

	<div class="control-group">
		<label class="control-label">Always BCC</label>
		<div class="controls">
			<input type="text" name="tenant-always_bcc" value="<?=(isset($settings['tenant']['always_bcc']['value']))?$settings['tenant']['always_bcc']['value']:''?>" />
			<span class="help-inline">Separate email address with commas.</span>
			<input type="hidden" name="tenant-always_bcc-override" value="0" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Email Domain</label>
		<div class="controls">
			<input type="text" name="tenant-domain" value="<?=(isset($settings['tenant']['domain']['value']))?$settings['tenant']['domain']['value']:''?>" />
			<span class="help-inline">Your custom email domain name.</span>
			<input type="hidden" name="tenant-domain-override" value="0" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Default "From" email</label>
		<div class="controls">
			<input type="text" name="tenant-from_email_default" value="<?=(isset($settings['tenant']['from_email_default']['value']))?$settings['tenant']['from_email_default']['value']:''?>" />
			<input type="hidden" name="tenant-from_email_default-override" value="0" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Default "From" name</label>
		<div class="controls">
			<input type="text" name="tenant-from_name_default" value="<?=(isset($settings['tenant']['from_name_default']['value']))?$settings['tenant']['from_name_default']['value']:''?>" />
			<input type="hidden" name="tenant-from_name_default-override" value="0" />
		</div>
	</div>

</fieldset>
<?php } ?>

<fieldset>
	<legend>User Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to you only.</p></legend>

	<p>There are no configuration settings for this section at the moment.</p>
</fieldset>

<div class="control-group">
	<label class="control-label"></label>
	<div class="controls">
		<button type="submit" class="btn btn-primary save">Save</button> or <a href="#" class="cancel">go back</a>
	</div>
</div>


</form>