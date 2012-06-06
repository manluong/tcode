//ADD
function submit_insert_helpdesk() {
	var comment = $('#comment').val();
	var requester = $('#customer_id').val();
	var subject = $('#subject').val();
	var assigned = $('#assign_id').val();
	var id = $('#hiddenIdAdmincp').attr('value');
	var cc_email = $('#cc_email').val();

	var group = $('#group').val();
	var status = $('#status').val();
	var type = $('#type').val();
	var priority = $('#priority').val();

	var url = '/helpdesk/save_insert_helpdesk';
	//Validate input data
	
	if(subject == ''){
		alert('Please input subject !');
		return false;
	}

	if(multiEmail(cc_email) == false){
		return false;
	}

	$.post(url,{
			id : id,
			subject : subject,
			assign : assigned,
			cc_email : cc_email,
			group : group,
			status : status,
			type : type,
			priority : priority,
			requester : requester,
		},function(data){
			if(comment != ''){
				insert_comment();
			}else{
				window.location='/helpdesk/edit/'+id;
			}

		}
	);
}

function multiEmail(email_field) {
	var emailReg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	
	var email = email_field.split(';');
	for (var i = 0; i < email.length; i++) {
		if (!emailReg.test(email[i])) {
			alert('one or more email addresses entered is invalid');
			return false;
		}
	}
	return true;
} 

function insert_comment() {
	var comment = $('#comment').val();
	var id_helpdesk = $('#hiddenIdAdmincp').attr('value');
    var id_comment = $('#hiddenCommentID').attr('value');
	var group_comment = $('#group').val();
	var status_comment = $('#status').val();
	var type_comment = $('#type').val();
	var priority_comment = $('#priority').val();

	if ($('#private').is(':checked')) {
		var pri = 1;
	} else {
		var pri = 0	;
	}

	var url_comment = '/helpdesk/save_comment';

	$.post(url_comment,{
			id : id_helpdesk,
            id_comment : id_comment,
			comment: comment,
			pri: pri,
			group : group_comment,
			status : status_comment,
			type : type_comment,
			priority : priority_comment,
		},function(data){
			if (data != '') {
				window.location= '/helpdesk/edit/'+id_helpdesk;
			}
		}
	);
}

//INDEX
$(document).ready(function(){
	//Auto complete assign
	$('#assign_name').autocomplete({
		source: '/card/ajax_auto_staff',
		minLength: 2,
		select: function(e, ui) {
			$('#assign_id').val(ui.item.id);
		}
	});

	$('#assign_name').on('change', function(e) {
		$('#assign_id').val('');
	});

	//Auto complete requester
	$('#customer_name').autocomplete({
		source: '/card/ajax_auto_customer',
		minLength: 2,
		select: function(e, ui) {
			$('#customer_id').val(ui.item.id);
		}
	});

	$('#customer_name').on('change', function(e) {
		$('#customer_id').val('');
	});

	$("#arrow").click(function(){
		$("#input_data_fillter").slideToggle();

		if($('#arrow').attr('class') == 'down_arrow'){
			$('#arrow').removeClass('down_arrow');
			$('#arrow').addClass('up_arrow');
		}else{
			$('#arrow').removeClass('up_arrow');
			$('#arrow').addClass('down_arrow');
		}
	});
});

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

function helpdesk_fillter(){
	var status = $('#status').val();
	var group = $('#group').val();
	var type = $('#type').val();
	var priority = $('#priority').val();

	var url = '/helpdesk/helpdesk_fillter';

	$.post(url,{
			status : status,
			group : group,
			type : type,
			priority : priority,
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

function helpdesk_fillter_all(){

	var status = $('#status').val();
	var group = $('#group').val();
	var type = $('#type').val();
	var priority = $('#priority').val();

	var customer = $('#customer_id').val();
	var assigned = $('#assign_id').val();
	var subject = $('#subject').val();
	var comments = $('#comment_content').val();

	var url = '/helpdesk/helpdesk_fillter_all';

	$.post(url,{
			status : status,
			group : group,
			type : type,
			priority : priority,
			customer : customer,
			assigned : assigned,
			subject : subject,
			comments : comments
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