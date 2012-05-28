<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />

<script type="text/javascript">
$(document).ready(function(){
	$("#helpdesk_change_info").click(function(){
		$("#helpdesk_show_info").hide();
		$("#input_data_info").show();
	});
	
	$("#helpdesk_save_info").click(function(){
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
				$("#input_data_info").hide();
				$("#helpdesk_show_info").show();
				$('#helpdesk_show_info').html(data);
			}
		);
	});
});

function submit_comment(){
	var comment = $('#comment').val();
	var id = $('#hiddenIdAdmincp').val();
	var priority = $('#priority').val();
	var group = $('#group').val();
	var status = $('#status').val();
	var type = $('#type').val();

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
			$('#wap_comment_list').html(data);
			$('#comment').attr('value','');
		}
	);
}
	
function show_detail_comment(id){
	$("#comment_detail_"+id).slideToggle();
	
	if($('#arrow_comment_'+id).attr('class') == 'down_arrow'){
		$('#arrow_comment_'+id).removeClass('down_arrow');
		$('#arrow_comment_'+id).addClass('up_arrow');
	}else{
		$('#arrow_comment_'+id).removeClass('up_arrow');
		$('#arrow_comment_'+id).addClass('down_arrow');
	}
}
</script>

<div id="breadcrumb">
	<div id="module_name" style="width:650px;">
		<ul>
			<li><a style="width:173px;" href="#" class="main">HELPDESK</a></li>
			<li class="arrow"></li>
			<li class="curent_page">View</li>
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
	<div id="invoice_fillter">
		<div class="invoice_title"><span class="arrow_title"></span><span>CASE INFORMATION</span></div>
		<div id="helpdesk_show_info">
			<ul>
				<li>
					<span class="input_data_label">Subject</span>
					<span class="fillter_input"><?=$result['subject']?></span>
				</li>
				<li>
					<span class="input_data_label">Assigned</span>
					<span class="fillter_input"><?=$this->Helpdesk_CommentM->get_assigname($result['assign_id'])?></span>
				</li>
				<li>
					<span class="input_data_label">CC (Email)</span>
					<span class="fillter_input"><?=$result['cc_email']?></span>
				</li>
				<li id="helpdesk_change_info">Change Info</li>
			</ul>
		</div>
		<!-- Hidden id-->
		<input type="hidden" value="<?=$id?>" name="hiddenIdAdmincp" id="hiddenIdAdmincp" />
		<input type="hidden" value="<?=$comment_id?>" name="hiddenCommentId" id="hiddenCommentId" />
		<div id="input_data_info" style="display:none;">
			<ul>
				<li>
					<span style="font-weight:normal;" class="input_data_label">Subject</span>
					<span class="fillter_input">
						<input type="text" id="subject" class="inv-field" value="<?=(isset($result->subject)? print $result->subject : print '')?>"/>
					</span>
				</li>
				<li>
					<span style="font-weight:normal;" class="input_data_label">Assigned</span>
					<span class="fillter_input">
						<select  name="assign" id="assign">
							<option value="">something</option>
							<?php if (!empty($assign)) {
									if (!empty($result['assign_id'])) {
										$value_assign = $result['assign_id'];
									} else {
										$value_assign = 0;
									}
									foreach ($assign as $k) {
							?>
							<option <?=($value_assign == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->nickname?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li style="height:22px;">
					<span style="font-weight:normal;" class="input_data_label">CC (Email)</span>
					<span class="fillter_input"><input type="text" id="cc_email" class="inv-field" value="<?=(isset($result['cc_email'])? print $result['cc_email'] : print '')?>"/></span>
				</li>
				<li id="helpdesk_save_info" style="margin-top:-5px;">Save</li>
			</ul>
		</div>
	</div>
	
	<div id="invoice_fillter">
		<div class="invoice_title"><span class="arrow_title"></span><span>DETAILS</span></div>
		<div id="helpdesk_fillter">
			<ul>
				<li>
					<span class="fillter_label">Status</span>
					<span class="fillter_input">
						<select disabled="disabled" name="status" id="status">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($status)) {
									if(!empty($result['status'])) {
										$value_status = $result['status'];
									} else {
										$value_status = 0;
									}
									foreach($status as $k) {
							?>
							<option <?=($value_status == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Group \ Department</span>
					<span class="fillter_input">
						<select disabled="disabled" name="group" id="group">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($group)) {
									if(!empty($result['group'])) {
										$value_group = $result['group'];
									} else {
										$value_group = 0;
									}
									foreach($group as $k) {
							?>
							<option <?=($value_group == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Type</span>
					<span class="fillter_input">
						<select disabled="disabled" name="type" id="type">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($type)) {
									if(!empty($result['type'])) {
											$value_type = $result['type'];
									} else {
											$value_type= 0;
									}
									foreach($type as $k) {
							?>
							<option <?=($value_type == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Priority</span>
					<span class="fillter_input">
						<select name="priority" id="priority">
							<option value="">- - - Something - - -</option>
							 <?php if(!empty($priority)) {
									if(!empty($result['priority'])) {
											$value_pri = $result['priority'];
									} else {
											$value_pri= 0;
									}
									foreach($priority as $k) {
							?>
							<option <?=($value_pri == $k->id?'selected=selected':'' )?> value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
			</ul>						
		</div>
		<div id="helpdesk_comment">
			<textarea rows="3" id="comment" ></textarea>
			<div id="comment_submit">
				<div style="width:735px;float:left;">
					<span><input type="checkbox" id="private"/> Private Comments (Only Staff see this comment)</span>
					<span></span>
				</div>
				<div style="float:left;margin-top:2px;"><button onclick="submit_comment();" href="#" class="btn btn-primary">SUBMIT</button></div>
			</div>
		</div>
		<div id="plupload">
			<!-- PLUpload-->
			<h1 style="display:none;">Custom example</h1>
			<p style="display:none;">Shows you how to use the core plupload API.</p>
			<div id="container" >
				<div id="pickfiles" class="btn_attach"></div>
				<div id="uploadfiles" class="btn_plupload">Upload File</div>
				<div id="filelist" style="float:left ;margin:23px 0 0 -154px;"></div>
			</div>
		</div>
	</div>
	
	<div id="comment_list">
		<div class="invoice_title" style="height:20px;"><span class="arrow_title"></span><span>COMMENTS / EVENTS</span></div>
		<div id="helpdesk_fillter">
			<div id="wap_comment_list">
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
						<div style="float:left;width:50px;margin:-22px 0 0 -48px" id="arrow_comment_<?=$k->id?>" onclick="show_detail_comment(<?=$k->id?>);" class="up_arrow"></div>
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
		url : '/helpdesk/upload/'+ $('#hiddenCommentId').attr('value'),

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