<?php
	//echo '<pre>', print_r($data, TRUE), '</pre>';
?>
<script type="text/javascript">
function ajax_edit(id){
	var url = '<?=site_url('card/card_ajax_edit');?>';

	$.post(url,{
			id : id,
		},function(data){
			$('#card_edit').html(data);
		}
	);
}
</script>
<div id="card_edit">
	<div class="invoice_title"><span class="arrow_title"></span><span>BASIC INFORMATION</span></div>
	<div id="helpdesk_show_info">
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
				<span class="input_data_label">Nickname</span>
				<span class="fillter_input"><?=$data['nickname'] ?></span>
			</li>
			<!-- Card email-->
			<?php 
				if(!empty($card_email)) {
					foreach($card_email as $k) {
			?>			
			<li>
				<span class="input_data_label">Email</span>
				<span class="fillter_input"><?=$k->email?></span>
			</li>					
			<?php }}?>
			<!-- Card social-->
			<?php 
				if(!empty($card_social)) {
					foreach($card_social as $k) {
					$type = $k->type;
					$typename = '';
					switch($type){
						case 0:
							$typename = 'Other';
							break;		
						case 1:
							$typename = 'facebook';
							break;					
						case 2:
							$typename = 'linkedin';
							break;				
						case 3:
							$typename = 'msn';
							break;					
						case 4:
							$typename = 'yahoo';
							break;						
						case 5:
							$typename = 'skype';
							break;					
						case 6:
							$typename = 'qq';
							break;					
						case 7:
							$typename = 'google';
							break;
						case 8:
							$typename = 'website';
							break;
					}
			?>			
			<li>
				<span class="input_data_label">Social</span>
				<span class="fillter_input"><?='Type: '.$typename.' | '.$k->name_id?></span>
			</li>					
			<?php }}?>
			<!-- Card phone-->
			<?php 
				if(!empty($card_phone)) {
					foreach($card_phone as $k) {
			?>			
			<li>
				<span class="input_data_label">Phone</span>
				<span class="fillter_input"><?=$k->extension.'-'.$k->country.'-'.$k->area.'-'.$k->number?></span>
			</li>					
			<?php }}?>
			<!-- Card Address-->
			<?php 
				if(!empty($card_address)) {
					foreach($card_address as $k) {
			?>			
			<li>
				<span class="input_data_label">Address</span>
				<span class="fillter_input"><?=$k->line_1.' / '.$k->line_2?></span>
				<span class="fillter_input"><?='City: '.$k->city.' - State: '.$k->state.' - Postal Code: '.$k->postal?></span>
			</li>					
			<?php }}?>
			<li style="margin:20px 0 0 118px;">	
				<span class="btn btn-inverse" onclick="ajax_edit(<?=$data['id']?>);">EDIT DETAIL<span> 
			</li>
		</ul>
	</div>
</div>