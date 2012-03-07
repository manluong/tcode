<div class="widget">
	<div class="widget-header">
		Dashboard
	</div>
	<div class="widget-body">
		<?php
			echo form_open('/setting/save/'.$app_name , array('class'=>'form-horizontal'));
		?>

		<?php if (APP_ROLE == 'TBOSS') { ?>
		<fieldset>
			<legend>Global</legend>
			<div class="control-group">
				<label class="control-label">Test</label>
				<div class="controls">
					<input type="text">
				</div>
			</div>
		</fieldset>
		<?php } ?>

		<?php if ($is_admin) { ?>
		<fieldset>
			<legend>Admin Settings</legend>
			<div class="control-group">
				<label class="control-label">Test</label>
				<div class="controls">
					<input type="text">
				</div>
			</div>
		</fieldset>
		<?php } ?>

		<fieldset>
			<legend>User Settings</legend>
			<div class="control-group">
				<label class="control-label">Test</label>
				<div class="controls">
					<input type="text">
				</div>
			</div>
		</fieldset>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save</button>
		</div>

		</form>
	</div>
</div>