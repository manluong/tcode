var req_search;
function search_product() {
	if ($('#frm_search').length == 0) return;
	if (req_search) req_search.abort();

	req_search = $.ajax({
		type: 'POST',
		url: $('#frm_search').attr('action'),
		data: $('#frm_search').serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#loader').show();
			$('#product_list').hide();
		},
		complete: function() {
			$('#loader').hide();
			$('#product_list').show();
			$('#tbl_product').css('width', '100%');
		},
		success: function(resp) {
			if (!resp.success) return;

			var data = new Array();
			for (i in resp.details) {
				var item = resp.details[i];
				var name = '';
				if (item.type == 'category') {
					name = '<a href="'+item.list_url+'"><strong>'+item.name+'</strong></a>';
				} else {
					name = item.name;
				}
				var row = new Array();
				row[0] = item.type;
				row[1] = name;
				row[2] = item.price;
				row[3] = '<a href="'+item.view_url+'">View</a>';
				data.push(row);
			}

			$('#tbl_product').dataTable({
				"bDestroy" : true,
				"aaData": data,
				"aaSorting": [[0, "asc"], [1, "asc"]],
				"aoColumns": [
					{ "sTitle": "Type" },
					{ "sTitle": "Name" },
					{ "sTitle": "Price" },
					{ "sTitle": "Detail" }
				],
				"aoColumnDefs": [{"bSearchable": false, "bVisible": false, "aTargets": [0]}],
				"sDom": "<<'pull-right'p>>t<<'pull-right'p>lfi>",
				"sPaginationType": "bootstrap",
				"iDisplayLength": 10,
				"oLanguage": {
					"sSearch" : "<div class=\"input-prepend\"><span class=\"add-on\"><i class=\"icon-filter\"></i></span></i>_INPUT_</div>",
					"sInfo": "_START_ to _END_ of _TOTAL_",
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
					'<option value="50">50 Rows</option>'+
					'<option value="-1">All</option>'+
					'</select>'
			}});
		}
	});
}

$(document).ready(function() {
	$('#product_radio input[type=radio]').on('click', function() {
		$('.product_ul').hide();
		$('#'+$(this).data('name')).show();
	});

	$('#btn_submit').click(function() {
		var frm = null;
		if ($('#product_radio').length > 0) {
			frm = $('#frm_'+$('#product_radio input[type=radio]:checked').data('name'));
		} else {
			frm = $(this).closest('form');
		}
		$.ajax({
			type: 'POST',
			url: $(frm).attr('action'),
			data: $(frm).serialize(),
			dataType: 'json',
			success: function(resp) {
				if (resp.success) {
					document.location.href = resp.details;
				} else {
					alert(resp.message);
				}
			}
		});
		return false;
	});

	search_product();
});