<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/resources/addon/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<link href="/resources/addon/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" rel="stylesheet" type="text/css" />

<script>
	var directory_contents = '';

	$(document).ready(function() {
		directory_contents = $('#directory_contents').dataTable({
				"bDestory": true,
				"sAjaxSource": "/docs/ajax_get_dir_contents/<?php echo $url['id_encrypted']; ?>/v",
				"sDom": "<<'pull-right'p>>t<<'pull-right'p>li>",
				"sPaginationType": "bootstrap",
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
					'</select> Rows'}
		});

		$("#uploader").pluploadQueue({
			// General settings
			runtimes : 'html5,flash',
			url : '/docs/ajax_upload/<?php echo $url['id_encrypted'] ?>',
			max_file_size : '10mb',
			unique_names : true,
			multiple_queues : true,

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
			silverlight_xap_url : '/resources/addon/plupload/js/plupload.silverlight.xap',

			init: {
				FileUploaded: function() {
					directory_contents.fnReloadAjax();
				}
			}
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
	$.extend($.fn.dataTableExt.oStdClasses, {
		"sSortAsc": "header headerSortDown",
		"sSortDesc": "header headerSortUp",
		"sSortable": "header"
	});

	$('#create-folder, #context-create-folder').click(function() {
		if ($('#name').val() !== '') {
			$.post(
				'/docs/ajax_create_folder/<?php echo $url['id_encrypted']; ?>/',
				{
					cardid: "<?php echo $this->UserM->get_card_id(); ?>",
					name: $('#name').val(),
				},
				function(resp) {
					directory_contents.fnReloadAjax();
				},
				'json'
			);
		}
	});
	/*
	var docs = {
		display_errors_state: false,
		prev_title: $('.docs-title').val(),

		run: function() {
			var context_menu = '<ul id="context-menu"> \
					<li><a id="context-create-folder" id="context-create-folder">Create Folder</a></li> \
					<li><a id="context-delete">Delete...</a></li> \
					<li><a id="context-settings">Settings...</a></li> \
				</ul>';

			$('body').prepend(context_menu);

			$(document).click(function(e) {
				if ( ! (e.target.id === 'tick-icon' || e.target.id === 'create-folder'
					|| e.target.id === 'folder-name'
					|| e.target.id === 'context-create-folder')) {
					$('#context-menu').hide();
					$('tr[type="add-folder"]').hide();
					reset_errors();
					//console.log('reset ran');
				}
			});

			function reset_errors() {
				$('.error').html('');
				docs.display_errors_state = false;
			}

			function display_errors(msg) {
				if (docs.display_errors_state !== true) {
					$("#folder-name").after('<span class="error">'+msg+'</span>');
					docs.display_errors_state = true;
				} else {
					if ($('.error').html() !== msg) {
						$('.error').html(msg);
					}
				}
			}

			function create_folder() {
			var pos = document.URL.indexOf('docs/');
			var url = document.URL.slice(pos);
			var url_arr = url.split('/');
			var id = url_arr[2];
			$.get('/docs/create_folder/'+id+'/create?name='+$('#folder-name').val())
				.success(function (data) {
					if (data.id) {
						var newRow = '<tr type="folder"><td class="col1 folder-td" value="'+data.id+'">'+data.folder_icon+' '+data.name+
							'</td><td class="col2"> -- </td><td class="col3"> -- </td></tr>';
						if ($('tr[type="folder"]').length !== 0) {
							$('tr[type="folder"]:first').before(newRow);
						} else {
							$('tr[type="started"]').replaceWith(newRow)
						}


						$('tr[type="add-folder"]').hide();
						$('#tick-icon, #cross-icon').show();
						$('#folder-name').val('');
					} else {
						$('#tick-icon, #cross-icon').hide();
						$('#error-icon').show();
					}
					blind_dblClick_folder();

				})
				.error(function(data) {
					$('#tick-icon, #cross-icon').hide();
					$('#error-icon').show();
				});
			}
			function validate() {
				var pattern = /^[a-zA-Z0-9_]*$/;
				var basic = pattern.exec($('#folder-name').val());
				var pattern = /^root$/;
				var root_test =  pattern.exec($('#folder-name').val());

				if (root_test !== null) {display_errors(' Root is not allowed');}
				if (basic === null) {display_errors(' Only alpahnumeric and -, _ accepted');}

				if (basic === null || root_test !== null) return null;
				else return '';

			}

			function validate_basic(value) {
				var pattern = /^[a-zA-Z0-9_\-\.]*$/;
				var basic = pattern.exec(value);
				if (basic === null) return false;
				return true;
			}

			function init_send() {
				if ($('#folder-name').val() == '') {
					display_errors('Please enter a name');
				}
				if (validate() !== null) {
					$('#tick-icon, #cross-icon').hide();
					$('#loader-icon').show();
					create_folder();
				}
				$('#loader-icon').hide();
			}

			function set_right_click_value(e) {
				docs.right_click_value = $(e.target).attr('value');
			}

			function blind_dblClick_folder() {
				$('.folder-td').each(function () {
					$(this).dblclick(function(e) {
						window.location = '/docs/view/'+$(e.target).attr('value')+'/list-view';
					})
				});
			}

			function change_title(title, e) {
				if (title === docs.prev_title) {
					return;
				}
				if (validate_basic(title)) {
					$.get('/docs/update_docs_title',
						{title: title, id: e.id}
					).success(function (data) {
						data = JSON.parse(data);
						$('.message').html(data.message).fadeIn().delay(1500).fadeOut();
					});
				} else {
					console.log('unallowed characters');
				}

			}

			$('#create-folder, #context-create-folder').click(function() {
				if ($('#name').val() !== '') {
					$.post('/docs/ajax_create_folder/<?php echo $url['id_encrypted']; ?>/',
						{
						id: "<?php echo $this->url['id_encrypted']; ?>",
						cardid: "<?php echo $this->UserM->info['cardid']; ?>",
						name: $('#name').val(),
						},
						function(data) {
							console.log(data);
					});
				}
			});

			// Context Menu bindings
			$('.folder-td').bind("contextmenu",function(e){
			e.preventDefault();
			set_right_click_value(e);
			$('#context-menu').show();
			$('#context-menu').css('top', e.pageY).css('left', e.pageX);
			});


			$('#context-menu a').click(function() {
				$('#context-menu').hide();
			});

			$('#context-delete').click(function(e) {
				console.log(docs.right_click_value);
			});

			$('#context-settings').click(function(e) {
				console.log(docs.right_click_value);
			});

			// Bind docs title
			$('.docs-title').on('click', function () {
				$(this).select();
			});
			$('.docs-title').on('blur', function (e) {
				change_title($('.docs-title').val(), e.target);
			})
			$('.docs-title').on('keypress', function (e) {
				if (e.keyCode === 13) {
					post_change_title($('.docs-title').val(), e.target);
				}
			})
			$('.docs-title').on('keydown', function(e) { //keypress doesn't work in chrome for escape
				if (e.keyCode === 27) { // bind escape
					$('.docs-title').val(docs.prev_title);
				}
			});

			//File operations
			$("#confirm").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
					"Confirm" : function() {
						$(this).dialog("close");
						$.get('/docs/delete_object?id='+$('#docs_id').val(), function() {

						}).success(function(data){

						}).error(function(data) {
							console.log('Error deleting file');
						});
					},
					"Cancel" : function() {
						$(this).dialog("close");
					}
				}
			});

			/*
			$('#cross-icon').click(function() {
				reset_errors();
				$('tr[type="add-folder"]').hide(function () {
				});
			});

			$('#folder-name').keydown(function(e){
				if (e.keyCode === 13) { //bind enter key
					init_send();
				}
			});

			$('#tick-icon, #error-icon').click(function() {
				init_send();
			});

			$('#delete').on('click', function() {
				//$("#confirm").dialog("open");
				var user_response = confirm("All files and versions will be removed.");
				if (user_response) {
					$.get('/docs/delete_docs?id='+$('#docs_id').val(), function() {

					}).success(function(data){
						console.log(data);
					}).error(function(data) {
						console.log('Error deleting file');
					});
				}
			});

			$('#filetree').dialog({
				autoOpen: false
			});

			$('#move').on('click', function() {
				$('#filetree').dialog('open');
			});

			$('.tree-folders').on('click', function(e) {
				console.log('id:%s', $(this).attr('folder_id'));
				$.get('/docs/move_file',
					{folder_id: $(this).attr('folder_id'), docs_id:$('#docs_id').val(),
					ver_id:$("#ver_id").val()
				},function() {

				}).success(function (data) {
					console.log(data);
				}).error(function() {

				});
				$('#filetree').dialog('close');
				e.stopPropagation();
			}); */




			/* $('#list-view').dataTable({
				"bAutoWidth": false,
				"bJQueryUI": false,
				"bProcessing": true,
				"sPaginationType": "full_numbers",
				"fnDrawCallback": function() {
					formui_reload();
				},
				"bPaginate": false,
				"bLengthChange": false,
				"bFilter": false,
				"bSort": false,
				"bInfo": false,
				"bAutoWidth": false

			});
			blind_dblClick_folder();
		}
	}
	docs.run();*/
</script>