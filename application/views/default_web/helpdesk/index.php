<script type="text/javascript" src="/resources/addon/helpdesk.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		$('#main_cases').html( '<table class="table table-striped" id="example"></table>' );
		$.extend( $.fn.dataTableExt.oStdClasses, {
				    "sWrapper": "dataTables_wrapper form-inline"
				} );
		
		var result = '<?=$result?>';
		load_helpdesk_list(result);
		
		$("#arrow").click(function(){
			$("#input_data_fillter").slideToggle();
			
			if($('#arrow').attr('class') == 'down_arrow'){
				$('#arrow').removeClass('down_arrow');
				$('#arrow').addClass('up_arrow');
			}else{
				$('#arrow').removeClass('up_arrow');
				$('#arrow').addClass('down_arrow');
			}
		});
	});
</script>

<div id="breadcrumb">
	<div id="module_name" style="width:650px;">
		<ul>
			<li><a style="width:173px;" href="#" class="main">HELPDESK</a></li>
			<li class="arrow"></li>
			<li class="curent_page">List</li>
			<li><a href="#" id="favoriteIcon" class="on" title="Remove from favorites"></a></li>
		</ul>
	</div>
	<div id="top_button" style="width:210px;">
		<ul>
			<li><a href="<?=site_url('helpdesk')?>" class="btn btn-inverse" href="#">LIST</a></li>
			<li><a class="btn btn-inverse" href="#">MY CASE</a></li>
			<li><a href="<?=site_url('helpdesk/add')?>" class="btn btn-inverse" href="#">NEW</a></li>
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
		<div class="invoice_title"><span class="arrow_title"></span><span>Fillter</span></div>
		<div id="helpdesk_fillter">
			<ul>
				<li>
					<span class="fillter_label">Status</span>
					<span class="fillter_input">
						<select name="status" id="status" onchange="status_fillter();">
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
					<span class="fillter_label">Group \ Department</span>
					<span class="fillter_input">
						<select name="group" id="group" onchange="group_fillter();">
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
					<span class="fillter_label">Type</span>
					<span class="fillter_input">
						<select name="type" id="type" onchange="type_fillter();">
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
					<span class="fillter_label">Priority</span>
					<span class="fillter_input">
						<select name="priority" id="priority" onchange="priority_fillter();">
							<option value="">- - - Something - - -</option>
							 <?php if(!empty($priority)) {								
									foreach($priority as $k) {
							?>
							<option value="<?=$k->id?>"><?=$k->name?></option>
							<?php }}?>
						</select>
					</span>
				</li>
				<li style="width:27px; margin:26px 0 0 72px;"><div id="arrow" class="down_arrow"></div></li>
				<li style="width:60px;padding-top:23px;"><button href="#" class="btn btn-primary">SUBMIT</button></li>
			</ul>						
		</div>
		<div class="ClearLeft"></div>
		<div style="height:163px;" id="input_data_fillter">
			<ul>
				<li>
					<span class="input_data_label">Customer</span>
					<span class="fillter_input"><input class="inv-field" type="text"/></span>
				</li>
				<li>
					<span class="input_data_label">Assigned</span>
					<span class="fillter_input"><input class="inv-field" type="text"/></span>
				</li>
				<li>
					<span class="input_data_label">Subject</span>
					<span class="fillter_input"><input class="inv-field" type="text"/></span>
				</li>
				<li>
					<span class="input_data_label">Comments</span>
					<span class="fillter_input"><input class="inv-field" type="text"/></span>
				</li>
			</ul>
		</div>
	</div>
	
	<div id="invoice_cases">
		<div id="top_cases">
			<div class="invoice_title" style="width:550px;"><span class="arrow_title"></span><span>Cases</span></div>
		</div>
		
		<div id="main_cases">
			
		</div>
	</div>
</div>
