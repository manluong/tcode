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



	<div id="status">
		<div class="avatar"><img src="/resources/template/<?=get_template()?>/images/placeholder-image.jpg" /></div>
		<div id="status-details">
			<strong><?=$user_name?></strong><br />
			<div>Status update</div>
			<div>Busy on ... @ ...</div>
		</div>
	</div>

	<div id="status-update" class="popover bottom">
		<div class="arrow"></div>
		<div class="popover-inner">
			<h4 class="popover-title">Update Status <a class="close status-close pull-right" href="#">&times;</a></h4>

			<div class="popover-content"><p>
				Currently:<br /> <input type="text" id="status-new" name="status_new" value="" />
				<div id="status-more" class="row-fluid">
					<a href="#" class="pull-right clearfix">More Options</a>
				</div>
				<div id="status-more-options" class="hide">
					Location:<br /> <input type="text" id="status-location" name="status_location" value="" />
					Availability:<br /> <input type="text" id="status-availability" name="status_availability" value="" />
					Working On:<br /> <input type="text" id="status-task" name="status_task" value="" />
				</div>
				<div class="form-actions">
					<a class="btn btn-primary">Update</a><a class="btn status-close">Cancel</a>
				</div>
			</p></div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$('#status').on('click', function() {
				if ($('#toggle_sidebar').hasClass('icon-chevron-right')) {
					show_sidebar();
				}

				$('#status-update').fadeIn();
				$('#global-search-options').slideUp();
			});
			$('#status-update a.status-close').on('click', function() {
				$('#status-update').fadeOut();
				$('#status-more').removeClass('hide');
				$('#status-more-options').slideUp();
			});

			$('#status-more a').on('click', function() {
				$('#status-more').addClass('hide');
				$('#status-more-options').slideDown();
			});
		});
	</script>



	<div id="nav">
		<ul class="unstyled">
		<?php
			foreach ($app_list as $field1) {
				$langname = 'apptitle_'.$field1['core_apps_name'];
				$icon = '';

				if ($field1['core_apps_icon']) $icon = $field1['core_apps_icon'];
		?>
			<li>
				<a href="<?=base_url().$field1['core_apps_name']?>">
					<span class="app-icon">
						<img src="/resources/template/<?=get_template()?>/images/placeholder-image.jpg" />
					</span>
					<span class="app-name"><?=$this->lang->line('core'.$langname)?></span>
				</a>
			</li>
		<?php
			}
		?>
		</ul>
	</div>

	<div id="sidebar-logo">
		<a href="#1">
			<img src="/resources/template/<?=get_template()?>/images/telcoson-embossed.png" />
		</a>
	</div>

	<div id="sidebar-footer">
		<span id="sidebar-footer-controls">
			<a href="/access/logout" title="Log Off"><i class="icon-off"></i></a>
			<a href="#" title="Settings"><i class="icon-cog"></i></a>
			<a href="#" title="History"><i class="icon-time"></i></a>
			<a href="#" title="Feedback"><i class="icon-comment"></i></a>
		</span>
		<i class="icon-chevron-left" id="toggle_sidebar"></i>
	</div>
</div>



<script>
$(document).ready(function() {
	$('#sidebar-footer-controls a').tooltip();

	$('#global-search-field').on('focus click', function() {
		$('#global-search-options').slideDown();
		$('#status-update').fadeOut();
	}).on('keypress', function(e) {
		if (e.which == 13) $('#global-search-options').slideUp();
	});

	$('#global-search-cancel').on('click', function() {
		$('#global-search-options').slideUp();
	});

	if ($.cookie) {
		if ($.cookie('menuCollapsed') === '1') {
			hide_sidebar();
		}
	}

	$('#toggle_sidebar').click(function() {
		if ($('#toggle_sidebar').hasClass('icon-chevron-left')) {
			hide_sidebar();
			$.cookie && $.cookie('menuCollapsed', '1', {
				expires : 365,
				path : "/"
			});
		} else {
			show_sidebar();
			$.cookie && $.cookie('menuCollapsed', '0', {
				expires : 365,
				path : "/"
			});
		}
	});
});
</script>