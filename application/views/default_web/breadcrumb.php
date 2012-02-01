<?php
if ($breadcrumb) {
	echo '<ul id="breadcrumb" class="breadcrumb">';

	foreach ($breadcrumb as $x => $bc) {
		if (isset($breadcrumb[$x+1])) {
			echo '<li>';
		} else {
			echo '<li class="active">';
		}

		echo '<a href="',$bc['link'],'" title="',$bc['title'],'" class="appname">';
		echo $bc['title'];
		echo '</a>';

		if (isset($breadcrumb[$x+1])) echo '<span class="divider">/</span>';

		echo '</li>';
	}
	echo '</ul>';
}

