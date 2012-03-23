<div class="container-fluid">
	<div class="row-fluid">
		<div class="span8">
			<div class="preview-content">
				<div class="title">
					<input type="text" value="" id="title_input" class="docs-title"><br><span class="message" style="display:none;"></span>
				</div>
				<div class="content">
					<img src="" id="image_placeholder">
					<div id="viewerPlaceHolder" style="width:660px;height:553px;display:block;position:relative;">
						<div id="documentViewer" class="viewer"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="span4">
			<div id="tree"></div>

			<div class="info">
				<h2>File Info</h2>
				<table class="table">
				<tbody>
				<tr>
				<td>Filename</td>
				<td class="filename"></td>
				</tr>
				<tr>
				<td>Size</td>
				<td class="filesize"></td>
				</tr>
				<tr>
				<td>Date</td>
				<td class="date"></td>
				</tr>
				<tr>
				<td>By</td>
				<td class="user-name"></td>
				</tr>

				<tr>
				<td></td>
				<td>
					<div class="actions btn-group">
						<a class="btn btn-primary" href="#"><i class="icon white user"></i> Actions</a>
						<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
						<ul class="dropdown-menu">
						<li><a href="#" id="move"><i class="icon-move"></i> Move</a></li>
						<li><a href="#" id="delete"><i class="icon-trash"></i> Delete All</a></li>
						<li  id="upload"><a href="#"><i class="icon-ban-circle"></i> Upload</a></li>
						<li><a href="#" id="download"><i class="icon-ban-circle"></i> Download</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="icon-pencil"></i> Permission</a></li>
						</ul>
					</div>
					<div class="alert alert-block alert-error" style="display:none;">
						<h4>Delete All!</h4>
						<p>This action will delete all versions of this document and cannot be undone!</p>
						<p>
							<a class="confirm-delete btn btn-danger">Confirm Delete</a>
							<a class="cancel-delete btn">Cancel</a>
						</p>
					</div>
				</td>
				</tr>
				</tbody>
				</table>

				<h2>Versions</h2>
				<table id="versions" class="table table-striped table-bordered table-condensed">
					<thead>
						<th>Name</th>
						<th>Size</th>
						<th>Last Modified</th>
						<th>Action</th>
					</thead>
					<td></td>
				</table>

				<h2>Comment</h2>
				<?php echo $this->commentsl->get_lite_html(); ?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function () {
	window.d = {
		docs_id: '<?php echo $url['id_encrypted']; ?>',
		init: function (url) {
			$.get(url).success(function(data) {
				if (data.success === 0) {
					$('.preview-content').html('No file found')
					return null;
				}
				d.data = data;
				var title_input_value = (data['docs_details']['a_docs_displayname'] !== '')
				? data['docs_details']['a_docs_displayname']
				: data['docs_details']['a_docs_ver_filename'];

				$('#title_input').attr('value', title_input_value);

				// Sets display
				if (data['docs_details']['a_docs_ver_mime'] === 'application/pdf') {
					$('.preview-content .content').html('\
						<div id="viewerPlaceHolder" style="width:660px;height:553px;display:block;position:relative;">\
							<div id="documentViewer" class="viewer"></div>\
						</div>\
					');
					var startDocument = data['docs_details']['a_docs_id'];
					function getDocumentUrl(document){
						return "/docs/pdfPreview?doc={doc}&format={format}&page={page}".replace("{doc}",document);
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
							RenderingOrder : 'html,flash',

							ViewModeToolsVisible : true,
							ZoomToolsVisible : true,
							NavToolsVisible : true,
							CursorToolsVisible : true,
							SearchToolsVisible : true,

							localeChain: 'en_US',
							}});
				}

				if (data['docs_details']['a_docs_ver_mime'] === 'image/png'
					|| data['docs_details']['a_docs_ver_mime'] === 'image/jpeg'
					|| data['docs_details']['a_docs_ver_mime'] === 'image/gif'
					) {
					$('.preview-content .content').html('\
							<img src="" id="image_placeholder">\
						');

					if (data['docs_details']['a_docs_dir_dirpath'] === '/') {
						$('#image_placeholder').attr('src', '/file/read'+data['docs_details']['a_docs_dir_dirpath']+data['docs_details']['a_docs_ver_filename']);
					} else {
						$('#image_placeholder').attr('src', '/file/read'+data['docs_details']['a_docs_dir_dirpath']+'/'+data['docs_details']['a_docs_ver_filename']);
					}

					$('#viewerPlaceHolder').hide();
				}

				$('#download').attr('href', '/docs/download_file/'+data['docs_details']['a_docs_ver_id']+'/download');
				// File info
				$('.filename').html(data['docs_details']['a_docs_ver_filename']);
				$('.filesize').html(data['docs_details']['a_docs_ver_filesize']);
				$('.date').html(data['docs_details']['a_docs_ver_stamp']);
				$('.user-name').html(data['docs_details']['a_docs_ver_filename']);

				var aaData = Array();
				for (var i=0;i<data['versions'].length;i++) {
					aaData[i] = ['<a href="#" docs_id="'+data['docs_details']['a_docs_id']+'" ver_id="'+data['versions'][i]['a_docs_ver_id']+'" class="old-ver">'+data['versions'][i]['a_docs_ver_filename']+'</a>',data['versions'][i]['a_docs_ver_filesize'],data['versions'][i]['a_docs_ver_stamp'], '<a class="btn btn-danger delete_ver" docs_id = "'+data['docs_details']['a_docs_id']+'" ver_id="'+data['versions'][i]['a_docs_ver_id']+'">Delete</a>'];
				}

				$('#versions').dataTable({
					"bDestroy" : true,
					"bRetrieve" : true,
					"aaData": aaData,
					"sDom": "<<'pull-right'p>f>t<<'pull-right'p>li>",
					"sPaginationType": "bootstrap",
					"bJQueryUI": true,
					"oLanguage": {
							"sSearch" : "<div class=\"input-prepend\"><span class=\"add-on\"><i class=\"icon-search\"></i></span></i>_INPUT_</div>",
							"sInfo": "Showing _START_ to _END_ of _TOTAL_",
							"sLengthMenu": "_MENU_ Rows per Page",
							"sInfoFiltered": " - filtering from _MAX_ records",
							"oPaginate": {
								"sPrevious": "Previous",
								"sNext": "Next"
							},
							"sLengthMenu": '<select>'+
							'<option value="10">10</option>'+
							'<option value="20">20</option>'+
							'<option value="30">30</option>'+
							'<option value="40">40</option>'+
							'<option value="50">50</option>'+
							'<option value="-1">All</option>'+
							'</select> Rows'
						}
				});

				// Binds delete version button
				$('.delete_ver').each(function() {
					$(this).on('click', function () {
						$.post('/docs/delete_single_ver/',
							{docs_id: $(this).attr('docs_id'), ver_id:$(this).attr('ver_id')
						}).success(function(){
							console.log('ok');
						});
					});
				});

				// Bind old version displays
				$('.old-ver').each(function() {
					$(this).on('click', function() {
						d.init('/docs/get_file_details/'+$(this).attr('docs_id')+'/v/'+$(this).attr('ver_id'));
					});
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
					$('.alert').show();
				});
				$('.cancel-delete').on('click', function() {
					$('.alert').hide();
				});
				$('.confirm-delete').on('click', function() {
					$.post('/docs/delete_all_docs/<?php echo $url['id_encrypted']; ?>').success(function() {
						$('.alert').hide();
						//window.location = '/docs';
					});
				});

				$('.docs-title').on('keyup', function(e) {
					if (e.keyCode === 13) {
						$.post('/docs/update_docs_title/<?php echo $this->url['id_encrypted']; ?>',
							{title:$('.docs-title').val()}
						).success(function(data) {
							console.log(data);
						})
					}
				});
			});
		}
	}
	d.init('/docs/get_file_details/<?php echo $url['id_encrypted']; ?>/v');
});
</script>