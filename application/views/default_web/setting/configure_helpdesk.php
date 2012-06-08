<div class="widget">
	<div class="widget-header">
		Helpdesk
	</div>
	<div class="widget-body">
		<?php
			echo form_open('/setting/ajax_save/'.$app_name , array('class'=>'form-horizontal'));
		?>

		<?php if (APP_ROLE == 'TBOSS' && $is_admin) { ?>
		<fieldset>
			<legend>Global Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to everyone including tenants.</p></legend>

			<!--
			<div class="control-group">
				<label class="control-label">Test</label>
				<div class="controls">
					<input type="text">
				</div>
			</div>
			-->
			<p>There are no configuration settings for this section at the moment.</p>
		</fieldset>
		<?php } ?>

		<?php if ($is_admin) { ?>
		<fieldset>
			<legend>Admin Level Settings <p class="pull-right" style="margin-top:15px;">Changes made here will apply to all users.</p></legend>

			<div class="control-group">
				<label class="control-label">Priority Options</label>
				<div class="controls">
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
				</div>
			</div>
		</fieldset>
		<?php } ?>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary save">Save</button> or <button type="button" class="cancel">go back</button>
		</div>

		</form>
	</div>
</div>