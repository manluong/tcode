<div class="top-setting">
	<div><h2>SETTINGS</h2><span> Invoice</span></div>
	<input type="button" class="btnX" />
</div>

<div class="content">
<ul id="tab-setting" class="nav nav-tabs">
	<li class="active"><a href="#tab-1">General</a></li>
	<?php foreach ($opts as $k => $v): ?>
	<li><a href="#tab-<?php echo $k ?>"><?php echo $v ?> Options</a></li>
	<?php endforeach ?>
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
					<span class="lb">Logo</span>
					<span class="fillter_input">
					</span>
				</li>
				<li>
					<span class="lb">Headline</span>
					<span class="fillter_input">
						<input type="text" name="tenant-headline" value="<?=(isset($settings['tenant']['headline']['value']))?$settings['tenant']['headline']['value']:''?>" />
						<input type="hidden" name="tenant-headline-override" value="0" />
					</span>
				</li>
				<li>
					<span class="lb">Invoice Title</span>
					<span class="fillter_input">
						<input type="text" name="tenant-invoice_title" value="<?=(isset($settings['tenant']['invoice_title']['value']))?$settings['tenant']['invoice_title']['value']:''?>" />
						<input type="hidden" name="tenant-invoice_title-override" value="0" />
					</span>
				</li>
				<li>
					<span class="lb">Quotation Title</span>
					<span class="fillter_input">
						<input type="text" name="tenant-quotation_title" value="<?=(isset($settings['tenant']['quotation_title']['value']))?$settings['tenant']['quotation_title']['value']:''?>" />
						<input type="hidden" name="tenant-quotation_title-override" value="0" />
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
	<?php foreach ($opts as $k => $v): ?>
	<div id="tab-<?php echo $k ?>" class="tab-pane">
		<div>
		<form action="/setting/ajax_add_option" method="post">
			<input type="hidden" name="app_name" value="invoice" />
			<input type="hidden" name="name" value="<?php echo $k ?>" />
			<input type="text" name="value" />
			<button type="button" class="btn btn-primary add-option" data-name="<?php echo $k ?>">Add</button>
		</form>
		</div>
		<table id="tbl-<?php echo $k ?>" cellspacing="0" cellpadding="0" class="tbList">
			<tbody>
				<?php foreach (${$k.'_opts'} as $opt): ?>
				<tr id="tr-opt-<?php echo $opt['id'] ?>">
					<td>
						<div id="div-opt-<?php echo $opt['id'] ?>">
							<span style="width: 490px;"><?php echo $opt['value'] ?></span>
							<div class="pull-right">
								<button type="button" class="btn btn-primary btn-mini change-option" data-id="<?php echo $opt['id'] ?>">Change</button>
							</div>
						</div>
						<div id="frm-opt-<?php echo $opt['id'] ?>" style="display: none;">
						<form action="/setting/ajax_save_options/invoice/<?php echo $k ?>" method="post">
							<span style="width: 300px;">
								<input type="hidden" name="id" value="<?php echo $opt['id'] ?>" />
								<input type="text" name="value[<?php echo $opt['id'] ?>]" value="<?php echo $opt['value'] ?>" />
								<input type="hidden" name="sort_order[<?php echo $opt['id'] ?>]" value="<?php echo $opt['sort_order'] ?>" />
							</span>
							<div class="pull-right">
								<button type="button" class="btn btn-primary btn-mini save-option" data-name="<?php echo $k ?>" data-id="<?php echo $opt['id'] ?>">Save</button> or <a href="#" class="cancel-option" data-id="<?php echo $opt['id'] ?>">cancel</a>
							</div>
						</form>
						</div>
					</td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<?php endforeach ?>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	function get_option_template(name, data, is_new) {
		var html = '' +
			'<td>' +
				'<div id="div-opt-'+data.id+'">' +
					'<span style="width: 490px;">'+data.value+'</span>' +
					'<div class="pull-right">' +
						'<button type="button" class="btn btn-primary btn-mini change-option" data-id="'+data.id+'">Change</button>' +
					'</div>' +
				'</div>' +
				'<div id="frm-opt-'+data.id+'" style="display: none;">' +
				'<form action="/setting/ajax_save_options/invoice/'+name+'" method="post">' +
					'<span style="width: 300px;">' +
						'<input type="hidden" name="id" value="'+data.id+'" />' +
						'<input type="text" name="value['+data.id+']" value="'+data.value+'" />' +
						'<input type="hidden" name="sort_order['+data.id+']" value="'+data.sort_order+'" />' +
					'</span>' +
					'<div class="pull-right">' +
						'<button type="button" class="btn btn-primary btn-mini save-option" data-name="'+name+'" data-id="'+data.id+'">Save</button> or <a href="#" class="cancel-option" data-id="'+data.id+'">cancel</a>' +
					'</div>' +
				'</form>' +
				'</div>' +
			'</td>';

		if (is_new) {
			html = '<tr id="tr-opt-'+data.id+'">'+html+'</tr>';
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
		if (frm.find('input[name="value"]').val().trim() == '') {
			return false;
		}
		btn.attr('disabled', 'disabled').addClass('disabled').html('Saving...');
		$.ajax({
			type: 'POST',
			url: frm.attr('action'),
			data: frm.serialize(),
			dataType: 'json',
			success: function(resp) {
				btn.html('Add').removeAttr('disabled').removeClass('disabled');
				if (resp.success) {
					var html = get_option_template(btn.data('name'), resp.details[0], true);
					$('#tbl-' + btn.data('name') + ' tbody').append(html);
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
		$('#frm-opt-'+id).hide();
		$('#div-opt-'+id).show();
	});

	$('.save-option').live('click', function (e) {
		var btn = $(this);
		var frm = $(this).closest('form');
		if (frm.find('input[name^="value"]').val().trim() == '') {
			return false;
		}
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
					$('#tr-opt-'+id).html(get_option_template(btn.data('name'), resp.details[0], false));
					$('#frm-opt-'+id).hide();
					$('#div-opt-'+id).show();
				} else {
					alert('Error');
				}
			}
		});
	});
});
</script>