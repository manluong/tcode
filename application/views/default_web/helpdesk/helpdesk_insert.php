
<script type="text/javascript" src="/resources/addon/helpdesk.js"></script>		
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/helpdesk.css" />


<style>
.file-wrapper {
display: inline-block;
overflow: hidden;
position: relative;
}

.file-wrapper input {
    opacity: 0.01;
    height: 100%;
    position: absolute;
    left: -131px;
    top: 0;
	width:84px;
	cursor:pointer;
}
</style>

<script type="text/javascript">

function submit_insert_helpdesk(){

	var subject = $('#subject').val();
	var id = $('#hiddenIdAdmincp').attr('value');
	var assign = $('#assign').val();
	var cc_email = $('#cc_email').val();
	var group = $('#a_helpdesk_comment_group').val();
	var status = $('#a_helpdesk_comment_status').val();
	var type = $('#a_helpdesk_comment_type').val();
	var priority = $('#a_helpdesk_comment_priority').val();
	
	var url = 'helpdesk/save_insert_helpdesk/';
	$.post(url,{
			id : id,
			subject : subject,
			assign : assign,
			cc_email : cc_email,
			group : group,
			status : status,
			type : type,
			priority : priority,
		},function(data){
			window.location="helpdesk";
		}
	);
}
</script>

<div class="table" style="width:1096px;">
	<div class="head_helpdesk">
		<div id="content_left">HelpDesk Insert</div>
	</div>
	
	<form id="frmManagement" action="helpdesk/save_insert_helpdesk/" method="post" enctype="multipart/form-data">
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
			<li><span class="helpdesk_info_span">CC Mail</span> <span class="input_change">: <input name="cc_email" value="" type="text" id="cc_email" /></span></li>
		</ul>
	</div>
	
	<input type="hidden" value="<?=(!empty($helpdesk_id)? $helpdesk_id : '0')?>" name="hiddenIdAdmincp" id="hiddenIdAdmincp" />
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
			
			<li class="controls" style="width:577px;">
				<!-- PLUpload-->
				<h1 style="display:none;">Custom example</h1>
				<p style="display:none;">Shows you how to use the core plupload API.</p>

				<div id="container" >
					<a class="btn" id="pickfiles" href="javascript:;">Attach files</a> 
					<a style="text-decoration:none;" id="uploadfiles" href="javascript:;">[Upload files]</a>
					<div id="filelist" style="margin-top:5px;"></div>
				</div>
			</li>
			
			<li class="controls" >
				<div onclick="submit_insert_helpdesk()" class="btn">Submit</div>
			</li>
		</ul>
	</div>
</form>
	
	<div class="clearAll"></div>
</div>

<script type="text/javascript">

// Custom example logic
function getid(id) {
	return document.getElementById(id);	
}

var uploader = new plupload.Uploader({
	runtimes : 'gears,html5,flash,silverlight,browserplus',
	browse_button : 'pickfiles',
	container: 'container',
	max_file_size : '10mb',
	url : '/helpdesk/upload/'+$('#hiddenIdAdmincp').attr('value'),

	filters : [
		{title : "Image files", extensions : "jpg,gif,png"},
		{title : "Zip files", extensions : "zip"}
	]
});

uploader.bind('FilesAdded', function(up, files) {
	for (var i in files) {
		getid('filelist').innerHTML += '<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b></div>';
	}
});

uploader.bind('UploadProgress', function(up, file) {
	getid(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
});

getid('uploadfiles').onclick = function() {
	uploader.start();
	return false;
};


uploader.init();

</script>