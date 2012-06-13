<div class="title">Settings</div> <span class="arrow"></span> <span class="simple">General</span>

<?php
	echo form_open('/setting/ajax_save/'.$app_name , array('class'=>'form-horizontal'));
?>

<?php if (APP_ROLE == 'TBOSS' && $is_admin) { ?>
<fieldset>
	<legend>Global Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to everyone including tenants.</p></legend>
	<p>There are no configuration settings for this section at the moment.</p>
</fieldset>
<?php } ?>

<?php if ($is_admin) { ?>
<fieldset>
	<legend>Admin Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to all users.</p></legend>

	<div class="control-group">
		<label class="control-label">Company Name</label>
		<div class="controls">
			<input type="text" name="tenant-company_name" value="<?=(isset($settings['tenant']['company_name']['value']))?$settings['tenant']['company_name']['value']:''?>" />
			<input type="hidden" name="tenant-company_name-override" value="0" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Time Zone</label>
		<div class="controls">
			<input type="text" name="tenant-timezone" value="<?=(isset($settings['tenant']['timezone']['value']))?$settings['tenant']['timezone']['value']:'0'?>" />
			<?=form_dropdown('tenant-timezone-override', $override_options, (isset($settings['tenant']['timezone']['can_override']))?$settings['tenant']['timezone']['can_override']:1);?>
		</div>
	</div>
</fieldset>
<?php } ?>

<fieldset>
	<legend>User Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to you only.</p></legend>

	<?php
		if (!isset($settings['tenant']['timezone']['can_override']) || $settings['tenant']['timezone']['can_override']==1) {
	?>
		<div class="control-group">
			<label class="control-label">Time Zone</label>
			<div class="controls">
				<input type="text" name="user-timezone" value="<?=(isset($settings['user']['timezone']['value']))?$settings['user']['timezone']['value']:'0'?>" />
			</div>
		</div>
	<?php
		}
	?>
</fieldset>

<div class="control-group">
	<label class="control-label"></label>
	<div class="controls">
		<button type="submit" class="btn btn-primary save">Save</button> or <a href="#" class="cancel">go back</a>
	</div>
</div>


</form>