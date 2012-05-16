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
