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
        <link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/chat.css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap-8f.css" />
	<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/all-module.css" />
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/modernizr-2.5.3.min.js"></script>
	<script type="text/javascript" src="/resources/addon/jquery.min.js"></script>
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->

	<?//=$head?>

</head>
<script type="text/javascript">
	var ROOT = '<?=PATH_URL?>';
</script>
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
			<?=$app_title?>

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
<?php if($this->UserM->is_logged_in()):?>
	<!-- chat area -->
	<div class="chatSection pof" id="chat" style="display:none;">
		<div class="chatSlider h50">
			<!-- chat list -->
			<div class="chatItemWrapper chatList por fr">
				<!-- show chat box -->
				<div class="chatBox poa" id="list_chat" style="display:none;">
					<div class="chatBoxIner pb5 rounded7 fl">
						<div class="chatBoxItem fl pt5 ph10 bgN">
							<div class="fl">
								<a href="#" class="fs11 cf6 mb0 fwb" id="chat_status">Online</a>
							</div>
							<div class="fr">
								<a href="#" class="fs11 cf6 mb0 fwb">Group Chat</a>
							</div>
						</div>
						<div class="chatBoxItem fl pv5 ph10 bgN setStatus" id="set_status" style="display:none;">
							<p class="cf5 mb0 fl fs10 mt10 mr10 fwb w40 tac">Change Status</p>
							<a href="#" class="fl mr5 mt3 tac pt5 ph5 tdn status" id="status_online">
								<i class="iChat iChat1"></i>
								<div class="clear"></div>
								<span class=" cf6 fwb fs10">Online</span>
							</a>
							<a href="#" class="fl mr5 mt3 tac pt5 ph5 tdn status" id="status_offline">
								<i class="iChat iChat7 dpib"></i>
								<div class="clear"></div>
								<span class=" cf6 fwb fs10">Offline</span>
							</a>
							<a href="#" class="fl mr5 mt3 tac pt5 ph5 tdn status" id="status_busy">
								<i class="iChat iChat8"></i>
								<div class="clear"></div>
								<span class=" cf6 fwb fs10">Do Not Distrub</span>
							</a>
							<a href="#" class="fl mt15 fs14 fwb cf6">x</a>
						</div>
						<div class="chatBoxItem fl pv5 ph10 bgN" id="group_chat" style="display:none;">
							<div class="fl w75p">
								<input class="inv-field w100p " type="text" >
							</div>
							<div class="tools fr">
								<a href="#" class="fl mr5 mt3"><i class="iChat iChat2"></i></a>
								<a href="#" class="fl w18 mt7 " style="display:none;" ><input type="checkbox" class="" /></a>
							</div>
						</div>
                                                <div class="chatScroll">
                                                </div>
					</div>
				</div>
				<!-- end show chat box -->
				<!-- show chat status -->
				<div class="chatItem dpb fl cp h50" id="show_hide_chat">
					<i class="iChat iChat1 mt10 mh10"></i>
				</div>
				<!-- end show chat status -->
			</div>
			<!-- end chat list -->
		</div>
	</div>
	<!-- end chat area -->

	<!-- Start Settings Box -->
	<div id="overlay"></div>
	<div id="settings" class="hide">
		<div class="relativeMe">
			<!-- <a href="#" class="closeModal">x</a> -->
			<div class="step1"></div>
			<div class="step2"></div>
		</div>
	</div>

	<!--
 				<div class="title">Settings</div> <span class="arrow"></span> <span class="simple">Activity app</span>

				<form>
					<div class="clearfix">
					    <label>Mobile Phone</label>
					    <div class="input">
					        <input type="text" id="mobile" value="" />
					    </div>
					</div>
					<div class="clearfix">
					    <label>Office</label>
					    <div class="input">
					        <input type="text" id="office" value="" />
					    </div>
					</div>
					<div class="clearfix">
					    <label>Website</label>
					    <div class="input">
					        <input type="text" id="website" value="" />
					    </div>
					</div>

					<div class="actionButtons">
						<button class="btn primary">Save</button> or <button class="cancel">go back</button>
					</div>

				</form>
	-->
	<!-- End Settings Box -->

    <?php endif;?>
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
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/strophe.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/strophe.xdomainrequest.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/buzz.js"></script>
	<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/chat.js"></script>

</body>
</html>