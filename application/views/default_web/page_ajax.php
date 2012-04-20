<?=$breadcrumb?>
<div id="content">
	<?=$content?>
</div>
<div id="content-footer">
	<?=$app_menu?>
	<?php echo '<div class="pull-right" style="padding:5px;">env: ',ENVIRONMENT,' - role: ',APP_ROLE,' - db: ',$debug['database'],'</div>'; ?>
</div>