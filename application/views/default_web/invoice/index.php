<script type="text/javascript" src="/resources/addon/helpdesk.js"></script>

<div id="invoice_list">
</div>

<div id="invoice_form">
</div>

<div id="invoice_view">
</div>

<script>
	$(document).ready(function() {
		helpdesk_ajax_content('/invoice/sendjson_list', 'invoice_list');
		helpdesk_ajax_content('/invoice/helpdesk_insert', 'invoice_form');
		helpdesk_ajax_content('/invoice/sendjson_view', 'invoice_view');
	});
</script>