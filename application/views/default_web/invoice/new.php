<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="content_top">
	<?php echo $quickjump ?>
</div>

<div id="boxes">
	<form id="invoice_form" action="/invoice/save" method="post">
	<div class="subtitle"><span class="subtitle-arrow"></span>INVOICE INFORMATION</div>
	<div class="subtitleContent dataF">
		<div class="dataFL">
			<div class="dataFT">Customer</div>
			<div class="dataVD">
				<input type="hidden" id="customer_id" name="customer_card_id" />
				<input type="text" id="customer_name" name="customer_card_name" class="inv-field" />
			</div>
		</div>
		<div class="dataFL">
			<div class="dataFT">Invoice Date</div>
			<div class="dataVD"><input type="text" name="invoice_stamp" class="inv-field datepicker" /></div>
		</div>
		<div class="dataFL">
			<div class="dataFT">Due Date</div>
			<div class="dataVD"><input type="text" name="payment_due_stamp" class="inv-field datepicker" /></div>
		</div>
		<div class="dataFL">
			<div class="dataFT">Purchase Order</div>
			<div class="dataVD"><input type="text" name="purchase_order_number" class="inv-field" /></div>
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
						<div class="invoice_total_price"><span id="lbl_sub_total">$0.00</span></div>
					</li>
					<?php foreach ($tax as $r): ?>
					<li class="total_hide">
						<div class="invoice_total_label">Tax - <?php echo $r['name'] ?></div>
						<div class="invoice_total_price">
							<span id="lbl_tax_<?php echo $r['id'] ?>_total">$0.00</span>
							<input type="hidden" name="addon_tax[<?php echo $r['id'] ?>][id]" />
							<input type="hidden" name="addon_tax[<?php echo $r['id'] ?>][tax_id]" value="<?php echo $r['id'] ?>" />
							<input type="hidden" id="tax_<?php echo $r['id'] ?>_total" name="addon_tax[<?php echo $r['id'] ?>][amount]" value="0" />
						</div>
					</li>
					<?php endforeach ?>
					<li style="font-size:18px;">
						<div class="invoice_total_label">Invoice Total</div>
						<div class="invoice_total_price">
							<span id="lbl_invoice_total">$0.00</span>
							<input type="hidden" id="invoice_total" name="total" />
						</div>
					</li>
					<li>
						<div class="invoice_total_label">Paid</div>
						<div class="invoice_total_price">$0.00</div>
					</li>
					<li style="font-weight:bold;">
						<div class="invoice_total_label">Balance</div>
						<div class="invoice_total_price"><span id="lbl_balance">$0.00</span></div>
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
						<option value="<?php echo $r['id'] ?>"><?php echo $r['name'] ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<div style="width:100%;"><textarea rows="3" id="terms_content" name="terms_content" class="input-xlarge"></textarea></div>
			</div>
			<div id="invoice_note_customer">
				<div style="width:100%;float:left;height:37px;">Notes for Customers</div>
				<div style="width:100%;"><textarea rows="3" name="memo" class="input-xlarge"></textarea></div>
			</div>
		</div>
	</div>
	</form>
</div>

<input type="hidden" id="invoice_item_count" value="0" />
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