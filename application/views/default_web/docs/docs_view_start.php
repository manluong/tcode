<form >
	<div id="uploader">
		<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
	</div>
</form>

<div id="docs">
<button id="create-folder">Create Folder</button>
</div>
<table id="table-list-view">
		<thead>
			<tr>
			<th>Name</th>
			<th>Size</th>
			<th>Last Modified</th>
			</tr>
		</thead>
		<tbody>
		<tr type="add-folder" style="display:none;">
			<td colspan="3" class="col1 folder-td">
			<?php echo $folder_icon ?>
			<input type="text" maxlength="30" size="30" id="folder-name" placeholder="Name your folder...">
			<?php echo $error_icon.' '.$loader_icon .' '.$tick_icon.' '.$cross_icon; ?>
			</td>
		</tr>
		<?php if ( ! empty($parent)) : ?>
		<tr type="folder">
			<td class="col1 folder-td" value="<?php echo $parent['a_docs_dir_parent']; ?>"><?php echo $folder_icon.'...'?></td>
			<td class="col2"> -- </td>
			<td class="col3"> -- </td>
		</tr>
		<?php endif; ?>

		<?php if ( ! empty($folders)) : ?>
		<?php foreach($folders as $folder) : ?>
		<tr type="folder">
			<td class="col1 folder-td" value="<?php echo $folder['a_docs_dir_id']; ?>"><?php echo $folder_icon.' '.$folder["a_docs_dir_name"];?></td>
			<td class="col2"> -- </td>
			<td class="col3"> -- </td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( ! empty($docs)) : ?>
		<?php foreach($docs as $doc) : ?>
		<tr type="docs">
			<td id="docs"><?php echo $doc_icon.' '.$doc["a_docs_ver_filename"];?></td>
			<td></td>
			<td></td>
		</tr>
		</tbody>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php if (empty($folders) && empty($docs)) : ?>
		<tr type="started"><td colspan="3">Get started by uploading some files now...</td></tr>
		<?php endif; ?>
</table>

<script type="text/javascript">
// Convert divs to queue widgets when the DOM is ready
$(document).ready(function() {
$("#uploader").pluploadQueue({
		// General settings
		runtimes : 'gears,flash,silverlight,browserplus,html5',
		url : '/docs/put_object/<?php echo $this->url['id_plain'] ?>/put',
		max_file_size : '10mb',
		chunk_size : '1mb',
		unique_names : true,

		// Resize images on clientside if we can
		resize : {width : 320, height : 240, quality : 90},

		// Specify what files to browse for
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		],

		// Flash settings
		flash_swf_url : '/resources/template/default_web/lib/plupload/js/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url : '/resources/template/default_web/lib/plupload/js/plupload.silverlight.xap'
	});

	// Client side form validation
	$('form').submit(function(e) {
        var uploader = $('#uploader').pluploadQueue();

        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });

            uploader.start();
        } else {
            alert('You must queue at least one file.');
        }

        return false;
    });
});
</script>