<?php
/*
	$this_divstyle['style']
	$this_divstyle['title']
	$this_divstyle['drag']
	$this_divstyle['collaps']
	$this_divstyle['boxless'];
	$this_divstyle['css']
	$this_divstyle['formtype']
	$this_divstyle['listtype']
	$this_divstyle['viewtype']
	$this_divstyle['formcss']
	$this_divstyle['tablecss']
	$this_divstyle['viewcss']
	//draggable="true"
*/

$collapse = ($divstyle[$active_style]['collaps'])
				? ' collapsible'
				: '';

$draggable = ($divstyle[$active_style]['drag'])
				? ' draggable="true"'
				: '';

$css = ($divstyle[$active_style]['css'])
		? ' class="'.$divstyle[$active_style]['css'].'"'
		: '';


if ($divstyle[$active_style]['boxless'] != 1) {
	echo '<div class="'.$gridnum.' portlet'.$collapse.'" id="'.$data['div']['element_id'].'_parent"'.$draggable.'>
	';
	if ($divstyle[$active_style]['title']) echo '<header><h2>'.$data['div']['title'] .'</h2></header>
	';
} else {
	echo '<div class="'.$gridnum.' portlet-boxless portlet" id="'.$data['div']['element_id'].'_parent">';
}

echo '<section class="no-padding"><div id="'.$data['div']['element_id'].'"'.$css.'>';
if (isset($data['html'])) echo $data['html'];
echo '</div></section>
';

echo '</div>
';
