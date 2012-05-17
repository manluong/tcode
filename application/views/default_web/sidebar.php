<ul class="tabsNavigation">
	<?php
		foreach ($app_list as $app) {

			if ($active_app == $app) {
				echo '<li class="active">';
			} else {
				echo '<li>';
			}

	?><div class="activeTab"><a href="<?=base_url().$app?>" data-app_name="<?=$app?>" class="ajax <?=$app?>"><?=lang('core_apps-name-'.$app)?></a></div></li><?php
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