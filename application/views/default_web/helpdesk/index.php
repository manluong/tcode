
<div id="helpdesk_list">
</div>

<div id="helpdesk_form">
</div>

<div id="helpdesk_view">
</div>
<script>
	$(document).ready(function() {
		helpdesk_ajax_content('/helpdesk/sendjson_list', 'helpdesk_list');
		
		//Add edit button
		alert('');
		$('<th class="sorting" rowspan="1" colspan="1" style="width: 45px;">Order</th>').insertAfter('.sorting:last');
		$('<td><a onclick="ajax_content("/helpdesk/sendjson_form/1/as","helpdesk_view");" href="#" class="btn btn-default">Edit</a></td>').insertAfter('.odd td:last');
		$('<td><a onclick="ajax_content("/helpdesk/sendjson_form/1/as","helpdesk_view");" href="#" class="btn btn-default">Edit</a></td>').insertAfter('.even td:last');
		
		helpdesk_ajax_content('/helpdesk/helpdesk_insert', 'helpdesk_form');
		helpdesk_ajax_content('/helpdesk/sendjson_view', 'helpdesk_view');
		
	});
</script>