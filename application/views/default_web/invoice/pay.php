<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="content_top">
	<?php echo $quickjump ?>
</div>

<div id="invoice_title_item_edit">Invoice #<?php echo $invoice['id'] ?></div>

<div id="boxes">
	<form action="/invoice/pay_save" method="post">
	<div class="subtitle"><span class="subtitle-arrow"></span>PAYMENT INFORMATION</div>
	<div class="subtitleContent clearfix">
		<table id="tbl_pay" style="width: 100%;">
			<tr class="tr_pay dataF">
				<td class="dataFL">
					<div class="dataFT">Invoice #</div>
					<span style="float: left;"><input type="text" name="addon_item[0][invoice_id]" value="<?php echo $invoice['id'] ?>" class="pay_invoice_id" /></span>
					<div class="dataFT" style="min-width: 70px; width: 70px;">Amount</div>
					<span><input type="text" name="addon_item[0][amount]" value="<?php echo $invoice['final_total'] ?>" class="pay_amount" /></span>
				</td>
			</tr>
			<tr id="tr_pay_total" class="dataF">
				<td class="dataFL">
					<div class="dataFT">&nbsp;</div>
					<div style="float: left; width: 225px;"><button type="button" id="btn_more_pay" class="btn btn-mini">More Invoice</button></div>
					<div class="dataFT" style="min-width: 70px; width: 70px;">Total</div>
					<span id="lbl_pay_total" style="text-align: left;"><?php echo '$'.number_format($invoice['final_total'], 2) ?></span>
					<input type="hidden" id="pay_total" name="amount" />
				</td>
			</tr>
		</table>

		<div class="dataF">
			<div class="dataFL">
				<div class="dataFT">Payment Type</div>
				<div class="dataVD"><input type="text" name="note" /></div>
			</div>
			<div class="dataFL">
				<div class="dataFT">Transaction No.</div>
				<div class="dataVD"><input type="text" name="transaction_number" /></div>
			</div>
		</div>

		<div class="pull-right">
			<button type="button" id="btn_submit" class="btn btn-primary">SUBMIT</button>
			<span class="actioncancel"> or <a href="/invoice/view/<?php echo $invoice['id'] ?>">Cancel</a></span>
		</div>
	</div>
	</form>
</div>

<input type="hidden" id="pay_item_count" value="1" />
<table style="display: none;">
	<tbody id="tr_pay_template">
		<tr class="tr_pay dataF">
			<td class="dataFL">
				<div class="dataFT">Invoice #</div>
				<span style="float: left;"><input type="text" name="addon_item[{xxxxx}][invoice_id]" class="pay_invoice_id" /></span>
				<div class="dataFT" style="min-width: 70px; width: 70px;">Amount</div>
				<span><input type="text" name="addon_item[{xxxxx}][amount]" class="pay_amount" /></span>
			</td>
		</tr>
	</tbody>
</table>