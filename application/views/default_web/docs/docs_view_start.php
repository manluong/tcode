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
			<td class="col1 folder-td" value="<?php echo $parent['a_docs_parentid']; ?>"><?php echo $folder_icon.'...'?></td>
			<td class="col2"> -- </td>
			<td class="col3"> -- </td>
		</tr>
		<?php endif; ?>

		<?php if ( ! empty($sub_folders)) : ?>
		<?php foreach($sub_folders as $folder) : ?>
		<tr type="sub_folder">
			<td class="col1 folder-td" value="<?php echo $folder['a_docs_id']; ?>"><?php echo $folder_icon.' '.$folder["a_docs_displayname"];?></td>
			<td class="col2"> -- </td>
			<td class="col3"> -- </td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( ! empty($docs)) : ?>
		<?php foreach($docs as $doc) : ?>
		<tr type="docs">
			<td class="col1 docs-td"><a href="/docs/file/<?php echo $doc['a_docs_id'] ;?>/view/<?php echo $doc['a_docs_ver_id'];?>">
				<?php $i = ($doc['a_docs_displayname'] !== '') ? $doc['a_docs_displayname'] : $doc["a_docs_ver_filename"]; ?>
				<?php echo $docs_icon.' '.$i;?></a>
			</td>
			<td><?php echo byte_size($doc['a_docs_ver_filesize']); ?></td>
			<td><?php echo $doc['a_docs_ver_stamp'];?></td>
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
		runtimes : 'html5,flash',
		url : '/docs/upload/<?php echo $this->url['id_plain'] ?>',
		max_file_size : '10mb',
		unique_names : true,

		// Resize images on clientside if we can
		resize : {width : 320, height : 240, quality : 90},

		// Specify what files to browse for
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"},
			{title : "PDF files", extensions : "pdf"},
		],

		// Flash settings
		flash_swf_url : '/resources/addon/plupload/js/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url : '/resources/addon/plupload/js/plupload.silverlight.xap'
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