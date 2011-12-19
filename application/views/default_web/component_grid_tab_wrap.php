<?php
echo '
	<!-- Tabs Section -->
	<div class="'.$gridnum.'">
		<div class="tabs">
			<ul>
				<li><a href="#pane-'.$data['div']['element_id'].'" id="tablink-'.$data['div']['element_id'].'">'.$data['div']['title'].'</a></li>
			</ul>
		<!-- tab "panes" -->
			<div class="portlet">
				<section id="pane-'.$data['div']['element_id'].'" class="no-padding">
					<div id="'.$data['div']['element_id'].'">
		';
if (isset($data['html'])) echo $data['html'];
echo '
						</div>
				</section>
			</div>
		</div>
	</div>
	<!-- End Tabs Section -->
';

