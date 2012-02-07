<?php
	if (isset($style)) {
		echo '<div id="appmenu_',$appmenu_gp,'" class="',$style,'">';
	} else {
		echo '<div id="appmenu_',$appmenu_gp,'" class="appsubmenu">';
	}
?>

    <ul class="nav nav-pills">
<?php
    if ($appmenu && $button['top']) {
		$menu = '';

		foreach ($button['top'] as $thisbutton) {
			if (isset($thisbutton['seprator'])) continue;

			$thisclass = $thisicon = '';

			if (isset($thisbutton['class'])) $thisclass = $thisbutton['class'];
			if (isset($thisbutton['icon'])) $thisicon = 'icon16 '.$thisbutton['icon'];

			if ( isset($button[$thisbutton['name']]) ) {
				$menu .= '<li class="'.$thisclass.' dropdown">';
				//$menu .= '<a href="'.$thisbutton['link'].'" class="'.$thisicon.' dropdown-toggle" data-toggle="dropdown">';
				$menu .= '<a href="#" class="'.$thisicon.' dropdown-toggle" data-toggle="dropdown">';
				$menu .= $thisbutton['lang'];
				$menu .= '<b class="caret"></b>';
				$menu .= '</a>';

				//sub menu
				$menu .= '<ul class="dropdown-menu bottom-up">';
				foreach ($button[$thisbutton['name']] as $thisbuttonchild) {
					$thisclass = $thisicon = '';
					$link = '#';
					$lang = 'undefined';

					if (isset($thisbuttonchild['class'])) $thisclass = $thisbuttonchild['class'];
					if (isset($thisbuttonchild['icon'])) $thisicon = 'icon16 '.$thisbuttonchild['icon'];
					if (isset($thisbuttonchild['link'])) $link = $thisbuttonchild['link'];
					if (isset($thisbuttonchild['lang'])) $lang = $thisbuttonchild['lang'];

					$menu .= '<li class="'.$thisclass.'">';
					$menu .= '<a href="'.$link.'" class="'.$thisicon.'">'.$lang.'</a>';
					$menu .= '</li>';
				}
				$menu .= '</ul>';

				$menu .= '</li>';
			} else {
				$menu .= ( isset($thisbutton['nolink']) )
					? '<li class="'.$thisclass.' nolink">'.$thisbutton['lang'].'</li>'
					: '<li class="'.$thisclass.'"><a href="'.$thisbutton['link'].'" class="'.$thisicon.' ajax">'.$thisbutton['lang'].'</a></li>';
			}

		}

		echo $menu;
    } //end if menu
?>
	</ul>


</div>