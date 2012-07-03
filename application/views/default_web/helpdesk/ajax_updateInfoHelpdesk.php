<?php if (!empty($info)) {
		foreach ($info as $k) {
?>
	<div class="dataVL">
		<div class="dataVT">Subject</div>
		<div class="dataVD"><?=$k->subject?></div>
	</div>
	<div class="dataVL">
		<div class="dataVT">Assigned</div>
		<div class="dataVD"><?=$this->Helpdesk_CommentM->get_assigname($k->assign_id)?></div>
	</div>
	<div class="dataVL">
		<div class="dataVT">CC (Email)</div>
		<div class="dataVD"><?=$k->cc_email?></div>
	</div>
	<div id="helpdesk_change_info">Change Info</div>
			
<?php }}?>

<script type="text/javascript">
$(document).ready(function(){
	$("#helpdesk_change_info").click(function(){
		$("#helpdesk_show_info").hide();
		$("#input_data_info").show();
	});
});
</script>