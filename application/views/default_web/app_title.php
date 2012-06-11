<div id="breadcrumb">
	<div id="module_name">
		<ul>
			<li><a href="#" class="main"><?=$active_app_name;?></a></li>
			<?php
				$count_breadcrumb = count($breadcrumb);
				if ($count_breadcrumb > 0) {
					echo '<li class="arrow"></li>';
					$x = 1;
					foreach($breadcrumb AS $bc) {
						echo '<li';
						if ($x == $count_breadcrumb) echo ' class="curent_page"';
						echo '>'.$bc['title'].'</li>';
						$x++;
					}
				}
			?>
			<li><a href="#" id="favoriteIcon" class="on" title="Remove from favorites"></a></li>
		</ul>
	</div>
	<?php if (count($app_menu) > 0) { ?>
	<div id="top_button">
		<ul>
			<?php foreach($app_menu AS $menu) { ?>
			<li><a href="<?=$menu['url']?>" <?=$menu['extra']?> class="btn btn-inverse" href="#"><?=$menu['title']?></a></li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
</div>