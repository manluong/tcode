<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/base.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/bootstrap.min.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/invoice.css" />

<br /><br /><br /><br />

<div id="invoice_detail" style="margin: 0 auto; width: 860px">
<div>
	<div id="view_company" class="left">
		<span><strong>YOUR COMPANY NAME</strong></span>
		<br />
		<span>
			123 Ways, Sun City<br />
			State, US
		</span>
	</div>
	<div id="invoice_header" class="left">
		<span>INVOICE</span>
	</div>
	<div class="clear"></div>
	<br />
	<div style="width: 100%;">
		<div style="width: 100%;"><span>BILL TO</span></div>
		<div id="view_customer" class="left">
			<strong>Facebook Inc</strong><br />
			<strong><?php echo $customer_name ?></strong><br />
			7 Temasek Bouleverd<br />
			10-4342<br />
			Singapore
		</div>
		<div id="view_detail" class="right">
			<table style="width: 100%;">
				<tr>
					<td>Invoice #</td>
					<td><strong><?php echo '#'.$invoice['id'] ?></strong></td>
				</tr>
				<tr>
					<td>Date</td>
					<td><strong><?php echo date('Y-m-d', strtotime($invoice['invoice_stamp'])) ?></strong></td>
				</tr>
				<tr>
					<td>Due Date</td>
					<td><strong><?php echo date('Y-m-d', strtotime($invoice['payment_due_stamp'])) ?></strong></td>
				</tr>
				<tr>
					<td>Purchase Order</td>
					<td><strong><?php echo $invoice['purchase_order_number'] ?></strong></td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div class="clear"></div>
<br /><br />

<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" style="width: 100%">
	<thead style="background-color: #DDDDDD;">
		<tr>
			<th style="width: 25%;">Product</th>
			<th style="width: 45%;">Description</th>
			<th style="width: 10%;">Unit Price</th>
			<th style="width: 10%;">Qty</th>
			<th style="width: 10%;">Total</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($invoice_items as $invoice_item): ?>
		<tr>
			<td><?php echo $invoice_item->a_product_name ?></td>
			<td>
				<?php echo $invoice_item->description ?>
				<?php if ($invoice_item->subscription_start_stamp): ?>
					<br />
					<?php echo date('Y-m-d', strtotime($invoice_item->subscription_start_stamp)).' to '.date('Y-m-d', strtotime($invoice_item->subscription_end_stamp)) ?>
				<?php endif ?>
				<?php if ($invoice_item->a_product_durationtype_name): ?>
					<br />
					<?php echo $invoice_item->a_product_durationtype_name ?>
				<?php endif ?>
				<?php if ($invoice_item->a_product_pricetype_name): ?>
					<br />
					<?php echo $invoice_item->a_product_pricetype_name ?>
				<?php endif ?>
				<?php if ($invoice_item->discount): ?>
					<br />
					<?php echo $invoice_item->discount.'% Discount' ?>
				<?php endif ?>
			</td>
			<td><?php echo '$'.number_format($invoice_item->unit_price, 2) ?></td>
			<td><?php echo $invoice_item->quantity ?></td>
			<td><?php echo '$'.number_format($invoice_item->total, 2) ?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>

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
	<span><strong>Terms</strong></span>
	<br />
	<?php echo $invoice_terms ?>
	<br /><br />
	<span><strong>Notes for Customer</strong></span>
	<br />
	<?php echo $invoice['memo'] ?>
</div>

<div class="div_btn clear">
</div>
</div>
