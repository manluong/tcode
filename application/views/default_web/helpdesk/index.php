<script type="text/javascript" src="/resources/addon/helpdesk.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#main_cases').html( '<table class="table table-striped" id="example"></table>' );
		$.extend( $.fn.dataTableExt.oStdClasses, {
				    "sWrapper": "dataTables_wrapper form-inline"
				} );
		helpdesk_fillter(<?=(!empty($helpdesk_card_id)?$helpdesk_card_id:'')?>);
	});
</script>

<div id="quickjump">
	<div class="quickjump_select">
		<div class="btn-group">
		  <button class="btn btn-inverse">CUSTOMER</button>
		  <button data-toggle="dropdown" class="btn btn-inverse dropdown-toggle">
			<span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu">
			<li><a href="#"><i class="icon-pencil"></i> Edit</a></li>
			<li><a href="#"><i class="icon-trash"></i> Delete</a></li>
			<li><a href="#"><i class="icon-ban-circle"></i> Ban</a></li>
		  </ul>
		</div>	
	</div>
	<div class="quickjump_card">
		<div class="quickjump_avatar avatar rounded20 fl mr5">
			<image class="rounded20" width="40" height="40" alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
		</div>
		<div class="quickjump_info">
			<ul>
				<li class="quickjump_title">Mr.</li>
				<li class="quickjump_name">Albert Z</li>
				<li class="quickjump_co">Facebook Inc. 
					<span class="quickjump_position">CEO</span>
				</li>
			</ul>
		</div>
	</div>
</div>

<div id="boxes">
	
	<div id="invoice_fillter">
		<div class="subtitle">
			<span class="subtitle-arrow"></span>Fillter
		</div>
		
		<div id="helpdesk_fillter">
			<ul>
				<li>
					<span class="fillter_label">Status</span>
					<span class="fillter_input">
						<select name="status" id="status" onchange="helpdesk_fillter();">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($status)) {
									for($i = 0; $i < count($status); $i++){
							?>
							<option value="<?=$status[$i]['id']?>"><?=$status[$i]['name']?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Group \ Department</span>
					<span class="fillter_input">
						<select name="group" id="group" onchange="helpdesk_fillter();">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($group)) {
									for($i = 0; $i < count($group); $i++){
							?>
							<option value="<?=$group[$i]['id']?>"><?=$group[$i]['name']?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Type</span>
					<span class="fillter_input">
						<select name="type" id="type" onchange="helpdesk_fillter();">
							<option value="">- - - Something - - -</option>
							<?php if(!empty($type)) {
									for($i = 0; $i < count($type); $i++){
							?>
							<option value="<?=$type[$i]['id']?>"><?=$type[$i]['name']?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">Priority</span>
					<span class="fillter_input">
						<select name="priority" id="priority" onchange="helpdesk_fillter();">
							<option value="">- - - Something - - -</option>
							 <?php if(!empty($priority)) {
									for($i = 0; $i < count($priority); $i++){
							?>
							<option value="<?=$priority[$i]['id']?>"><?=$priority[$i]['name']?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li style="width:27px; margin:26px 0 0 72px;"><div id="arrow" class="down_arrow"></div></li>
			</ul>
		</div>
		<div class="ClearLeft"></div>
		<div style="height:193px;" id="input_data_fillter">
			<div class="dataFL">
				<div class="dataFT">Requester</div>
				<div class="dataVD">
					<input type="hidden" id="customer_id" name="customer_id" />
					<input type="text" id="customer_name" name="customer_name" class="inv-field" />
				</div>
			</div>
			<div class="dataFL">
				<div class="dataFT">Assigned</div>
				<div class="dataVD">
					<input type="hidden" id="assign_id" name="assign_id" />
					<input type="text" id="assign_name" name="assign_name" class="inv-field" />
				</div>
			</div>
			<div class="dataFL">
				<div class="dataFT">Subject</div>
				<div class="dataVD">
					<input id="subject" class="inv-field" type="text"/>
				</div>
			</div>

			<div class="dataFL">
				<div class="dataFT">Comments</div>
				<div class="dataVD">
					<input type="hidden" id="helpdesk_id" name="helpdesk_id" />
					<input type="text" id="comment_content" name="comment_content" class="inv-field" />
				</div>
			</div>

			<div class="dataFL" style="padding-left:182px;">
				<span class="">
					<button onclick="helpdesk_fillter_all();" href="javascript:void(0)" class="btn btn-primary">SUBMIT</button>
				</span>
			</div>
		</div>
	</div>

	<div id="invoice_cases" style="margin-top:20px;">
		<div id="top_cases" style="position:relative;">
			<div id="helpdesk_loadding"></div>
			<div class="subtitle-noshadow">
				<span class="subtitle-arrow"></span>Cases
			</div>
		</div>
		<div id="main_cases"></div>
	</div>
</div>
