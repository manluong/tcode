<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="invoice_nav">
	<ul class="nav_left">
		<li class="main">Invoice</li>
		<li>&gt; Edit</li>
	</ul>
	<ul class="nav_right">
		<li><a href="#">Dashboard</a></li>
		<li><a href="/invoice">List</a></li>
		<li><a href="/invoice/add">New</a></li>
	</ul>
</div>

<form id="invoice_form" action="/invoice/edit_save" method="post">
<input type="hidden" name="invoice_id" value="<?php echo $invoice['id'] ?>" />
<div id="invoice_info">
	<ul id="form_show">
		<li>
			<span class="invoice_info_span">Customer</span>
			<select name="customer_id">
				<option value="">---- Select ----</option>
				<?php foreach ($customer as $r): ?>
				<option value="<?php echo $r->id ?>"<?php echo ($r->id == $invoice['customer_card_id']) ? ' selected="selected"' : '' ?>><?php echo $r->nickname ?></option>
				<?php endforeach ?>
			</select>
		</li>
		<li>
			<span class="invoice_info_span">Date of Issue</span>
			<input type="text" name="issue_date" value="<?php echo date('Y-m-d', strtotime($invoice['invoice_stamp'])) ?>" class="datepicker" style="width: 200px" />
		</li>
		<li>
			<span class="invoice_info_span">Due Date</span>
			<input type="text" name="due_date" value="<?php echo date('Y-m-d', strtotime($invoice['payment_due_stamp'])) ?>" class="datepicker" style="width: 200px" />
		</li>
		<li>
			<span class="invoice_info_span">PO Number</span>
			<input type="text" name="po_number" value="<?php echo $invoice['purchase_order_number'] ?>" />
		</li>
	</ul>
</div>

<div id="invoice_item_list">
	<div class="invoice_item header clear">
		<div class="col-1">&nbsp;</div>
		<div class="col-2">Product</div>
		<div class="col-3">Description</div>
		<div class="col-4">Unit Price</div>
		<div class="col-5">Qty</div>
		<div class="col-6">Total</div>
	</div>
	<?php foreach ($invoice_items as $invoice_item): ?>
	<div class="invoice_item clear">
		<div class="col-1">
			<a href="#" class="remove">x</a>
			<a href="#" class="more">+</a>
			<a href="#" class="move">m</a>
		</div>
		<div class="col-2">
			<input type="hidden" name="invoice_item_id[]" value="<?php echo $invoice_item->id ?>" />
			<input type="hidden" name="product_id[]" value="<?php echo $invoice_item->product_id ?>" class="product_id" />
			<input type="text" name="product_name[]" value="<?php echo $invoice_item->a_product_name ?>" class="product_name" />
		</div>
		<div class="col-3"><input type="text" name="description[]" value="<?php echo $invoice_item->description ?>" /></div>
		<div class="col-4"><input type="text" name="unit_price[]" value="<?php echo (float)$invoice_item->unit_price ?>" class="unit_price cal" /></div>
		<div class="col-5"><input type="text" name="qty[]" value="<?php echo $invoice_item->quantity ?>" class="qty cal" /></div>
		<div class="col-6">
			<input type="hidden" name="total[]" value="<?php echo (float)$invoice_item->total ?>" class="item_total" />
			<label class="item_total_label"><?php echo '$'.number_format($invoice_item->total, 2) ?></label>
		</div>
		<div class="invoice_item_sub clear"<?php echo ($invoice_item->price_type) ? '' : ' style="display: none;"' ?>>
			<div class="col-7"><input type="text" name="from[]" value="<?php echo date('Y-m-d', strtotime($invoice_item->subscription_start_stamp)) ?>" class="item_datepicker" /></div>
			<div class="col-8"><input type="text" name="to[]" value="<?php echo date('Y-m-d', strtotime($invoice_item->subscription_end_stamp)) ?>" class="item_datepicker" /></div>
			<div class="col-9">
				<select name="duration[]">
					<option value="">Period</option>
					<?php foreach ($duration_type as $r): ?>
					<option value="<?php echo $r->a_product_durationtype_id ?>"<?php echo ($r->a_product_durationtype_id == $invoice_item->duration_type) ? ' selected="selected"' : '' ?>><?php echo $r->a_product_durationtype_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-10">
				<select name="price_type[]">
					<option value="">Price Type</option>
					<?php foreach ($price_type as $r): ?>
					<option value="<?php echo $r->a_product_pricetype_id ?>"<?php echo ($r->a_product_pricetype_id == $invoice_item->price_type) ? ' selected="selected"' : '' ?>><?php echo $r->a_product_pricetype_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-11"><input type="text" name="discount[]" value="<?php echo $invoice_item->discount ?>" class="discount cal" /></div>
			<div class="col-12">
				<input type="checkbox" class="tax-gst" style="width: auto" /> GST
				<input type="checkbox" class="tax-vat" style="width: auto" /> VAT
			</div>
		</div>
	</div>
	<?php endforeach ?>
