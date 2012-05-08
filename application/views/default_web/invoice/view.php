<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="invoice_nav">
	<ul class="nav_left">
		<li class="main">Invoice</li>
		<li>&gt; View</li>
	</ul>
	<ul class="nav_right">
		<li><a href="#">Dashboard</a></li>
		<li><a href="/invoice">List</a></li>
		<li><a href="/invoice/add">New</a></li>
	</ul>
</div>

<div>
	<?php echo '#'.$invoice['id'] ?>
	<a href="#" >Pay</a>
	<a href="/invoice/edit/<?php echo $invoice['id']?>" >Edit</a>
	<a href="#" >Send</a>
	<a href="#" >PDF</a>
	<a href="#" >Print</a>
</div>

<div id="invoice_info">
	<ul id="form_show">
		<li>
			<span class="invoice_info_span">Customer</span>
			<?php echo $invoice['customer_card_id'] ?>
		</li>
		<li>
			<span class="invoice_info_span">Date of Issue</span>
			<?php echo $invoice['invoice_stamp'] ?>
		</li>
		<li>
			<span class="invoice_info_span">Due Date</span>
			<?php echo $invoice['payment_due_stamp'] ?>
		</li>
		<li>
			<span class="invoice_info_span">PO Number</span>
			<?php echo $invoice['purchase_order_number'] ?>
		</li>
		<li>
			<span class="invoice_info_span">Tax</span>
			<?php echo $invoice['tax_id'] ?>
		</li>
		<li>
			<span class="invoice_info_span">Currency</span>
			<?php echo $invoice['currency'] ?>
		</li>
	</ul>
</div>

<table class="table">
<thead>
	<tr>
		<th style="width: 10%">Product</th>
		<th style="width: 30%">Description</th>
		<th style="width: 10%">Unit Price</th>
		<th style="width: 10%">Qty</th>
		<th style="width: 10%">Discount</th>
		<th style="width: 10%">Tax</th>
		<th style="width: 10%">Total</th>
	</tr>
</thead>
<tbody>
	<?php foreach ($invoice_items as $invoice_item): ?>
	<tr>
		<td><?php echo $invoice_item->product_id ?></td>
		<td><?php echo $invoice_item->description ?></td>
		<td><?php echo $invoice_item->unit_price ?></td>
		<td><?php echo $invoice_item->quantity ?></td>
		<td><?php echo $invoice_item->discount ?></td>
		<td><?php echo $invoice_item->tax_id ?></td>
		<td><?php echo $invoice_item->total ?></td>
	</tr>
	<?php endforeach ?>
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
	<br />
	aaaaaaaaaaaaaaaaaaaaaaaaa
</div>
<div id="terms" class="right">
	<span>Notes for Customer</span>
	<br />
	bbbbbbbbbbbbbbbbbbbbbbbbb
</div>
