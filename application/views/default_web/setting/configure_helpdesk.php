<div class="top-setting">
	<div><h2>SETTINGS</h2><span> Helpdesk</span></div>
	<input type="button" class="btnX" />
</div>

<div class="content">
<ul id="tab-setting" class="nav nav-tabs">
	<li class="active"><a href="#tab-1">General</a></li>
	<li><a href="#tab-2">Priority Options</a></li>
	<li><a href="#tab-3">Case Type Options</a></li>
</ul>
<div class="tab-content">
	<div id="tab-1" class="tab-pane active">
		<?php
			echo form_open('/setting/ajax_save/'.$app_name , array('class'=>'form-horizontal'));
		?>

		<?php if (APP_ROLE == 'TBOSS' && $is_admin) { ?>
		<div class="dtitle">
			<span class="upper">Global Level Settings</span>
			<span>Changes made here will apply to everyone including tenants.</span>
		</div>
		<div class="form">
			<div class="textcontent">There are no configuration settings for this section at the moment.</div>
		</div>
		<?php } ?>

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
// 							$priority_options = (isset($settings['tenant']['priority']['value']))
// 													? json_decode($settings['tenant']['priority']['value'])
// 													: array();

// 							foreach($priority_options AS $p) {
// 								echo '<input type="text" name="tenant-priority[]" value="',$p,'" /><br />';
// 							}
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
	<div id="tab-2" class="tab-pane">
		<div>
		<form action="/setting/ajax_add_option" method="post">
			<input type="hidden" name="app_name" value="helpdesk" />
			<input type="hidden" name="name" value="priority" />
			<input type="text" name="value" />
			<button type="button" class="btn btn-primary add-option" data-table="tbl-priority">Add</button>
		</form>
		</div>
		<table id="tbl-priority" cellspacing="0" cellpadding="0" class="tbList">
			<tbody>
				<?php foreach ($priority_options as $option): ?>
				<tr id="tr-opt-<?php echo $option['id'] ?>">
					<td>
						<div id="div-opt-<?php echo $option['id'] ?>">
							<span style="width: 490px;"><?php echo $option['value'] ?></span>
							<div class="pull-right">
								<button type="button" class="btn btn-primary btn-mini change-option" data-id="<?php echo $option['id'] ?>">Change</button>
							</div>
						</div>
						<div id="frm-opt-<?php echo $option['id'] ?>" style="display: none;">
						<form action="/setting/ajax_save_options/helpdesk/priority" method="post">
							<span style="width: 300px;">
								<input type="hidden" name="id" value="<?php echo $option['id'] ?>" />
								<input type="text" name="value" value="<?php echo $option['value'] ?>" />
								<!-- <input type="hidden" name="sort_order" value="<?php echo $option['sort_order'] ?>" /> -->
							</span>
							<div class="pull-right">
								<button type="button" class="btn btn-primary btn-mini save-option" data-id="<?php echo $option['id'] ?>">Save</button> or <a href="#" class="cancel-option" data-id="<?php echo $option['id'] ?>">cancel</a>
							</div>
						</form>
						</div>
					</td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<div id="tab-3" class="tab-pane">
		<!-- <div>
		<form action="/setting/ajax_add_option" method="post">
			<input type="hidden" name="app_name" value="helpdesk" />
			<input type="hidden" name="name" value="case_type" />
			<input type="text" name="value" />
			<button type="button" class="btn btn-primary add-option" data-table="tbl-case_type">Add</button>
		</form>
		</div>
		<table id="tbl-case_type" cellspacing="0" cellpadding="0" class="tbList">
			<tbody>
				<?php foreach ($case_type_options as $option): ?>
				<tr>
					<td style="width: 490px;"><span><?php echo $option['value'] ?></span></td>
					<td><button class="btn btn-primary btn-mini change-option">Change</button></td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table> -->
	</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	function get_option_template(option, is_new) {
		var html = '' +
			'<td>' +
				'<div id="div-opt-'+option.id+'">' +
					'<span style="width: 490px;">'+option.value+'</span>' +
					'<div class="pull-right">' +
						'<button type="button" class="btn btn-primary btn-mini change-option" data-id="'+option.id+'">Change</button>' +
					'</div>' +
				'</div>' +
				'<div id="frm-opt-'+option.id+'" style="display: none;">' +
				'<form action="/setting/ajax_save_options/helpdesk/priority" method="post">' +
					'<span style="width: 300px;">' +
						'<input type="hidden" name="id" value="'+option.id+'" />' +
						'<input type="text" name="value" value="'+option.value+'" />' +
					'</span>' +
					'<div class="pull-right">' +
						'<button type="button" class="btn btn-primary btn-mini save-option" data-id="'+option.id+'">Save</button> or <a href="#" class="cancel-option" data-id="'+option.id+'">cancel</a>' +
					'</div>' +
				'</form>' +
				'</div>' +
			'</td>';

		if (is_new) {
			html = '<tr id="tr-opt-'+option.id+'">'+html+'</tr>';
		}
		return html;
	}

	$('#tab-setting a').on('click', function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

	$('.add-option').on('click', function (e) {
		var btn = $(this);
		var frm = $(this).closest('form');
		btn.attr('disabled', 'disabled').addClass('disabled').html('Saving...');
		$.ajax({
			type: 'POST',
			url: frm.attr('action'),
			data: frm.serialize(),
			dataType: 'json',
			success: function(resp) {
				btn.html('Add').removeAttr('disabled').removeClass('disabled');
				if (resp.success) {
					var html = get_option_template(resp.details[0], true);
					$('#' + btn.data('table') + ' > tbody').append(html);
				} else {
					alert('Error');
				}
			}
		});
	});

	$('.change-option').live('click', function (e) {
		var id = $(this).data('id');
		$('#div-opt-'+id).hide();
		$('#frm-opt-'+id).show();
	});

	$('.cancel-option').live('click', function (e) {
		e.preventDefault();
		var id = $(this).data('id');
		$('#div-opt-'+id).show();
		$('#frm-opt-'+id).hide();
	});

	$('.save-option').live('click', function (e) {
		var btn = $(this);
		var frm = $(this).closest('form');
		btn.attr('disabled', 'disabled').addClass('disabled').html('Saving...');
		$.ajax({
			type: 'POST',
			url: frm.attr('action'),
			data: frm.serialize(),
			dataType: 'json',
			success: function(resp) {
				btn.html('Save').removeAttr('disabled').removeClass('disabled');
				if (resp.success) {
					var id = btn.data('id');
					$('#tr-'+id).html(get_option_template(resp.details[0], false));
					$('#div-opt-'+id).hide();
					$('#frm-opt-'+id).show();
				} else {
					alert('Error');
				}
			}
		});
	});
});
</script>