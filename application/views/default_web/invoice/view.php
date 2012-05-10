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

<div id="view_header">
	<div class="left"><span><?php echo '#'.$invoice['id'] ?></span></div>
	<div class="right">
		<button class="btn">Pay</button>
		<a href="/invoice/edit/<?php echo $invoice['id']?>"><button class="btn">Edit</button></a>
		<button class="btn">Send</button>
		<button class="btn">PDF</button>
		<button class="btn">Print</button>
	</div>
</div>

<div>
	<div class="left">
		<span>Your Company</span>
		<br />
		<span>Address</span>
	</div>
	<div class="right">
		<span>INVOICE</span>
	</div>
	<div class="clear"></div>
	<br />
	<div class="left">
		<span>Customer <?php echo $customer_name ?></span>
		<br />
		<span>Address</span>
	</div>
	<div class="right">
		<table>
			<tr>
				<td>Date of Issue</td>
				<td><?php echo date('Y-m-d', strtotime($invoice['invoice_stamp'])) ?></td>
			</tr>
			<tr>
				<td>Due Date</td>
				<td><?php echo date('Y-m-d', strtotime($invoice['payment_due_stamp'])) ?></td>
			</tr>
			<tr>
				<td>PO Number</td>
				<td><?php echo $invoice['purchase_order_number'] ?></td>
			</tr>
		</table>
	</div>
</div>

<br /><br /><br /><br /><br /><br />

<div id="invoice_item_list" class="clear">
	<div class="invoice_item header clear">
		<div><span>Product</span></div>
		<div class="desc"><span>Description</span></div>
		<div><span>Unit Price</span></div>
		<div><span>Qty</span></div>
		<div><span>Discount</span></div>
		<div><span>Tax</span></div>
		<div><span>Total</span></div>
		<div class="act"></div>
	</div>
	<?php foreach ($invoice_items as $invoice_item): ?>
	<div class="invoice_item clear">
		<div><?php echo $invoice_item->product_id ?></div>
		<div class="desc"><?php echo $invoice_item->description ?></div>
		<div><?php echo $invoice_item->unit_price ?></div>
		<div><?php echo $invoice_item->quantity ?></div>
		<div><?php echo $invoice_item->discount ?></div>
		<div><?php echo $invoice_item->tax_id ?></div>
		<div><?php echo $invoice_item->total ?></div>
		<div class="invoice_item_sub header clear"<?php echo ($invoice_item->price_type) ? '' : ' style="display: none;"' ?>>
			<div><span>Price Type</span></div>
			<div><span>From</span></div>
			<div><span>To</span></div>
			<div><span>Duration</span></div>
		</div>
		<div class="invoice_item_sub clear"<?php echo ($invoice_item->price_type) ? '' : ' style="display: none;"' ?>>
			<div><?php echo $invoice_item->price_type ?></div>
			<div><?php echo date('Y-m-d', strtotime($invoice_item->subscription_start_stamp)) ?></div>
			<div><?php echo date('Y-m-d', strtotime($invoice_item->subscription_end_stamp)) ?></div>
			<div><?php echo $invoice_item->duration_type ?></div>
		</div>
	</div>
	<?php endforeach ?>
</div>

<br /><br /><br /><br /><br /><br />

<div id="total" class="right">
	<span class="left">Sub Total</span><span class="right"></span>
	<br />
	<span class="left">Tax Total</span><span class="right"></span>
	<br />
	<br />
	<span class="left">Invoice Total</span><span class="right"></span>
	<br />
	<span class="left">Paid</span>
	<br />
	<br />
	<span class="left">Balance</span>
</div>

<div class="clear">
	<div class="terms left">
		<span>Terms</span>
		<br />
		<?php echo $invoice_terms ?>
	</div>
	<div class="terms right">
		<span>Notes for Customer</span>
		<br />
		<?php echo $invoice['memo'] ?>
	</div>
</div>

<div class="div_btn clear">
</div>