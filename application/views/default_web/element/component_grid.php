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

$div_id = (isset($data['div']['element_id']))
			? $data['div']['element_id']
			: '';

if ($gridnum == 12) echo '<div class="row-fluid">';

	echo '<div class="span'.$gridnum.'" id="'.$div_id.'">';
	if (isset($data['html'])) echo $data['html'];
	echo '</div>';

if ($gridnum == 12) echo '</div>';