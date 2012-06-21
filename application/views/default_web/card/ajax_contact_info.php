<div id="user_profile">
	<div id="user_avatar"><img alt="avatar" src="<?=site_url('resources/template/default_web/img/invoice')?>/invoice-avatar.jpg"/></div>
	<div id="user_info">
		<ul>
			<li class="user_sex">Mr.</li>
			<li class="user_name"><?=(!empty($detail['final_display_name'])?$detail['final_display_name']:'')?></li>
			<li class="user_position"><?=(!empty($detail['organization_name'])?$detail['organization_name']:'')?></li>
		</ul>
	</div>
</div>
<div id="contact_info">
	<ul>
		<li>
			<span class="input_data_label">Phone</span>
			<?php if(!empty($detail['addon_tel'])){
					$t = $detail['addon_tel'][0];
					$tel = $t['extension'].'-'.$t['area'].'-'.$t['country'].'-'.$t['number'];
			?>
			<span class="fillter_input"><?=$tel?></span>
			<?php }?>
		</li>
		<li>
			<span class="input_data_label">Office</span>
			<?php if(!empty($detail['addon_address'])){
					$t = $detail['addon_address'][0];
					$off = $t['line_1'];
			?>
			<span class="fillter_input"><?=$off?></span>
			<?php }?>
		</li>
		<li>
			<span class="input_data_label">Email</span>
			<?php if(!empty($detail['addon_email'])){
					$t = $detail['addon_email'][0];
					$email = $t['email'];
			?>
			<span class="fillter_input"><?=$email?></span>
			<?php }?>
		</li>
		<li style="margin:10px 0 0 121px;">
			<a href="<?=site_url('card/view/'.$detail['id'])?>" style="width:30px; height:10px;line-height:10px;" class="btn btn-inverse pjax">View</a>
		</li>
	</ul>
</div>