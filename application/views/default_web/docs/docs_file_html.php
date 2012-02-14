<div class="preview-content">
	<div class="title">
		<input type="text" value="" id="title_input" class="docs-title"><br><span class="message" style="display:none;"></span>
	</div>
	<img src="" id="s3_object">
	<?php //echo $s3_object; ?>
</div>
<div id="tree"></div>
<div class="actions btn-group">
	<a class="btn btn-primary" href="#"><i class="icon white user"></i> Actions</a>
	<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
	<ul class="dropdown-menu">
	<li><a href="#" id="move"><i class="icon-move"></i> Move</a></li>
	<li><a href="#" id="delete"><i class="icon-trash"></i> Delete</a></li>
	<li  id="upload"><a href="#"><i class="icon-ban-circle"></i> Upload</a></li>
	<li><a href="#" id="download"><i class="icon-ban-circle"></i> Download</a></li>
	<li class="divider"></li>
	<li><a href="#"><i class="icon-pencil"></i> Permission</a></li>
	</ul>
</div>
<div class="side">
	<div class="info">
		<h2>File Info</h2>
		<table id="file_info" class="table table-striped table-bordered table-condensed">
			<thead>
				<th>Name</th>
				<th>Size</th>
				<th>Last Modified</th>
			</thead>
			<td></td>
		</table>
		<h2>Versions</h2>
		<table id="versions" class="table table-striped table-bordered table-condensed">
			<thead>
				<th>Name</th>
				<th>Size</th>
				<th>Last Modified</th>
			</thead>
			<td></td>
		</table>
	</div>
</div>

<script>
$(document).ready(function () {
	window.d = {
		docs_id: '<?php echo $url['id_encrypted']; ?>',
		init: function () {
			$.getJSON('/docs/get_file_details/<?php echo $url['id_encrypted']; ?>/v').success(function(data) {
				var title_input_value = (data['docs_details']['a_docs_displayname'] !== '')
				? data['docs_details']['a_docs_displayname']
				: data['docs_details']['a_docs_ver_filename'];

				$('#title_input').attr('value', title_input_value);
				$('#s3_object').attr('src', data['s3object']);
				$('#download').attr('href', '/docs/download_file/'+data['docs_details']['a_docs_ver_id']+'/download');

				$('#file_info').dataTable({
					"bProcessing" : true,
					"aaData": [[data['docs_details']['a_docs_ver_filename'], data['docs_details']['a_docs_ver_filesize'], data['docs_details']['a_docs_ver_stamp']]],
					"sDom": "<'row'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>",
					"sPaginationType": "bootstrap",
					"bJQueryUI": true
				});

				var aaData = Array();
				for (var i=0;i<data['versions'].length;i++) {
					aaData[i] = [data['versions'][i]['a_docs_ver_filename'],data['versions'][i]['a_docs_ver_filesize'],data['versions'][i]['a_docs_ver_stamp']];
				}

				$('#versions').dataTable({
					"bProcessing" : true,
					"aaData": aaData,
					"sDom": "<'row'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>",
					"sPaginationType": "bootstrap",
					"bJQueryUI": true
				});

				// versions
				var uploader = new plupload.Uploader({
					runtimes: "html5, flash",
					browse_button: "upload",
					url: "/docs/upload_single/"+data['docs_details']['a_docs_id']+"/upload/"+data['docs_details']['a_docs_ver_id'],
					filters: [
						{title : "Image files", extensions : "jpg,gif,png"},
						{title : "Zip files", extensions : "zip"},
						{title : "PDF files", extensions : "pdf"},
					],
					init: {
						QueueChanged: function() {
							uploader.start();
						},
						Error: function() {
							console.log('error');
						},
						FileUploaded: function() {
							console.log('ok');
						}
					}
				});
				uploader.init();
			});


			$.get('/docs/json_tree/1/v').success(function (data) {
				var html = '';
				function generate_html(data) {
					html += '<ul>';
					html += '<li class="tree-folders">/<button class="btn-primary move" folder_id="1">Move</button></li>';

					for(var i=0;i<data.length;i++) {
						html += '<li class="tree-folders">'+data[i].a_docs_displayname+'<button class="btn-primary move" folder_id="'+data[i].a_docs_id+'">Move</button></li>';
						if (data[i].child !== undefined) {
							generate_html(data[i].child);
						}
						else {
							html += '</li>';
						}
					}
					html += '</ul>';
					return html;
				}
				;
				$('#tree').html(generate_html(data));

				$('.move').on('click', function () {
					$.post('/docs/move_file/'+d.docs_id+'/',{folder_id: $(this).attr('folder_id')})
						.success(function(data) {});
				});

				$('#delete').on('click', function () {
					$.post('/docs/delete_docs/'+d.docs_id).success(function() {
						//window.location = '/docs';
					});
				});
			});
		}
	}
	d.init();
});
</script>