</div>

<div id="invoice_item_template" style="display: none;">
	<div class="invoice_item temp clear">
		<div class="col-1">
			<a href="#" class="remove">x</a>
			<a href="#" class="more">+</a>
			<a href="#" class="move">m</a>
		</div>
		<div class="col-2">
			<input type="hidden" name="invoice_item_id[]" />
			<input type="hidden" name="product_id[]" class="product_id" />
			<input type="text" name="product_name[]" class="product_name" />
		</div>
		<div class="col-3"><input type="text" name="description[]" /></div>
		<div class="col-4"><input type="text" name="unit_price[]" class="unit_price cal" /></div>
		<div class="col-5"><input type="text" name="qty[]" class="qty cal" /></div>
		<div class="col-6">
			<input type="hidden" name="total[]" class="item_total" />
			<label class="item_total_label"></label>
		</div>
		<div class="invoice_item_sub clear" style="display: none;">
			<div class="col-7"><input type="text" name="from[]" class="item_datepicker" /></div>
			<div class="col-8"><input type="text" name="to[]" class="item_datepicker" /></div>
			<div class="col-9">
				<select name="duration[]">
					<option value="">Period</option>
					<?php foreach ($duration_type as $r): ?>
					<option value="<?php echo $r->a_product_durationtype_id ?>"><?php echo $r->a_product_durationtype_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-10">
				<select name="price_type[]">
					<option value="">Price Type</option>
					<?php foreach ($price_type as $r): ?>
					<option value="<?php echo $r->a_product_pricetype_id ?>"><?php echo $r->a_product_pricetype_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-11"><input type="text" name="discount[]" class="discount cal" /></div>
			<div class="col-12">
				<input type="checkbox" class="tax-gst" style="width: auto" /> GST
				<input type="checkbox" class="tax-vat" style="width: auto" /> VAT
			</div>
		</div>
	</div>
</div>
<br />
<div class="clear">
	<div class="apply left">
		<input id="add_row" type="button" class="btn" value="ADD ROW" />
		Apply to all items:
		<input type="text" id="apply_all_discount" style="width: 100px;" />
		<input type="checkbox" id="apply_all_gst" data-tax="gst" /> GST
		<input type="checkbox" id="apply_all_vat" data-tax="vat" /> VAT
	</div>
	<div id="total" class="right">
		<span class="left">Sub Total</span><span id="sub_total" class="right"></span>
		<br />
		<!-- <span class="left">TAX - GST(5%)</span><span id="tax_gst" class="right"></span>
		<br />
		<span class="left">TAX - VAT(10%)</span><span id="tax_vat" class="right"></span>
		<br /> -->
		<span class="left">Discount</span><span id="discount_total" class="right"></span>
		<br />
		<br />
		<span class="left">Invoice Total</span><span id="invoice_total" class="right"></span>
		<br />
		<span class="left">Paid</span>
		<br />
		<br />
		<span class="left">Balance</span>
	</div>
</div>

<div class="clear">
	<div class="terms left">
		<span>Terms</span>
		<select id="terms_id" name="terms_id" class="right">
			<option value="">Pre Define Terms</option>
			<?php foreach ($terms as $r): ?>
			<option value="<?php echo $r->id ?>"<?php echo ($r->id == $invoice['terms_id']) ? ' selected="selected"' : '' ?>><?php echo $r->name ?></option>
			<?php endforeach ?>
		</select>
		<br />
		<div class="clear"></div>
		<textarea id="terms_content" name="terms_content"><?php echo $invoice_terms ?></textarea>
	</div>
	<div class="terms right">
		<span>Notes for Customer</span>
		<br />
		<textarea name="notes" class="notes"><?php echo $invoice['memo'] ?></textarea>
	</div>
</div>

<div class="div_btn clear">
	<input id="submit_btn" type="submit" class="btn" value="Submit" />
	<a href="/invoice/view/<?php echo $invoice['id']?>"><input type="button" class="btn" value="Cancel" /></a>
</div>
</form>