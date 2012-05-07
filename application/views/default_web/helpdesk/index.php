
<div id="helpdesk_list"></div>

<div id="helpdesk_view"></div>

<div id="helpdesk_form"></div>

<script>
	$(document).ready(function() {
		helpdesk_ajax_content('/helpdesk/sendjson_list', 'helpdesk_list');
		//helpdesk_ajax_content('/helpdesk/sendjson_insert_form', 'helpdesk_form');
		//helpdesk_ajax_content('/helpdesk/sendjson_view', 'helpdesk_view');
		
	});
</script>