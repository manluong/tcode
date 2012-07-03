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
						//if ($x == $count_breadcrumb) echo ' class="curent_page"';
						echo ' class="simple"';
						echo '>';
						if (!empty($bc['url'])) echo '<a href="',$bc['url'],'">';
						echo $bc['title'];
						if (!empty($bc['url'])) echo '</a>';
						echo '</li>';
						if ($x != $count_breadcrumb) echo '<li class="arrow"></li>';
						$x++;
					}
				}
			?>
			<li><a href="#" id="favoriteIcon" class="on" title="Remove from favorites"></a></li>
		</ul>
	</div>
	<?php if (count($app_menu) > 0) { ?>
	<div class="rightBar">
		<?php foreach($app_menu AS $menu) { ?>
		<a href="<?=$menu['url']?>" <?=$menu['extra']?> class="btn btn-inverse"><?=$menu['title']?></a>
		<?php } ?>
	</div>
	<?php } ?>
</div>

<?php
	$return_url = get_return_url();
	if (strlen($return_url) > 0) {
		$return_text = str_replace(
							'#app_name#',
							$this->lang->line('core_apps-name-'.$this->re_url['app']),
							$this->lang->line('core-return_to_app')
						);
		echo '<div style="margin-bottom:20px;">';
		echo '<a href="/',$return_url,'" class="btn">',$return_text,'</a>';
		echo '</div>';
	}
?>