<div id="helloworld_list">
</div>

<div id="helloworld_view">
</div>

<div id="helloworld_form">
</div>

<script>
	$(document).ready(function() {
		helpdesk_ajax_content('/tcode/helpdesk/sendjson_list', 'helloworld_list');
		helpdesk_ajax_content('/tcode/helpdesk/sendjson_form', 'helloworld_form');
	});
</script>