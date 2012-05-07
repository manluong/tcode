<?php if(!empty($info)){
		foreach($info as $k){
?>
<li><span class="helpdesk_info_span">Subject</span> : <?=$k->subject?></li>
<li><span class="helpdesk_info_span">Creator</span> : <a href="#">Customer A</a></li>
<li><span class="helpdesk_info_span">Assigned</span> : <?=$this->DS_Helpdesk_Nodataset->getAssignName($k->assign_id)?></li>
<li><span class="helpdesk_info_span">CC</span> : <?=$k->cc_email?></li>
<li><div onclick="return show_form_change();" class="btn btn-inverse" href="#">Change</div></li>
<?php }}?>