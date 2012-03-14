<div class="preview-content">
	<div class="title">
		<input type="text" value="" id="title_input" class="docs-title"><br><span class="message" style="display:none;"></span>
	</div>
	<img src="" id="image_placeholder">
	<p id="viewerPlaceHolder" style="width:660px;height:553px;display:block"><div id="documentViewer" class="viewer"></div>Document loading..</p>
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
			$.get('/docs/get_file_details/<?php echo $url['id_encrypted']; ?>/v').success(function(data) {
				if (data.success === 0) {
					$('.preview-content').html('No file found')
					return null;
				}
				d = data;
				var title_input_value = (data['docs_details']['a_docs_displayname'] !== '')
				? data['docs_details']['a_docs_displayname']
				: data['docs_details']['a_docs_ver_filename'];

				$('#title_input').attr('value', title_input_value);

				// Sets display
				if (data['docs_details']['a_docs_ver_mime'] === 'application/pdf') {
					console.log(data['docs_details']['a_docs_id']);
					var startDocument = data['docs_details']['a_docs_id'];
					function getDocumentUrl(document){
						return "/docs/pdfPreview?doc={doc}&format={format}&page={page}&id="+ data['docs_details']['a_docs_id'].replace("{doc}",document);
					}
					var fp = new FlexPaperViewer(
							'FlexPaperViewer',
							'documentViewer', { config : {

							DOC : escape(getDocumentUrl(startDocument)),
							key : "@63fe01a5804d28236b8$bd56bc57d4f62cc10eb",
							Scale : 0.6,
							ZoomTransition : 'easeOut',
							ZoomTime : 0.5,
							ZoomInterval : 0.2,
							FitPageOnLoad : false,
							FitWidthOnLoad : false,
							FullScreenAsMaxWindow : false,
							ProgressiveLoading : false,
							MinZoomSize : 0.2,
							MaxZoomSize : 5,
							SearchMatchAll : false,
							InitViewMode : 'Portrait',
							RenderingOrder : 'html5,flash',

							ViewModeToolsVisible : true,
							ZoomToolsVisible : true,
							NavToolsVisible : true,
							CursorToolsVisible : true,
							SearchToolsVisible : true,

							localeChain: 'en_US'
							}});
				}

				/*
				if (data['docs_details']['a_docs_ver_mime'] === 'application/pdf') {
					var fp = new FlexPaperViewer(
						'/resources/addon/docs/FlexPaper_1.5.1_flash/FlexPaperViewer',
						'viewerPlaceHolder', { config : {
						SwfFile : escape('/docs/pdfPreview?doc='+data['docs_details']['a_docs_ver_filename']),
						Scale : 0.6,
						ZoomTransition : 'easeOut',
						ZoomTime : 0.5,
						ZoomInterval : 0.2,
						FitPageOnLoad : true,
						FitWidthOnLoad : false,
						FullScreenAsMaxWindow : false,
						ProgressiveLoading : false,
						MinZoomSize : 0.2,
						MaxZoomSize : 5,
						SearchMatchAll : false,
						InitViewMode : 'Portrait',
						PrintPaperAsBitmap : false,

						ViewModeToolsVisible : true,
						ZoomToolsVisible : true,
						NavToolsVisible : true,
						CursorToolsVisible : true,
						SearchToolsVisible : true,

						localeChain: 'en_US'
						}});
					$('#image_placeholder').hide();
				} */

				if (data['docs_details']['a_docs_ver_mime'] === 'image/png') {
					//$('#image_placeholder').attr('src', data['s3object']);
					$('#image_placeholder').attr('src', '/file/read'+data['docs_details']['a_docs_dir_dirpath']+data['docs_details']['a_docs_ver_filename']);
					$('#viewerPlaceHolder').hide();
				}

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

				// bind upload
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
				html += '<ul>';
				html += '<li class="tree-folders">/<button class="btn-primary move" folder_id="1">Move</button></li>';
				function generate_html(data, index) {
					html += '<ul>';
					for(var i=0;i<data.length;i++) {
						html += '<li class="tree-folders">'+data[i].a_docs_displayname+'<button class="btn-primary move" folder_id="'+data[i].a_docs_id+'">Move</button></li>';
						if (data[i].child !== undefined) {
							generate_html(data[i].child, i);
						}
						else {
							html += '</li>';
							if (index !== 0 && data[index+1] !== undefined) generate_html(data[index+1], 0);
						}
					}
					html += '</ul></ul>';
					return html;
				}

				$('#tree').html(generate_html(data, 0));

				$('.move').on('click', function () {
					$.post('/docs/move_file/<?php echo $url['id_encrypted']; ?>/',{folder_id: $(this).attr('folder_id')})
						.success(function(data) {});
				});

				$('#delete').on('click', function () {
					$.post('/docs/delete_docs/<?php echo $url['id_encrypted']; ?>').success(function() {
						//window.location = '/docs';
					});
				});
			});
		}
	}
	d.init();
});
</script>