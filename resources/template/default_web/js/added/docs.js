$(document).ready(function() {
	var docs = function () {
		var display_errors_state = false;
		var right_click_value = 0;

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

		$('#create-folder, #context-create-folder').click(function() {
			$('tr[type="add-folder"]').show();
			$('#folder-name').val('').focus();
		});

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

		}); */
		blind_dblClick_folder();
	}
	docs();
});