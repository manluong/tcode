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
        <?php if($this->UserM->is_logged_in()):?>
        <!-- chat area -->
    <div class="chatSection pof" id="chat">
        <div class="chatSlider h50">
            <!-- chat list -->
            <div class="chatItemWrapper chatList por fr">
                <!-- show chat box -->
                <div class="chatBox poa">
                    <div class="chatBoxIner pb5 rounded7 fl">
                        <div class="chatBoxItem fl pv5 ph10">
                            <div class="avatar rounded14 fl mr5"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class=" rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 fwb">Andy Anthony</span>
                            <div class="tools fr">
                                <a href="#" class="fl mr5 mt3"><i class="iChat iChat2"></i></a>
                                <a href="#" class="fl w18 mt7"><input type="checkbox" class="styled" /></i></a>
                            </div>
                        </div>
                        <div class="chatBoxItem fl pv5 ph10">
                            <div class="avatar rounded14 fl mr5"><img src="http://diendan.hocmai.vn/customavatars/avatar1163893_1.gif" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 fwb">Andy Anthony</span>
                            <div class="tools fr">
                                <a href="#" class="fl mr5 mt3"><i class="iChat iChat2"></i></a>
                                <a href="#" class="fl w18 mt7"><input type="checkbox" class="styled" /></i></a>
                            </div>
                        </div>
                        <div class="chatBoxItem fl pv5 ph10">
                            <div class="avatar rounded14 fl mr5"><img src="http://1.bp.blogspot.com/-W7sLIzOS9ws/T3cbzjHNtOI/AAAAAAAAcF4/SpKt8glEWqo/s400/avatar%2Bshock%2B%252870%2529.jpg" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 fwb">Tung Pham Thanh</span>
                            <div class="tools fr">
                                <a href="#" class="fl mr5 mt3"><i class="iChat iChat3"></i></a>
                                <a href="#" class="fl w18 mt7"><input type="checkbox" class="styled" /></i></a>
                            </div>
                        </div>
                        <div class="chatBoxItem fl pv5 ph10">
                            <div class="avatar rounded14 fl mr5"><img src="http://mobileworld.vn/forum/customavatars/avatar44905_19.gif" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 fwb">Andy Anthony</span>
                            <div class="tools fr">
                                <a href="#" class="fl mr5 mt3"><i class="iChat iChat2"></i></a>
                                <a href="#" class="fl w18 mt7"><input type="checkbox" class="styled" /></i></a>
                            </div>
                        </div>
                        <div class="chatBoxItem fl pv5 ph10">
                            <div class="avatar rounded14 fl mr5"><img src="http://profile.ak.fbcdn.net/hprofile-ak-snc4/276943_207161932643727_628715287_q.jpg" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 fwb">Tung Pham Thanh</span>
                            <div class="tools fr">
                                <a href="#" class="fl mr5 mt3"><i class="iChat iChat3"></i></a>
                                <a href="#" class="fl w18 mt7"><input type="checkbox" class="styled" /></i></a>
                            </div>
                        </div>
                        <div class="chatBoxItem fl pv5 ph10">
                            <div class="avatar rounded14 fl mr5">
                                <span class="rounded14 cf4 bg4 fwb noAvatar tac vam dpib">GP</span>
                            </div>
                            <span class="fl dpb ofh cf2 mt5 fwb">Group Chat</span>
                            <div class="tools fr">
                                <a href="#" class="fl mr5 mt3"><i class="iChat iChat3"></i></a>
                                <a href="#" class="fl w18 mt7"><input type="checkbox" class="styled" /></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end show chat box -->
                <!-- show chat status -->
                <div class="chatItem dpb fl cp active h50">
                    <i class="iChat iChat1 mt10 mh10"></i>
                </div>
                <!-- show chat status -->
            </div>
            <!-- chat list -->
            <!-- chat 1 -->
            <div class="chatItemWrapper por fl mr1">
                <!-- show chat box -->
                <div class="chatBox poa ">
                    <div class="chatBoxIner pb5 rounded7 fl bigChat">
                        <div class="chatBoxItem fl pv5 ph10">
                            <div class="fl">
                                <a href="#" class="fs11 tdu cf1">Loading History</a>
                            </div>
                            <div class="fr">
                                <a href="#"><i class="iChat iChat4 fl mr10 mt2"></i></a>
                                <a href="#"><i class="iChat iChat5 fl mr10 mt5"></i></a>
                                <a href="#"><i class="iChat iChat6 fl"></i></a>
                            </div>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10">
                            <div class="avatar rounded14 fl mr10"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class=" rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 w80p">
                                Whatssss?<br />
                            </span>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10">
                            <div class="avatar rounded14 fl mr10"><img src="http://diendan.hocmai.vn/customavatars/avatar1163893_1.gif" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 w80p">
                                Hey <br />
                            </span>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10">
                            <div class="avatar rounded14 fl mr10"><img src="http://1.bp.blogspot.com/-W7sLIzOS9ws/T3cbzjHNtOI/AAAAAAAAcF4/SpKt8glEWqo/s400/avatar%2Bshock%2B%252870%2529.jpg" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 w80p">
                                How is the design?<br />
                            </span>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10">
                            <div class="avatar rounded14 fl mr10"><img src="http://mobileworld.vn/forum/customavatars/avatar44905_19.gif" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 w80p">
                                Hey <br />
                            </span>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10">
                            <div class="avatar rounded14 fl mr10"><img src="http://profile.ak.fbcdn.net/hprofile-ak-snc4/276943_207161932643727_628715287_q.jpg" alt="" width="28" class="rounded14"></div>
                            <span class="fl dpb ofh cf1 mt5 w80p">
                                Hey <br />
                                What?<br />
                            </span>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10">
                            <div class="avatar rounded14 fl mr10">
                                <span class="rounded14 cf4 bg4 fwb noAvatar tac vam dpib">GP</span>
                            </div>
                            <span class="fl dpb ofh cf1 mt5 w80p">
                                Hey <br />
                                It's ok. Not bad... dad ad adada adada da dad ad ada a ad ad aadd ad aad  ad da
                            </span>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10">
                            <div class="avatar rounded14 fl mr10">
                                <span class="rounded14 cf4 bg4 fwb noAvatar tac vam dpib">GP</span>
                            </div>
                            <span class="fl dpb ofh cf1 mt5 w80p">
                               <img src="/resources/template/default_web/img/typing.gif" alt="" width=""/>
                            </span>
                        </div>
                        <div class="chatBoxItem fl pv1 ph10 bgN">
                            <input class="inv-field w95p mt10" type="text" onclick="value=''" onblur="if(value==''){value='This is description'};" value="This is description">
                        </div>
                    </div>
                </div>
                <!-- end show chat box -->
                <!-- show chat status -->
                <div class="chatItem fl cp h50 ph10 por active">
                    <span class="count bg2 fs12 fwb tac rounded7  lhn poa dpn">7</span>
                    <a href="" class="dpb mt10">
                    <div class="avatar rounded14 fl mr5"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class="rounded14"></div>
                    <span class="fl  dpb ofh cf1 mt5 fwb">Andrwe..</span>
                    </a>
                </div>
                <!-- show chat status -->
            </div>
            <!-- end chat 1 -->

            <div class="chatItemWrapper por fl mr1">
                <!-- show chat box -->
                <div class="chatBox poa dpn">
                    <!-- chat html here -->
                </div>
                <!-- end show chat box -->
                <!-- show chat status -->
                <div class="chatItem fl cp h50 ph10 por">
                    <span class="count bg2 fs12 fwb tac rounded7 lhn poa dpb">9</span>
                    <a href="" class="dpb mt10">
                        <div class="avatar rounded14 fl mr5"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class="rounded14"></div>
                        <span class="fl  dpb ofh cf1 mt5 fwb">Andrwe..</span>
                    </a>
                </div>
                <!-- show chat status -->
            </div>

            <div class="chatItemWrapper por fl mr1">
                <!-- show chat box -->
                <div class="chatBox poa dpn">
                    <!-- chat html here -->
                </div>
                <!-- end show chat box -->
                <!-- show chat status -->
                <div class="chatItem fl cp h50 ph10 por">
                    <span class="count bg2 fs12 fwb tac rounded7 lhn poa dpn">1</span>
                    <a href="" class="dpb mt10">
                        <div class="avatar rounded14 fl mr5"><img src="http://profile.ak.fbcdn.net/hprofile-ak-snc4/276943_207161932643727_628715287_q.jpg" alt="" width="28" class="rounded14"></div>
                        <span class="fl  dpb ofh cf1 mt5 fwb">Andrwe..</span>
                    </a>
                </div>
                <!-- show chat status -->
            </div>

            <div class="chatItemWrapper por fl mr1">
                <!-- show chat box -->
                <div class="chatBox poa dpn">
                    <!-- chat html here -->
                </div>
                <!-- end show chat box -->
                <!-- show chat status -->
                <div class="chatItem fl cp h50 ph10 por">
                    <span class="count bg2 fs12 fwb tac rounded7 lhn poa dpn">1</span>
                    <a href="" class="dpb mt10">
                        <div class="avatar rounded14 fl mr5"><img src="http://profile.ak.fbcdn.net/hprofile-ak-snc4/276943_207161932643727_628715287_q.jpg" alt="" width="28" class="rounded14"></div>
                        <span class="fl  dpb ofh cf1 mt5 fwb">Kobe Br..</span>
                    </a>
                </div>
                <!-- show chat status -->
            </div>




        </div>
    </div>
    <!-- end chat area -->
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

	<script>

	</script>
</body>
</html>