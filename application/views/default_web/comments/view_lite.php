<div class="comments">
	<?php if (count($comments)>=2) : ?>
	<div class="show_more">
		<a href="#" class="show_more_comments" data-last="<?=$comments[1]['id']?>" data-threaded="false" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>">Show older comments</a>
	</div>
	<?php endif; ?>

	<?php foreach ($comments AS $comment): ?>
	<div class="post reply">
		<div class="avatar">
			<img class="avatar" src="/resources/template/<?=get_template()?>/img/placeholder-image.jpg" />
		</div>

		<div class="content">
			<span class="name"><?=$comment['card_info']['card_fname'].' '.$comment['card_info']['card_lname']?></span>
			<span class="text"><?=$comment['text']?></span>

			<div class="post_controls">
				<a href="#" class="comment_reply" data-reply_to="<?=$comment['id']?>" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>">Reply</a> ·
				<span class="displaydate" title="<?=$comment['created_stamp_iso8601']?>">
					<?=$comment['created_stamp_iso']?>
				</span>
			</div>
		</div>
	</div>
	<?php endforeach; ?>

	<div class="new_comment">
		<input type="text" name="text" class="comment_input" value="" placeholder="write new comment..." autocomplete="off" data-app_id="<?=$app_id?>" data-app_data_id="<?=$app_data_id?>" data-parent_id="0" />
	</div>
</div>