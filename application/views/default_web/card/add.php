<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/contact.css" />

<?php
	//echo '<pre>';
	//print_r($data);
	//echo '</pre>';
?>
<div  id="contact_edit" class="container">
	<div class="row span6">
		<form method="post" action="/card/save" class="form-horizontal" id="card-edit-form" data-ajax_save="/card/ajax_save">
			<?php
				if (!$is_new) echo form_hidden('id', $data['id']);
			?>
			<fieldset>
				<div class="control-group" style="padding-left:160px;">
					<?php 
						foreach($role as $role_value => $role_label){
							echo '<input type="radio" name="role" class="role" value="'.$role_value.'" /> '.$role_label.'&nbsp;&nbsp;';
						}
					?>
					<input type="hidden" name="addon_access_user_role[0][role_id]" id="addon_role" value="">
					<script>
						$(document).ready(function() {
							$('.role').on('click', function() {
								$('#addon_role').val($(this).attr('value'));
							});
						});
					</script>
				</div>
				<div class="control-group" style="position:relative;">
					<h1 style="display:none;">Custom example</h1>
					<p style="display:none;">Shows you how to use the core plupload API.</p>
					<div id="container" >
						<label id="pickfiles" class="control-label btn_upload"></label>
						<div id="uploadfiles">Upload File</div>
						<div id="filelist"></div>
					</div>
					<div class="controls" style="margin-top:24px;">
						<div class="btn-group" data-toggle="buttons-radio">
							<?php
								foreach($title_option AS $title_val => $title_label) {
									if ($title_val == 0) continue;
									echo '<button type="button" class="btn title_button';
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
					<label class="control-label" for="first_name">First  Name</label>
					<div class="controls">
						<?=form_input('first_name', '', 'id="first_name"')?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="middle_name">Middle</label>
					<div class="controls">
						<?=form_input('middle_name', '', 'id="middle_name"')?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="last_name">Last Name</label>
					<div class="controls">
						<?=form_input('last_name', '', 'id="last_name"')?>
					</div>
				</div>
				
				<div style="margin-top:20px;" class="control-group">
					<label class="control-label" for="organization_name">Company</label>
					<div class="controls">
						<?=form_input('organization_name', '', 'id="organization_name"')?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="organization_title">Position</label>
					<div class="controls">
						<?=form_input('organization_title', '', 'id="organization_title"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="organization_number">Co. Reg.#</label>
					<div class="controls">
						<?=form_input('organization_number', '', 'id="organization_number"')?>
					</div>
				</div>

				<div style="padding-left:514px;font-size:11px;margin-bottom:2px;" class="control-group">
					Default
				</div>
				<!-- ADDON TEL -->
				<div id="addon_tel"></div>
				<button style="margin-left:442px;" type="button" class="btn btn-mini pull-right button_add" id="tel_add">More Phone</button>
				<script>
					var tel_index = 0;
					var tel_type_options = jQuery.parseJSON('<?=json_encode($tel_type_options)?>');
					var tel_label = 'Phone';

					$(document).ready(function() {
						var new_tel = '<div class="control-group">'+
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
								
						$('#addon_tel').html(new_tel);
						
						$('#tel_add').on('click', function() {
							tel_index++;
							var new_tel = '<div class="control-group">'+
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
						
						});

						$('#addon_tel').on('click', '.tel_is_default_radio', function() {
							var selected = $(this).val();
							$('.tel_is_default_hidden').attr('value', 0);
							$('#tel_is_default_'+selected).attr('value', 1);
						});
					});

				</script>
				<br/><br/>
				<!-- END ADDON TEL -->
				
				<!-- ADDON EMAIL -->
				<div id="addon_email"></div>
				<button type="button" class="btn btn-mini pull-right button_add " id="email_add">Add Email</button>
				<script>
					var email_index = 0;
					var email_type_options = jQuery.parseJSON('<?=json_encode($email_type_options)?>');
					var email_label = '<?=$email_label?>';

					$(document).ready(function() {
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
										'<input type="hidden" name="addon_email['+email_index+'][is_default]" value="0" class="email_is_default_hidden" id="email_is_default_'+email_index+'" />'+
									'</div>'+
								'</div>';
						
						$('#addon_email').html(new_email);
						
						$('#email_add').on('click', function() {
							email_index++;
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
										'<input type="hidden" name="addon_email['+email_index+'][is_default]" value="0" class="email_is_default_hidden" id="email_is_default_'+email_index+'" />'+
									'</div>'+
								'</div>';
							$('#addon_email').append(new_email);
						});

						$('#addon_email').on('click', '.email_is_default_radio', function() {
							var selected = $(this).val();
							$('.email_is_default_hidden').attr('value', 0);
							$('#email_is_default_'+selected).attr('value', 1);
						});
					});
				</script>
				<br/><br/>
				<!-- END ADDON EMAIL -->
				
				<!-- ADDON ADDRESS -->
				<div id="addon_address"></div>
				<button style="margin: -10px 0 0 376px;" type="button" class="btn btn-mini pull-right button_add" id="address_add">More Address</button>
				<script>
					var address_index = 0;
					var address_type_options = jQuery.parseJSON('<?=json_encode($address_type_options)?>');
					var address_label = '<?=$address_label?>';
					var address_countries_options = jQuery.parseJSON('<?=json_encode($countries)?>');

					$(document).ready(function() {
						var new_address = '<div class="control-group">'+
												'<label class="control-label" for="address_'+address_index+'">'+address_label+'</label>'+
												'<div id="contact_address" class="controls"><ul><li>'+
													'<input type="hidden" name="addon_address['+address_index+'][id]" value="" />'+
													'<select name="addon_address['+address_index+'][type]" class="input-small">';
							$(address_type_options).each(function(k, v) {
								new_address += '<option value="'+k+'">'+v+'</option>';
							});
							new_address += '</select> '+
										'<input type="text" name="addon_address['+address_index+'][line_1]" id="address_line_1_'+address_index+'" style="width: 209px;"/>'+
										'<input type="radio" name="address_is_default_radio" value="'+address_index+'" class="address_is_default_radio" /></li><li>'+
										'<input type="text" name="addon_address['+address_index+'][line_2]" id="address_line_2_'+address_index+'" style="width: 302px;" /></li>'+
										'<li id="address_label"><span style="width:106px;">City</span><span style="width:101px;">State</span><span style="width:107px;">Postal Code</span></li>'+
										'<li><input type="text" name="addon_address['+address_index+'][city]" id="address_city_'+address_index+'" style="width: 92px;" /> '+
										'<input type="text" name="addon_address['+address_index+'][state]" id="address_state_'+address_index+'" style="width: 92px;" /> '+
										'<input type="text" name="addon_address['+address_index+'][postal]" id="address_postal_'+address_index+'" style="width: 92px;" /> '+
										'<input type="hidden" name="addon_address['+address_index+'][is_default]" value="0" class="address_is_default_hidden" id="address_is_default_'+address_index+'" /></li><li>'+
										'<button type="button" class="btn btn-info" id="map"><i class="icon-map-marker icon-white"></i> Map</button>'+
										'<input type="text" name="addon_address['+address_index+'][country]" style="margin-left:43px; width:197px;">';
									'</li></ul></div>'+
								'</div>';
								
						$('#addon_address').html(new_address);
						
						$('#address_add').on('click', function() {
							address_index++;
							var new_address = '<div class="control-group">'+
												'<label class="control-label" for="address_'+address_index+'">'+address_label+'</label>'+
												'<div id="contact_address" class="controls"><ul><li>'+
													'<input type="hidden" name="addon_address['+address_index+'][id]" value="" />'+
													'<select name="addon_address['+address_index+'][type]" class="input-small">';
							$(address_type_options).each(function(k, v) {
								new_address += '<option value="'+k+'">'+v+'</option>';
							});
							new_address += '</select> '+
										'<input type="text" name="addon_address['+address_index+'][line_1]" id="address_line_1_'+address_index+'" style="width: 209px;"/>'+
										'<input type="radio" name="address_is_default_radio" value="'+address_index+'" class="address_is_default_radio" /></li><li>'+
										'<input type="text" name="addon_address['+address_index+'][line_2]" id="address_line_2_'+address_index+'" style="width: 302px;" /></li>'+
										'<li id="address_label"><span style="width:106px;">City</span><span style="width:101px;">State</span><span style="width:107px;">Postal Code</span></li>'+
										'<li><input type="text" name="addon_address['+address_index+'][city]" id="address_city_'+address_index+'" style="width: 92px;" /> '+
										'<input type="text" name="addon_address['+address_index+'][state]" id="address_state_'+address_index+'" style="width: 92px;" /> '+
										'<input type="text" name="addon_address['+address_index+'][postal]" id="address_postal_'+address_index+'" style="width: 92px;" /> '+
										'<input type="hidden" name="addon_address['+address_index+'][is_default]" value="0" class="address_is_default_hidden" id="address_is_default_'+address_index+'" /></li><li>'+
										'<button type="button" class="btn btn-info" id="map"><i class="icon-map-marker icon-white"></i> Map</button>'+
										'<input type="text" name="addon_address['+address_index+'][country]" style="margin-left:43px; width:197px;">';
									'</li></ul></div>'+
								'</div>';
							$('#addon_address').append(new_address);
						});

						$('#addon_address').on('click', '.address_is_default_radio', function() {
							var selected = $(this).val();
							$('.address_is_default_hidden').attr('value', 0);
							$('#address_is_default_'+selected).attr('value', 1);
						});
					});

				</script>
				<br/><br/>
				<!-- END ADDON ADDRESS -->
				
				<!-- END ADDON SOCIAL -->
				<div id="addon_social"></div>
				<button style="margin-left:396px" type="button" class="btn btn-mini pull-right button_add" id="social_add">More Social</button>
				<script>
					var social_index = 0;
					var social_type_options = jQuery.parseJSON('<?=json_encode($social_type_options)?>');
					var social_label = '<?=$social_label?>';

					$(document).ready(function() {
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
								
						$('#addon_social').html(new_social);
						
						$('#social_add').on('click', function() {
							social_index++;
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
						});
					});

				</script>
				<br/><br />
				<!-- END ADDON SOCIAL -->
				
				<div class="control-group">
					<label class="control-label">Gender</label>
					<div class="controls">
						<div class="btn-group" data-toggle="buttons-radio">
							<?php
								foreach($gender AS $title_val => $title_label) {
									echo '<button type="button" class="btn title_button';
									echo '" id="title_',$title_val,'" value="',$title_val,'">';
									echo $title_label,'</button>';
								}
							?>
						</div>
						<input type="hidden" name="addon_extra[0][gender]" id="addon_gender" value="">
						<script>
							$(document).ready(function() {
								$('.title_button').on('click', function() {
									$('#addon_gender').val($(this).attr('value'));
								});
							});
						</script>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="birth_date">Birthday</label>
					<div class="controls">
						<?=form_input('addon_extra[0][birth_date]', '', 'id="addon_extra_birth_date"')?>
					</div>
				</div>
				<div style="margin-top:20px;" class="control-group">
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
	$(function() {
		$( "#addon_extra_birth_date" ).datepicker();
	});
	
	$(document).ready(function() {
		$('.remove').live('click', function() {
			$(this).closest('.control-group').remove();
		});

		$('form').validator({messageClass:'alert alert-error'}).submit(function(e) {
			var form = $(this);
			var ajax_url = form.attr('data-ajax_save');

			if (typeof ajax_url === 'undefined' || ajax_url.length == 0) return;

			if (!e.isDefaultPrevented()) {
				$.post(
					ajax_url,
					form.serialize(),
					function(resp) {
						if (resp.success) {
							$.pjax({
								url: resp.details,
								container: '#main',
								timeout: 10000
							});
						} else {
							//show errors
							form.data('validator').invalidate(resp.details);
						}
					},
					'json'
				)
			}

			e.preventDefault();
		});
	});
	
	function getid(id) {
		return document.getElementById(id);
	}
	var uploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		browse_button : 'pickfiles',
		container: 'container',
		max_file_size : '10mb',
		url : '/card/upload/',

		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		]
	});
	uploader.bind('FilesAdded', function(up, files) {
		for (var i in files) {
			getid('filelist').innerHTML += '<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b></div>';
		}
	});
	uploader.bind('UploadProgress', function(up, file) {
		getid(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
	});
	getid('uploadfiles').onclick = function() {
		uploader.start();
		return false;
	};
	uploader.init();
	
</script>