<style>
	.comments { list-style-type: none; padding-left:0; margin:10px; font-family:"Lucida Grande"; }
	.replies { list-style-type: none; padding-left:0; }
	.comments li, .replies li { padding: 5px; overflow:auto; }
	.comments li.comment { border: solid 1px #999; margin-bottom:10px; }
	.replies li.reply { border-bottom: solid 1px #666; background-color: #EFE; }

	.comment .avatar { float:left; width:40px; height:40px; margin: 0 5px 0 0;  vertical-align:top; }
	.align { display:table-cell; width:50000px; vertical-align:top; }
	.reply .avatar { width:30px; height:30px; }

	.name { font-weight:bold; color:#393; }
	.text { margin: 10px 0; font-size: 12px; }
	.datetime { font-size:10px; color: #AAA; margin: 2px 0; }
	.comment .controls { margin:5px 0; }
	.comment_input { width: 100%; }
</style>


<ul class="comments">
	<li class="new_comment">
		New Comment:
		<?=form_open('/comments/ajax_save_comment')?>
			<input type="hidden" name="app_id" value="<?=$app_id?>" />
			<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />

			<input type="text" name="text" class="comment_input" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>" />
		<?=form_close()?>
	</li>

	<?php foreach ($comments AS $comment): ?>
	<li class="comment" id="comment_<?=$comment['id']?>">
		<div class="avatar">
			<!-- $user['avatar'] -->
		</div>

		<div class="align">

			<div class="name"><?=$comment['card_info']['card_fname'],' ',$comment['card_info']['card_lname']?></div>
			<div class="text"><?=$comment['text']?></div>

			<div class="controls">
				<span class="datetime"><?=$comment['datetime']?></span>
				Like
			</div>

			<ul class="replies">
				<?php foreach ($comment['replies'] AS $reply): ?>
					<li class="reply">
						<div class="avatar"><!-- $reply['user']['avatar'] --></div>
						<div class="align">
							<div class="name"><?=$reply['card_info']['card_fname'],' ',$reply['card_info']['card_lname']?></div>
							<div class="text"><?=$reply['text']?></div>
							<span class="datetime"><?=$reply['datetime']?></span>
						</div>
					</li>
				<?php endforeach; ?>

				<li class="new_reply">
					Reply:
					<?=form_open('/comments/ajax_save_reply')?>
						<input type="hidden" name="app_id" value="<?=$app_id?>" />
						<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
						<input type="hidden" name="parent_id" value="<?=$comment['id']?>" />

						<input type="text" name="text" class="comment_input" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>" data-parent_id="<?=$comment['id']?>" />
					<?=form_close()?>
				</li>
			</ul>

		</div>

	</li>
	<?php endforeach; ?>
</ul>

<script>
	$(document).ready(function(){
		$('input.comment_input').keypress(function(e){
			if (e.which == 13) {	//if enter key is pressed
				var textbox = $(this);
				var app_id = textbox.attr('data-app_id');
				var app_data_id = textbox.attr('data-app_data_id');
				var parent_id = textbox.attr('data-parent_id');
				var text = textbox.attr('value');

				if (typeof parent_id == 'undefined') {
					var url = '/comments/ajax_save_comment';
					var is_reply = false;
				} else {
					var url = '/comments/ajax_save_reply';
					var is_reply = true;
				}

				$.post(
					url,
					{app_id: app_id, app_data_id: app_data_id, parent_id: parent_id, text: text},
					function(result) {
						if (result.success) {
							textbox.val('');
							if (is_reply) {
								$('#comment_'+parent_id+' li.new_reply').before(
									'<li class="reply"><div class="text">'+text+'</div></li>'
								);
							} else {
								$('.comments li.new_comment').before(
									'<li class="comment" id="comment_'+result.new_id+'"><div class="text">'+text+'</div></li>'
								);
							}
						} else {
							alert('Unable to save comment. Please try again.'+result.message);
						}
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