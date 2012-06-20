<div class="top-setting">
	<div class="title">SETTINGS</div>
	<input type="button" class="btnX" />
</div>
<div class="cont-icon">
	<ul>
	<?php foreach ($list AS $app): ?>
		<?php $url = isset($app['url']) ? $app['url'] : '/setting/ajax_configure/'.$app['name'] ?>
		<li><a href="<?php echo $url ?>" class="<?php echo $app['name'] ?>"><img src="/resources/template/default_web/img/setting/<?php echo $app['name'] ?>.png" /><span><?php echo ucwords($app['name']) ?></span></a></li>
	<?php endforeach ?>
	</ul>
</div>