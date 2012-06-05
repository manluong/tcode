<ul class="tabsNavigation">
	<?php
		foreach ($app_list as $app) {
			if ($app['display'] == 0) continue;

			if ($active_app == $app) {
				echo '<li class="active">';
			} else {
				echo '<li>';
			}

	?><div class="activeTab"><a href="<?=base_url().$app['name']?>" data-app_name="<?=$app['name']?>" class="ajax <?=$app['name']?>"><?=lang('core_apps-name-'.$app['name'])?></a></div></li><?php
		}
	?>
</ul>

<ul id="bottomMenus">
	<li>
		<a href="#" class="history"></a>
		<a href="#" class="favorites"></a>
	</li>
</ul>

<a href="#" class="appsIconTop tabsNavigationPagers"></a>
<a href="#" class="appsIconBottom tabsNavigationPagers"></a>