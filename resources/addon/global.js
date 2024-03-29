$(document).ready(function(){
	$.timeago.settings.allowFuture = true;

	$('.pjax').pjax({
		container: '#main',
		timeout: 5000
	});
/*
	$('#content-container').on('click', 'button.show_more_replies', function(e){
		var id = $(this).attr('data-parent_id');
		var show_more_button = $(this);
		var show_replies = show_more_button.attr('data-show_replies');
		$.post(
			'/comments/ajax_load_more_replies',
			{parent_id:id, show_replies:show_replies},
			function(result) {
				if (result.success) {
					var new_replies = '';
					$.each(result.details, function(k, v) {
						v.reply_class = "loaded";
						new_replies += Mustache.to_html(tpl_comments.reply, v);
					});
					$(new_replies).hide().prependTo('#comment_'+id+' div.replies').fadeIn('slow');
					show_more_button.hide();
					$('span.displaydate').timeago();
				}
			},
			'json'
		);
	});
*/


	$('#content-container').on('click', 'a.show_more_comments', function(e){
		var show_more_button = $(this);
		var last_id = show_more_button.attr('data-last');
		var threaded = show_more_button.attr('data-threaded');
		var app_id = show_more_button.attr('data-app_id');
		var app_data_id = show_more_button.attr('data-app_data_id');

		$.post(
			'/comments/ajax_load_more_comments',
			{last_id: last_id, threaded:threaded, app_id: app_id, app_data_id: app_data_id},
			function(result) {
				if (result.success) {
					var new_posts = '';
					$.each(result.details, function(k, v) {
						v.app_id = app_id;
						v.app_data_id = app_data_id;
						v.reply = 'reply';
						new_posts += Mustache.to_html(tpl_comments.post, v);
					});

					$(new_posts).hide().insertBefore(show_more_button).fadeIn('slow');
					show_more_button.remove();
					$('span.displaydate').timeago();
				}
			},
			'json'
		);
	});

	//sets the parent_id attr of the input box to the reply that was clicked on
	$('#content-container').on('click', '.comment_reply', function(e) {
		var reply = $(this);
		var reply_to = reply.attr('data-reply_to');
		var app_id = reply.attr('data-app_id');
		var data_id = reply.attr('data-app_data_id');

		var reply_to_text = $('div.content[data-comment_id='+reply_to+']').find('span.name').html();
		if (reply_to_text == null) reply_to_text = $('div.content[data-comment_id='+data_id+']').find('span.name').html();
		reply_to_text = 'Replying to '+reply_to_text;

		var input_field = $('.comment_input[data-app_id='+app_id+'][data-app_data_id='+data_id+']');

		if (input_field.length) {
			input_field.attr('data-parent_id', reply_to);
			input_field.parent().find('div.reply_to').html(reply_to_text);
		} else {
			var v = [];
			v.app_id = app_id;
			v.app_data_id = data_id;
			v.parent_id = reply_to;
			v.reply_to_text = reply_to_text;
			var input_html = Mustache.to_html(tpl_comments.input, v);
			$('div.comments[data-app_id='+app_id+'][data-app_data_id='+data_id+']').append(input_html);
		}
	});

	//saves the comment
	$('#content-container').on('keypress', 'input.comment_input', function(e) {
		if (e.which != 13) return;	//if enter key is pressed

		var textbox = $(this);
		var app_id = textbox.attr('data-app_id');
		var app_data_id = textbox.attr('data-app_data_id');
		var parent_id = textbox.attr('data-parent_id');
		var text = textbox.val();
		if (text.length == 0) e.preventDefault();

		textbox.attr('disabled', 'disabled');

		$.post(
			'/comments/ajax_save_comment',
			{app_id: app_id, app_data_id: app_data_id, parent_id: parent_id, text: text},
			function(result) {
				if (result.success) {
					textbox.val('');
					result.details.app_id = app_id;
					result.details.app_data_id = app_data_id;
					result.details.reply = 'reply';
					textbox.parent().before(
						Mustache.to_html(tpl_comments.post, result.details)
					);
					$('span.displaydate').timeago();
				} else {
					alert('Unable to save comment. Please try again.'+result.message);
				}
				textbox.removeAttr('disabled');
			},
			'json'
		);

		//prevent enter key from submitting the form
		e.preventDefault();
	});


	// Settings
	var is_settings_loaded = false;
	$('.userDropdownPadding .settings').on('click', function() {
		if (!is_settings_loaded) {
			$.get(
				'/setting/ajax_index',
				function(resp) {
					$('#settings div.step1').html(resp);
					is_settings_loaded = true;
				},
				'html'
			);
		}
		$('#settings, #overlay').fadeIn('fast');
		$('.userDropdownPadding').hide();
		return false;
	});

	$(document).on('click', '#settings .cont-icon a', function() {
		$('#settings .step1').hide();
		var url = $(this).attr('href');
		$.get(
			url,
			function(resp) {
				$('#settings div.step2').html(resp).fadeIn('fast');
			},
			'html'
		);
		return false;
	});

	$(document).on('click', '#settings .save', function() {
		var button = $(this);
		var form = button.parents('form');
		button.attr('disabled', 'disabled').addClass('disabled').html('Saving...');
		$.post(
			form.attr('action'),
			form.serializeArray(),
			function(resp) {
				if (resp.success) {
					button.html('Saved!').delay('500').removeAttr('disabled').removeClass('disabled').delay('500').promise().done(function () {
						$('#settings .step2').hide();
						$('#settings .step1').fadeIn('fast');
					});
				}
			},
			'json'
		);
		return false;
	});

	$(document).on('click', '#settings .cancel', function() {
		$('#settings .step2').hide();
		$('#settings .step1').fadeIn('fast');
		return false;
	});

	$(document).on('click', '#overlay, .btnX', function() {
		$('#settings .step2').hide();
		$('#settings .step1').fadeIn('fast');
		$('#settings, #overlay').fadeOut('fast');
		return false;
	});






});