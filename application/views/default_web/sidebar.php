<div class="likeMain">
	<div class="nano tabsNavigation">
		<ul class="content">
		<?php
			foreach ($app_list as $app) {
				if ($app['display'] == 0) continue;

				if ($active_app == $app) {
					echo '<li class="active">';
				} else {
					echo '<li>';
				}

		?><div class="activeTab"><a href="<?=base_url().$app['name']?>" data-app_name="<?=$app['name']?>" class="<?=$app['name']?>"><?=lang('core_apps-name-'.$app['name'])?></a></div></li><?php
			}
		?>
			<li class="spacer"></li>
		</ul>
	</div>

	<ul id="bottomMenus">
		<li>
			<a href="#" class="history"></a>
			<a href="#" class="favorites"></a>
		</li>
	</ul>
</div>