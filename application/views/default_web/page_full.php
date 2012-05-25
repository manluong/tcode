<!DOCTYPE html><html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">

	<title><?=$title?></title>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/style.css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/all-module.css" />
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/modernizr-2.5.3.min.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.min.js"></script>
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->

	<?//=$head?>

</head>

<div id="popup_message" class="alert"></div>

<body class="fullwidth">
	<!-- Start Header -->
	<header>
		<div class="rightMenu">
			<?=$user_controls?>
		</div>

		<div class="logoSpace">
			<a href="/"><?=$company_name?></a>
		</div>
	</header>
	<!-- End Header -->

	<?=$sidebar?>

	<div role="main" id="main" class="clearfix">
		<div class="mainContent">
			<?//=$breadcrumb?>

			<?=$content?>
		</div>
	</div>

	<!-- Start Footer -->
	<footer>
		<a href="#" class="logo"></a>
		<div class="links">
			<a href="#">About</a> <a href="#">Send us feedback</a>
			<?php echo '<span style="font-size:11px;">DEBUG: env: ',ENVIRONMENT,' - role: ',APP_ROLE,' - db: ',$debug['database'],' - time taken: {elapsed_time}s - memory usage: {memory_usage}</span>'; ?>
		</div>
	</footer>
	<!-- End Footer -->

	<script type="text/javascript" src="/resources/addon/tpl.js"></script>
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

	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/dataTables.bootstrap-paging.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/theme-global.js"></script>

	<!-- new theme stuff -->
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/custom-form-elements.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/jquery.smooth-scroll.min.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/plugins.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/script.js"></script>

	<script>

	</script>
</body>
</html>