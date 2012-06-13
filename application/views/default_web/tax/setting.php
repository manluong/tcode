<div class="title">Settings</div> <span class="arrow"></span> <span class="simple">Tax</span>

<?php
	echo form_open('/tax/ajax_save_setting/', array('class'=>'form-horizontal'));
?>

<?php if ($is_admin) { ?>
<fieldset>
	<legend>Admin Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to all users.</p></legend>

	<p>Select which of the following tax settings to use in 8force.</p>
	<?php foreach($tax_use AS $t) { ?>
	<div class="control-group">
		<label class="control-label"><?=$t['name']?></label>
		<div class="controls">
			<?php
				$checked = ($t['disabled'] == '0');
				echo form_checkbox('disabled['.$t['id'].']', '1', $checked);
			?>
		</div>
	</div>
	<?php } ?>
</fieldset>
<?php } ?>


<div class="control-group">
	<label class="control-label"></label>
	<div class="controls">
		<button type="submit" class="btn btn-primary save">Save</button> or <a href="#" class="cancel">go back</a>
	</div>
</div>


</form>