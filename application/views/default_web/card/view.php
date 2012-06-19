<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/contact.css" />
<script type="text/javascript" src="/resources/addon/contacts.js"></script>
<script type="text/javascript">
function ajax_edit(id){
	$.pjax({
		url: '/card/edit/'+id,
		container: '#contact_info_detail',
		timeout: 100000
	});
}
function confirm_delete(id){
	$.pjax({
		url: '/card/confirm_delete/'+id,
		container: '#contact_info_detail',
		timeout: 100000
	});
}
</script>
<?php 
	//echo '<pre>';
	//print_r($role);
	//echo '</pre>';
?>
<div id="payment_boxes">
	<div class="payment_detail">
		<div class="payment_label">HELPDESK</div>
		<div class="payment_price" style="width:115px;">
			<ul>
				<li style="font-weight:bold;">OPEN</li>
				<li class="green_price"><?=($helpdesk_summary>0?$helpdesk_summary:'')?></li>
				<li class="total">TOTAL <?=($helpdesk_summary>0?$helpdesk_summary:'')?></li>
			</ul>
		</div>
	</div>

	<?php if ($invoice_summary['all_count']): ?>
	<div class="payment_detail payment_active">
		<div class="payment_label">INVOICE</div>
		<div class="payment_price">
			<ul>
				<li style="font-weight:bold;"><?php echo $invoice_summary['unpaid_count'] ?> DUE</li>
				<li class="red_price"><?php echo '$'.number_format($invoice_summary['unpaid_total'], 2) ?></li>
				<li class="total">ALL INVOICES <?php echo '$'.number_format($invoice_summary['all_total'], 2) ?></li>
			</ul>
		</div>
	</div>
	<?php endif ?>

</div>

<div id="boxes" style="float:left;margin-left:54px;">
	<div id="contact_info">
		<div style="width:100%;" class="invoice_title">
			<span class="arrow_title"></span>
			<span style="text-transform:uppercase;">Basic information</span>
		</div>
		<div id="contact_info_detail">
			<ul>
				<li>
					<span class="input_data_label">Role</span>
					<span class="fillter_input">
						<?php
							$role = $data['addon_access_user_role']['role_id'];
							$role_name = '';
							switch($role){
								case 0:
									$role_name = 'None';
									break;
								case 2:
									$role_name = 'Staff';
									break;
								case 3:
									$role_name = 'Customer';
									break;
								case 5:
									$role_name = 'Vendor';
									break;
							}
							echo $role_name;
						?>
					</span>
				</li>
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
					<span style="cursor:pointer;color:#aaaaaa;" onclick="confirm_delete(<?=$data['id']?>);">or delete</span>
				</li>
				<li style="margin-top:10px;">
					<span class="input_data_label">Birthday</span>
					<span class="fillter_input" style="font-size:13px; font-weight:bold;">This is the notes for this guy. bah bah bah. Click to edit this notes.</span>
				</li>
			</ul>
		</div>
	</div>
	<div style="float:left;width:250px;">
		<div id="customer_detail" style="margin-top:20px;">
			<div style="width:100%;" class="invoice_title">
				<span class="arrow_title"></span>
				<span style="text-transform:uppercase;"><?=$card_role?> DETAILS</span>
			</div>
			
			<ul id="view_active">
				<li>
					<span class="input_data_label">Status</span>
					<span class="fillter_input"><?=($data['active']==1?'Active':'Unactive')?></span>
				</li>
				<li style="margin:10px 0 0 95px;">
					<button id="btn_view_active" style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">EDIT DETAILS</button>
				</li>
			</ul>
			
			<ul id="edit_active" style="display:none;">
				<li>
					<span class="input_data_label">Status</span>
					<span class="fillter_input">
						<select id="select_active">
							<option value=""> Change status</option>
							<option <?=($data['active']==1?'selected="selected"':'')?> value="1">Active</option>
							<option <?=($data['active']==0?'selected="selected"':'')?> value="0">Unactive</option>
						</select>
					</span>
				</li>
				<li style="margin:10px 0 0 95px;">
					<button id="btn_edit_active" onclick="ajax_change_status(<?=$data['id']?>);" style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">EDIT DETAILS</button>
				</li>
			</ul>
		</div>
		
		<div id="password_detail" style="margin-top:20px;width:250px;float:left;">
			<div style="width:100%;" class="invoice_title">
				<span class="arrow_title"></span>
				<span style="text-transform:uppercase;">LOGIN</span>
			</div>
			
			<ul>
				<li>
					<span class="input_data_label">Password</span>
					<span class="fillter_input">Active</span>
				</li>
				<li>
					<span class="input_data_label">Expiry Date</span>
					<span class="fillter_input">21/12/2012</span>
				</li>
				<li style="margin:10px 0 0 95px;">
					<button id="btn_view_active" style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">EDIT DETAILS</button>
				</li>
			</ul>
		</div>
	</div>
</div>
