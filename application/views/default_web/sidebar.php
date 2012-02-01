<div id="company-logo">
	<img src="/resources/template/<?=get_template()?>/images/placeholder-image.jpg" />
	<h3><?=$company_name?></h3>
</div>

<div id="global-search-container">
	<div id="global-search">
		<i class="icon-search"></i><input type="text" name="global-search" id="global-search-field" class="search-query fade_text" data-default_text="search globally" />
	</div>
</div>

<div id="status">
	<div class="avatar"><img src="/resources/template/<?=get_template()?>/images/placeholder-image.jpg" /></div>
	<div class="status-details">
		<strong>Name</strong><br />
		<div>Status update</div>
		<div>Busy on ... @ ...</div>
	</div>
</div>

<div id="nav">
	<ul class="unstyled">
	<?php
		foreach ($app_list as $field1) {
			$langname = "apptitle_".$field1['core_apps_name'];

			$icon = ($field1['core_apps_icon'])
				? $field1['core_apps_icon']
				: '';
	?>
		<li>
			<a href="<?=base_url()?><?=$field1['core_apps_name']?>">
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
		<!--<a href="/access/logout" title="Log Off"><img src="/resources/template/<?=get_template()?>/images/icon-logout.png" /></a>-->
		<a href="/access/logout" title="Log Off"><i class="icon-off" ></i></a>
		<a href="#" title="Settings"><i class="icon-cog" ></i></a>
		<a href="#" title="History"><i class="icon-time" ></i></a>
		<a href="#" title="Feedback"><i class="icon-comment" ></i></a>
	</span>
	<i class="icon-chevron-left" id="toggle_sidebar"></i>
</div>


<script>
$(document).ready(function() {
	$('#sidebar-footer-controls a').tooltip();

	$('#toggle_sidebar').click(function() {
		if ($('#toggle_sidebar').hasClass('icon-chevron-left')) {
			$('#toggle_sidebar').removeClass('icon-chevron-left').addClass('icon-chevron-right');

			$('#sidebar').animate({
				width:'-=170px'
			},300);

			$('#content-container').animate({
				left:'-=170px'
			},300);

			$('#status .avatar').animate({
				width:'30px',
				height:'30px'
			},300);

			$('#status').animate({
				height:'30px'
			},300);

			$('#sidebar-footer').animate({
				width:'-=170px'
			},300);

			$('#company-logo h3').slideToggle(200);
			$('#company-logo img').animate({
				height:'30px'
			},500,function() {
				resize_nav();
			});

			$('#global-search-container').slideToggle(200);
			$('#status .status-details').slideToggle(200);
			$('#nav .app-name').slideToggle(200);
			$('#sidebar-footer-controls').slideToggle(0);
			$('#sidebar-logo').fadeOut(200);
		} else {
			$('#toggle_sidebar').removeClass('icon-chevron-right').addClass('icon-chevron-left');

			$('#sidebar').animate({
				width:'+=170px'
			},300);

			$('#content-container').animate({
				left:'+=170px'
			},300);

			$('#status .avatar').animate({
				width:'50px',
				height:'50px'
			},300);

			$('#status').animate({
				height:'50px'
			},300);

			$('#sidebar-footer').animate({
				width:'+=170px'
			},300);

			$('#company-logo h3').slideToggle(200).css('display', 'inline');
			$('#company-logo img').animate({
				height:'50px'
			},500,function() {
				resize_nav();
			});

			$('#global-search-container').slideToggle(200);
			$('#status .status-details').slideToggle(200);
			$('#nav .app-name').slideToggle(200);
			$('#sidebar-footer-controls').slideToggle(200);
			$('#sidebar-logo').fadeIn(2000);
		}

	});
});
</script>