<div class="preview-content">
	<div class="title">
		<input type="text" value="<?php echo ( ! empty($docs_detail['a_docs_displayname']) ) ?
		$docs_detail['a_docs_displayname'] : $docs_detail['a_docs_ver_filename'] ; ?>" id="<?php echo $docs_detail['a_docs_id']; ?>" class="docs-title"><br><span class="message" style="display:none;"></span>
	</div>
	<?php echo $s3_object; ?>
</div>