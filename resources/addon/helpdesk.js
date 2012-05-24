function submit_insert_helpdesk(){
	var comment = $('#a_helpdesk_comment_comment').val();
	var subject = $('#subject').val();
	var id = $('#hiddenIdAdmincp').attr('value');
	var assign = $('#assign').val();
	var cc_email = $('#cc_email').val();
	var group = $('#a_helpdesk_comment_group').val();
	var status = $('#a_helpdesk_comment_status').val();
	var type = $('#a_helpdesk_comment_type').val();
	var priority = $('#a_helpdesk_comment_priority').val();

	var url = '<?=site_url('helpdesk/save_insert_helpdesk');?>';

	$.post(url,{
			id : id,
			subject : subject,
			assign : assign,
			cc_email : cc_email,
			group : group,
			status : status,
			type : type,
			priority : priority
		},function(data){
			if(comment != ''){
				submit_comment();
			}else{
				window.location='<?=site_url('helpdesk');?>';
			}

		}
	);
}

function submit_comment(){
	var comment = $('#a_helpdesk_comment_comment').val();
	var id_helpdesk = $('#hiddenIdAdmincp').attr('value');
        var id_comment = $('#hiddenCommentID').attr('value');
	var priority_helpdesk = $('#a_helpdesk_comment_priority').val();
	var group_helpdesk = $('#a_helpdesk_comment_group').val();
	var status_helpdesk = $('#a_helpdesk_comment_status').val();
	var type_helpdesk = $('#a_helpdesk_comment_type').val();

	if ($('#private').is(':checked')) {
		var pri = 1;
	} else {
		var pri = 0	;
	}

	var url_comment = '<?=site_url('helpdesk/save_comment');?>';

	$.post(url_comment,{
			id : id_helpdesk,
                        id_comment : id_comment,
			comment: comment,
			pri: pri,
			group : group_helpdesk,
			status : status_helpdesk,
			type : type_helpdesk,
			priority : priority_helpdesk
		},function(data){
			if (data != '') {
				window.location='<?=site_url('helpdesk');?>';
			}
		}
	);
}