<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="invoice_list">
</div>

<div id="invoice_view">
</div>

<div id="invoice_new">
</div>

<script>
	$(document).ready(function() {
		ajax_content('invoice/sendjson_list', 'invoice_list');
		ajax_content('invoice/sendjson_view/1', 'invoice_view');
		//ajax_content('invoice/sendjson_new', 'invoice_new');
	});
</script>