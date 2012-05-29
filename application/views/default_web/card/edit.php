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

				<div id="addon_tel">
				<?php
					$x = 0;
					foreach($data['addon_tel'] AS $e) {
						echo '<div class="control-group">';
							echo '<button class="btn btn-mini pull-left remove">X</button>';
							echo '<label class="control-label" for="tel_',$e['id'],'">',$tel_label,'</label>';
							echo '<div class="controls">';
								echo form_hidden('addon_tel['.$x.'][id]', $e['id']);
								echo form_dropdown('addon_tel['.$x.'][type]', $e['type_options'], $e['type'], 'class="input-small"'),' ';
								echo form_input('addon_tel['.$x.'][country]', $e['country'], 'id="tel_country_'.$e['id'].'" style="width: 40px;"'),' ';
								echo form_input('addon_tel['.$x.'][area]', $e['area'], 'id="tel_area_'.$e['id'].'" style="width: 40px;"'),' ';
								echo form_input('addon_tel['.$x.'][number]', $e['number'], 'id="tel_number_'.$e['id'].'" style="width: 100px;"'),' ';
								echo form_input('addon_tel['.$x.'][extension]', $e['extension'], 'id="tel_extension_'.$e['id'].'" style="width: 40px;"'),' ';
								$checked = ($e['is_default'] == 1);
								echo form_radio('tel_is_default_radio', $x, $checked, 'class="tel_is_default_radio"');
								if ($checked) {
									echo '<input type="hidden" name="addon_tel['.$x.'][is_default]" value="1" class="tel_is_default_hidden" id="tel_is_default_'.$x.'" />';
								} else {
									echo '<input type="hidden" name="addon_tel['.$x.'][is_default]" value="0" class="tel_is_default_hidden" id="tel_is_default_'.$x.'" />';
								}

							echo '</div>';
						echo '</div>';

						$x++;
					}
				?>
				</div>

				<button type="button" class="btn btn-mini pull-right" id="tel_add">More Phone</button>
				<script>
					var tel_index = <?=$x?>;
					var tel_type_options = jQuery.parseJSON('<?=json_encode($tel_type_options)?>');
					var tel_label = '<?=$tel_label?>';

					$(document).ready(function() {
						$('#tel_add').on('click', function() {
							var new_tel = '<div class="control-group">'+
												'<button class="btn btn-mini pull-left remove">X</button>'+
												'<label class="control-label" for="tel_'+tel_index+'">'+tel_label+'</label>'+
												'<div class="controls">'+
													'<input type="hidden" name="addon_tel['+tel_index+'][id]" value="" />'+
													'<select name="addon_tel['+tel_index+'][type]" class="input-small">';
							$(tel_type_options).each(function(k, v) {
								new_tel += '<option value="'+k+'">'+v+'</option>';
							});
							new_tel += '</select> '+
										'<input type="text" name="addon_tel['+tel_index+'][country]" id="tel_country_'+tel_index+'" style="width: 40px;" /> '+
										'<input type="text" name="addon_tel['+tel_index+'][area]" id="tel_area_'+tel_index+'" style="width: 40px;" /> '+
										'<input type="text" name="addon_tel['+tel_index+'][number]" id="tel_number_'+tel_index+'" style="width: 100px;" /> '+
										'<input type="text" name="addon_tel['+tel_index+'][extension]" id="tel_extension_'+tel_index+'" style="width: 40px;" /> '+
										'<input type="radio" name="tel_is_default_radio" value="'+tel_index+'" class="tel_is_default_radio" />'+
										'<input type="hidden" name="addon_tel['+tel_index+'][is_default]" value="0" class="tel_is_default_hidden" id="tel_is_default_'+tel_index+'" />'
									'</div>'+
								'</div>';

							$('#addon_tel').append(new_tel);
							tel_index++;
						});

						$('#addon_tel').on('click', '.tel_is_default_radio', function() {
							var selected = $(this).val();
							$('.tel_is_default_hidden').attr('value', 0);
							$('#tel_is_default_'+selected).attr('value', 1);
						});
					});

				</script>

				<br /><br />

				<div id="addon_email">
				<?php
					$x = 0;
					foreach($data['addon_email'] AS $e) {
						echo '<div class="control-group">';
							echo '<button class="btn btn-mini pull-left remove">X</button>';
							echo '<label class="control-label" for="email_',$e['id'],'">',$e['email_label'],'</label>';
							echo '<div class="controls">';
								echo form_hidden('addon_email['.$x.'][id]', $e['id']);
								echo form_dropdown('addon_email['.$x.'][type]', $e['type_options'], $e['type'], 'class="input-small"'),' ';
								echo form_input('addon_email['.$x.'][email]', $e['email'], 'id="email_'.$e['id'].'"'),' ';
								$checked = ($e['is_default'] == 1);
								echo form_radio('email_is_default_radio', $x, $checked, 'class="email_is_default_radio"');
								if ($checked) {
									echo '<input type="hidden" name="addon_email['.$x.'][is_default]" value="1" class="email_is_default_hidden" id="email_is_default_'.$x.'" />';
								} else {
									echo '<input type="hidden" name="addon_email['.$x.'][is_default]" value="0" class="email_is_default_hidden" id="email_is_default_'.$x.'" />';
								}

							echo '</div>';
						echo '</div>';

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
												'<button class="btn btn-mini pull-left remove">X</button>'+
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
										'<input type="hidden" name="addon_email['+email_index+'][is_default]" value="0" class="email_is_default_hidden" id="email_is_default_'+email_index+'" />'+
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

				<br /><br />

				<div id="addon_address">
				<?php
					$x = 0;
					foreach($data['addon_address'] AS $e) {
						echo '<div class="control-group">';
							echo '<button class="btn btn-mini pull-left remove">X</button>';
							echo '<label class="control-label" for="address_',$e['id'],'">',$address_label,'</label>';
							echo '<div class="controls">';
								echo form_hidden('addon_address['.$x.'][id]', $e['id']);
								echo form_dropdown('addon_address['.$x.'][type]', $e['type_options'], $e['type'], 'class="input-small"'),' ';
								echo form_input('addon_address['.$x.'][line_1]', $e['line_1'], 'id="address_line_1_'.$e['id'].'" style="width: 209px;"'),' ';
								$checked = ($e['is_default'] == 1);
								echo form_radio('address_is_default_radio', $x, $checked, 'class="address_is_default_radio"');
								if ($checked) {
									echo '<input type="hidden" name="addon_address['.$x.'][is_default]" value="1" class="address_is_default_hidden" id="address_is_default_'.$x.'" />';
								} else {
									echo '<input type="hidden" name="addon_address['.$x.'][is_default]" value="0" class="address_is_default_hidden" id="address_is_default_'.$x.'" />';
								}

								echo form_input('addon_address['.$x.'][line_2]', $e['line_2'], 'id="address_line_2_'.$e['id'].'" style="width: 302px;"'),' ';
								echo form_input('addon_address['.$x.'][city]', $e['city'], 'id="address_city_'.$e['id'].'" style="width: 92px;"'),' ';
								echo form_input('addon_address['.$x.'][state]', $e['state'], 'id="address_state_'.$e['id'].'" style="width: 92px;"'),' ';
								echo form_input('addon_address['.$x.'][postal]', $e['postal'], 'id="address_postal_'.$e['id'].'" style="width: 92px;"'),' ';
								echo '<br /><button type="button" class="btn" id="map">Map</button>';
								echo form_dropdown('addon_address['.$x.'][country]', $countries, $e['country'], 'class="input-small" style="margin-left: 175px;'),' ';
							echo '</div>';
						echo '</div>';

						$x++;
					}
				?>
				</div>

				<button type="button" class="btn btn-mini pull-right" id="address_add">More Address</button>
				<script>
					var address_index = <?=$x?>;
					var address_type_options = jQuery.parseJSON('<?=json_encode($address_type_options)?>');
					var address_label = '<?=$address_label?>';
					var address_countries_options = jQuery.parseJSON('<?=json_encode($countries)?>');

					$(document).ready(function() {
						$('#address_add').on('click', function() {
							var new_address = '<div class="control-group">'+
												'<button class="btn btn-mini pull-left remove">X</button>'+
												'<label class="control-label" for="address_'+address_index+'">'+address_label+'</label>'+
												'<div class="controls">'+
													'<input type="hidden" name="addon_address['+address_index+'][id]" value="" />'+
													'<select name="addon_address['+address_index+'][type]" class="input-small">';
							$(address_type_options).each(function(k, v) {
								new_address += '<option value="'+k+'">'+v+'</option>';
							});
							new_address += '</select> '+
										'<input type="text" name="addon_address['+address_index+'][line_1]" id="address_line_1_'+address_index+'" style="width: 209px;" /> '+
										'<input type="radio" name="address_is_default_radio" value="'+address_index+'" class="address_is_default_radio" />'+
										'<input type="text" name="addon_address['+address_index+'][line_2]" id="address_line_2_'+address_index+'" style="width: 302px;" /> '+
										'<input type="text" name="addon_address['+address_index+'][city]" id="address_city_'+address_index+'" style="width: 92px;" /> '+
										'<input type="text" name="addon_address['+address_index+'][state]" id="address_state_'+address_index+'" style="width: 92px;" /> '+
										'<input type="text" name="addon_address['+address_index+'][postal]" id="address_postal_'+address_index+'" style="width: 92px;" /> '+
										'<input type="hidden" name="addon_address['+address_index+'][is_default]" value="0" class="address_is_default_hidden" id="address_is_default_'+address_index+'" />'+
										'<br /><button type="button" class="btn" id="map">Map</button>'+
										'<select name="addon_address['+address_index+'][country]" class="input-small" style="margin-left: 175px;">';
							$.each(address_countries_options, function(k, v) {
								new_address += '<option value="'+k+'">'+v+'</option>';
							});
							new_address += '</select> '+
									'</div>'+
								'</div>';

							$('#addon_address').append(new_address);
							address_index++;
						});

						$('#addon_address').on('click', '.address_is_default_radio', function() {
							var selected = $(this).val();
							$('.address_is_default_hidden').attr('value', 0);
							$('#address_is_default_'+selected).attr('value', 1);
						});
					});

				</script>

				<br /><br />

				<div id="addon_social">
				<?php
					$x = 0;
					foreach($data['addon_social'] AS $e) {
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

				<br /><br />

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

<script type="text/javascript">
	$(document).ready(function() {
		$('.remove').live('click', function() {
			$(this).closest('.control-group').remove();
		});
	});
</script>