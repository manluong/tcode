<div class="widget">
	<div class="widget-header">
		Application List
	</div>
	<div class="widget-body">
		<ul class="unstyled app_list">
		<?php
		foreach($list AS $app) {
			if (isset($selected) && $selected == $app['name']) {
				echo '<li class="selected">';
			} else {
				echo '<li>';
			}
			echo '<a href="/setting/configure/'.$app['name'].'" class="ajax">',ucwords($app['name']),'</a></li>';
		}
		?>
		</ul>
	</div>
</div>