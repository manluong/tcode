<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/contact.css" />
<div  id="contact_edit" class="container" style="width:200px;float:left;">
	<?php if($per == TRUE){?>
		<div class="alert_pink">You deleted successful !</div>
		<div>
			<a href="<?=site_url('card/index')?>" style="height:12px;line-height:12px;" class="btn btn-inverse">BACK</a>
		</div>
	<?php }else{ ?>
		<div class="alert_pink"> <strong>Sorry!</strong> You can not delete this contact. Information in other application is associated to it</div>
		<div>
			<a href="<?=site_url('card/index')?>" style="height:12px;line-height:12px;" class="btn btn-inverse">BACK</a>
		</div>
	<?php }?>
</div>