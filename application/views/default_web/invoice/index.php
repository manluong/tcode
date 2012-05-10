<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
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

<form id="frm_search" action="/invoice/search" method="post">
<div id="search_main">
	<div id="search_customer">
		<span class="block">Customer</span>
		<input type="hidden" id="customer_id" name="customer_id" />
		<input type="text" id="customer_name" name="customer_name" />
	</div>
	<div id="search_date_range">
		<span class="block">Date Range</span>
		<select name="date_range">
			<option value="">---- Select ----</option>
			<option value="1">Last Month</option>
		</select>
		<input type="text" name="date_range_from" class="datepicker" />
		<input type="text" name="date_range_to" class="datepicker" />
	</div>
	<div id="search_invoice_range">
		<span>Invoice Range</span>
		<input type="button" class="btn" value="All" />
		<input type="button" class="btn" value="Paid" />
		<input type="button" class="btn" value="Unpaid" />
		<input type="hidden" id="total_min" name="total_min" />
		<input type="hidden" id="total_max" name="total_max" />
		<div id="slider" class="clear"></div>
	</div>
</div>

<div id="search_more" class="clear">
	<a id="more_options" href="#">More Options</a>
	<table style="display: none;">
		<tr>
			<td class="first">Invoice #</td>
			<td><input type="text" name="invoice_id" /></td>
		</tr>
		<tr>
			<td class="first">PO Number</td>
			<td><input type="text" name="po_number" /></td>
		</tr>
		<tr>
			<td class="first">Notes</td>
			<td><input type="text" name="notes" /></td>
		</tr>
	</table>
</div>
<input type="submit" id="search_btn" class="btn" value="Search" />
</form>

<div id="invoice_list">
</div>