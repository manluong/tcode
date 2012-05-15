<?php if(!empty($comment)){
		foreach($comment as $k){
		$date = date('F d Y g:iA',strtotime($k->created_stamp));
?>
<div id="comment_content_left">
	<div id="wap_comment_left">
		<div id="wap_icon_mail"><image src="/resources/template/<?=get_template()?>/img/mail.png" /></div>
		<div id="comment_name">Andy<br/><a href="#">Company A</a></div>
		<div id="comment_time"><?=$date?></div>
	</div>
	<div id="comment_content"><?=$k->comment?></div>
</div>
<?php }}?>