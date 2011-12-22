<div id="docs">
<button id="create-folder">Create Folder</button>
</div>
<table id="list-view">
		<thead>
			<tr>
			<th>Name</th>
			<th>Size</th>
			<th>Last Modified</th>
			</tr>
		</thead>
		<tbody>
		<tr type="add-folder" style="display:none;">
			<td colspan="3">
			<?php echo $folder_icon ?>
			<input type="text" maxlength="30" size="30" id="folder-name" placeholder="Name your folder...">
			<?php echo $error_icon.' '.$loader_icon .' '.$tick_icon.' '.$cross_icon; ?>
			</td>
		</tr>
		
		<?php if ( ! empty($folders)) : ?>
		<?php foreach($folders as $folder) : ?>
		<tr type="folder">
			<td ><?php echo $folder_icon.' '.$folder["a_docs_dir_name"];?></td>
			<td> -- </td>
			<td> -- </td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( ! empty($docs)) : ?>
		<?php foreach($docs as $doc) : ?>
		<tr type="docs">
			<td ><?php echo $doc_icon.' '.$doc["a_docs_ver_filename"];?></td>
			<td></td>
			<td></td>
		</tr>
		</tbody>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php if (empty($folders) && empty($docs)) : ?>
		<tr><td colspan="3">Get started by uploading some files now...</td></tr>
		<?php endif; ?>
</table>

