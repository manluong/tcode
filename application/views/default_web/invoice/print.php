<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:300" type="text/css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/bootstrap.min.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/bootstrap-8f.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/style.css" />
<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/app.css" />

<style type="text/css">
<!--
html body {
	background-color: #FFF;
}

#invoice_container {
	border: none;
}
-->
</style>

<div id="boxes" class="clearfix">
	<div id="invoice_container" class="clearfix">
		<div id="invoice_detail">
			<div id="invoice_header" class="clearfix">
				<div id="invoice_company_info">
					<div id="invoice_company_name">YOUR COMPANY NAME</div>
					<div id="invoice_company_address">
						123 Ways, Sun City<br />
						State, US
					</div>
				</div>
				<div id="invoice_title">INVOICE</div>
			</div>
			<div id="invoice_main" class="clearfix">
				<div id="invoice_bill_to">BILL TO</div>
				<div id="invoice_customer_info">
					<div id="invoice_customer_name">
						Facebook Inc<br />
						<?php echo trim($invoice['customer_card_info']['first_name'].' '.$invoice['customer_card_info']['last_name']) ?>
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
				<?php if (isset($invoice['addon_item'])): ?>
				<?php $sub_total = 0 ?>
				<?php foreach ($invoice['addon_item'] as $invoice_item): ?>
				<tr>
					<td><?php echo $invoice_item['name'] ?></td>
					<td>
						<?php echo $invoice_item['description'] ?>
						<div class="sub_desc">
							<?php if ($invoice_item['subscription_start_stamp']): ?>
								<?php echo date('Y-m-d', strtotime($invoice_item['subscription_start_stamp'])).' to '.date('Y-m-d', strtotime($invoice_item['subscription_end_stamp'])) ?>
								<br />
							<?php endif ?>
							<?php if ($invoice_item['discount']): ?>
								<?php echo $invoice_item['discount'].'% Discount' ?>
								<br />
							<?php endif ?>
							<?php if ($invoice_item['tax_use_id']): ?>
								<?php echo $invoice_item['tax_use_name'] ?>
								<br />
							<?php endif ?>
						</div>
					</td>
					<td><?php echo '$'.number_format($invoice_item['unit_price'], 2) ?></td>
					<td><?php echo $invoice_item['quantity'] ?></td>
					<td class="total"><?php echo '$'.number_format($invoice_item['total'], 2) ?></td>
				</tr>
				<?php $sub_total += $invoice_item['total'] ?>
				<?php endforeach ?>
				<?php endif ?>
			</tbody>
		</table>

		<table id="invoice_summary" class="clearfix">
			<tr>
				<th>Sub Total</th>
				<td><?php echo '$'.number_format($sub_total, 2) ?></td>
			</tr>
			<?php if (isset($invoice['addon_tax'])): ?>
			<?php foreach ($tax as $r): ?>
				<?php foreach ($invoice['addon_tax'] as $invoice_tax): ?>
					<?php if ($invoice_tax['tax_id'] == $r['id']): ?>
					<tr>
						<th>Tax - <?php echo $r['name'] ?></th>
						<td><?php echo '$'.number_format($invoice_tax['amount'], 2) ?></td>
					</tr>
					<?php endif ?>
				<?php endforeach ?>
			<?php endforeach ?>
			<?php endif ?>
			<tr id="invoice_total">
				<th class="bold">Invoice Total</th>
				<td class="bold"><?php echo '$'.number_format($invoice['total'], 2) ?></td>
			</tr>
			<tr>
				<th>Paid</th>
				<td>$0.00</td>
			</tr>
			<tr id="invoice_balance">
				<th class="bold">Balance</th>
				<td class="bold"><?php echo '$'.number_format($invoice['total'], 2) ?></td>
			</tr>
		</table>

		<div id="invoice_other_info">
			<div class="title">Notes:</div>
			<div class="content">
				<?php echo $invoice['memo'] ?>
			</div>

			<div class="title">Terms &amp; Conditions:</div>
			<div class="content">
				<?php echo $terms_content ?>
			</div>
		</div>
	</div>
</div>