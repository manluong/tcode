<div class="top-setting">
	<div><h2>SETTINGS</h2><span> General</span></div>
	<input type="button" class="btnX" />
</div>

<div class="content">
<?php
	echo form_open('/setting/ajax_save/'.$app_name , array('class'=>'form-horizontal'));
?>

<?php //if (APP_ROLE == 'TBOSS' && $is_admin) { ?>
<div class="dtitle">
	<span class="upper">Global Level Settings</span>
	<span>Changes made here will apply to everyone including tenants.</span>
</div>
<div class="form">
	<p>There are no configuration settings for this section at the moment.</p>
</div>
<?php //} ?>

<?php //if ($is_admin) { ?>
<div class="dtitle">
	<span class="upper">Admin Level Settings</span>
	<span>Changes made here will apply to all users.</span>
</div>
<div class="form">
	<ul>
		<li>
			<span class="lb">Company Name</span>
			<span class="fillter_input">
				<input type="text" name="tenant-company_name" value="<?=(isset($settings['tenant']['company_name']['value']))?$settings['tenant']['company_name']['value']:''?>" class="inv-field" />
				<input type="hidden" name="tenant-company_name-override" value="0" />
			</span>
		</li>
		<li>
			<span class="lb">Time Zone</span>
			<span class="fillter_input">
				<input type="text" name="tenant-timezone" value="<?=(isset($settings['tenant']['timezone']['value']))?$settings['tenant']['timezone']['value']:'0'?>" class="inv-field" />
				<?=form_dropdown('tenant-timezone-override', $override_options, (isset($settings['tenant']['timezone']['can_override']))?$settings['tenant']['timezone']['can_override']:1);?>
			</span>
		</li>
	</ul>
</div>
<?php //} ?>

<div class="dtitle">
	<span class="upper">User Level Settings</span>
	<span>Changes made here will apply to you only.</span>
</div>
<div class="form">
	<?php //if (!isset($settings['tenant']['timezone']['can_override']) || $settings['tenant']['timezone']['can_override']==1) { ?>
	<ul>
		<li>
			<span class="lb">Time Zone</span>
			<span class="fillter_input">
				<input type="text" name="user-timezone" value="<?=(isset($settings['user']['timezone']['value']))?$settings['user']['timezone']['value']:'0'?>" />
			</span>
		</li>
	</ul>
	<?php //} ?>
</div>

<div class="bot">
	<button type="submit" class="btn btn-primary save">Save</button> or <a href="#" class="cancel">go back</a>
</div>

</form>
</div>