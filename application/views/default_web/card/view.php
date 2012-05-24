<?php
	echo '<pre>', print_r($data, TRUE), '</pre>';
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
				<span class="fillter_input"><?=$data[first_name]?></span>
			</li>
			<li>
				<span class="input_data_label">Last Name</span>
				<span class="fillter_input"><?=$data[last_name]?></span>
			</li>
			<li>
				<span class="input_data_label">Nickname</span>
				<span class="fillter_input"><?=$data[nickname] ?></span>
			</li>
			<li>
				<span class="input_data_label">Organization Name</span>
				<span class="fillter_input"><?=$data[organization_name] ?></span>
			</li>
			<li>
				<span class="input_data_label">Organization Title</span>
				<span class="fillter_input"><?=$data[organization_title] ?></span>
			</li>
			<li>
				<span class="input_data_label">Organization Number</span>
				<span class="fillter_input"><?=$data[organization_number] ?></span>
			</li>
			<li style="margin-left:118px;">
				<span class="btn btn-inverse" onclick="ajax_edit(<?=$data['id']?>);">EDIT DETAIL<span>
			</li>
		</ul>
	</div>
</div>