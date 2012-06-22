<div class="top-setting">
	<div><h2>SETTINGS</h2><span> Helpdesk</span></div>
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
	<div class="textcontent">There are no configuration settings for this section at the moment.</div>
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
			<span class="lb">Priority Options</span>
			<span class="fillter_input">
				<?php
					$priority_options = (isset($settings['tenant']['priority']['value']))
											? json_decode($settings['tenant']['priority']['value'])
											: array();

					foreach($priority_options AS $p) {
						echo '<input type="text" name="tenant-priority[]" value="',$p,'" /><br />';
					}
				?>
				<input type="text" name="tenant-priority[]" value="" />
				<input type="hidden" name="tenant-priority-override" value="0" />
			</span>
		</li>
	</ul>
</div>
<?php //} ?>

<div class="bot">
	<button type="submit" class="btn btn-primary save">Save</button> or <a href="#" class="cancel">go back</a>
</div>

</form>
</div>