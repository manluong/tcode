	<!-- Tabs Section -->
	<div class="<?=$gridnum?>">
		<div class="tabs">
			<ul>
				<?php foreach ($data as $this_row): ?>
			<li><a href="#pane-<?=$this_row['div']['element_id']?>" id="tablink-'<?=$this_row['div']['element_id']?>"><?=$this_row['div']['title']?></a></li>
				<?php endforeach; ?>				
			</ul>
		<!-- tab "panes" -->
			
				<?php foreach ($data as $this_row): ?>
				<div class="portlet">
				<section id="pane-<?=$this_row['div']['element_id']?>" class="no-padding">
					<div id="<?=$this_row['div']['element_id']?>">
						<?=$this_row['html']?>
					</div>
				</section>	
				</div>			
				<?php endforeach; ?>
			
		</div>
	</div>
	<!-- End Tabs Section -->
