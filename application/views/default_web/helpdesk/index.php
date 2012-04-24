<div id="helloworld_list">
</div>

<div id="helloworld_view">
</div>

<div id="helloworld_form">
</div>

<script>
	$(document).ready(function() {
		ajax_content('http://apple.local.net/tcode/helpdesk/sendjson_list', 'helloworld_list');
		ajax_content('http://apple.local.net/tcode/helpdesk/sendjson_view', 'helloworld_view');
		ajax_content('http://apple.local.net/tcode/helpdesk/sendjson_form', 'helloworld_form');
	});
</script>