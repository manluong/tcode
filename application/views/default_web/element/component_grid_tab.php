<!-- Tabs Section -->
<div class="row-fluid">
	<div class="tabbable widget span<?=$gridnum?>">

		<ul class="nav nav-tabs">
		<?php foreach ($data as $this_row): ?>
			<li><a href="#<?=$this_row['div']['element_id']?>" data-toggle="tab"><?=$this_row['div']['title']?></a></li>
		<?php endforeach; ?>
		</ul>
		<!-- tab "panes" -->

		<div class="tab-content widget-body">
		<?php foreach ($data as $this_row): ?>
			<div id="<?=$this_row['div']['element_id']?>" class="tab-pane">
				<?=$this_row['html']?>
			</div>
		<?php endforeach; ?>
		</div>

	</div>
</div>
<!-- End Tabs Section -->