
function load_helpdesk_list(data){
	var json = jQuery.parseJSON(data);
	var content = new Array();
	for (i in json) {
		 var item = json[i];
		 var row  = new Array();
		
		 row[0] = '<a href="/helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
		 row[1] = item.cc_email;
		 row[2] = item.assign_id;
		 row[3] = item.created_stamp;
		 content.push(row);
	}
	load_datatable(content);
}

function status_fillter(){
	var value = $('#status').val();
	var url = '/helpdesk/ajax_status_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="/helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
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
	var url = '/helpdesk/ajax_group_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="/helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
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
	var url = '/helpdesk/ajax_type_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="/helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
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
	var url = '/helpdesk/ajax_priority_fillter';

	$.post(url,{
			value : value
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="/helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
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

//EDIT

$(document).ready(function(){
	$("#helpdesk_change_info").click(function(){
		$("#helpdesk_show_info").hide();
		$("#input_data_info").show();
	});
	
	$("#helpdesk_save_info").click(function(){
		var subject = $('#subject').val();
		var assign = $('#assign').val();
		var cc_email = $('#cc_email').val();
		var id = $('#hiddenIdAdmincp').val();

		var url = '/helpdesk/ajaxChangeInfoHelpDesk';
		$.post(url,{
				id : id,
				assign : assign,
				subject: subject,
				cc_email: cc_email,
			},function(data){
				$("#input_data_info").hide();
				$("#helpdesk_show_info").show();
				$('#helpdesk_show_info').html(data);
			}
		);
	});
});

function submit_comment(){
	var comment = $('#comment').val();
	var id = $('#hiddenIdAdmincp').val();
	var priority = $('#priority').val();
	var group = $('#group').val();
	var status = $('#status').val();
	var type = $('#type').val();

	if ($('#private').is(':checked')) {
		var pri = 1;
	} else {
		var pri = 0	;
	}
	if (comment == '') {
		alert('Please input comment !');
		return false;
	}
	var url = '/helpdesk/save_comment';

	$.post(url,{
			id : id,
			comment: comment,
			pri: pri,
			group : group,
			status : status,
			type : type,
			priority : priority,
		},function(data){
			$('#wap_comment_list').html(data);
			$('#comment').attr('value','');
		}
	);
}
	
function show_detail_comment(id){
	$("#comment_detail_"+id).slideToggle();
	
	if($('#arrow_comment_'+id).attr('class') == 'down_arrow'){
		$('#arrow_comment_'+id).removeClass('down_arrow');
		$('#arrow_comment_'+id).addClass('up_arrow');
	}else{
		$('#arrow_comment_'+id).removeClass('up_arrow');
		$('#arrow_comment_'+id).addClass('down_arrow');
	}
}