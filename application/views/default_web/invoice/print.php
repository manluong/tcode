<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/style.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/bootstrap.min.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/all-module.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/invoice.css" />

<div id="boxes" class="clearfix">
	<div id="invoice_container" class="clearfix">
		<div id="invoice_detail">
			<div id="invoice_header" class="clearfix">
				<div id="company_info">
					<div id="company_name">YOUR COMPANY NAME</div>
					<div id="company_address">
						123 Ways, Sun City<br />
						State, US
					</div>
				</div>
				<div id="invoice_title">INVOICE</div>
			</div>
			<div id="invoice_main" class="clearfix">
				<div id="bill_to">BILL TO</div>
				<div id="customer_info">
					<div id="customer_name">
						Facebook Inc<br />
						<?php echo $invoice['card_info']['nickname'] ?>
					</div>
					<div id="customer_address">
						7 Temasek Bouleverd<br />
						10-4342<br />
						Singapore 123123
					</div>
				</div>
				<table id="invoice_info">
					<tr>
						<th>Invoice#</th>
						<td><?php echo $invoice['id'] ?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo date('Y-m-d', strtotime($invoice['invoice_stamp'])) ?></td>
					</tr>
					<tr>
						<th>Due Date</th>
						<td><?php echo date('Y-m-d', strtotime($invoice['payment_due_stamp'])) ?></td>
					</tr>
					<tr>
						<th>Purchase Order</th>
						<td><?php echo $invoice['purchase_order_number'] ?></td>
					</tr>
				</table>
			</div>
		</div>

		<table id="invoice_item">
			<thead>
				<tr>
					<th style="width: 220px;">Product</th>
					<th style="width: 360px;">Description</th>
					<th style="width: 85px;">Unit Price</th>
					<th style="width: 70px;">Qty</th>
					<th style="width: 85px;">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($invoice['addon_item'] as $invoice_item): ?>
				<tr>
					<td><?php echo $invoice_item['a_product_name'] ?></td>
					<td>
						<?php echo $invoice_item['description'] ?>
						<div class="sub_desc">
							<?php if ($invoice_item['subscription_start_stamp']): ?>
								<?php echo date('Y-m-d', strtotime($invoice_item['subscription_start_stamp'])).' to '.date('Y-m-d', strtotime($invoice_item['subscription_end_stamp'])) ?>
								<br />
							<?php endif ?>
							<?php if ($invoice_item['a_product_durationtype_name']): ?>
								<?php echo $invoice_item['a_product_durationtype_name'] ?>
								<br />
							<?php endif ?>
							<?php if ($invoice_item['a_product_pricetype_name']): ?>
								<?php echo $invoice_item['a_product_pricetype_name'] ?>
								<br />
							<?php endif ?>
							<?php if ($invoice_item['discount']): ?>
								<?php echo $invoice_item['discount'].'% Discount' ?>
								<br />
							<?php endif ?>
						</div>
					</td>
					<td><?php echo '$'.number_format($invoice_item['unit_price'], 2) ?></td>
					<td><?php echo $invoice_item['quantity'] ?></td>
					<td class="total"><?php echo '$'.number_format($invoice_item['total'], 2) ?></td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<table id="total" class="clearfix">
			<tr>
				<th>Sub Total</th>
				<td>$906.60</td>
			</tr>
			<tr>
				<th>Tax - GST</th>
				<td>$65.60</td>
			</tr>
			<tr id="invoice_total">
				<th class="bold">Invoice Total</th>
				<td class="bold">$976.50</td>
			</tr>
			<tr>
				<th>Paid</th>
				<td>$0</td>
			</tr>
			<tr id="balance">
				<th class="bold">Balance</th>
				<td class="bold">$976.50</td>
			</tr>
		</table>

		<div id="other_info">
			<div class="info_title">Notes:</div>
			<div class="info_content">
				<?php echo $invoice['memo'] ?>
			</div>

			<div class="info_title">Terms &amp; Conditions:</div>
			<div class="info_content">
				<?php echo $invoice_terms ?>
			</div>
		</div>
	</div>
</div>
