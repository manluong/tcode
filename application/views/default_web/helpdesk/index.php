<script type="text/javascript" src="/tcode/resources/addon/helpdesk.js"></script>

<div id="helpdesk_list">
</div>

<div id="helpdesk_form">
</div>

<div id="helpdesk_view">
</div>
<script>
	$(document).ready(function() {
		helpdesk_ajax_content('/<?=PATH_URL?>/helpdesk/sendjson_list', 'helpdesk_list');
		helpdesk_ajax_content('/<?=PATH_URL?>/helpdesk/helpdesk_insert', 'helpdesk_form');
		helpdesk_ajax_content('/<?=PATH_URL?>/helpdesk/sendjson_view', 'helpdesk_view');
	});
</script>