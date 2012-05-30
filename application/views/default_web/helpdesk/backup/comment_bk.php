<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />
<link href="/resources/template/default_web/helpdesk.css" media="screen" rel="stylesheet" type="text/css" />

<script type="text/javascript">
function showRequest(formData, jqForm, options) {
	var form = jqForm[0];
}

function show_form_change(){
	$('#form_show').hide();
	$('#form_change').show();
}

function show_form_show(){
	var subject = $('#subject').val();
	var assign = $('#assign').val();
	var cc_email = $('#cc_email').val();
	var id = $('#hiddenIdAdmincp').val();

	var url = '<?=site_url('helpdesk/ajaxChangeInfoHelpDesk');?>';

	$.post(url,{
			id : id,
			assign : assign,
			subject: subject,
			cc_email: cc_email,
		},function(data){
			$('#form_change').hide();
			$('#form_show').show();
			$('#form_show').html(data);
		}
	);

}

function submit_comment(){
	var comment = $('#a_helpdesk_comment_comment').val();
	var id = $('#hiddenIdAdmincp').val();
	var priority = $('#a_helpdesk_comment_priority').val();
	var group = $('#a_helpdesk_comment_group').val();
	var status = $('#a_helpdesk_comment_status').val();
	var type = $('#a_helpdesk_comment_type').val();

	if ($('#private').is(':checked')) {
		var pri = 1;
	} else {
		var pri = 0	;
	}
	if (comment == '') {
		alert('Please input comment !');
		return false;
	}
	var url = '<?=site_url('helpdesk/save_comment');?>';

	$.post(url,{
			id : id,
			comment: comment,
			pri: pri,
			group : group,
			status : status,
			type : type,
			priority : priority,

		},function(data){
			$('#ajax_comment_left').html(data);
			$('#a_helpdesk_comment_comment').attr('value','');
		}
	);
}
</script>

