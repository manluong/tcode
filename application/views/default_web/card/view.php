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

			<li style="margin:20px 0 0 118px;">
				<span class="btn btn-inverse" onclick="ajax_edit(<?=$data['id']?>);">EDIT DETAIL</span>
			</li>
		</ul>
	</div>
</div>