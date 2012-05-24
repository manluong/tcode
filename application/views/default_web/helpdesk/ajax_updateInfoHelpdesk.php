<ul>
<?php if (!empty($info)) {
		foreach ($info as $k) {
?>
	<li>
		<span class="input_data_label">Subject</span>
		<span class="fillter_input"><?=$k->subject?></span>
	</li>
	<li>
		<span class="input_data_label">Assigned</span>
		<span class="fillter_input"><?=$this->Helpdesk_CommentM->get_assigname($k->assign_id)?></span>
	</li>
	<li>
		<span class="input_data_label">CC (Email)</span>
		<span class="fillter_input"><?=$k->cc_email?></span>
	</li>
	<li id="helpdesk_change_info">Change Info</li>
<?php }}?>
</ul>
<script type="text/javascript">
$(document).ready(function(){
	$("#helpdesk_change_info").click(function(){
		$("#helpdesk_show_info").hide();
		$("#input_data_info").show();
	});
});
</script>