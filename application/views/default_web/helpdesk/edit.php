<script>
$(document).ready(function(){
	$("#helpdesk_change_info").click(function(){
		$("#helpdesk_show_info").hide();
		$("#input_data_info").show();
	});
	
	$("#helpdesk_save_info").click(function(){
		$("#input_data_info").hide();
		$("#helpdesk_show_info").show();
	});
	
	//Show info comment
	$("#comment_arrow").click(function(){
		$("#comment_detail").slideToggle();
		
		if($('#comment_arrow').attr('class') == 'down_arrow'){
			$('#comment_arrow').removeClass('down_arrow');
			$('#comment_arrow').addClass('up_arrow');
		}else{
			$('#comment_arrow').removeClass('up_arrow');
			$('#comment_arrow').addClass('down_arrow');
		}
	});
});

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
					<span class="fillter_input">This is the subject of the ticket!</span>
				</li>
				<li>
					<span class="input_data_label">Assigned</span>
					<span class="fillter_input">John</span>
				</li>
				<li>
					<span class="input_data_label">CC (Email)</span>
					<span class="fillter_input">ea@123.com</span>
				</li>
				<li id="helpdesk_change_info">Change Info</li>
			</ul>
		</div>
		
		<div id="input_data_info" style="display:none;">
			<ul>
				<li>
					<span style="font-weight:normal;" class="input_data_label">Subject</span>
					<span class="fillter_input"><input type="text" class="inv-field"/></span>
				</li>
				<li>
					<span style="font-weight:normal;" class="input_data_label">Assigned</span>
					<span class="fillter_input"><input type="text" class="inv-field"/></span>
				</li>
				<li style="height:22px;">
					<span style="font-weight:normal;" class="input_data_label">CC (Email)</span>
					<span class="fillter_input"><input type="text" class="inv-field"/></span>
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
						<select name="date_fillter" id="date_fillter">
							<option>- - - Select - - -</option>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Group \ Department</span>
					<span class="fillter_input">
						<select name="date_fillter" id="date_fillter">
							<option>- - - Select - - -</option>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Type</span>
					<span class="fillter_input">
						<select name="date_fillter" id="date_fillter">
							<option>- - - Select - - -</option>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Priority</span>
					<span class="fillter_input">
						<select name="date_fillter" id="date_fillter">
							<option>- - - Select - - -</option>
						</select>
					</span>
				</li>
			</ul>						
		</div>
		<div id="helpdesk_comment">
			<textarea rows="3" id="comment" ></textarea>
			<div id="comment_submit">
				<div style="width:735px;float:left;">
					<span><input type="checkbox" /> Private Comments (Only Staff see this comment)</span>
					<span></span>
				</div>
				<div style="float:left;margin-top:2px;"><button href="#" class="btn btn-primary">SUBMIT</button></div>
			</div>
		</div>
		<div id="plupload">
			<div class="btn_attach"></div>
			<div class="btn_plupload">Upload File</div>
		</div>
	</div>
	
	<div id="comment_list">
		<div class="invoice_title"><span class="arrow_title"></span><span>COMMENTS / EVENTS</span></div>
		<div id="helpdesk_fillter">
		
			<div class="comment_info">
				<div class="comment_info_user">
					<div class="comment_user_avatar"><image src="<?=site_url('resources/template/default_web/img/helpdesk')?>/comment_avatar.png"/></div>
					<div class="comment_user_name">
						<div style="color:#444444;font-size:13px;width:100%;height:14px;"><strong>Tim C.</strong> Apple Inc.</div>
						<div style="font-size:11px;color:#b0b0b0;">12 minutes ago</div>
					</div>
				</div>
				<div class="comment_content">
					<div class="wap_comment_content">
						<p>This is comments for this ticket. This is comments for this ticket. This is comments for this ticket.</p>
						<p>This is comments for this ticket. This is comments for this ticket. This is comments for this ticket. </p>
						<br/>
						<p>This is comments for this ticket. This is comments for this ticket.</p>
						<p>This is comments for this ticket. This is comments for this ticket. This is comments for this ticket.  </p>
					</div>
					<div id="comment_arrow" class="up_arrow"></div>
					<div id="comment_detail" class="comment_detail">
						<p>Type set to Incident</p>
						<p>Subject set to 'Help Me!'</p>
						<p>Email send to 'Andy'</p><br/>
						<span style="font-size:11px;">
							<p>Client: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:12.0) Gecko/20100101 Firefox/12.0</p>
							<p>IP address: 115.66.148.168</p>
							<p>Location: Singapore, 00, Singapore</p>
						</span>
					</div>
				</div>
			</div>
			
			<div class="comment_info">
				<div class="comment_info_user">
					<div class="comment_user_avatar"><image src="<?=site_url('resources/template/default_web/img/helpdesk')?>/comment_avatar.png"/></div>
					<div class="comment_user_name">
						<div style="color:#444444;font-size:13px;width:100%;height:14px;"><strong>Tim C.</strong> Apple Inc.</div>
						<div style="font-size:11px;color:#b0b0b0;">12 minutes ago</div>
					</div>
				</div>
				<div class="comment_content">
					<div class="wap_comment_content">
						<p>This is comments for this ticket. This is comments for this ticket. This is comments for this ticket.</p>
						<p>This is comments for this ticket. This is comments for this ticket. This is comments for this ticket. </p>
						<br/>
						<p>This is comments for this ticket. This is comments for this ticket.</p>
						<p>This is comments for this ticket. This is comments for this ticket. This is comments for this ticket.  </p>
					</div>
					<div id="comment_arrow" class="up_arrow"></div>
					<div id="comment_detail" class="comment_detail">
						<p>Type set to Incident</p>
						<p>Subject set to 'Help Me!'</p>
						<p>Email send to 'Andy'</p><br/>
						<span style="font-size:11px;">
							<p>Client: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:12.0) Gecko/20100101 Firefox/12.0</p>
							<p>IP address: 115.66.148.168</p>
							<p>Location: Singapore, 00, Singapore</p>
						</span>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>