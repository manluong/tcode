
function load_helpdesk_list(data){
	var json = jQuery.parseJSON(data);
	var content = new Array();
	for (i in json) {
		 var item = json[i];
		 var row  = new Array();
		
		 row[0] = '<a href="helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
		 row[1] = item.cc_email;
		 row[2] = item.assign_id;
		 row[3] = item.created_stamp;
		 content.push(row);
	}
	load_datatable(content);
}

function status_fillter(){
	var value = $('#status').val();
	var url = 'helpdesk/ajax_status_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
				 row[1] = item.cc_email;
				 row[2] = item.assign_id;
				 row[3] = item.created_stamp;
				 content.push(row);
			}
			load_datatable(content);
		}
	);
}

function group_fillter(){
	var value = $('#group').val();
	var url = 'helpdesk/ajax_group_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
				 row[1] = item.cc_email;
				 row[2] = item.assign_id;
				 row[3] = item.created_stamp;
				 content.push(row);
			}
			load_datatable(content);
		}
	);
}

function type_fillter(){
	var value = $('#type').val();
	var url = 'helpdesk/ajax_type_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
				 row[1] = item.cc_email;
				 row[2] = item.assign_id;
				 row[3] = item.created_stamp;
				 content.push(row);
			}
			load_datatable(content);
		}
	);
}

function priority_fillter(){
	var value = $('#priority').val();
	var url = 'helpdesk/ajax_priority_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
				 row[1] = item.cc_email;
				 row[2] = item.assign_id;
				 row[3] = item.created_stamp;
				 content.push(row);
			}
			load_datatable(content);
		}
	);
}
	
function load_datatable(data){
	$('#example').dataTable( {
		"sDom": "<<'pull-right'p>>t<<'pull-right'p>lfi>",
		"sPaginationType": "bootstrap",
		"iDisplayLength": 10,
		"bDestroy": true,
		"aaData": data,
		"aoColumns": [
			{ "sTitle": "Subject" },
			{ "sTitle": "CC Email" },
			{ "sTitle": "Assign ID" },
			{ "sTitle": "Created" },
		],
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
	}})
}
