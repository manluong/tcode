<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/contact.css" />
<script type="text/javascript">
function ajax_edit(id){
	$.pjax({
		url: '/card/edit/'+id,
		container: '#contact_info_detail',
		timeout: 10000
	});
}
</script>

<div id="breadcrumb">
	<div id="module_name" style="width:650px;">
		<ul>
			<li><a style="width:165px;" href="#" class="main">CONTACT</a></li>
			<li class="arrow"></li>
			<li class="curent_page">Albert Z</li>
			<li class="arrow"></li>
			<li class="curent_page">Contact Information</li>
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

<div id="payment_boxes">
	<div class="payment_detail">
		<div class="payment_label">INVOICE</div>
		<div class="payment_price">
			<ul>
				<li style="font-weight:bold;">2 DUE</li>
				<li class="red_price">$345.60</li>
				<li class="total">ALL INVOICES $1569.0</li>
			</ul>
		</div>
	</div>
	
	<div class="payment_detail payment_active">
		<div class="payment_label">INVOICE</div>
		<div class="payment_price">
			<ul>
				<li style="font-weight:bold;">2 DUE</li>
				<li class="red_price">$345.60</li>
				<li class="total">ALL INVOICES $1569.0</li>
			</ul>
		</div>
	</div>
	
	<div class="payment_detail" style="margin-right:0px;">
		<div class="payment_label">INVOICE</div>
		<div class="payment_price">
			<ul>
				<li style="font-weight:bold;">2 DUE</li>
				<li class="green_price">$345.60</li>
				<li class="total">ALL INVOICES $1569.0</li>
			</ul>
		</div>
	</div>
	
	<div class="payment_detail">
		<div class="payment_label" style="text-transform:uppercase;">INVOICE</div>
		<div class="payment_price">
			<ul>
				<li style="font-weight:bold;">2 DUE</li>
				<li class="green_price">$345.60</li>
				<li class="total">ALL INVOICES $1569.0</li>
			</ul>
		</div>
	</div>
</div>

<div id="boxes">
	<div id="contact_info">
		<div style="width:100%;" class="invoice_title">
			<span class="arrow_title"></span>
			<span style="text-transform:uppercase;">Basic information</span>
		</div>
		<div id="contact_info_detail">
			<ul>
				<li>
					<span class="input_data_label">First Name</span>
					<span class="fillter_input"><?=$data['first_name']?></span>
				</li>
				<li>
					<span class="input_data_label">Last Name</span>
					<span class="fillter_input"><?=$data['last_name']?></span>
				</li>
				<li>
					<span class="input_data_label">Display Name</span>
					<span class="fillter_input"><?=$data['display_name'] ?></span>
				</li>
				<!-- CARD EMAIL -->
				<?php if(!empty($card_email)){
						foreach($card_email as $k){
				?>
				<li>
					<span class="input_data_label" style="<?=($k->is_default == 1?'color:red':'')?>">Email</span>
					<span class="fillter_input" style="<?=($k->is_default == 1?'color:red':'')?>"><?=$k->email?></span>
				</li>
				<?php }}?>
				<!-- CARD ADDRESS -->
				<?php if(!empty($card_address)) {
						foreach($card_address as $k) {
				?>
				<li>
					<span class="input_data_label" style="<?=($k->is_default == 1?'color:red':'')?>">Address</span>
					<span class="fillter_input" style="<?=($k->is_default == 1?'color:red':'')?>"><?=$k->line_1.' | '.$k->line_2?></span>
					<span class="fillter_input" style="<?=($k->is_default == 1?'color:red':'')?>"><?=' City: '.$k->city.' - State: '.$k->state.' - Postal Code: '.$k->postal?></span>
				</li>
				<?php }}?>
				<!-- CARD SOCIAL -->
				<?php if(!empty($card_social)) {
						foreach($card_social as $k) {
						$type = $k->type;
						$typename = '';
						switch($type){
							case 0:
								$typename = 'Other';
								break;
							case 1:
								$typename = 'Facebook';
								break;
							case 2:
								$typename = 'Linkedin';
								break;
							case 3:
								$typename = 'Msn';
								break;
							case 4:
								$typename = 'Yahoo';
								break;
							case 5:
								$typename = 'Skype';
								break;
							case 6:
								$typename = 'qq';
								break;
							case 7:
								$typename = 'Google';
								break;
							case 8:
								$typename = 'Website';
								break;
						}
				?>
				<li>
					<span class="input_data_label"><?=$typename?></span>
					<span class="fillter_input"><?=$k->name_id?></span>
				</li>
				<?php }}?>
				<!-- CARD PHONE -->
				<?php if(!empty($card_phone)){
						foreach($card_phone as $k){
				?>
				<li>
					<span class="input_data_label" style="<?=($k->is_default == 1?'color:red':'')?>">Phone</span>
					<span class="fillter_input" style="<?=($k->is_default == 1?'color:red':'')?>"><?=$k->extension.' - '.$k->area.' - '.$k->country.' - '.$k->number?></span>
				</li>
				<?php }}?>
				<li style="margin:10px 0 0 95px;">
					<button onclick="ajax_edit(<?=$data['id']?>);" style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">EDIT DETAILS</button>
				</li>
				<li style="margin-top:10px;">
					<span class="input_data_label">Birthday</span>
					<span class="fillter_input" style="font-size:13px; font-weight:bold;">This is the notes for this guy. bah bah bah. Click to edit this notes.</span>
				</li>
			</ul>
		</div>
	</div>
	
	<div id="customer_detail">
		<div style="width:100%;" class="invoice_title">
			<span class="arrow_title"></span>
			<span style="text-transform:uppercase;">CUSTOMER DETAILS</span>
		</div>
		<ul>
			<li>
				<span class="input_data_label">Skype</span>
				<span class="fillter_input">mark@facebook.com</span>
			</li>
			<li>
				<span class="input_data_label">Facebook</span>
				<span class="fillter_input">mark@facebook.com</span>
			</li>
			<li>
				<span class="input_data_label">Gender</span>
				<span class="fillter_input">mark@facebook.com</span>
			</li>
			<li>
				<span class="input_data_label">Birthday</span>
				<span class="fillter_input">mark@facebook.com</span>
			</li>
			<li style="margin:10px 0 0 95px;">
				<button style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">EDIT DETAILS</button>
			</li>
		</ul>
	</div>
	
</div>