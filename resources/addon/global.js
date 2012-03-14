$(document).ready(function(){
	$.timeago.settings.allowFuture = true;


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
					$.each(result.data, function(k, v) {
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



	$('#content-container').on('click', 'button.show_more_comments', function(e){
		var show_more_button = $(this);
		var page = show_more_button.attr('data-comments_page');
		var per_page = show_more_button.attr('data-per_page');
		var app_id = show_more_button.attr('data-app_id');
		var app_data_id = show_more_button.attr('data-app_data_id');

		$.post(
			'/comments/ajax_load_more_comments',
			{ page: page, per_page:per_page, app_id: app_id, app_data_id: app_data_id },
			function(result) {
				if (result.success) {
					if (result.data.length < per_page) show_more_button.hide();

					var new_comments = '';
					$.each(result.data, function(k, v) {
						v.app_id = app_id;
						v.app_data_id = app_data_id;
						v.replies_html = '';
						$.each(v.replies, function(sk, sv) {
							v.replies_html += Mustache.to_html(tpl_comments.reply, sv);
						});

						v.show_more_replies = ''
						if (parseInt(v.reply_count) > 5) {
							var show_more = [];
							show_more.id = v.id;
							show_more.reply_count = parseInt(v.reply_count) - 5;
							v.show_more_replies = Mustache.to_html(tpl_comments.show_more_replies_button, show_more);
						}

						new_comments += Mustache.to_html(tpl_comments.comment, v);
					});
					$(new_comments).hide().insertBefore(show_more_button).fadeIn('slow');
					show_more_button.attr('data-comments_page', parseInt(page)+1);
					$('span.displaydate').timeago();
				}
			},
			'json'
		);
	});


	$('#content-container').on('keypress', 'input.comment_input', function(e){
		if (e.which == 13) {	//if enter key is pressed
			var textbox = $(this);
			var app_id = textbox.attr('data-app_id');
			var app_data_id = textbox.attr('data-app_data_id');
			var parent_id = textbox.attr('data-parent_id');
			var text = textbox.val();

			var textboxform = textbox.closest('form');

			var sending = '<div class="sending">Sending message</div>';

			if (text.length == 0) {
				e.preventDefault();
				return false;
			}

			if (parent_id == 0) {
				var is_reply = false;
				var url = '/comments/ajax_save_comment';
			} else {
				var is_reply = true;
				var url = '/comments/ajax_save_reply';
			}

			textbox.attr('disabled', 'disabled').fadeTo('slow', 0.1);
			var sending_div = $(sending);
			sending_div.insertAfter(textbox).hide().fadeTo('slow', 1.0);
			$(textboxform).attr('onSubmit', 'return false;')

			$.post(
				url,
				{ app_id: app_id, app_data_id: app_data_id, parent_id: parent_id, text: text },
				function(result) {
					if (result.success) {
						textbox.val('');
						if (is_reply) {
							$('#comment_'+parent_id+' div.new_reply').before(
								Mustache.to_html(tpl_comments.reply, result.data)
							);
						} else {
							result.data.app_id = app_id;
							result.data.app_data_id = app_data_id;
							textbox.parent().after(
								Mustache.to_html(tpl_comments.comment, result.data)
							);
						}
						$('span.displaydate').timeago();
					} else {
						alert('Unable to save comment. Please try again.'+result.message);
					}
					textbox.removeAttr('disabled').fadeTo('slow', 1.0);
					sending_div.fadeTo('slow', 0.1).delay(5000).remove();
					$(textboxform).removeAttr('onSubmit');
				},
				'json'
			);

			//prevent enter key from submitting the form
			e.preventDefault();
			return false;
		}
	});



});