<div class="container_top">
	<div class="company"><a href="/" class="companytext"><?=$companyname?></a></div>
	<div class="user"><a href="/"><?=$welcome?></a></div>

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
							<li><a href="<?=$this_his['furi']?>"><?=$this_his['text']?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				</li>
				<?php if (isset($can_follow)) : ?>
				<li id="follow" class="icon16 icon16-switch"><a href='#'>Follow</a></li>
				<?php endif; ?>
				<?php if (isset($can_favorite)) : ?>
				<li id="fav" class="icon16 icon16-switch"><a href='#'>Favorite</a>
				<input type="text" id="fav_name" name="fav_name" style="display:none;">
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>

<div id="telcoson_logo"><img src="/resources/images/telcoson-s.png" alt="Icon" width="55" height="20"></div>
<?php //print_r($_ci_CI); ?>
<script>
$(document).ready(function () {
	$('#fav').on('click', function(e) {
		$('#fav_name').show();
		e.preventDefault();
	});
	$('#fav_name').on('keydown', function(e){
		if (e.keyCode =='13') {
			$.get('/log/add_favorite/<?php echo $_ci_CI->url['id_encrypted']; ?>/add',  {fav_name: $('#fav_name').val(), url:"<?php echo $_SERVER['REQUEST_URI'];?>"}, function() {

			}).success(function(data) {
				console.log(data);
			});
			$('#fav_name').hide();
		}
	});
	$('#follow').on('click', function(e) {
		$.get('/log/add_follow/<?php echo $_ci_CI->url['id_encrypted']; ?>/add',{following_app_data_id: "<?php echo $_ci_CI->url['app_id']; ?>", following_log_type_id: "<?php echo $_ci_CI->log_data['log_type']['id']; ?>"}, function() {

		}).success(function(data) {
			console.log(data);
		});
		e.preventDefault();
	});
});
</script>