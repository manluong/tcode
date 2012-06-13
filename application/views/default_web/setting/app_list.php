<div class="title">Settings</div>
<ul class="icons">
	<?php
		foreach($list AS $app) {
			echo '<li><a href="';
			if (isset($app['url'])) {
				echo $app['url'];
			} else {
				echo '/setting/ajax_configure/'.$app['name'];
			}
			echo '" class="',$app['name'],'">',ucwords($app['name']),'</a></li>';
		}
	?>
</ul>