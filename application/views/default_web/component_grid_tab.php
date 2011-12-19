<?php
if (!isset($build_tab['li'])) $build_tab['li'] = $build_tab['section'] = '';

$build_tab['li'] .= '
	<li><a href="#pane-'.$data['div']['element_id'].'" id="tablink-'.$data['div']['element_id'].'">'.$data['div']['title'].'</a></li>
';
$build_tab['section'] .= '
	<div class="portlet">
		<section id="pane-'.$data['div']['element_id'].'" class="no-padding">
			<div id="'.$data['div']['element_id'].'">
';

if (isset($data['html'])) $build_tab['section'] .= $data['html'];

$build_tab['section'] .= '
			</div>
		</section>
	</div>
';