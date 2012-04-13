function hide_sidebar() {
	$('#toggle_sidebar').removeClass('icon-chevron-left').addClass('icon-chevron-right');

	$('#sidebar').animate({
		width:'-=170px'
	},300);

	$('#content-container').animate({
		left:'-=170px'
	},300);
	$('#content-footer').animate({
		left:'-=170px'
	},300);

	$('#sidebar-footer').animate({
		width:'-=170px'
	},300);

	$('#status .avatar img').animate({
		width:'30px',
		height:'30px'
	},500,function() {
		$('#nav').outerHeight($('#nav').outerHeight()+220).css('padding-bottom','30px');
	});

	$('#company-logo').slideToggle(200);
	$('#global-search-container').slideToggle(200);
	$('#status-details').slideToggle(200);
	$('#nav .app-name').slideToggle(200);
	$('#sidebar-footer-controls').slideToggle(0);
	$('#sidebar-logo').fadeOut(200);

	$('#status-update').fadeOut();

	$.cookie && $.cookie('menuCollapsed', '1', {
		expires : 365,
		path : "/"
	});
}

function show_sidebar() {
	$('#toggle_sidebar').removeClass('icon-chevron-right').addClass('icon-chevron-left');

	$('#sidebar').animate({
		width:'+=170px'
	},300);

	$('#content-container').animate({
		left:'+=170px'
	},300);
	$('#content-footer').animate({
		left:'+=170px'
	},300);

	$('#sidebar-footer').animate({
		width:'+=170px'
	},300);

	$('#status .avatar img').animate({
		width:'40px',
		height:'40px'
	},500,function() {
		$('#nav').outerHeight($('#nav').outerHeight()-220).css('padding-bottom','100px');
	});

	$('#company-logo').slideToggle(200);
	$('#global-search-container').slideToggle(200);
	$('#status-details').slideToggle(200);
	$('#nav .app-name').slideToggle(200);
	$('#sidebar-footer-controls').slideToggle(200);
	$('#sidebar-logo').fadeIn(2000);

	$.cookie && $.cookie('menuCollapsed', '0', {
		expires : 365,
		path : "/"
	});
}

function show_popup_message(message, alert_type) {
	var alert_class = '';

	if (alert_type == 'error') {
		alert_class = 'alert-error';
	} else if (alert_type == 'success') {
		alert_class = 'alert-success';
	} else {
		alert_class = 'alert-warning';
	}

	$('#popup_message').addClass(alert_class)
		.html(message)
		.animate({top:'30px'},700).delay(1000).animate({top:'-50px'},700)
		.promise().done(function() {
			$(this).removeClass(alert_class).html('');
		});
}
