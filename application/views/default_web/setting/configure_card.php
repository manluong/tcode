<div class="widget">
	<div class="widget-header">
		Card
	</div>
	<div class="widget-body">
		<?php
			echo form_open('/setting/ajax_save/'.$app_name , array('class'=>'form-horizontal'));
		?>

		<?php if (APP_ROLE == 'TBOSS' && $is_admin) { ?>
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
			<button type="submit" class="btn btn-primary save">Save</button> or <button type="button" class="cancel">go back</button>
		</div>

		</form>
	</div>
</div>