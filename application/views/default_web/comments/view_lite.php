<div class="comments-lite row-fluid">
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
				if ($comment['reply_count'] > $show_replies) {
					echo '<button class="btn btn-info show_more_replies" data-parent_id="'.$comment['id'].'" data-show_replies="',$show_replies,'">Show '.($comment['reply_count']-$show_replies).' Replies</button>';
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

	<?php if (count($comments)>=2) : ?>
		<button class="btn btn-info show_more_comments" data-comments_page="2" data-per_page="2" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>">Show older comments</button>
	<?php endif; ?>
</div>