<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<script type="text/javascript" src="/resources/addon/contacts.js"></script>
<script type="text/javascript" src="/resources/addon/jquery.imgareaselect.min.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/contact.css" />
<style>
.control-group{
	position:relative !important;
}
.remove{
	position:absolute !important;
	left:0px ;
	top:0px ;
}
</style>

<?php
session_start(); //Do not remove this
//only assign a new timestamp if the session variable is empty
if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key'])==0){
    $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
	$_SESSION['user_file_ext']= "";
}
$upload_dir = "upload_pic"; 				// The directory for the images to be saved in
$upload_path = $upload_dir."/";				// The path to where the image will be saved
$large_image_prefix = "resize_"; 			// The prefix name to large image
$thumb_image_prefix = "thumbnail_";			// The prefix name to the thumb image
$large_image_name = $large_image_prefix.$_SESSION['random_key'];     // New name of the large image (append the timestamp to the filename)
$thumb_image_name = $thumb_image_prefix.$_SESSION['random_key'];     // New name of the thumbnail image (append the timestamp to the filename)
$max_file = "3"; 							// Maximum file size in MB
$max_width = "500";							// Max width allowed for the large image
$thumb_width = "100";						// Width of thumbnail image
$thumb_height = "100";						// Height of thumbnail image
// Only one of these image types should be allowed for upload
$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this
$image_ext = "";	// initialise variable, do not change this.
foreach ($allowed_image_ext as $mime_type => $ext) {
    $image_ext.= strtoupper($ext)." ";
}

	//echo '<pre>';
	//print_r($data);
	//echo '</pre>';
