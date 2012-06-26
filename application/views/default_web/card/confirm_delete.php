<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/contact.css" />
<div  id="contact_edit" class="container" style="width:350px;float:left;">
	<?php if($per == 1){?>
	<div class="alert_yellow"> <strong>Confirm!</strong> Please confirm to delete this contact</div>
	<div>
		<button onclick="delete_card(<?=$card_id?>);" style="height:22px;line-height:12px;" class="btn btn-inverse">DELETE</button>
		<a href="<?=site_url('card/view/'.$card_id)?>"> or cancel</a>
	</div>
	<?php }else{ ?>
	<div class="alert_yellow"> <strong>Sorry!</strong> You can not to delete this contact</div>
	<div>
		<a href="<?=site_url('card/view/'.$card_id)?>" style="height:12px;line-height:12px;" class="btn btn-inverse">BACK</a>
	</div>
	<?php }?>
</div>

<script type="text/javascript">
function delete_card(id){
	$.pjax({
		url: '/card/delete/'+id,
		container: '#contact_info_detail',
		timeout: 100000
	});
}
</script>