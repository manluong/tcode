<script src="/resources/addon/jquery.timeago.js" type="text/javascript"></script>






<style>
	.comments { list-style-type: none; padding-left:0; margin:0; font-family:"Lucida Grande"; }
	.replies { list-style-type: none; padding-left:0; }
	.comments li, .replies li { padding: 5px; overflow:auto; }
	.comments li.comment { border: solid 1px #999; margin-bottom:10px; }
	.replies li.reply { border-bottom: solid 1px #9C9; background-color: #EFE; }
	.replies li.loaded { background-color:#FFE; }

	.comment .avatar { float:left; width:40px; height:40px; margin: 0 5px 0 0;  vertical-align:top; }
	.align { display:table-cell; width:50000px; vertical-align:top; }
	.reply .avatar { width:30px; height:30px; }

	.name { font-weight:bold; color:#393; }
	.text { margin: 10px 0; font-size: 12px; }
	.displaydate { font-size:10px; color: #AAA; margin: 2px 0; }
	.comment .controls { margin:5px 0; }
	.comment_input { width: 95%; }

	div.sending { border: solid 1px #A8A; background-color:#FEF; text-align:center; font-weight:bold; padding:5px; color:#A8A; }

	.show_more_replies { background-color:#EEF; border-bottom: solid 1px #9C9; padding:5px; text-align: center; cursor:pointer; }
	.show_more_comments { background-color:#EEF; border: solid 1px #99C; padding:5px; text-align: center; cursor:pointer; }
</style>






<ul class="comments">
	<li class="new_comment">
		<?=form_open('/comments/save')?>
			<input type="hidden" name="app_id" value="<?=$app_id?>" />
			<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
			<input type="hidden" name="parent_id" value="0" />
			<?=set_return_url();?>

			<input type="text" name="text" class="comment_input" value="" placeholder="write new comment..." autocomplete="off" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>" data-parent_id="0" />
		<?=form_close()?>
	</li>

	<?php foreach ($comments AS $comment): ?>
	<li class="comment" id="comment_<?=$comment['id']?>">
		<div class="avatar">
			<!-- $user['avatar'] -->
		</div>

		<div class="align">

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

			<ul class="replies">
				<?php foreach ($comment['replies'] AS $reply): ?>
					<li class="reply">
						<div class="avatar"><!-- $reply['user']['avatar'] --></div>
						<div class="align">
							<div class="name"><?=$reply['card_info']['card_fname'].' '.$reply['card_info']['card_lname']?></div>
							<div class="text"><?=$reply['text']?></div>
							<span class="displaydate" title="<?=$reply['created_stamp_iso8601']?>">
								<?=$reply['created_stamp_iso']?>
							</span>
						</div>
					</li>
				<?php endforeach; ?>

				<li class="new_reply">
					<?=form_open('/comments/save')?>
						<input type="hidden" name="app_id" value="<?=$app_id?>" />
						<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
						<input type="hidden" name="parent_id" value="<?=$comment['id']?>" />
						<?=set_return_url();?>

						<input type="text" name="text" class="comment_input" value="" placeholder="reply..." autocomplete="off" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>" data-parent_id="<?=$comment['id']?>" />
					<?=form_close()?>
				</li>
			</ul>

		</div>

	</li>
	<?php endforeach; ?>

	<?php if (count($comments)>=5) : ?>
		<li class="show_more_comments" data-comments_page="2" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>">Show more comments</li>
	<?php endif; ?>
</ul>






<script>
	$(document).ready(function(){
		$.timeago.settings.allowFuture = true;
		$('span.displaydate').timeago();



		$('ul.comments').on('click', 'div.show_more_replies', function(e){
			var id = $(this).attr('data-parent_id');
			var show_more_div = $(this);
			$.post(
				'/comments/ajax_load_more_replies',
				{parent_id:id},
				function(result) {
					if (result.success) {
						var new_replies = '';
						$.each(result.data, function(k, v) {
							new_replies +=
								'<li class="loaded reply">'+
									'<div class="avatar"></div>'+
									'<div class="align">'+
										'<div class="name">'+v.card_info.card_fname+' '+v.card_info.card_lname+'</div>'+
										'<div class="text">'+v.text+'</div>'+
										'<span class="displaydate" title="'+v.created_stamp_iso8601+'">'+
											v.created_stamp_iso+
										'</span>'+
									'</div>'+
								'</li>';
						});
						$(new_replies).hide().prependTo('#comment_'+id+' ul.replies').fadeIn('slow');
						show_more_div.hide();
						$('span.displaydate').timeago();
					}
				},
				'json'
			);
		});



		$('ul.comments').on('click', 'li.show_more_comments', function(e){
			var show_more_div = $(this);
			var page = show_more_div.attr('data-comments_page');
			var app_id = show_more_div.attr('data-app_id');
			var app_data_id = show_more_div.attr('data-app_data_id');

			$.post(
				'/comments/ajax_load_more_comments',
				{ page: page, app_id: app_id, app_data_id: app_data_id },
				function(result) {
					if (result.success) {
						if (result.data.length < 5) $('li.show_more_comments').hide();

						var new_comments = '';
						$.each(result.data, function(k, v) {
							new_comments +=
								'<li class="comment" id="comment_'+v.id+'">'+
									'<div class="avatar"></div>'+
									'<div class="align">'+
										'<div class="name">'+v.card_info.card_fname+' '+v.card_info.card_lname+'</div>'+
										'<div class="text">'+v.text+'</div>'+
										'<div class="controls">'+
											'<span class="displaydate" title="'+v.created_stamp_iso8601+'">'+
												v.created_stamp_iso+
											'</span> '+
										'</div>'+
										'<ul class="replies">';
											if (parseInt(v.reply_count) > 5) {
												new_comments += '<div class="show_more_replies" data-parent_id="'+v.id+'">Show '+(parseInt(v.reply_count)-5)+' Replies</div>';
											}
											$.each(v.replies, function(sk, sv) {
												new_comments += '<li class="reply">'+
													'<div class="avatar"></div>'+
													'<div class="align">'+
														'<div class="name">'+sv.card_info.card_fname+' '+sv.card_info.card_lname+'</div>'+
														'<div class="text">'+sv.text+'</div>'+
														'<span class="displaydate" title="'+sv.created_stamp_iso8601+'">'+
															sv.created_stamp_iso+
														'</span>'+
													'</div>'+
												'</li>';
											});

							new_comments +=	'<li class="new_reply">'+
												'<input type="text" name="text" class="comment_input" value="" placeholder="reply..." autocomplete="off" data-app_id="'+app_id+'" data-app_data_id="'+app_data_id+'" data-parent_id="'+v.id+'" />'+
											'</li>'+
										'</ul>'+
									'</div>'+
								'</li>'
						});
						$(new_comments).hide().insertBefore('li.show_more_comments').fadeIn('slow');
						show_more_div.attr('data-comments_page', parseInt(page)+1);
						$('span.displaydate').timeago();
					}
				},
				'json'
			);
		});


		$('ul.comments').on('keypress', 'input.comment_input', function(e){
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
								$('#comment_'+parent_id+' li.new_reply').before(
									'<li class="reply">'+
										'<div class="avatar"></div>'+
										'<div class="align">'+
											'<div class="name">'+result.data.card_info.card_fname+' '+result.data.card_info.card_lname+'</div>'+
											'<div class="text">'+result.data.text+'</div>'+
											'<span class="displaydate" title="'+result.data.created_stamp_iso8601+'">'+
												result.data.created_stamp_iso+
											'</span>'+
										'</div>'+
									'</li>'
								);
							} else {
								$('.comments li.new_comment').after(
									'<li class="comment" id="comment_'+result.data.id+'">'+
										'<div class="avatar"></div>'+
										'<div class="align">'+
											'<div class="name">'+result.data.card_info.card_fname+' '+result.data.card_info.card_lname+'</div>'+
											'<div class="text">'+result.data.text+'</div>'+
											'<div class="controls">'+
												'<span class="displaydate" title="'+result.data.created_stamp_iso8601+'">'+
													result.data.created_stamp_iso+
												'</span> '+
											'</div>'+
											'<ul class="replies">'+
												'<li class="new_reply">'+
													'<input type="text" name="text" class="comment_input" value="" placeholder="reply..." autocomplete="off" data-app_id="'+app_id+'" data-app_data_id="'+app_data_id+'" data-parent_id="'+result.data.id+'" />'+
												'</li>'+
											'</ul>'+
										'</div>'+
									'</li>'
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