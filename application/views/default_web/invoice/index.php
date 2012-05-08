<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/invoice.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="invoice_nav">
	<ul class="nav_left">
		<li class="main">Invoice</li>
		<li>&gt; List</li>
	</ul>
	<ul class="nav_right">
		<li><a href="#">Dashboard</a></li>
		<li><a href="/invoice">List</a></li>
		<li><a href="/invoice/add">New</a></li>
	</ul>
</div>

<div id="invoice_list">
</div>

<div id="invoice_edit">
</div>

<script>
	$(document).ready(function() {
		ajax_content('invoice/sendjson_list', 'invoice_list');
		//ajax_content('invoice/sendjson_edit/1', 'invoice_edit');
	});
</script>