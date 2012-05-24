<div class="container">
	<div class="row span6">

		<form method="post" action="/card/save" class="form-horizontal">
			<?php
				if (!$is_new) echo form_hidden('id', $data['id']);
			?>

			<fieldset>
				<legend>Basic Information</legend>

				<div class="control-group">
					<label class="control-label">Upload Photo</label>
					<div class="controls">
						<div class="btn-group" data-toggle="buttons-radio">
							<?php
								foreach($data['title_options'] AS $title_val => $title_label) {
									if ($title_val == 0) continue;
									echo '<button type="button" class="btn title_button';
									if ($data['title'] == $title_val) echo " active";
									echo '" id="title_',$title_val,'" value="',$title_val,'">';
									echo $title_label,'</button>';
								}
							?>
						</div>
						<input type="hidden" name="title" id="title" value="<?=$data['title']?>">
						<script>
							$(document).ready(function() {
								$('.title_button').on('click', function() {
									$('#title').val($(this).attr('value'));
								});
							});
						</script>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="first_name"><?=$data['first_name_label']?></label>
					<div class="controls">
						<?=form_input('first_name', $data['first_name'], 'id="first_name"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="middle_name"><?=$data['middle_name_label']?></label>
					<div class="controls">
						<?=form_input('middle_name', $data['middle_name'], 'id="middle_name"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="last_name"><?=$data['last_name_label']?></label>
					<div class="controls">
						<?=form_input('last_name', $data['last_name'], 'id="last_name"')?>
					</div>
				</div>

				<br />

				<div class="control-group">
					<label class="control-label" for="organization_name"><?=$data['organization_name_label']?></label>
					<div class="controls">
						<?=form_input('organization_name', $data['organization_name'], 'id="organization_name"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="organization_title"><?=$data['organization_title_label']?></label>
					<div class="controls">
						<?=form_input('organization_title', $data['organization_title'], 'id="organization_title"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="organization_number"><?=$data['organization_number_label']?></label>
					<div class="controls">
						<?=form_input('organization_number', $data['organization_number'], 'id="organization_number"')?>
					</div>
				</div>

				<br />

				<div id="addon_email">
				<?php
					$x = 0;
					$email_type_options = array();
					$email_label = '';
					foreach($data['addon_email'] AS $e) {
						if ($x == 0) {
							$email_type_options = $e['type_options'];
							$email_label = $e['email_label'];
						}
						echo '<div class="control-group">';
							echo '<label class="control-label" for="email_',$e['id'],'">',$e['email_label'],'</label>';
							echo '<div class="controls">';
								echo form_hidden('addon_email['.$x.'][id]', $e['id']);
								echo form_dropdown('addon_email['.$x.'][type]', $e['type_options'], $e['type'], 'class="input-small"'),' ';
								echo form_input('addon_email['.$x.'][email]', $e['email'], 'id="email_'.$e['id'].'"'),' ';
								$checked = ($e['is_default'] == 1);
								echo form_radio('email_is_default_radio', $x, $checked, 'class="email_is_default_radio"');
							echo '</div>';
						echo '</div>';

						if ($checked) {
							echo '<input type="hidden" name="addon_email['.$x.'][is_default]" value="1" class="email_is_default_hidden" id="email_is_default_'.$x.'" />';
						} else {
							echo '<input type="hidden" name="addon_email['.$x.'][is_default]" value="0" class="email_is_default_hidden" id="email_is_default_'.$x.'" />';
						}

						$x++;
					}
				?>
				</div>

				<button type="button" class="btn btn-mini pull-right" id="email_add">Add Email</button>
				<script>
					var email_index = <?=$x?>;
					var email_type_options = jQuery.parseJSON('<?=json_encode($email_type_options)?>');
					var email_label = '<?=$email_label?>';

					$(document).ready(function() {
						$('#email_add').on('click', function() {
							var new_email = '<div class="control-group">'+
												'<label class="control-label" for="email_'+email_index+'">'+email_label+'</label>'+
												'<div class="controls">'+
													'<input type="hidden" name="addon_email['+email_index+'][id]" value="" />'+
													'<select name="addon_email['+email_index+'][type]" class="input-small">';
							$(email_type_options).each(function(k, v) {
								new_email += '<option value="'+k+'">'+v+'</option>';
							});
							new_email += '</select> '+
										'<input type="text" name="addon_email['+email_index+'][email]" id="email_'+email_index+'" /> '+
										'<input type="radio" name="email_is_default_radio" value="'+email_index+'" class="email_is_default_radio" />'+
										'<input type="hidden" name="addon_email['+email_index+'][is_default]" value="0" class="email_is_default_hidden" id="email_is_default_'+email_index+'" />'
									'</div>'+
								'</div>';

							$('#addon_email').append(new_email);
							email_index++;
						});

						$('#addon_email').on('click', '.email_is_default_radio', function() {
							var selected = $(this).val();
							$('.email_is_default_hidden').attr('value', 0);
							$('#email_is_default_'+selected).attr('value', 1);
						});
					});

				</script>

				<br />

				<div id="addon_social">
				<?php
					$x = 0;
					$social_type_options = array();
					$social_label = '';
					foreach($data['addon_social'] AS $e) {
						if ($x == 0) {
							$social_type_options = $e['type_options'];
							$social_label = 'Social';
						}
						echo '<div class="control-group">';
							echo '<label class="control-label" for="social_',$e['id'],'">',$social_label,'</label>';
							echo '<div class="controls">';
								echo form_hidden('addon_social['.$x.'][id]', $e['id']);
								echo form_dropdown('addon_social['.$x.'][type]', $e['type_options'], $e['type'], 'class="input-small"'),' ';
								echo form_input('addon_social['.$x.'][name_id]', $e['name_id'], 'id="social_'.$e['id'].'"'),' ';
							echo '</div>';
						echo '</div>';

						$x++;
					}
				?>
				</div>

				<button type="button" class="btn btn-mini pull-right" id="social_add">More Social</button>
				<script>
					var social_index = <?=$x?>;
					var social_type_options = jQuery.parseJSON('<?=json_encode($social_type_options)?>');
					var social_label = '<?=$social_label?>';

					$(document).ready(function() {
						$('#social_add').on('click', function() {
							var new_social = '<div class="control-group">'+
												'<label class="control-label" for="social_'+social_index+'">'+social_label+'</label>'+
												'<div class="controls">'+
													'<input type="hidden" name="addon_social['+social_index+'][id]" value="" />'+
													'<select name="addon_social['+social_index+'][type]" class="input-small">';
							$(social_type_options).each(function(k, v) {
								new_social += '<option value="'+k+'">'+v+'</option>';
							});
							new_social += '</select> '+
										'<input type="text" name="addon_social['+social_index+'][name_id]" id="social_'+social_index+'" /> '+
									'</div>'+
								'</div>';

							$('#addon_social').append(new_social);
							social_index++;
						});
					});

				</script>

				<br />
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button type="submit" class="btn btn-primary">Save</button> or <a href="/card/view/<?=$data['id']?>">cancel</a>
					</div>
				</div>

			</fieldset>
		</form>

	</div>
</div>