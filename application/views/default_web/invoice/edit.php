<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="content_top">
	<?php echo $quickjump ?>
</div>

<div id="invoice_title_item_edit"><span style="color:#2a9611;">Draft</span> Invoice #<?php echo $invoice['id'] ?></div>

<div id="boxes">
	<form id="invoice_form" action="/invoice/save" method="post">
	<input type="hidden" name="id" value="<?php echo $invoice['id'] ?>" />
	<div class="subtitle"><span class="subtitle-arrow"></span>INVOICE INFORMATION</div>
	<div class="subtitleContent dataF">
		<div class="dataFL">
			<div class="dataFT">Customer</div>
			<div class="dataVD">
				<input type="hidden" id="customer_id" name="customer_card_id" value="<?php echo $invoice['customer_card_id'] ?>" />
				<input type="text" id="customer_name" name="customer_card_name" value="<?php echo trim($invoice['customer_card_info']['first_name'].' '.$invoice['customer_card_info']['last_name']) ?>" class="inv-field" />
			</div>
		</div>
		<div class="dataFL">
			<div class="dataFT">Invoice Date</div>
			<div class="dataVD"><input type="text" name="invoice_stamp" value="<?php echo date('Y-m-d', strtotime($invoice['invoice_stamp'])) ?>" class="inv-field datepicker" /></div>
		</div>
		<div class="dataFL">
			<div class="dataFT">Due Date</div>
			<div class="dataVD"><input type="text" name="payment_due_stamp" value="<?php echo date('Y-m-d', strtotime($invoice['payment_due_stamp'])) ?>" class="inv-field datepicker" /></div>
		</div>
		<div class="dataFL">
			<div class="dataFT">Purchase Order</div>
			<div class="dataVD"><input type="text" name="purchase_order_number" value="<?php echo $invoice['purchase_order_number'] ?>" class="inv-field" /></div>
		</div>
	</div>

	<div id="invoice_detail">
		<div class="subtitle-noshadow"><span class="subtitle-arrow"></span>DETAILS</div>
		<div id="invoice_main_cases">
			<div id="invoice_item_list_header">
				<div style="width: 28px;"></div>
				<div style="width: 216px;">Product</div>
				<div style="width: 360px;">Description</div>
				<div style="width: 85px;">Unit Price</div>
				<div style="width: 88px;">Qty</div>
				<div style="width: 80px;">Total</div>
			</div>
			<div id="invoice_item_list">
				<?php $c = 0 ?>
				<?php if (isset($invoice['addon_item'])): ?>
				<?php $sub_total = 0 ?>
				<?php foreach ($invoice['addon_item'] as $invoice_item): ?>
				<div class="invoice_item clearfix">
					<div class="col-1">
						<ul>
							<li class="row_delete"></li>
							<li class="row_move"></li>
							<li class="row_more row_down"></li>
						</ul>
					</div>
					<div class="invoice_item_main">
						<div>
							<input type="hidden" name="addon_item[<?php echo $c ?>][id]" value="<?php echo $invoice_item['id'] ?>" />
							<input type="hidden" name="addon_item[<?php echo $c ?>][invoice_id]" value="<?php echo $invoice_item['invoice_id'] ?>" />
							<input type="hidden" name="addon_item[<?php echo $c ?>][product_id]" value="<?php echo $invoice_item['product_id'] ?>" class="product_id" />
							<input type="text" name="addon_item[<?php echo $c ?>][product_name]" value="<?php echo $invoice_item['name'] ?>" class="col-2 inv-field product_name" />
							<input type="hidden" name="addon_item[<?php echo $c ?>][sort_order]" value="<?php echo $invoice_item['sort_order'] ?>" class="sort_order" />
						</div>
						<div><input type="text" name="addon_item[<?php echo $c ?>][description]" value="<?php echo $invoice_item['description'] ?>" class="col-3" /></div>
						<div><input type="text" name="addon_item[<?php echo $c ?>][unit_price]" value="<?php echo (float)$invoice_item['unit_price'] ?>" class="col-4 unit_price cal" /></div>
						<div><input type="text" name="addon_item[<?php echo $c ?>][quantity]" value="<?php echo $invoice_item['quantity'] ?>" class="col-5 qty cal" /></div>
						<div>
							<input type="hidden" name="addon_item[<?php echo $c ?>][total]" value="<?php echo (float)$invoice_item['total'] ?>" class="item_total" />
							<label class="col-6 item_total_label"><?php echo '$'.number_format($invoice_item['total'], 2) ?></label>
						</div>
					</div>
					<div class="invoice_item_sub clear"<?php echo ($invoice_item['tax_use_id']) ? '' : ' style="display: none;"' ?>>
						<div><input type="text" name="addon_item[<?php echo $c ?>][subscription_start_stamp]" value="<?php echo date('Y-m-d', strtotime($invoice_item['subscription_start_stamp'])) ?>" class="col-7 item_datepicker" /></div>
						<div><input type="text" name="addon_item[<?php echo $c ?>][subscription_end_stamp]" value="<?php echo date('Y-m-d', strtotime($invoice_item['subscription_end_stamp'])) ?>" class="col-8 item_datepicker" /></div>
						<div><input type="text" name="addon_item[<?php echo $c ?>][discount]" value="<?php echo $invoice_item['discount'] ?>" class="col-11 discount cal" /></div>
						<div>
							<select name="addon_item[<?php echo $c ?>][tax_use_id]" class="col-12 tax cal">
								<option value="">No Tax</option>
								<?php foreach ($tax_use as $r): ?>
								<option value="<?php echo $r['id'] ?>"<?php echo ($r['id'] == $invoice_item['tax_use_id']) ? ' selected="selected"' : '' ?>><?php echo $r['name'] ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</div>
				<?php $sub_total += $invoice_item['total'] ?>
				<?php $c++ ?>
				<?php endforeach ?>
				<?php endif ?>
			</div>
		</div>

		<div>
			<div id="invoice_footer">
				<div id="invoice_add_row">
					<input type="button" class="btn btn-inverse" value="ADD ROW" />
				</div>
				<div id="invoice_all_discount">
					<ul>
						<li>Apply to all items</li>
						<li><input type="text" id="apply_all_discount" class="inv-field" /></li>
						<li>
							<select id="apply_all_tax" style="width: 200px;">
								<option value="-1">-- Update Tax for All Items --</option>
								<option value="">No Tax</option>
								<?php foreach ($tax_use as $r): ?>
								<option value="<?php echo $r['id'] ?>"><?php echo $r['name'] ?></option>
								<?php endforeach ?>
							</select>
						</li>
					</ul>
				</div>
			</div>
			<div id="invoice_total_price">
				<ul>
					<li>
						<div class="invoice_total_label">Sub Total</div>
						<div class="invoice_total_price"><span id="lbl_sub_total"><?php echo '$'.number_format($sub_total, 2) ?></span></div>
					</li>
					<?php foreach ($tax as $r): ?>
					<li class="total_hide">
						<div class="invoice_total_label">Tax - <?php echo $r['name'] ?></div>
						<div class="invoice_total_price">
							<?php $has_tax = false ?>
							<?php if (isset($invoice['addon_tax'])): ?>
							<?php foreach ($invoice['addon_tax'] as $invoice_tax): ?>
								<?php if ($invoice_tax['tax_id'] == $r['id']): ?>
									<span id="lbl_tax_<?php echo $r['id'] ?>_total"><?php echo '$'.number_format($invoice_tax['amount'], 2) ?></span>
									<input type="hidden" name="addon_tax[<?php echo $r['id'] ?>][id]" value="<?php echo $invoice_tax['id'] ?>" />
									<input type="hidden" name="addon_tax[<?php echo $r['id'] ?>][tax_id]" value="<?php echo $invoice_tax['tax_id'] ?>" />
									<input type="hidden" id="tax_<?php echo $r['id'] ?>_total" name="addon_tax[<?php echo $r['id'] ?>][amount]" value="<?php echo $invoice_tax['amount'] ?>" />
									<?php $has_tax = true ?>
								<?php endif ?>
							<?php endforeach ?>
							<?php endif ?>
							<?php if (!$has_tax): ?>
								<span id="lbl_tax_<?php echo $r['id'] ?>_total">$0.00</span>
								<input type="hidden" name="addon_tax[<?php echo $r['id'] ?>][id]" />
								<input type="hidden" name="addon_tax[<?php echo $r['id'] ?>][tax_id]" value="<?php echo $r['id'] ?>" />
								<input type="hidden" id="tax_<?php echo $r['id'] ?>_total" name="addon_tax[<?php echo $r['id'] ?>][amount]" value="0" />
							<?php endif ?>
						</div>
					</li>
					<?php endforeach ?>
					<li style="font-size:18px;">
						<div class="invoice_total_label">Invoice Total</div>
						<div class="invoice_total_price">
							<span id="lbl_invoice_total"><?php echo '$'.number_format($invoice['total'], 2) ?></span>
							<input type="hidden" id="invoice_total" name="total" value="<?php echo $invoice['total'] ?>" />
						</div>
					</li>
					<li>
						<div class="invoice_total_label">Paid</div>
						<div class="invoice_total_price">$0.00</div>
					</li>
					<li style="font-weight:bold;">
						<div class="invoice_total_label">Balance</div>
						<div class="invoice_total_price"><span id="lbl_balance"><?php echo '$'.number_format($invoice['total'], 2) ?></span></div>
					</li>
				</ul>
			</div>
		</div>
		<div id="invoice_submit_all_page"><button id="btn_submit" class="btn btn-primary">SUBMIT</button></div>

		<div id="invoice_note">
			<div id="invoice_term_condition">
				<div id="invoice_term_condition_title">Terms &amp; Conditions</div>
				<div style="float:left;width:150px;">
					<select id="terms_id" name="terms_id" class="right">
						<option value="">Pre Define Terms</option>
						<?php foreach ($terms as $r): ?>
						<option value="<?php echo $r['id'] ?>"<?php echo ($r['id'] == $invoice['terms_id']) ? ' selected="selected"' : '' ?>><?php echo $r['name'] ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<div style="width:100%;"><textarea rows="3" id="terms_content" name="terms_content" class="input-xlarge"><?php echo $terms_content ?></textarea></div>
			</div>
			<div id="invoice_note_customer">
				<div style="width:100%;float:left;height:37px;">Notes for Customers</div>
				<div style="width:100%;"><textarea rows="3" name="memo" class="input-xlarge"><?php echo $invoice['memo'] ?></textarea></div>
			</div>
		</div>
	</div>
	</form>
