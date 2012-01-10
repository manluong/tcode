<div class="preview-content">
	<div class="title">
		<input type="text" value="<?php echo ( ! empty($docs_detail['a_docs_displayname']) ) ?
		$docs_detail['a_docs_displayname'] : $docs_detail['a_docs_ver_filename'] ; ?>" id="<?php echo $docs_detail['a_docs_ver_id']; ?>" class="docs-title"><br><span class="message" style="display:none;"></span>
	</div>
	<?php echo $s3_object; ?>
</div>
<?php if (isset($past_versions)) : ?>
<ul class="past-versions">
<?php foreach ($past_versions as $version) : ?>
	<li><a href="/docs/file/<?php echo $docs_detail['a_docs_id'] ;?>/view/<?php echo $version['a_docs_ver_id']; ?>"><?php echo ($version['a_docs_displayname'] !== '') ? $version['a_docs_displayname'] : $version['a_docs_ver_filename']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>