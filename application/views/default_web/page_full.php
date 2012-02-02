<!DOCTYPE html><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">

	<title><?=$title?></title>

	<script type="text/javascript" src="/resources/addon/jquery.min.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.ui.min.js"></script>
	<script type="text/javascript" src="/resources/addon/tpanel.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.dataTables.min.js"></script>

	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/bootstrap-tooltip.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/bootstrap-dropdown.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/dataTables.bootstrap-paging.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/theme-global.js"></script>

	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/base.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap.min.responsive.css" />

	<?=$head?>

</head>

<body>
	<div id="popup_message" class="hide"></div>

    <div class="container-full">
		<?=$sidebar?>

		<div id="content-container">
			<?=$breadcrumb?>
			<div id="content">
				<?=$content?>
			</div>
			<div id="content-footer">
				<?=$app_menu?>
			</div>
		</div>

	</div>

	<script>
		$(document).ready(function() {
			resize_nav();

			$('.dropdown-toggle').dropdown()
		});

		$(window).resize(function(){
			resize_nav();
		});

		function resize_nav() {
			var sidebar_height = $('#sidebar').outerHeight();
			sidebar_height -= $('#company-logo').outerHeight();
			sidebar_height -= $('#global-search-container').outerHeight();
			sidebar_height -= $('#status').outerHeight();
			sidebar_height -= $('#sidebar-footer').outerHeight();

			$('#nav').outerHeight(sidebar_height);

			var offset = $('#status').offset();
			var offset_top = offset.top + $('#status').outerHeight() - 15;
			$('#status-update').offset({ top: offset_top });
		}
	</script>

</body>
</html>