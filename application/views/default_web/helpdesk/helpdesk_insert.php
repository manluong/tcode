<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/main.css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/jquery.fancybox-1.3.4.css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/style_caledar.css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/styles.css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/helpdesk.css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/base.css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap.min.css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/bootstrap-responsive.min.css" />


<script type="text/javascript">
function showRequest(formData, jqForm, options) {
	var form = jqForm[0];
}

function submit_insert_helpdesk(){
	var subject = $('#subject').val();
	var assign = $('#assign').val();
	var cc_email = $('#cc_email').val();
	var group = $('#a_helpdesk_comment_group').val();
	var status = $('#a_helpdesk_comment_status').val();
	var type = $('#a_helpdesk_comment_type').val();
	var priority = $('#a_helpdesk_comment_priority').val();
	
	var url = 'helpdesk/save_insert_helpdesk/';
	$.post(url,{
			subject : subject,
			assign: assign,
			cc_email: cc_email,
			group:group,
			status:status,
			type:type,
			priority:priority,
		},function(data){
			if(data != ''){
				helpdesk_ajax_content('/helpdesk/sendjson_list', 'helpdesk_list');
			}else{
				alert('Insert unsucessfull !');
			}
		}
	);
	
}
</script>

<div class="table" style="width:1096px;">
	<div class="head_helpdesk">
		<div id="content_left">HelpDesk Insert</div>
		
		<div id="content_right">
			<div id="time_curent"><?=date('F d Y g:i A',strtotime($result->stamp))?></div>
			<?php if(!empty($result->stamp_update)){
				$update_time = time() - strtotime($result->stamp_update);
				$update_time = intval($update_time /1200);
			?>
			<div id="time_update">Update by Staff <?=$update_time ?> hours ago</div>
			<?php }?>
		</div>
	</div>
	<form id="frmManagement" action="<?=PATH_URL.'admincp/'.$module.'/save/'?>" method="post" enctype="multipart/form-data">
	<div id="helpdesk_info" style="height:200px;">
		<ul id="form_change" >
			<li><span class="helpdesk_info_span">Subject</span> <span class="input_change">: <input value="" type="text" name="subject" id="subject" /></span></li>
			<li><span class="helpdesk_info_span">Creator</span> : <a href="#">Customer A</a></li>
			
			<li><span class="helpdesk_info_span">Assigned</span> : 
				<select  name="assign" id="assign">
					<option value="">something</option>
					<?php if(!empty($assign)){
							foreach($assign as $k){
					?>
					<option value="<?=$k->id?>"><?=$k->nickname?></option>
					<?php }}?>
				</select>
			</li>
			
			<li><span class="helpdesk_info_span">CC</span> <span class="input_change">: <input name="cc_email" value="" type="text" id="cc_email" /></span></li>
		</ul>
	</div>
	
	<input type="hidden" value="<?=$id?>" name="hiddenIdAdmincp" id="hiddenIdAdmincp" />
	<div id="helpdesk_select">
		<ul>
			<li class="controls">
				<label class="control-label" for="select01">Group\Department</label>
				<select  name="a_helpdesk_comment_group" id="a_helpdesk_comment_group">
					<option value="">something</option>
					<?php if(!empty($group)){
							if(!empty($result->group)){
								$value_group = $result->group;
							}else{
								$value_group = 0;
							}
							foreach($group as $k){
					?>
					<option <?=($value_group == $k->access_gpsub_id?'selected=selected':'' )?> value="<?=$k->access_gpsub_id?>"><?=$k->access_gpsub_name?></option>
					<?php }}?>
				</select>
			</li>
			<li class="controls">
				<label class="control-label" for="select01">Status</label>
				<select  id="a_helpdesk_comment_status" name="a_helpdesk_comment_status">
					<option value="">something</option>
					<?php if(!empty($status)){
							if(!empty($result->status)){
								$value_status = $result->status;
							}else{
								$value_status = 0;
							}
							foreach($status as $k){
					?>
					<option <?=($value_status == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
					<?php }}?>
				</select>
			</li>
			<li class="controls">
				<label class="control-label" for="select01">Type</label>
				<select  id="a_helpdesk_comment_type" name="a_helpdesk_comment_type">
					<option value="">something</option>
					<?php if(!empty($type)){
							if(!empty($result->type)){
								$value_type = $result->type;
							}else{
								$value_type= 0;
							}
							foreach($type as $k){
					?>
					<option <?=($value_type == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
					<?php }}?>
				</select>
			</li>
			<li class="controls">
				<label class="control-label" for="select01">Priority</label>
				<select id="a_helpdesk_comment_priority" name="a_helpdesk_comment_priority">
					<option value="">something</option>
					<?php if(!empty($priority)){
							if(!empty($result->priority)){
								$value_pri = $result->priority;
							}else{
								$value_pri= 0;
							}
							foreach($priority as $k){
					?>
					<option <?=($value_pri == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
					<?php }}?>
				</select>
			</li>
			
			<li class="controls">
				<span style="width:577px;float:left;"><button  class="btn" type="submit">Attach File</button></span>
			</li>
			
			<li class="controls" >
				<div onclick="return submit_insert_helpdesk();" class="btn" >Submit</div>
			</li>
		</ul>
	</div>
</form>
	
	<div class="clearAll"></div>
</div>