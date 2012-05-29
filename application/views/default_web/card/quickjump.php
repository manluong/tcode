<div id="user_profile">
	<div id="user_avatar"><img alt="avatar" src="/resources/template/default_web/img/invoice/invoice-avatar.jpg"/></div>
	<div id="user_info">
		<ul>
			<li class="user_sex"><?=$title_options[$title]?></li>
			<li class="user_name"><?=(strlen($display_name)>0)?$display_name:$first_name.' '.$last_name;?></li>
			<li class="user_position"><?=$organization_name?><span style="font-weight: normal;"><?=$organization_title?></span></li>
		</ul>
	</div>
</div>
<div id="customer_list">
	<div class="btn-group">
		<a href="#" class="btn btn-inverse"><?=$role['name']?></a>
		<a href="#" data-toggle="dropdown" class="btn btn-inverse dropdown-toggle"><span class="caret"></span></a>
		<ul class="dropdown-menu">
			<li><a href="/card/edit/<?=$id?>"><i class="icon-pencil"></i> Edit</a></li>
			<li><a href="#"><i class="icon-trash"></i> Delete</a></li>
			<li><a href="#"><i class="icon-ban-circle"></i> Ban</a></li>
		</ul>
	</div>
</div>
<script>
	var quickjump_id = <?=$id?>;
</script>