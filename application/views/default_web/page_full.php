<!DOCTYPE html><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">

	<title><?=$title?></title>

	<script type="text/javascript" src="/resources/addon/tpl.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.min.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.ui.min.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.tools.min.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.dataTables.fnReloadAjax.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.timeago.js"></script>
	<script type="text/javascript" src="/resources/addon/mustache.js"></script>
	<script type="text/javascript" src="/resources/addon/pjax.min.js"></script>

	<script type="text/javascript" src="/resources/addon/global.js"></script>
	<script type="text/javascript" src="/resources/addon/content.js"></script>
	<script type="text/javascript" src="/resources/addon/tpanel.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery-ui-timepicker-addon.js"></script>
	
	<script type="text/javascript" src="/resources/addon/helpdesk.js"></script>
	
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/dataTables.bootstrap-paging.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/theme-global.js"></script>

	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/base.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap-responsive.min.css" />

	<!-- Docs -->
		<!-- plupload -->
		<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
		<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
		<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
		<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />
		<!-- end plupload -->

		<!-- Adaptive ui -->
		<link rel="stylesheet" type="text/css" href="/resources/addon/docs/flexpaper.css" />
		<script type="text/javascript" src="/resources/addon/jquery.extensions.min.js"></script>
		<script type="text/javascript" src="/resources/addon/docs/flexpaper.js"></script>
		<script type="text/javascript" src="/resources/addon/docs/flexpaper_handlers.js"></script>
		<!-- end adaptive ui -->
	<!-- end Docs -->

	<?//=$head?>

</head>

<body>
    <div class="container-full">
		<?=$sidebar?>

		<div id="popup_message" class="alert"></div>
		<div id="content-container">
			<?=$breadcrumb?>
			<div id="content">
				<?=$content?>
			</div>
			<div id="content-footer">
				<?=$app_menu?>
				<?php echo '<div class="pull-right" style="padding:5px;">env: ',ENVIRONMENT,' - role: ',APP_ROLE,' - db: ',$debug['database'],'</div>'; ?>
			</div>
		</div>

	</div>

	<script>
		$(document).ready(function() {
			resize_nav();

			$('.dropdown-toggle').dropdown();
		});

		$(window).resize(function(){
			setTimeout('resize_nav()', 1000);
		});

		$('#content-container').on('click', function() {
			$('#status-update').slideUp(300);
			$('#global-search-options').slideUp(300);
			setTimeout('resize_nav()', 400);
		});

		function resize_nav() {
			var sidebar_height = $('#sidebar').outerHeight();
			sidebar_height -= $('#company-logo').outerHeight();
			sidebar_height -= $('#global-search-container').outerHeight();
			sidebar_height -= $('#status').outerHeight();
			sidebar_height -= $('#sidebar-footer').outerHeight();

			$('#nav').outerHeight(sidebar_height);
		}
	</script>

</body>
</html>