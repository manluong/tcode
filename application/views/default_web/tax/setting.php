<div class="top-setting">
	<div><h2>SETTINGS</h2><span> Tax</span></div>
	<input type="button" class="btnX" />
</div>

<div class="content">
<?php
	echo form_open('/tax/ajax_save_setting/', array('class'=>'form-horizontal'));
?>

<?php if ($is_admin) { ?>
<div class="dtitle">
	<span class="upper">Admin Level Settings</span>
	<span>Changes made here will apply to all users.</span>
</div>
<div class="form">
	<div class="textcontent">Select which of the following tax settings to use in 8force.</div>
	<?php foreach($tax_use AS $t) { ?>
	<ul>
		<li>
			<span class="lb"><?=$t['name']?></span>
			<span class="fillter_input">
				<?php
					$checked = ($t['disabled'] == '0');
					echo form_checkbox('disabled['.$t['id'].']', '1', $checked);
				?>
			</span>
		</li>
	</ul>
	<?php } ?>
</div>
<?php } ?>

<div class="bot">
	<button type="submit" class="btn btn-primary save">Save</button> or <a href="#" class="cancel">go back</a>
</div>

</form>
</div>