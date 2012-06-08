<?php if (!empty($comment)) {
		for($i = 0; $i < count($comment) ; $i++){
		$pri = $comment[$i]['priority'] ;
		$date = strtotime($comment[$i]['created_stamp']);
		$minute = (int)((time() - $date)/60) ;
		
		$day = 0;
		$hour = 0;
		if($minute > 60){
			$hour = (int)($minute/60);
			$minute = ($minute%60);
		}
		if($hour > 24){
			$day = (int)($hour/24);
			$hour = ($hour%24);
		}
		
?>
<div class="comment_info">
	<div class="comment_info_user">
		<div class="comment_user_avatar"><image src="<?=site_url('resources/template/default_web/img/helpdesk')?>/comment_avatar.png"/></div>
		<div class="comment_user_name">
			<div style="color:#444444;font-size:13px;width:100%;height:14px;"><strong>Tim C.</strong> Apple Inc.</div>
			<div style="font-size:11px;color:#b0b0b0;"><?=($day>0 ? $day.' day ' : '')?><?=($hour>0 ? $hour.' hour ' : '')?><?=$minute.' minute'?> ago</div>
		</div>
	</div>
	<div class="comment_content">
		<div class="wap_comment_content"><?=$comment[$i]['comment']?></div>
		<div style="float:left;width:50px;margin:-22px 0 0 -48px" id="arrow_comment_<?=$comment[$i]['id']?>" onclick="show_detail_comment(<?=$comment[$i]['id']?>);" class="up_arrow"></div>
		<div id="comment_detail_<?=$comment[$i]['id']?>" class="comment_detail">
			<?php if(!empty($pri)){?>
			<p>Priority set to <?=$this->Helpdesk_CommentM->get_priority_type($pri)?></p>
			<?php }?>
			<p>Subject set to '<?=$result['subject']?>'</p>
			<p>Email send to '<?=$result['cc_email']?>'</p>
			<?php 
				$file = $this->Helpdesk_CommentM->get_comment_files($comment[$i]['id']);
				if(!empty($file)){
					foreach($file as $f){
			?>
			<p><?= '<a href="/file/read/'.$f->filename.'">View Document</a>'; ?></p>
			<?php }}?>
			<br/><span style="font-size:11px;">
				<p>Client: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:12.0) Gecko/20100101 Firefox/12.0</p>
				<p>IP address: 115.66.148.168</p>
				<p>Location: Singapore, 00, Singapore</p>
			</span>
		</div>
	</div>
</div>
<?php }}?>