</div>

<input type="hidden" id="invoice_item_count" value="<?php echo $c ?>" />
<div id="invoice_item_template" style="display: none;">
	<div class="invoice_item temp clearfix">
		<div class="col-1">
			<ul>
				<li class="row_delete"></li>
				<li class="row_move"></li>
				<li class="row_more row_down"></li>
			</ul>
		</div>
		<div class="invoice_item_main">
			<div>
				<input type="hidden" name="addon_item[{xxxxx}][id]" />
				<input type="hidden" name="addon_item[{xxxxx}][product_id]" class="product_id" />
				<input type="text" name="addon_item[{xxxxx}][product_name]" class="col-2 inv-field product_name" />
				<input type="hidden" name="addon_item[{xxxxx}][sort_order]" class="sort_order" />
			</div>
			<div><input type="text" name="addon_item[{xxxxx}][description]" class="col-3" /></div>
			<div><input type="text" name="addon_item[{xxxxx}][unit_price]" class="col-4 unit_price cal" /></div>
			<div><input type="text" name="addon_item[{xxxxx}][quantity]" class="col-5 qty cal" /></div>
			<div>
				<input type="hidden" name="addon_item[{xxxxx}][total]" class="item_total" />
				<label class="col-6 item_total_label"></label>
			</div>
		</div>
		<div class="invoice_item_sub clear" style="display: none;">
			<div><input type="text" name="addon_item[{xxxxx}][subscription_start_stamp]" class="col-7 item_datepicker" /></div>
			<div><input type="text" name="addon_item[{xxxxx}][subscription_end_stamp]" class="col-8 item_datepicker" /></div>
			<div><input type="text" name="addon_item[{xxxxx}][discount]" class="col-11 discount cal" /></div>
			<div>
				<select name="addon_item[{xxxxx}][tax_use_id]" class="col-12 tax cal">
					<option value="">No Tax</option>
					<?php foreach ($tax_use as $r): ?>
					<option value="<?php echo $r['id'] ?>"><?php echo $r['name'] ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var ary_tax = $.parseJSON('<?php echo json_encode($tax) ?>');
	var ary_tax_use = $.parseJSON('<?php echo json_encode($tax_use) ?>');
</script>