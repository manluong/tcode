<div class="widget">
	<div class="widget-header">
		<h4>Comments</h4>
	</div>
	<div class="widget-body">
		<div class="comments row-fluid">
			<div class="new_comment row-fluid">
			<?=form_open('/comments/save')?>
				<input type="hidden" name="app_id" value="<?=$app_id?>" />
				<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
				<input type="hidden" name="parent_id" value="0" />
				<?=set_return_url();?>
				<input type="text" name="text" class="comment_input" value="" placeholder="write new comment..." autocomplete="off" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>" data-parent_id="0" />
			<?=form_close()?>
			</div>

			<?php foreach ($comments AS $comment): ?>
			<div class="comment row-fluid" id="comment_<?=$comment['id']?>">

				<div class="avatar">
					<img class="avatar" src="/resources/template/<?=get_template()?>/img/placeholder-image.jpg" />
				</div>

				<div class="content">

					<div class="name"><?=$comment['card_info']['card_fname'].' '.$comment['card_info']['card_lname']?></div>
					<div class="text"><?=$comment['text']?></div>

					<div class="controls">
						<span class="displaydate" title="<?=$comment['created_stamp_iso8601']?>">
							<?=$comment['created_stamp_iso']?>
						</span>
					</div>

					<?php
						if ($comment['reply_count'] > 5) {
							echo '<div class="show_more_replies" data-parent_id="'.$comment['id'].'">Show '.($comment['reply_count']-5).' Replies</div>';
						}
					?>

					<div class="replies row-fluid">
						<?php foreach ($comment['replies'] AS $reply): ?>
							<div class="reply row-fluid">
								<div class="avatar">
									<img class="avatar" src="/resources/template/<?=get_template()?>/img/placeholder-image.jpg" />
								</div>
								<div class="content">
									<div class="name"><?=$reply['card_info']['card_fname'].' '.$reply['card_info']['card_lname']?></div>
									<div class="text"><?=$reply['text']?></div>
									<span class="displaydate" title="<?=$reply['created_stamp_iso8601']?>">
										<?=$reply['created_stamp_iso']?>
									</span>
								</div>
							</div>
						<?php endforeach; ?>

						<div class="new_reply row-fluid">
							<?=form_open('/comments/save')?>
								<input type="hidden" name="app_id" value="<?=$app_id?>" />
								<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
								<input type="hidden" name="parent_id" value="<?=$comment['id']?>" />
								<?=set_return_url();?>

								<div class="row-fluid">
								<input type="text" name="text" class="comment_input" value="" placeholder="reply..." autocomplete="off" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>" data-parent_id="<?=$comment['id']?>" />
								</div>
							<?=form_close()?>
						</div>
					</div>

				</div>
			</div>

			<?php endforeach; ?>

			<?php if (count($comments)>=5) : ?>
				<div class="show_more_comments" data-comments_page="2" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>">Show more comments</div>
			<?php endif; ?>

		</div>
	</div>
</div>



<script>
	$(document).ready(function(){
		$.timeago.settings.allowFuture = true;
		$('span.displaydate').timeago();



		$('div.comments').on('click', 'div.show_more_replies', function(e){
			var id = $(this).attr('data-parent_id');
			var show_more_div = $(this);
			$.post(
				'/comments/ajax_load_more_replies',
				{parent_id:id},
				function(result) {
					if (result.success) {
						var new_replies = '';
						$.each(result.data, function(k, v) {
							v.reply_class = "loaded";
							new_replies += Mustache.to_html(tpl_comments.reply, v);
						});
						$(new_replies).hide().prependTo('#comment_'+id+' div.replies').fadeIn('slow');
						show_more_div.hide();
						$('span.displaydate').timeago();
					}
				},
				'json'
			);
		});



		$('div.comments').on('click', 'div.show_more_comments', function(e){
			var show_more_div = $(this);
			var page = show_more_div.attr('data-comments_page');
			var app_id = show_more_div.attr('data-app_id');
			var app_data_id = show_more_div.attr('data-app_data_id');

			$.post(
				'/comments/ajax_load_more_comments',
				{ page: page, app_id: app_id, app_data_id: app_data_id },
				function(result) {
					if (result.success) {
						if (result.data.length < 5) $('div.show_more_comments').hide();

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
								v.show_more_replies = '<div class="show_more_replies" data-parent_id="'+v.id+'">Show '+(parseInt(v.reply_count)-5)+' Replies</div>';
							}

							new_comments += Mustache.to_html(tpl_comments.comment, v);
						});
						$(new_comments).hide().insertBefore('div.show_more_comments').fadeIn('slow');
						show_more_div.attr('data-comments_page', parseInt(page)+1);
						$('span.displaydate').timeago();
					}
				},
				'json'
			);
		});


		$('div.comments').on('keypress', 'input.comment_input', function(e){
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
								$('.comments div.new_comment').after(
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
</script>