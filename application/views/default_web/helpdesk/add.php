<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />

<script type="text/javascript">

function submit_insert_helpdesk(){
	var comment = $('#comment').val();
	var requester = $('#requester').val();
	var subject = $('#subject').val();
	var assigned = $('#assigned').val();
	var id = $('#hiddenIdAdmincp').attr('value');
	var cc_email = $('#cc_email').val();
	
	var group = $('#group').val();
	var status = $('#status').val();
	var type = $('#type').val();
	var priority = $('#priority').val();

	var url = '<?=site_url('helpdesk/save_insert_helpdesk');?>';

	$.post(url,{
			id : id,
			subject : subject,
			assign : assigned,
			cc_email : cc_email,
			group : group,
			status : status,
			type : type,
			priority : priority
		},function(data){
			if(comment != ''){
				submit_comment();
			}else{
				window.location='<?=site_url('helpdesk');?>';
			}

		}
	);
}

function submit_comment(){
	var comment = $('#comment').val();
	var id_helpdesk = $('#hiddenIdAdmincp').attr('value');
    var id_comment = $('#hiddenCommentID').attr('value');
	var group_comment = $('#group').val();
	var status_comment = $('#status').val();
	var type_comment = $('#type').val();
	var priority_comment = $('#priority').val();

	if ($('#private').is(':checked')) {
		var pri = 1;
	} else {
		var pri = 0	;
	}

	var url_comment = '<?=site_url('helpdesk/save_comment');?>';

	$.post(url_comment,{
			id : id_helpdesk,
            id_comment : id_comment,
			comment: comment,
			pri: pri,
			group : group_comment,
			status : status_comment,
			type : type_comment,
			priority : priority_comment,
		},function(data){
			if (data != '') {
				window.location='<?=site_url('helpdesk');?>';
			}
		}
	);
}
</script>

<div id="breadcrumb">
	<div id="module_name" style="width:650px;">
		<ul>
			<li><a style="width:173px;" href="#" class="main">HELPDESK</a></li>
			<li class="arrow"></li>
			<li class="curent_page">New</li>
			<li><a href="#" id="favoriteIcon" class="on" title="Remove from favorites"></a></li>
		</ul>
	</div>
	<div id="top_button" style="width:210px;">
		<ul>
			<li><button class="btn btn-inverse" href="#">LIST</button></li>
			<li><button class="btn btn-inverse" href="#">MY CASE</button></li>
			<li><button class="btn btn-inverse" href="#">NEW</button></li>
		</ul>
	</div> 
</div>

<div id="content_top">
	<div id="user_profile">
		<div id="user_avatar"><image alt="avatar" src="<?=site_url('resources/template/default_web/img/invoice')?>/invoice-avatar.jpg"/></div>
		<div id="user_info">
			<ul>
				<li class="user_sex">Mr.</li>
				<li class="user_name">Albert Z</li>
				<li class="user_position">Facebook Inc. <span style="font-weight:normal;">CEO</span></li>
			</ul>
		</div>
	</div>
	<div id="customer_list">
		<div class="btn-group">
		  <a href="#" class="btn btn-inverse">CUSTOMER</a>
		  <a href="#" data-toggle="dropdown" class="btn btn-inverse dropdown-toggle"><span class="caret"></span></a>
		  <ul class="dropdown-menu">
			<li><a href="#"><i class="icon-pencil"></i> Edit</a></li>
			<li><a href="#"><i class="icon-trash"></i> Delete</a></li>
			<li><a href="#"><i class="icon-ban-circle"></i> Ban</a></li>
		  </ul>
		</div>
	</div>
</div>

<div id="boxes">
	<div id="invoice_fillter" style="height:228px;">
		<div class="invoice_title"><span class="arrow_title"></span><span>NEW CASE INFORMATION</span></div>
		<div id="input_data_info">
			<ul>
				<li>
					<span class="input_data_label">Requester</span>
					<span class="fillter_input"><input id="requester" type="text" class="inv-field"/></span>
				</li>
				<li>
					<span class="input_data_label">Subject</span>
					<span class="fillter_input" id="new_helpdesk_subject"><input id="subject" type="text" class="inv-field"/></span>
				</li>
				<li>
					<span class="input_data_label">Assigned</span>
					<span class="fillter_input"><input id="assigned" type="text" class="inv-field"/></span>
				</li>
				<li style="height:22px;">
					<span class="input_data_label">CC (Email)</span>
					<span class="fillter_input"><input id="cc_email" type="text" class="inv-field"/></span>
				</li>
			</ul>
		</div>
	</div>
	
	<!-- Hidden ID-->
	<input type="hidden" value="<?=(!empty($helpdesk_id)? $helpdesk_id : '0')?>" name="hiddenIdAdmincp" id="hiddenIdAdmincp" />
	<input type="hidden" value="<?=(!empty($comment_id)? $comment_id : '0')?>" name="hiddenCommentID" id="hiddenCommentID" />
	
	<div id="invoice_fillter">
		<div class="invoice_title"><span class="arrow_title"></span><span>DETAILS</span></div>
		<div id="helpdesk_fillter">
			<ul>
				<li>
					<span class="fillter_label"><strong>Status</strong></span>
					<span class="fillter_input">
						<select name="status" id="status">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($status)) {
									foreach($status as $k) {
							?>
							<option value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				
				<li>
					<span style="width:130px;" class="fillter_label"><strong>Group \ Department</strong></span>
					<span class="fillter_input">
						<select name="group" id="group">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($group)) {
									foreach($group as $k) {
							?>
							<option value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				
				<li>
					<span class="fillter_label"><strong>Type</strong></span>
					<span class="fillter_input">
						<select name="type" id="type">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($type)) {
									foreach($type as $k) {
							?>
							<option value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				
				<li>
					<span class="fillter_label"><strong>Priority</strong></span>
					<span class="fillter_input">
						<select name="priority" id="priority">
							<option value="">- - - Something - - -</option>
							 <?php if(!empty($priority)) {								
									foreach($priority as $k) {
							?>
							<option value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
			</ul>						
		</div>
		<div id="helpdesk_comment">
			<textarea rows="3" id="comment" ></textarea>
			<div id="comment_submit">
				<div id="plupload" style="width:733px;margin-left:0px;">
					<!-- PLUpload-->
					<h1 style="display:none;">Custom example</h1>
					<p style="display:none;">Shows you how to use the core plupload API.</p>
					<div id="container" >
						<div id="pickfiles" class="btn_attach"></div>
						<div id="uploadfiles" class="btn_plupload">Upload File</div>
						<div id="filelist" style="float:left ;margin:23px 0 0 -154px;"></div>
					</div>
					
				</div>
				<div style="float:left;margin-top:2px;"><button href="#" onclick="submit_insert_helpdesk()" class="btn btn-primary">SUBMIT</button></div>
			</div>
		</div>
	</div>
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
		url : '/helpdesk/upload/'+$('#hiddenCommentID').attr('value'),

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