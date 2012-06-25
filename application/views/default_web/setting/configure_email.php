<div class="top-setting">
	<div><h2>SETTINGS</h2><span> Email</span></div>
	<input type="button" class="btnX" />
</div>

<div class="content">
<?php
	echo form_open('/setting/ajax_save/'.$app_name , array('class'=>'form-horizontal'));
?>

<?php if (APP_ROLE == 'TBOSS' && $is_admin) { ?>
<div class="dtitle">
	<span class="upper">Global Level Settings</span>
	<span>Changes made here will apply to everyone including tenants.</span>
</div>
<div class="form">
	<ul>
		<li>
			<span class="lb">Email Domain</span>
			<span class="fillter_input">
				<input type="text" name="global-domain" value="<?=(isset($settings['global']['domain']['value']))?$settings['global']['domain']['value']:''?>" />
				<span class="help-inline">Default email domain name.</span>
				<input type="hidden" name="global-domain-override" value="1" />
			</span>
		</li>
		<li>
			<span class="lb">Default "From" email</span>
			<span class="fillter_input">
				<input type="text" name="global-from_email_default" value="<?=(isset($settings['global']['from_email_default']['value']))?$settings['global']['from_email_default']['value']:''?>" />
				<input type="hidden" name="global-from_email_default-override" value="1" />
			</span>
		</li>
		<li>
			<span class="lb">Default "From" name</span>
			<span class="fillter_input">
				<input type="text" name="global-from_name_default" value="<?=(isset($settings['global']['from_name_default']['value']))?$settings['global']['from_name_default']['value']:''?>" />
				<input type="hidden" name="global-from_name_default-override" value="1" />
			</span>
		</li>
	</ul>
</div>
<?php } ?>

<?php if ($is_admin) { ?>
<div class="dtitle">
	<span class="upper">Admin Level Settings</span>
	<span>Changes made here will apply to all users.</span>
</div>
<div class="form">
	<ul>
		<li>
			<span class="lb">Always BCC</span>
			<span class="fillter_input">
				<input type="text" name="tenant-always_bcc" value="<?=(isset($settings['tenant']['always_bcc']['value']))?$settings['tenant']['always_bcc']['value']:''?>" />
				<span class="help-inline">Separate email address with commas.</span>
				<input type="hidden" name="tenant-always_bcc-override" value="0" />
			</span>
		</li>
		<li>
			<span class="lb">Email Domain</span>
			<span class="fillter_input">
				<input type="text" name="tenant-domain" value="<?=(isset($settings['tenant']['domain']['value']))?$settings['tenant']['domain']['value']:''?>" />
				<span class="help-inline">Your custom email domain name.</span>
				<input type="hidden" name="tenant-domain-override" value="0" />
			</span>
		</li>
		<li>
			<span class="lb">Default "From" email</span>
			<span class="fillter_input">
				<input type="text" name="tenant-from_email_default" value="<?=(isset($settings['tenant']['from_email_default']['value']))?$settings['tenant']['from_email_default']['value']:''?>" />
				<input type="hidden" name="tenant-from_email_default-override" value="0" />
			</span>
		</li>
		<li>
			<span class="lb">Default "From" name</span>
			<span class="fillter_input">
				<input type="text" name="tenant-from_name_default" value="<?=(isset($settings['tenant']['from_name_default']['value']))?$settings['tenant']['from_name_default']['value']:''?>" />
				<input type="hidden" name="tenant-from_name_default-override" value="0" />
			</span>
		</li>
	</ul>
</div>
<?php } ?>

<div class="dtitle">
	<span class="upper">User Level Settings</span>
	<span>Changes made here will apply to you only.</span>
</div>
<div class="form">
	<div class="textcontent">There are no configuration settings for this section at the moment.</div>
</div>

<div class="bot">
	<button type="submit" class="btn btn-primary save">Save</button> or <a href="#" class="cancel">go back</a>
</div>

</form>
</div>