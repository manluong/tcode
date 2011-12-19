<div class="container_top">
	<div class="company"><a href="index.php" class="companytext"><?=$companyname?></a></div>
	<div class="user"><a href="index.php"><?=$welcome?></a></div>

	<div class="container_menu">
		<div id="topmenu">
			<ul class="dropdown dropdown-vertical">
				<li><a href="./" class="icon16 icon16-television-off">Home</a></li>
				<li><a href="./" class="icon16 icon16-wrench">Setting</a></li>
				<li><a href="/access/logout" class="icon16 icon16-switch">Logout</a></li>
				<li><a href="#" class="icon16 icon16-switch">History</a>
				<?php if (isset($history)): ?>
					<ul>
						<?php foreach ($history as $this_his): ?>
							<li><a href="?'<?=$this_his['furi']?>'"><?=$this_his['text']?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				</li>
			</ul>
		</div>
	</div>
</div>

<div id="telcoson_logo"><img src="html/images/telcoson-s.png" alt="Icon" width="55" height="20"></div>