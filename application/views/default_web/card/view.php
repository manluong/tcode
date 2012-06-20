<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/contact.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
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
	//print_r($data);
	//echo '</pre>';
?>
<div id="payment_boxes">
	<?php ($helpdesk_summary == FALSE ? $helpdesk_total = 0 : $helpdesk_total = count($helpdesk_summary))?>
	<div class="payment_detail">
		<div class="payment_label">HELPDESK</div>
		<div class="payment_price" style="width:115px;">
			<ul>
				<li style="font-weight:bold;">OPEN</li>
				<li class="green_price"><?=$helpdesk_total?></li>
				<li class="total">TOTAL <?=$helpdesk_total?></li>
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
					<span class="fillter_input"><?=$card_role?></span>
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
				
				<li>
					<span class="input_data_label">Birthday</span>
					<span class="fillter_input"><?php
                                        // -- Leo fix
                                            $birthday = explode('-',$data['addon_extra'][0]['birth_date']);
                                            $birthday = $birthday[1].'/'.$birthday[2].'/'.$birthday[0];
                                        // -- End
                                        echo $birthday;
                                        ?></span>
				</li>
				<li style="margin:10px 0 0 95px;">
					<button onclick="ajax_edit(<?=$data['id']?>);" style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">EDIT DETAILS</button>
					<span style="cursor:pointer;color:#aaaaaa;" onclick="confirm_delete(<?=$data['id']?>);">or delete</span>
				</li>
				<li style="margin-top:10px;">
					<span class="input_data_label">Note</span>
					<span class="fillter_input" style="font-size:13px;"><?=$data['addon_notes'][0]['note']?></span>
				</li>
			</ul>
		</div>
	</div>
	<?php if($user_role['role_id'] == 0 || $user_role['role_id'] == ''){?>
	<div id="customer_detail" style="margin-top:20px;">
		<div style="width:100%;" class="invoice_title">
			<span class="arrow_title"></span>
			<span style="text-transform:uppercase;">CONTACT TYPE</span>
		</div>
		<div class="control-group" style="padding-top:10px;">
			<?php 
				$i = 0;
				foreach($role as $role_value => $role_label){
					$i++;
					echo '<input '.($i==1?'checked="checked" ':'').'type="radio" name="role" class="role" value="'.$role_value.'" /> '.$role_label.'&nbsp;&nbsp;';
				}
			?>
			<input type="hidden" name="addon_access_user_role[0][role_id]" id="addon_role" value="">
			<script>
				$(document).ready(function() {
					$('.role').on('click', function() {
						$('#addon_role').val($(this).attr('value'));
					});
				});
			</script>
		</div>
		<div style="margin-top:20px;" class="control-group">
			<label class="control-label"></label>
			<div class="controls">
				<button onclick="save_role(<?=$data['id']?>);" type="submit" class="btn btn-inverse">Save</button>
			</div>
		</div>
	</div>
	<?php }else { ?>
	<div style="float:left;width:250px;">
		<div id="customer_detail" style="margin-top:20px;">
			<div style="width:100%;" class="invoice_title">
				<span class="arrow_title"></span>
				<span style="text-transform:uppercase;"><?=$user_role['name']?> DETAILS</span>
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
			
			<ul id="view_pass">
				<li>
					<span class="input_data_label">Password</span>
					<span class="fillter_input">xxxxxx</span>
				</li>
				<li>
					<span class="input_data_label">Expiry Date</span>
					<span class="fillter_input"><?=$data['addon_access_user'][0]['expire_stamp']?></span>
				</li>
				<li style="margin:10px 0 0 95px;">
					<button id="btn_view_pass" style="height:20px;line-height:12px;" class="btn btn-inverse">EDIT DETAILS</button>
				</li>
			</ul>
			
			<ul id="edit_pass" style="display:none;">
				<li>
					<span class="input_data_label">Password</span>
					<span class="fillter_input"><input type="password" id="access_pass" value=""/></span>
				</li>
				<li>
					<span class="input_data_label">Expiry Date</span>
					<span class="fillter_input"><input type="text" id="expiry_date" value=""/></span>
				</li>
				<li style="margin:10px 0 0 95px;">
					<button onclick="ajax_change_pass(<?=$data['addon_access_user'][0]['id']?>);" id="btn_view_active" style="height:20px;line-height:12px;" class="btn btn-inverse">SAVE</button>
				</li>
			</ul>
		</div>
	</div>
	<?php }?>
</div>
<script type="text/javascript">
	$(function() {
		$( "#expiry_date" ).datepicker();
	});
</script>