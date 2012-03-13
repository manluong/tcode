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
	<script type="text/javascript" src="/resources/addon/jquery.timeago.js"></script>
	<script type="text/javascript" src="/resources/addon/mustache.js"></script>
	<script type="text/javascript" src="/resources/addon/pjax.min.js"></script>

	<script type="text/javascript" src="/resources/addon/global.js"></script>
	<script type="text/javascript" src="/resources/addon/content.js"></script>
	<script type="text/javascript" src="/resources/addon/tpanel.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery-ui-timepicker-addon.js"></script>

	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/dataTables.bootstrap-paging.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/theme-global.js"></script>

	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/base.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/jquerytool.css" />

	<!-- Docs -->
	<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
<script type="text/javascript" src="/resources/addon/docs/plupload/js/plupload.full.js"></script>
	<script type="text/javascript" src="/resources/addon/docs/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<script type="text/javascript" src="/resources/addon/docs/FlexPaper_1.5.1_flash/js/flexpaper_flash.js"></script>
	<link href="/resources/addon/docs/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />
	<!-- end Docs -->

	<?=$head?>

</head>

<body>
    <div class="container-full">
		<?=$sidebar?>

		<div id="popup_message" class="hide"></div>
		<div id="content-container">
			<?=$breadcrumb?>
			<div id="content">
				<?=$content?>
			</div>
			<div id="content-footer">
				<?=$app_menu?>
			</div>
			<?=$jsonload?>
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