<div class="table" style="width:1096px;">

	<div class="head_helpdesk">
		<div id="content_left">HelpDesk Case #<?=$id?></div>

		<div id="content_right">
			<div id="time_curent"><?=date('F d Y g:i A',strtotime($result->created_stamp))?></div>
			<?php if (!empty($result->modified_stamp)) {
				$update_time = time() - strtotime($result->modified_stamp);
				$update_time = intval($update_time /1200);
			?>
			<div id="time_update">Update by Staff <?=$update_time ?> hours ago</div>
			<?php }?>
		</div>
	</div>
	<form id="frmManagement" action="" method="post" enctype="multipart/form-data">
	<div id="helpdesk_info">
		<ul id="form_show">
			<li><span class="helpdesk_info_span">Subject</span> : <?=$result->subject?></li>
			<li><span class="helpdesk_info_span">Creator</span> : <a href="#">Customer A</a></li>
			<li><span class="helpdesk_info_span">Assigned</span> : <?=$this->Helpdesk_CommentM->get_assigname($result->assign_id)?></li>
			<li><span class="helpdesk_info_span">CC</span> : <?=$result->cc_email?></li>
			<li><div onclick="return show_form_change();" class="btn btn-inverse" href="#">Change</div></li>
		</ul>

		<ul id="form_change" style="display:none">
			<li><span class="helpdesk_info_span">Subject</span> <span class="input_change">: <input value="<?php if(isset($result->subject)) { print $result->subject; }else{ print '';} ?>" type="text" name="subject" id="subject" /></span></li>
			<li><span class="helpdesk_info_span">Creator</span> : <a href="#">Customer A</a></li>

			<li><span class="helpdesk_info_span">Assigned</span> :
				<select  name="assign" id="assign">
					<option value="">something</option>
					<?php if (!empty($assign)) {
							if (!empty($result->assign_id)) {
								$value_assign = $result->assign_id;
							} else {
								$value_assign = 0;
							}
							foreach ($assign as $k) {
					?>
					<option <?=($value_assign == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->display_name?></option>
					<?php }}?>
				</select>
			</li>

			<li><span class="helpdesk_info_span">CC</span> <span class="input_change">: <input name="cc_email" value="<?php if(isset($result->cc_email)) { print $result->cc_email; }else{ print '';} ?>" type="text" id="cc_email" /></span></li>
			<li><div onclick="return show_form_show();" class="btn btn-inverse" href="#">Save</div></li>
		</ul>
	</div>

	<input type="hidden" value="<?=$id?>" name="hiddenIdAdmincp" id="hiddenIdAdmincp" />
	<div id="helpdesk_select">
		<ul>
			<li class="controls">
				<label class="control-label" for="select01">Group\Department</label>
				<select  disabled="disabled" name="a_helpdesk_comment_group" id="a_helpdesk_comment_group">
					<option value="">something</option>
					<?php if (!empty($group)) {
							if (!empty($result->group)) {
								$value_group = $result->group;
							} else {
								$value_group = 0;
							}
							foreach ($group as $k) {
					?>
					<option <?=($value_group == $k->access_gpsub_id?'selected=selected':'' )?> value="<?=$k->access_gpsub_id?>"><?=$k->access_gpsub_name?></option>
					<?php }}?>
				</select>
			</li>
			<li class="controls">
				<label class="control-label" for="select01">Status</label>
				<select  disabled="disabled" id="a_helpdesk_comment_status" name="a_helpdesk_comment_status">
					<option value="">something</option>
					<?php if (!empty($status)) {
							if (!empty($result->status)) {
								$value_status = $result->status;
							} else {
								$value_status = 0;
							}
							foreach ($status as $k) {
					?>
					<option <?=($value_status == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
					<?php }}?>
				</select>
			</li>
			<li class="controls">
				<label class="control-label" for="select01">Type</label>
				<select  disabled="disabled" id="a_helpdesk_comment_type" name="a_helpdesk_comment_type">
					<option value="">something</option>
					<?php if (!empty($type)) {
							if (!empty($result->type)) {
								$value_type = $result->type;
							} else {
								$value_type= 0;
							}
							foreach ($type as $k) {
					?>
					<option <?=($value_type == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
					<?php }}?>
				</select>
			</li>
			<li class="controls">
				<label class="control-label" for="select01">Priority</label>
				<select id="a_helpdesk_comment_priority" name="a_helpdesk_comment_priority">
					<option value="">something</option>
					<?php if (!empty($priority)) {
							if (!empty($result->priority)) {
								$value_pri = $result->priority;
							} else {
								$value_pri= 0;
							}
							foreach ($priority as $k) {
					?>
					<option <?=($value_pri == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
					<?php }}?>
				</select>
			</li>

			<li class="controls" style="width:100%">
				<textarea rows="3" id="a_helpdesk_comment_comment" value="" class="input-xlarge"></textarea>
			</li>
			<li class="controls" style="width:100%">
                            <label class="checkbox">
                                <input type="checkbox" value="" id="private">
                                Private comments (Only staff see this comments)
                            </label>
			</li>

                        <li class="controls" style="width:577px;height:27px;">
                            <!-- PLUpload-->
                            <h1 style="display:none;">Custom example</h1>
                            <p style="display:none;">Shows you how to use the core plupload API.</p>

                            <div id="container" >
                                <a class="btn" id="pickfiles" href="javascript:;">Attach files</a>
                                <a style="text-decoration:none;" id="uploadfiles" href="javascript:;">[Upload files]</a>
                                <div id="filelist" style="margin-top:5px;"></div>
                            </div>
			</li>

			<li class="controls" style="width:400px;">
				<span style="width:597px;float:left;">
					<?php if (!empty($file_attach)) {
							foreach ($file_attach as $k) {
					?>
					<a href="http://apple.8force.net/file/read/<?=$k->filename?>" class="btn" target="_blank">File Attach</a>
					<?php }}?>
				</span>
				<div onclick="return submit_comment();" class="btn" >Submit</div>
			</li>

			<li style="margin-top:10px;" class="controls">
				<a target="_blank" href="<?=site_url('helpdesk/out_put_pdf');?>/<?=$id?>" class="btn">Download PDF File</a>
			</li>
		</ul>
	</div>
</form>
	<div id="comment_info">
		<div id="comment_title"><span id="comment_h1">Comments</span>Comment only | Show all comment</div>
		<div id="comment_content">
			<div id="ajax_comment_left">
				<?php if (!empty($comment)) {
					foreach ($comment as $k) {
					$date = date('F d Y g:i A',strtotime($k->created_stamp));
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
			</div>

			<div id="comment_content_right">
				<div id="info_comment">
					<p>Type send to Incident</p>
					<p>Subject send to <?=$result->subject?></p>
					<p>Email send to Andy</p><br/>
					<p><a href="#">Show user agent info</a></p>
				</div>
			</div>
		</div>
	</div>
	<div class="clearAll"></div>
</div>