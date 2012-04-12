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

					<div id="tree" class="alert">
						Move<br>
						<select id="dir_select"></select>
						<a class="move btn btn-danger">Move</a>
						<a class="btn">Cancel</a>
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
				var doc_details = data['details']['docs_details'];
				var doc_versions = data['details']['versions'];
				var title_input_value = doc_details['display_name'];

				$('#title_input').attr('value', title_input_value);

				// Sets display
				if (doc_details['mime'] === 'application/pdf') {
					$('.preview-content .content').html('\
						<div id="viewerPlaceHolder" style="width:100%;height:553px;display:block;position:relative;">\
							<div id="documentViewer" class="viewer"></div>\
						</div>\
					');
					var startDocument = doc_details['hash'];
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

				if (doc_details['mime'] === 'image/png' || doc_details['mime'] === 'image/jpeg' || doc_details['mime'] === 'image/gif' ) {
					$('.preview-content .content').html('<img src="" id="image_placeholder">');
					$('#image_placeholder').attr('src', '/file/read/'+doc_details['hash']);
					$('#viewerPlaceHolder').hide();
				}

				$('#download').attr('href', '/docs/download_file/'+doc_details['id']+'/download');
				// File info
				$('.filename').html(doc_details['file_name']);
				$('.filesize').html(doc_details['file_size']);
				$('.date').html(doc_details['created_stamp']);
				$('.user-name').html(doc_details['created_card_info']['card_fname']+' '+doc_details['created_card_info']['card_lname']);

				var aaData = Array();
				for (var i=0;i<doc_versions.length;i++) {
					aaData[i] = [
						'<a href="#" docs_id="'+doc_details['id']+'" hash="'+doc_versions[i]['hash']+'" class="old-ver">'
							+ doc_versions[i]['file_name']
							+ '</a>',
						doc_versions[i]['file_size'],doc_versions[i]['created_stamp'],
						'<a class="btn btn-danger delete_ver" docs_id = "'+doc_details['id']+'" hash="'+doc_versions[i]['hash']+'">\n\
							Delete</a>'
					];
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
				$('.delete_ver').on('click', function () {
					$.post('/docs/ajax_delete_version/',
						{ hash:$(this).attr('hash') },
						function(resp) {
							if (resp.success) {
								console.log('ok');
							} else {
								console.log('not ok');
							}
						},
						'json'
					);
				});

				// Bind old version displays
				$('.old-ver').on('click', function() {
					d.init('/docs/ajax_get_file_details/'+$(this).attr('docs_id')+'/v/'+$(this).attr('hash'));
				});

				// bind upload
				var uploader = new plupload.Uploader({
					runtimes: "html5, flash",
					browse_button: "upload",
					url: "/docs/ajax_overwrite/"+doc_details['id'],
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

			$.get('/docs/ajax_dir_tree/0/v').success(function (data) {
				/* old tree
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
				}*/
				function hypen(depth) {
					var hypen = '';
					for(var i=0;i<depth;i++) {
						hypen += '-';
					}
					return hypen;
				}
				var html = '';
				var depth = '';
				function generate_html(data, d, index){
					depth = d;
					for(var i=0;i<data.length;i++) {
						html += '<option value="'+data[i]['id']+'">'+hypen(depth)+' '+data[i]['name']+'</option>';
						if (data[i].hasOwnProperty('child')) {
							depth++;
							generate_html(data[i]['child'], depth, i);
						} else {
							depth = 0;
						}
					}
					return html;
				}
				/*
				function generate_child_html(html, data, depth) {
					depth++;
					for(var i=0;i<data.length;i++) {
						html += '<option value="'+data[i]['a_docs_id']+'">'+hypen(depth) + data[i]['a_docs_displayname']+'</option>';

						if (data[i].hasOwnProperty('child')) {
							generate_child_html(html, data[i]['child'], depth);
						}
					}
					return html;
				}*/
				//old tree $('#tree').html(generate_html(data, 0));
				$('#dir_select')
					.html('<option value="0">/</option>')
					.append(generate_html(data,0, 0));


				$('.move').on('click', function () {
					/*$.post('/docs/ajax_move_file/<?php echo $url['id_encrypted']; ?>/',{folder_id: $(this).attr('folder_id')})
						.success(function(data) {});
				});*/
					$.post('/docs/ajax_move_file/<?php echo $url['id_encrypted']; ?>/',{folder_id: $('#dir_select').val()})
						.success(function(data) {});
				});

				$('#delete').on('click', function () {
					$('.alert').show();
				});
				$('.cancel-delete').on('click', function() {
					$('.alert').hide();
				});
				$('.confirm-delete').on('click', function() {
					$.post('/docs/ajax_delete_file/<?php echo $url['id_encrypted']; ?>').success(function() {
						$('.alert').hide();
						//window.location = '/docs';
					});
				});

				$('.docs-title').on('keyup', function(e) {
					if (e.keyCode === 13) {
						$.post(
							'/docs/ajax_update_docs_display_name/<?php echo $this->url['id_encrypted']; ?>',
							{
								title: $('.docs-title').val()
							},
							function(resp) {
								if (resp.success) {
									//success
								} else {

								}
							},
							'json'
						);
					}
				});
			});
		}
	}
	d.init('/docs/ajax_get_file_details/<?php echo $url['id_encrypted']; ?>/v');
});
</script>