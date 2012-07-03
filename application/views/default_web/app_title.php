<div id="breadcrumb">

	<?php if (count($app_menu) > 0) { ?>
	<div class="rightBar">
		<?php foreach($app_menu AS $menu) { ?>
		<a href="<?=$menu['url']?>" <?=$menu['extra']?> class="btn btn-inverse"><?=$menu['title']?></a>
		<?php } ?>
	</div>
	<?php } ?>
		
	<?php
		//back link
		$return_url = get_return_url();
		if (strlen($return_url) > 0) {
			//<a class="simple" href="#">BACK TO MAIN</a><span class="arrowBack"></span>
			$return_text = str_replace(
								'#app_name#',
								$this->lang->line('core_apps-name-'.$this->re_url['app']),
								$this->lang->line('core-return_to_app')
							);
			//echo '<div style="margin-bottom:20px;">';
			//echo '<a href="/',$return_url,'" class="btn">',$return_text,'</a>';
			//echo '</div>';
			echo '<a class="simple" href="/',$return_url,'">',$return_text,'</a><span class="arrowBack"></span>';
		}
	?>
	<a href="#" class="main"><?=$active_app_name;?></a>
	<?php
		$count_breadcrumb = count($breadcrumb);
		if ($count_breadcrumb > 0) {
			$x = 1;
			foreach($breadcrumb AS $bc) {
				echo '<span class="arrow"></span>';
				if (!empty($bc['url'])) {
					echo '<a class="simple tabsContent" href="',$bc['url'],'">',$bc['title'],'</a>';
				} else {
					echo '<span>',$bc['title'],'</span>';
				}
				$x++;
			}
		}
	?>
	<a class="simple" href="#" class="star"></a> <a href="#" id="favoriteIcon" class="on" title="Remove from favorites"></a>
	
</div>