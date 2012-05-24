
<?php if (!empty($comment)) {
		foreach ($comment as $k) {
		$date = strtotime($k->created_stamp);
		$date = (time() - $date)%60 ;
?>
<div class="comment_info">
	<div class="comment_info_user">
		<div class="comment_user_avatar"><image src="<?=site_url('resources/template/default_web/img/helpdesk')?>/comment_avatar.png"/></div>
		<div class="comment_user_name">
			<div style="color:#444444;font-size:13px;width:100%;height:14px;"><strong>Tim C.</strong> Apple Inc.</div>
			<div style="font-size:11px;color:#b0b0b0;"><?=$date?> minutes ago</div>
		</div>
	</div>
	<div class="comment_content">
		<div class="wap_comment_content"><?=$k->comment?></div>
		<div style="float:left;width:50px;margin:-22px 0 0 -46px" id="arrow_comment_<?=$k->id?>" onclick="show_detail_comment(<?=$k->id?>);" class="up_arrow"></div>
		<div id="comment_detail_<?=$k->id?>" class="comment_detail">
			<p>Type set to Incident</p>
			<p>Subject set to '<?=$result->subject?>'</p>
			<p>Email send to '<?=$result->cc_email?>'</p><br/>
			<span style="font-size:11px;">
				<p>Client: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:12.0) Gecko/20100101 Firefox/12.0</p>
				<p>IP address: 115.66.148.168</p>
				<p>Location: Singapore, 00, Singapore</p>
			</span>
		</div>
	</div>
</div>
<?php }}?>