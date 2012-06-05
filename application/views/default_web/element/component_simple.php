<?php

//if ($this_divstyle['collaps']) $collapsible = ' collapsible';
//if ($this_divstyle['drag']) $draggable = ' draggable="true"';
//if ($this_divstyle['css']) $cssadd = ' class="'.$this_divstyle['css'].'"';
$cssadd = $draggable = '';

if ($divstyle[$active_style]['boxless']) exit;

echo '<div class="" id="'.$data['div']['element_id'].'_parent"'.$draggable.'>';
/*
if ($this_divstyle['title']){
$h_html .= '
<header>
<h2>'.$lang['core'][$this_output['title']] .'</h2>
</header>
';
}
*/
echo '
		<section class="no-padding">
			<div id="'.$data['div']['element_id'].'"'.$cssadd.'>
				'.$data['html'].'
			</div>
		</section>
	</div>
';