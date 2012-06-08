<div class="title">Settings</div>
<ul class="icons">
	<?php
		foreach($list AS $app) {
			echo '<li><a href="#" class="',$app['name'],'">',ucwords($app['name']),'</a></li>';
		}
	?>
</ul>