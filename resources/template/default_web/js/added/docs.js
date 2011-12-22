$(document).ready(function() {
	function create_folder() {
		$.get('/docs/create_folder?name='+$('#folder-name').val())
			.success(function (data) {
				if (data.id) {
					var newRow = '<tr type="folder"><td>'+data.folder_icon+' '+data.name+"</td><td> -- </td><td> -- </td></tr>";
					$('tr[type="folder"]:first').before(newRow);
					$('tr[type="add-folder"]').hide();
					$('#tick-icon, #cross-icon').show();
					$('#folder-name').val('');
				} else {
					$('#tick-icon, #cross-icon').hide();
					$('#error-icon').show();
				}

			})
			.error(function(data) {
				$('#tick-icon, #cross-icon').hide();
				$('#error-icon').show();
			});
	}
	function validate() {
		var pattern = /^[a-zA-Z0-9_]*$/;
		return pattern.exec($('#folder-name').val());
	}

	function init_send() {
		if (validate() !== null) {
			$('#tick-icon, #cross-icon').hide();
			$('#loader-icon').show();
			create_folder();
		} else {
			$("#folder-name").after('<span class="error">Only alpahnumeric and -, _ accepted</span>');
		}
		$('#loader-icon').hide();
	}

	$('#create-folder, #cross-icon').click(function() {
		$('#folder-name').val('').focus();
		$('tr[type="add-folder"]').toggle();
	});

	$('#folder-name').keydown(function(e){
		if (e.keyCode === 13) { //bind enter key
			init_send();
		}
	});

	$('#tick-icon, #error-icon').click(function() {
		init_send();
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
});