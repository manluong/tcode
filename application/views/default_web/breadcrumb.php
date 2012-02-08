<?php
//echo '<div class="row-fluid>';
	echo '<ul id="breadcrumb" class="breadcrumb">';

	if (isset($breadcrumb)) {
		foreach ($breadcrumb as $x => $bc) {
			if (isset($breadcrumb[$x+1])) {
				echo '<li>';
			} else {
				echo '<li class="active">';
			}

			echo '<a href="',$bc['link'],'" title="',$bc['title'],'" class="appname ajax">';
			echo $bc['title'];
			echo '</a>';

			if (isset($breadcrumb[$x+1])) echo '<span class="divider">/</span>';

			echo '</li>';
		}
	}
	echo '<li class="pull-right"><i class="icon-star-empty"></i></li>';

	echo '</ul>';


//echo '</div>';

