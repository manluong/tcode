<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="invoice_nav">
	<ul class="nav_left">
		<li class="main">Invoice</li>
		<li>&gt; New</li>
	</ul>
	<ul class="nav_right">
		<li><a href="/invoice">List</a></li>
		<li><a href="/invoice/add">New</a></li>
	</ul>
</div>

<form id="invoice_form" action="/invoice/add_save" method="post">
<div id="invoice_info">
	<ul id="form_show">
		<li>
			<span class="invoice_info_span">Customer</span>
			<select name="customer_id">
			<?php foreach ($customer as $id => $name): ?>
				<option value="<?php echo $id ?>"><?php echo $name ?></option>
			<?php endforeach ?>
			</select>
		</li>
		<li>
			<span class="invoice_info_span">Date of Issue</span>
			<input type="text" name="issue_date" class="datepicker" style="width: 200px" />
		</li>
		<li>
			<span class="invoice_info_span">Due Date</span>
			<input type="text" name="due_date" class="datepicker" style="width: 200px" />
		</li>
		<li>
			<span class="invoice_info_span">PO Number</span>
			<input type="text" />
		</li>
	</ul>
</div>

<table class="table">
<thead>
	<tr>
		<th style="width: 5%"></th>
		<th style="width: 10%">Product</th>
		<th style="width: 30%">Description</th>
		<th style="width: 10%">Unit Price</th>
		<th style="width: 10%">Qty</th>
		<th style="width: 10%">Discount</th>
		<th style="width: 10%">Tax</th>
		<th style="width: 10%">Total</th>
		<th style="width: 5%"></th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><a href="#" class="add">+</a></td>
		<td><input type="text" class="input_table"></td>
		<td><input type="text" class="input_table"></td>
		<td><input type="text" class="input_table"></td>
		<td><input type="text" class="input_table"></td>
		<td><input type="text" class="input_table"></td>
		<td><input type="text" class="input_table"></td>
		<td><input type="text" class="input_table"></td>
		<td><a href="#" class="remove">x</a></td>
	</tr>
	<!-- <tr>
		<td></td>
		<td colspan="3">
			<table class="default">
				<thead>
					<tr>
						<td>Price Type</td>
						<td>From</td>
						<td>To</td>
						<td>Duration</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Monthly Recur</td>
						<td>1 July 2012</td>
						<td>31 July 2012</td>
						<td>1 Month</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td></td>
		<td></td>
		<td></td>
	</tr> -->
</tbody>
</table>

<div id="total" class="right">
	<span>Sub Total</span>
	<br />
	<span>Tax Total</span>
	<br />
	<br />
	<span>Invoice Total</span>
	<br />
	<span>Paid</span>
	<br />
	<br />
	<span>Balance</span>
</div>
<div class="clear"></div>
<div id="terms" class="left">
	<span>Terms</span>
	<select class="right">
		<option>Pre Define Terms</option>
	</select>
	<br />
	<div class="clear"></div>
	<textarea></textarea>
</div>
<div id="terms" class="right">
	<span>Notes for Customer</span>
	<br />
	<textarea></textarea>
</div>
<input id="submit_btn" type="submit" class="btn" value="Submit" />
</form>