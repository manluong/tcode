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

	//Validate data input
	if(subject == ''){
		alert('Please input subject !');
		return false;
	}
	if(cc_email != ''){
		if(multiEmail(cc_email) == false){
			return false;
		}
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
		if (!emailReg.test(email[i].trim())) {
			alert('One or more email addresses entered is invalid');
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
		source: '/card/ajax_auto_all_contact',
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

function helpdesk_fillter(card_id){
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
			card_id : card_id,
		},function(data){
			var json = jQuery.parseJSON(data);
			var content = new Array();
			for (i in json) {
				 var item = json[i];
				 var row  = new Array();
				 row[0] = '<a href="/helpdesk/edit/'+item.id+'">'+item.subject+'</a>';
				 row[1] = item.cc_email;
				 row[2] = item.assign_id;
				 var date = item.created_stamp;
				 date = date.split('-');
				 row[3] = date[0]+'/'+date[1]+'/'+date[2];
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
				 var date = item.created_stamp;
				 date = date.split('-');
				 row[3] = date[0]+'/'+date[1]+'/'+date[2];
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
		"aaSorting": [[ 3, "desc" ]],
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

//HELPDESK EDIT

$(document).ready(function(){
	$("#helpdesk_change_info").click(function(){
		$("#helpdesk_show_info").hide();
		$("#input_data_info").show();
	});

	$("#helpdesk_save_info").click(function(){
		var subject = $('#subject').val();
		var assign = $('#assign_id').val();
		var cc_email = $('#cc_email').val();
		var id = $('#hiddenIdAdmincp').val();

		var url = '/helpdesk/ajaxChangeInfoHelpDesk';

		//Validate data input
		if(subject == ''){
			alert('Please input subject !');
			return false;
		}
		if(multiEmail(cc_email) == false){
			return false;
		}
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
	var id_helpdesk = $('#hiddenIdAdmincp').val() ;
	var id_comment = $('#hiddenCommentId').val() ;
	var priority = $('#priority').val();
	var group = $('#group').val();
	var status = $('#status').val();
	var type = $('#type').val();

	//Change helpdesk info
	var url1 = '/helpdesk/resave_helpdesk_info';
	$.post(url1,{
			id : id_helpdesk,
			status : status,
			priority : priority,
		},function(data){
		}
	);

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
			id : id_helpdesk,
			id_comment : id_comment,
			comment: comment,
			pri: pri,
			group : group,
			status : status,
			type : type,
			priority : priority,
		},function(data){
			//$('#wap_comment_list').html(data);
			//$('#comment').attr('value','');
			parse_comment_list(data);
		}
	);
}

function parse_comment_list(data){
	var json = jQuery.parseJSON(data);
	var html ='';
	var input_hidden = '';
	for (i in json) {
		 var item = json[i];
		 var display_name = (item.display_name == null ? '' : item.display_name);
		 var organization_name = (item.organization_name == null ? '' : item.organization_name);
		 input_hidden = '<input type="hidden" value="'+item.id+'" name="hiddenCommentId" id="hiddenCommentId" />';
		 var pri = '';
		 switch (item.priority){
			case 1:
				pri = 'Low';
				break;
			case 2:
				pri = 'Mid';
				break;
			case 3:
				pri = 'High';
				break;
		 }
		 html += '<div class="comment_info">'+
						'<div class="comment_info_user">'+
							'<div class="comment_user_avatar"><image src="/resources/template/default_web/img/helpdesk/comment_avatar.png"/></div>'+
								'<div class="comment_user_name">'+
								'<div style="color:#444444;font-size:13px;width:100%;height:14px;"><strong>'+display_name+'</strong>'+organization_name+'</div>'+
								'<div style="font-size:11px;color:#b0b0b0;">'+prettyDate(item.created_stamp)+'</div>'+
							'</div>'+
						'</div>'+
						'<div class="comment_content">'+
							'<div class="wap_comment_content">'+item.comment+'</div>'+
							'<div style="float:left;width:50px;margin:-22px 0 0 -48px" id="arrow_comment_'+item.id+'" onclick="show_detail_comment('+item.id+');" class="up_arrow"></div>'+
							'<div id="comment_detail_'+item.id+'" class="comment_detail">'+
								'<p>Priority set to '+pri+'</p>'+
								'<p>Subject set to "'+item.subject+'"</p>'+
								'<p>Email send to "'+item.cc_email+'"</p>'+
							'<br/><span style="font-size:11px;">'+
							'<p>Client: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:12.0) Gecko/20100101 Firefox/12.0</p>'+
							'<p>IP address: 115.66.148.168</p>'+
							'<p>Location: Singapore, 00, Singapore</p>'+
							'</span>'+
						'</div>'+
					'</div>'+
				'</div>';
	}
	$('#wap_comment_list').html(input_hidden+html);
	$('#comment').attr('value','');
}

function prettyDate(time){
	var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
		diff = (((new Date()).getTime() - date.getTime()) / 1000),
		day_diff = Math.floor(diff / 86400);

	if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
		return;

	return day_diff == 0 && (
			diff < 60 && "just now" ||
			diff < 120 && "1 minute ago" ||
			diff < 3600 && Math.floor( diff / 60 ) + " minutes ago" ||
			diff < 7200 && "1 hour ago" ||
			diff < 86400 && Math.floor( diff / 3600 ) + " hours ago") ||
		day_diff == 1 && "Yesterday" ||
		day_diff < 7 && day_diff + " days ago" ||
		day_diff < 31 && Math.ceil( day_diff / 7 ) + " weeks ago";
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