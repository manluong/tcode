<div id="sidebar">
	<div id="company-logo">
	<?php
		if ($company_logo != '') {
			echo "<img src=\"/resources/images/$company_logo\" />";
		} else {
			echo "<h3>$company_name</h3>";
		}
	?>
	</div>

	<div id="global-search-container">
		<div id="global-search">
			<input type="text" name="global-search" id="global-search-field" class="search-query" />
			<i class="icon-search" id="global-search-icon"></i>
		</div>
		<div id="global-search-options" class="hide">
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="optionsCheckboxList1" value="option1">
						Contacts
					</label>
					<label class="checkbox">
						<input type="checkbox" name="optionsCheckboxList2" value="option2">
						Vendors
					</label>
					<label class="checkbox">
						<input type="checkbox" name="optionsCheckboxList3" value="option3">
						Documents
					</label>
					<a class="btn btn-primary">Search</a>
					<a class="btn" id="global-search-cancel">Cancel</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$('#global-search-field').on('focus click', function() {
				$('#global-search-options').slideDown(300);
				hide_status_update();
			}).on('keypress', function(e) {
				if (e.which == 13) $('#global-search-options').slideUp(300);
			});

			$('#global-search-cancel').on('click', function() {
				$('#global-search-options').slideUp(300);
			});
		});
	</script>




	<div id="status">
		<div class="row-fluid">
			<div class="avatar"><img src="/resources/template/<?=get_template()?>/img/avatar.png" /></div>
			<div id="status-details">
				<strong><?=$user_name?></strong><br />
				<div id="status-current"></div>
				<div>
					<span id="status-availability"></span>
					<span id="status-location"></span>
					<span id="status-task"></span>
				</div>
			</div>
		</div>

		<div id="status-update" class="row-fluid">
			<form id="status-update-form">
				Currently:<br /> <input type="text" id="status-message" name="message" value="" />

				<div id="status-more-options" class="hide">
					Availability:<br /> <select id="status-status_type_id" name="status_type_id"></select>
					Location:<br /> <select id="status-location_id" name="location_id"></select>
					Working On:<br /> <select id="status-task_id" name="task_id"></select>
				</div>

				<div id="status-more" class="row-fluid">
					<a href="#" class="pull-right">More Options</a>
				</div>

				<input type="hidden" name="geo_lat" val="" />
				<input type="hidden" name="geo_lng" val="" />

				<a class="btn btn-primary" id="status-update-submit">Update</a><a class="btn status-close">Cancel</a>
			</form>
		</div>
	</div>

	<script>
		var status_dropdowns_loaded = false;
		var stor_status;

		$(document).ready(function() {
			update_status();

			//Make status update
			$('#status-update-submit').on('click', function() {
				var new_status = $('#status-update-form').serializeArray();
				$.post(
					'/status/ajax_update',
					new_status,
					function(resp) {
						if (resp.success) {
							update_status();
							hide_status_update();
						}
					},
					'json'
				)
			});

			//Open status update form
			$('#status-details, #status .avatar').on('click', function() {
				if ($('#toggle_sidebar').hasClass('icon-chevron-right')) {
					show_sidebar();
				}

				$('#status-update').slideDown(300);
				$('#global-search-options').slideUp(300);
				$('#status-message').focus().on('keypress', function(e) {
					if (e.which == 13) $('#status-update-submit').click();
				});

				setTimeout('resize_nav()', 500);
			});

			//override status update form's default submit event
			$('#status-update-form').on('submit', function(e) {
				e.preventDefault();
				return false;
			});

			//Close status update form
			$('#status-update a.status-close').on('click', function() {
				hide_status_update();
			});

			//Open up more options on the status update form
			$('#status-more a').on('click', function() {
				//loads up all the dropdown fields and show the extra fields
				if (!status_dropdowns_loaded) {
					load_status_locations();
					load_status_tasks();
					load_status_availability();
					status_dropdowns_loaded = true;
				}

				$('#status-more').addClass('hide');
				$('#status-more-options').slideDown(300);
				setTimeout('resize_nav()', 500);
			});
		});

		function hide_status_update() {
			$('#status-update').slideUp(300);
			$('#status-more').removeClass('hide');
			$('#status-more-options').slideUp(300);
			setTimeout('resize_nav()', 500);
		}

		function update_status() {
			$.get(
				'/status/ajax_get_status',
				function (resp) {
					if (resp.data.message != '') {
						stor_status = resp.data;
						$('#status-current').html(resp.data.message);
						$('#status-message').val(resp.data.message);

						if (typeof resp.data.status_type != 'undefined' && resp.data.status_type != '' && resp.data.status_type != null) $('#status-availability').html(resp.data.status_type).addClass('label');
						if (typeof resp.data.location != 'undefined' && resp.data.location != '' && resp.data.location != null) $('#status-location').html('@ ' + resp.data.location);
						if (typeof resp.data.task != 'undefined' && resp.data.task != '' && resp.data.task != null) $('#status-task').html('on ' + resp.data.task);

						if (resp.data.availability == 1) {
							$('#status-availability').addClass('label-success');
						} else {
							$('#status-availability').addClass('label-important');
						}
					} else {
						$('#status-current').html('');
					}
				},
				'json'
			);
		}

		function load_status_locations() {
			$.get(
				'/locations/ajax_get_list',
				function(resp) {
					var target = $('#status-location_id');
					$(resp.data).each(function(i, v) {
						target.append('<option value="'+v.id+'">'+v.name+'</option>');
					});
					if (stor_status.location_id != '') {
						$('#status-location_id').val(stor_status.location_id);
					}
				},
				'json'
			);
		}

		function load_status_tasks() {
			$.get(
				'/tasks/ajax_get_list',
				function(resp) {
					var target = $('#status-task_id');
					$(resp.data).each(function(i, v) {
						target.append('<option value="'+v.id+'">'+v.name+'</option>');
					});
					if (stor_status.task_id != '') {
						$('#status-task_id').val(stor_status.task_id);
					}
				},
				'json'
			);
		}

		function load_status_availability() {
			$.get(
				'/status/ajax_get_availability_list',
				function(resp) {
					var target = $('#status-status_type_id');
					$(resp.data).each(function(i, v) {
						target.append('<option value="'+v.id+'">'+v.name+'</option>');
					});
					if (stor_status.status_type_id != '') {
						$('#status-status_type_id').val(stor_status.status_type_id);
					}
				},
				'json'
			);
		}
	</script>



	<div id="nav">
		<ul class="unstyled">
		<?php
			foreach ($app_list as $field1) {
				$langname = 'apptitle_'.$field1['core_apps_name'];
				$icon = '';
		?>
			<li>
				<a href="<?=base_url().$field1['core_apps_name']?>" data-app_name="<?=$field1['core_apps_name']?>" class="ajax">
					<span class="app-icon" title="<?=$this->lang->line('core_'.$langname)?>">
						<img src="/resources/images/appicons/30/<?=$field1['core_apps_name']?>.png" />
					</span>
					<span class="app-name"><?=$this->lang->line('core_'.$langname)?></span>
				</a>
			</li>
		<?php
			}
		?>
		</ul>
	</div>

	<script>
	   $(document).ready(function() {
		  $('a.ajax').pjax('#content-container');
		  $('#nav span.app-icon').tooltip({
			  trigger: 'manual',
			  placement: 'right'
		  });
		  $('#nav').on('mouseover', 'span.app-icon', function(e) {
			  if ($('#toggle_sidebar').hasClass('icon-chevron-right')) $(this).tooltip('show');
		  }).on('mouseout', 'span.app-icon', function(e) {
			  if ($('#toggle_sidebar').hasClass('icon-chevron-right')) $(this).tooltip('hide');
		  });
	   });
	</script>



	<div id="sidebar-logo">
		<a href="#1">
			<img src="/resources/template/<?=get_template()?>/img/telcoson-embossed.png" />
		</a>
	</div>



	<div id="sidebar-footer">
		<span id="sidebar-footer-controls">
			<a href="/access/logout" title="Log Off"><i class="icon-off"></i></a>
			<a href="/setting" title="Settings" class="ajax"><i class="icon-cog"></i></a>
			<a href="#" title="History"><i class="icon-time"></i></a>
			<a href="#" title="Feedback"><i class="icon-comment"></i></a>
		</span>
		<i class="icon-chevron-left" id="toggle_sidebar"></i>
	</div>

	<script>
		$(document).ready(function() {
			$('#sidebar-footer-controls a').tooltip();

			if ($.cookie) {
				if ($.cookie('menuCollapsed') === '1') {
					hide_sidebar();
				}
			}

			$('#toggle_sidebar').click(function() {
				if ($('#toggle_sidebar').hasClass('icon-chevron-left')) {
					hide_sidebar();
				} else {
					show_sidebar();
				}
			});
		});
	</script>



</div>