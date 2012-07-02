var req_search;
function search_phone() {
        if ($('#frm_search').length == 0) return;
        if (req_search) req_search.abort();
    
        req_search = $.ajax({
		type: 'POST',
		url: $('#frm_search').attr('action'),
		data: $('#frm_search').serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#loader').show();
			$('#phone_list').hide();
		},
		complete: function() {
			$('#loader').hide();
			$('#phone_list').show();
			$('#tbl_phone').css('width', '100%');
		},
		success: function(resp) {
			if (!resp.success) return;
                        
			var data = new Array();
			for (i in resp.details) {
				var item = resp.details[i];
				var row = new Array();
				row[0] = item.did_cdr_did;
                                row[1] = item.did_cdr_numorg;
				row[2] = item.did_cdr_start;
                                row[3] = item.did_cdr_duration;
                                if (item.did_cdr_inout == 0){ 
                                    row[4] = 'Incoming Fax';
                                }else if(item.did_cdr_inout == 1){
                                    row[4] = 'Incoming Call'
                                }else if(item.did_cdr_inout == 2){
                                    row[4] = 'Incoming Voicemail';
                                }else if(item.did_cdr_inout == 3){
                                    row[4] = 'Outgoing Fax';
                                }else if(item.did_cdr_inout == 4){
                                    row[4] = 'Outgoing Call';
                                }else if(item.did_cdr_inout == 5){
                                    row[4] = 'Outgoing Voicemail';
                                }
                                
                                if ((item.did_cdr_inout == 0) || (item.did_cdr_inout == 3)) { 
                                    row[5] = '<a href="#">View Fax</a>';
                                }else if ((item.did_cdr_inout == 1) || (item.did_cdr_inout == 4) 
                                    || (item.did_cdr_inout == 2) || (item.did_cdr_inout == 5)){
                                    row[5] = 'Listen'
                                    row[5] = '<a href="#">Listen</a>';
                                }
				data.push(row);
			}

			$('#tbl_phone').dataTable({
				"bDestroy" : true,
				"aaData": data,
				"aoColumns": [
					{ "sTitle": "From" },
					{ "sTitle": "" },
					{ "sTitle": "Time/Duration" },
					{ "sTitle": "" },
					{ "sTitle": "Call Type" },
                                        { "sTitle": "" },
				],
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
        $('#date_range').on('change', function(e) {
		if ($(this).val() == 1) {
			now = new Date();
			$('#date_range_from').val($.datepicker.formatDate('yy-mm-dd', new Date(now.getFullYear(), now.getMonth() - 1, 1)));
			$('#date_range_to').val($.datepicker.formatDate('yy-mm-dd', new Date(new Date(now.getFullYear(), now.getMonth(), 1) - 1)));
		} else {
			$('#date_range_from').val('');
			$('#date_range_to').val('');
		}
		search_phone();
	});
        
        $('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		onSelect: function() {
			search_phone();
		}
	});
        
        $('#inout-group button').on('click', function() {
		$('#inout').val($(this).data('value'));
		search_phone();
	});
        
        $('#calltype-group button').on('click', function() {
		$('#calltype').val($(this).data('value'));
		search_phone();
	});
        
        search_phone();
})