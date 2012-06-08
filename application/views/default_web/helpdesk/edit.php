<script type="text/javascript" src="/resources/addon/helpdesk.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />

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
			<li><a href="<?=site_url('helpdesk')?>" class="btn btn-inverse" href="#">LIST</a></li>
			<li><a onclick="helpdesk_fillter(<?=$card_id?>);" class="btn btn-inverse" href="#">MY CASE</a></li>
			<li><a href="<?=site_url('helpdesk/add')?>" class="btn btn-inverse" href="#">NEW</a></li>
		</ul>
	</div>
</div>

<div id="content_top">
	<?//=$quickjump?>
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
		<!-- Hidden Helpdesk Id-->
		<input type="hidden" value="<?=$id?>" name="hiddenIdAdmincp" id="hiddenIdAdmincp" />
		<!-- End Hidden Helpdesk Id-->
		<div id="input_data_info" style="display:none;">
			<ul>
				<li>
					<span style="font-weight:normal;" class="input_data_label">Subject</span>
					<span class="fillter_input">
						<input type="text" id="subject" class="inv-field" value="<?=(isset($result['subject']) ? $result['subject'] : '')?>"/>
					</span>
				</li>
				<li>
					<span style="font-weight:normal;" class="input_data_label">Assigned</span>
					<span class="fillter_input">
						<input type="hidden" id="assign_id" name="assign_id" />
						<input type="text" id="assign_name" name="assign_name" class="inv-field" />
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
						<select name="status" id="status">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($status)) {
									if(!empty($result['status'])) {
										$value_status = $result['status'];
									} else {
										$value_status = 0;
									}
									for($i = 0; $i < count($status); $i++){
							?>
							<option <?=($value_status == $status[$i]['id']?'selected=selected':'' )?> value="<?=$status[$i]['id']?>"><?=$status[$i]['name']?></option>
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
									for($i = 0; $i < count($group); $i++){
							?>
							<option <?=($value_group == $group[$i]['id']?'selected=selected':'' )?> value="<?=$group[$i]['id']?>"><?=$group[$i]['name']?></option>
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
									for($i = 0; $i < count($type); $i++){
							?>
							<option <?=($value_type == $type[$i]['id']?'selected=selected':'' )?> value="<?=$type[$i]['id']?>"><?=$type[$i]['name']?></option>
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
									for($i = 0; $i < count($priority); $i++){
							?>
							<option <?=($value_pri == $priority[$i]['id']?'selected=selected':'' )?> value="<?=$priority[$i]['id']?>"><?=$priority[$i]['name']?></option>
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
				<div style="display:none;" id="uploadfiles"></div>
				<div id="filelist" style="float:left ;margin:23px 0 0 0;"></div>
			</div>
		</div>
	</div>

	<div id="comment_list">
		<div class="invoice_title" style="height:20px;"><span class="arrow_title"></span><span>COMMENTS / EVENTS</span></div>
		<div id="helpdesk_fillter">
			<div id="wap_comment_list">
				<!-- Hidden Comment Id-->
				<input type="hidden" value="<?=$comment_id?>" name="hiddenCommentId" id="hiddenCommentId" />
				<!-- End Hidden Comment Id-->
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
			{title : "Zip files", extensions : "zip"},
			{title : "Pdf files", extensions : "pdf"},
			{title : "Doc files", extensions : "doc,docx"}
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
	jQuery('input[type="file"]').change(function(){
	   uploader.start();
	});
</script>