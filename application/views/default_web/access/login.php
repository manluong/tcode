<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>8Force</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">

	<link rel="stylesheet" href="/resources/template/default_web/css/bootstrap.min.css">
	<link rel="stylesheet" href="/resources/template/default_web/css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="/resources/template/default_web/css/bootstrap-8f.css">
	<link href='http://fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="/resources/template/default_web/css/style.css">
	<link rel="stylesheet" href="/resources/template/default_web/css/app.css">

</head>

<body class="login">

<div class="loginBox">
	<div class="logoSpace">
		<a href=""><?=$data['company_name']?></a>
	</div>

	<?php
		if (isset($status) && in_array($status, array(3,6,7,8))) {
		echo '
			<div class="alert alert-error">
				<i class="icon-exclamation-sign"></i>
				<strong>'.lang('access-login_status-'.$status).'</strong>
			</div>
		';
		}
	?>

	<form method="post" action="<?php echo base_url('access/login');?>">
		<?=reset_return_url_form();?>
		<div class="loginBoxText pt20">
			<!--Your request will be send to your team who can grant you access.-->
		</div>

		<?php if ($cookie_login) { ?>
			<div class="controls">
				<div class="input-append">
					<div class="avatar rounded20 mr5 pr15">
						<img width="40" height="40" class="rounded20" alt="" src="/resources/template/default_web/img/avatar.jpg">
						<span class="pl10"></span>
						<input class="span3" id="appendedInputButton" size="16" type="password" name="login_password" placeholder="Password">
						<button class="btn btn-primary" type="submit"><?=lang('button_login')?></button>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="controls pt20">
				<div class="input-append">
					<input class="span3" id="appendedInputButton" size="16" type="text" name="login_email" placeholder="<?=lang('login_email')?>">
					<input class="span2" id="appendedInputButton" size="16" type="password" name="login_password" placeholder="<?=lang('login_password')?>">
					<button class="btn btn-primary" type="submit"><?=lang('button_login')?></button>
				</div>
			</div>
		<?php } ?>
	</form>

    <div class="8forcelogo pt20">
    	<a href="#"><img width="150" height="27" class="rounded20" alt="" src="/resources/template/default_web/img/poweredby.png"></a>
    </div>

</div>

<div class="bottom-login">
	<ul>
    	<li><a href="/access/change_user" class="ico-user-grey">Change User</a></li>
        <li><a href="#" class="ico-plus-grey">Part of the team? Request a invite here</a></li>
        <li><a href="#" class="ico-learn-grey">Learn more</a></li>
        <li><a href="#" class="ico-cloud-grey">Start a new company</a></li>
    </ul>
</div>
</body>
</html>
