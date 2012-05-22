
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

<div id="boxes">
	<div id="invoice_fillter" style="height:228px;">
		<div class="invoice_title"><span class="arrow_title"></span><span>NEW CASE INFORMATION</span></div>
		<div id="input_data_info">
			<ul>
				<li>
					<span class="input_data_label">Requester</span>
					<span class="fillter_input"><input name="requester" type="text" class="inv-field"/></span>
				</li>
				<li>
					<span class="input_data_label">Subject</span>
					<span class="fillter_input" id="new_helpdesk_subject"><input name="subject" type="text" class="inv-field"/></span>
				</li>
				<li>
					<span class="input_data_label">Assigned</span>
					<span class="fillter_input"><input name="assigned" type="text" class="inv-field"/></span>
				</li>
				<li style="height:22px;">
					<span class="input_data_label">CC (Email)</span>
					<span class="fillter_input"><input name="cc_mail" type="text" class="inv-field"/></span>
				</li>
			</ul>
		</div>
	</div>
	
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
									if(!empty($result->status)) {
										$value_status = $result->status;
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
					<span style="width:130px;" class="fillter_label"><strong>Group \ Department</strong></span>
					<span class="fillter_input">
						<select name="group" id="group">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($group)) {
									if(!empty($result->group)) {
										$value_group = $result->group;
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
					<span class="fillter_label"><strong>Type</strong></span>
					<span class="fillter_input">
						<select name="type" id="type">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($type)) {
									if(!empty($result->type)) {
											$value_type = $result->type;
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
					<span class="fillter_label"><strong>Priority</strong></span>
					<span class="fillter_input">
						<select name="priority" id="priority">
							<option value="">- - - Something - - -</option>
							 <?php if(!empty($priority)) {
									if(!empty($result->priority)) {
											$value_pri = $result->priority;
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
				<div id="plupload" style="width:733px;margin-left:0px;">
					<div class="btn_attach"></div>
					<div class="btn_plupload">Upload File</div>
				</div>
				<div style="float:left;margin-top:2px;"><button href="#" class="btn btn-primary">SUBMIT</button></div>
			</div>
		</div>
	</div>
</div>