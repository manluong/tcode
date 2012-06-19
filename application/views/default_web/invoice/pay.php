<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="content_top">
	<?php echo $quickjump ?>
</div>

<div id="title_item_edit">Invoice #<?php echo $invoice['id'] ?></div>

<div id="boxes">
	<form action="/invoice/pay_save" method="post">
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>PAYMENT INFORMATION</span></div>
		<div id="input_data_info">
			<table id="tbl_pay">
				<tr class="tr_pay">
					<td>
						<span class="input_data_label">Invoice #</span>
						<span class="fillter_input"><input type="text" name="addon_item[0][invoice_id]" value="<?php echo $invoice['id'] ?>" class="pay_invoice_id inv-field" /></span>
					</td>
					<td>
						<span class="input_data_label">Amount</span>
						<span class="fillter_input"><input type="text" name="addon_item[0][amount]" value="<?php echo $invoice['final_total'] ?>" class="pay_amount inv-field" /></span>
					</td>
				</tr>
				<tr id="tr_pay_total">
					<td>
						<span class="input_data_label">&nbsp;</span>
						<span class="fillter_input"><button type="button" id="btn_more_pay" class="btn btn-mini">More Invoice</button></span>
					</td>
					<td>
						<span class="input_data_label">Total</span>
						<span id="lbl_pay_total" class="input_data_label" style="text-align: left;"><?php echo '$'.number_format($invoice['final_total'], 2) ?></span>
						<input type="hidden" id="pay_total" name="amount" />
					</td>
				</tr>
			</table>

			<ul>
				<li>
					<span class="input_data_label">Payment Type</span>
					<span class="fillter_input"><input type="text" name="note" class="inv-field" /></span>
				</li>
				<li>
					<span class="input_data_label">Transaction No.</span>
					<span class="fillter_input"><input type="text" name="transaction_number" class="inv-field" /></span>
				</li>
			</ul>
		</div>

		<div class="pull-right">
			<button type="button" id="btn_submit" class="btn btn-primary">SUBMIT</button>
			<span style="color: #aaa">or <a href="/invoice/view/<?php echo $invoice['id'] ?>">cancel</a></span>
		</div>
	</div>
	</form>
</div>

<input type="hidden" id="pay_item_count" value="1" />
<table style="display: none;">
	<tbody id="tr_pay_template">
		<tr class="tr_pay">
			<td>
				<span class="input_data_label">Invoice #</span>
				<span class="fillter_input"><input type="text" name="addon_item[{xxxxx}][invoice_id]" class="pay_invoice_id inv-field" /></span>
			</td>
			<td>
				<span class="input_data_label">Amount</span>
				<span class="fillter_input"><input type="text" name="addon_item[{xxxxx}][amount]" class="pay_amount inv-field" /></span>
			</td>
		</tr>
	</tbody>
</table>