?>
<div id="contact_edit" class="container">
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
					<div>
						<label onclick="load_upload_form()" class="control-label btn_upload"></label>
						<div id="uploadfiles">Upload File</div>
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
												'<label class="control-label  contact" for="tel_'+tel_index+'">'+tel_label+'</label>'+
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
												'<button class="btn btn-mini pull-left remove">X</button>'+
												'<label class="control-label contact" for="email_'+email_index+'">'+email_label+'</label>'+
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
												'<label class="control-label contact" for="address_'+address_index+'">'+address_label+'</label>'+
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
												'<button class="btn btn-mini pull-left remove">X</button>'+
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
												'<button class="btn btn-mini pull-left remove">X</button>'+
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
									echo '<button type="button" class="btn gender_button';
									echo '" id="title_',$title_val,'" value="',$title_val,'">';
									echo $title_label,'</button>';
								}
							?>
						</div>
						<input type="hidden" name="addon_extra[0][gender]" id="gender" value="0">
						<script>
							$(document).ready(function() {
								$('.gender_button').on('click', function() {
									$('#gender').val($(this).attr('value'));
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
				<div class="control-group">
					<label class="control-label" for="birth_date">Note</label>
					<div class="controls">
						<?=form_input('addon_notes[0][note]','', 'id="addon_notes_note"')?>
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

//	function getid(id) {
//		return document.getElementById(id);
//	}
//	var uploader = new plupload.Uploader({
//		runtimes : 'gears,html5,flash,silverlight,browserplus',
//		browse_button : 'pickfiles',
//		container: 'container',
//		max_file_size : '10mb',
//		url : '/card/upload/',
//
//		filters : [
//			{title : "Image files", extensions : "jpg,gif,png"},
//			{title : "Zip files", extensions : "zip"}
//		]
//	});
//	uploader.bind('FilesAdded', function(up, files) {
//		for (var i in files) {
//			getid('filelist').innerHTML += '<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b></div>';
//		}
//	});
//	uploader.bind('UploadProgress', function(up, file) {
//		getid(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
//	});
//	getid('uploadfiles').onclick = function() {
//		uploader.start();
//		return false;
//	};
//	uploader.init();
</script>

<!-- Crop image function -->
<?php /*
function resizeImage($image,$width,$height,$scale) {
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image);
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image);
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image);
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$image);
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$image,90);
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$image);
			break;
    }

	chmod($image, 0777);
	return $image;
}
//You do not need to alter these functions
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);

	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image);
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image);
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image);
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$thumb_image_name);
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$thumb_image_name,90);
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$thumb_image_name);
			break;
    }
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}
//You do not need to alter these functions
function getHeight($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}
//You do not need to alter these functions
function getWidth($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}

//Image Locations
$large_image_location = $upload_path.$large_image_name.$_SESSION['user_file_ext'];
$thumb_image_location = $upload_path.$thumb_image_name.$_SESSION['user_file_ext'];

//Create the upload directory with the right permissions if it doesn't exist
if(!is_dir($upload_dir)){
	mkdir($upload_dir, 0777);
	chmod($upload_dir, 0777);
}

//Check to see if any images with the same name already exist
if (file_exists($large_image_location)){
	if(file_exists($thumb_image_location)){
		$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name.$_SESSION['user_file_ext']."\" alt=\"Thumbnail Image\"/>";
	}else{
		$thumb_photo_exists = "";
	}
   	$large_photo_exists = "<img src=\"".$upload_path.$large_image_name.$_SESSION['user_file_ext']."\" alt=\"Large Image\"/>";
} else {
   	$large_photo_exists = "";
	$thumb_photo_exists = "";
}

if (isset($_POST["upload"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
	$userfile_size = $_FILES['image']['size'];
	$userfile_type = $_FILES['image']['type'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

		foreach ($allowed_image_types as $mime_type => $ext) {
			if($file_ext==$ext && $userfile_type==$mime_type){
				$error = "";
				break;
			}else{
				$error = "Only <strong>".$image_ext."</strong> images accepted for upload<br />";
			}
		}
		//check if the file size is above the allowed limit
		if ($userfile_size > ($max_file*1048576)) {
			$error.= "Images must be under ".$max_file."MB in size";
		}
	}else{
		$error= "Select an image for upload";
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){
			//this file could now has an unknown file extension (we hope it's one of the ones set above!)
			$large_image_location = $large_image_location.".".$file_ext;
			$thumb_image_location = $thumb_image_location.".".$file_ext;

			//put the file ext in the session so we know what file to look for once its uploaded
			$_SESSION['user_file_ext']=".".$file_ext;

			move_uploaded_file($userfile_tmp, $large_image_location);
			chmod($large_image_location, 0777);

			$width = getWidth($large_image_location);
			$height = getHeight($large_image_location);
			//Scale the image if it is greater than the width set above
			if ($width > $max_width){
				$scale = $max_width/$width;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}else{
				$scale = 1;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}
			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}
		//Refresh the page to show the new uploaded image
		header("location:".$_SERVER["PHP_SELF"]);
		exit();
	}
}

if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists)>0) {
	//Get the new coordinates to crop the image.
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	//Scale the image to the thumb_width set above
	$scale = $thumb_width/$w;
	$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
	//Reload the page again to view the thumbnail
	header("location:".$_SERVER["PHP_SELF"]);
	exit();
}


if ($_GET['a']=="delete" && strlen($_GET['t'])>0){
//get the file locations
	$large_image_location = $upload_path.$large_image_prefix.$_GET['t'];
	$thumb_image_location = $upload_path.$thumb_image_prefix.$_GET['t'];
	if (file_exists($large_image_location)) {
		unlink($large_image_location);
	}
	if (file_exists($thumb_image_location)) {
		unlink($thumb_image_location);
	}
	header("location:".$_SERVER["PHP_SELF"]);
	exit();
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
	$current_large_image_width = getWidth($large_image_location);
	$current_large_image_height = getHeight($large_image_location);
?>
<script type="text/javascript">
function preview(img, selection) {
	var scaleX = <?php echo $thumb_width;?> / selection.width;
	var scaleY = <?php echo $thumb_height;?> / selection.height;

	$('#thumbnail + div > img').css({
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
}

$(document).ready(function () {
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}else{
			return true;
		}
	});
});

$(window).load(function () {
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview });
});

</script>
<?php  } */?>

<div id="upload_avatar">
	<div id="contact_close"></div>
	<div id="contact_select_file">
		<h1 style="display:none;">Custom example</h1>
		<p style="display:none;">Shows you how to use the core plupload API.</p>
		<div id="container" >
			<button id="pickfiles" style="width:85px; height:25px;line-height:10px;" class="btn btn-inverse">Select File</button>
			<div style="display:none;" id="uploadfiles"></div>
			<div id="filelist" style="float:left ;margin:-57px 0 0 -66px;"></div>
		</div>
	</div>
	<div id="breadcrumb">
		<div id="module_name" style="width:650px;">
			<ul>
				<li><a style="width:173px;" href="#" class="main">CONTACT</a></li>
				<li class="arrow"></li>
				<li class="curent_page">Upload Avatar</li>
				<li><a href="#" id="favoriteIcon" class="on" title="Remove from favorites"></a></li>
			</ul>
		</div>
	</div>

	<div id="upload_crop">
		<?php
		//Display error message if there are any
		if(strlen($error)>0){
			echo "<ul><li><strong>Error!</strong></li><li>".$error."</li></ul>";
		}
		if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
			echo $large_photo_exists."&nbsp;".$thumb_photo_exists;
			echo "<p><a href=\"".$_SERVER["PHP_SELF"]."?a=delete&t=".$_SESSION['random_key'].$_SESSION['user_file_ext']."\">Delete images</a></p>";
			echo "<p><a href=\"".$_SERVER["PHP_SELF"]."\">Upload another</a></p>";
			//Clear the time stamp session and user file extension
			$_SESSION['random_key']= "";
			$_SESSION['user_file_ext']= "";
		}else{
				if(strlen($large_photo_exists)>0){?>
				<div align="center">
					<img height="400" src="<?=site_url($upload_path.$large_image_name.$_SESSION['user_file_ext'])?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
					<div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
						<img src="<?=site_url($upload_path.$large_image_name.$_SESSION['user_file_ext'])?>" style="position: relative;" alt="Thumbnail Preview" />
					</div>
					<br style="clear:both;"/>
					<form name="thumbnail" action="<?=site_url('card/add')?>" method="post">
						<input type="hidden" name="x1" value="" id="x1" />
						<input type="hidden" name="y1" value="" id="y1" />
						<input type="hidden" name="x2" value="" id="x2" />
						<input type="hidden" name="y2" value="" id="y2" />
						<input type="hidden" name="w" value="" id="w" />
						<input type="hidden" name="h" value="" id="h" />
						<input type="submit" name="upload_thumbnail" value="Save Thumbnail" id="save_thumb" />
					</form>
				</div>
			<hr />
			<?php 	} ?>
			<form name="photo" enctype="multipart/form-data" action="<?=site_url('card/add')?>" method="post" style="display:none;">
			Photo <input type="file" name="image" size="30"  /> <input type="submit" name="upload" value="Upload" />
			</form>
		<?php } ?>
	</div>

</div>

