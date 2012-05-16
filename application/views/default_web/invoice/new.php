<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="invoice_nav">
	<ul class="nav_left">
		<li class="main">Invoice</li>
		<li>&gt; New</li>
	</ul>
	<ul class="nav_right">
		<li><a href="#">Dashboard</a></li>
		<li><a href="/invoice">List</a></li>
		<li><a href="/invoice/add">New</a></li>
	</ul>
</div>

<form id="invoice_form" action="/invoice/add_save" method="post">
<div id="invoice_info">
	<ul id="form_show">
		<li>
			<span class="invoice_info_span">Customer</span>
			<select name="customer_id">
				<option value="">---- Select ----</option>
				<?php foreach ($customer as $r): ?>
				<option value="<?php echo $r->id ?>"><?php echo $r->nickname ?></option>
				<?php endforeach ?>
			</select>
		</li>
		<li>
			<span class="invoice_info_span">Date of Issue</span>
			<input type="text" name="issue_date" class="datepicker" style="width: 200px" />
		</li>
		<li>
			<span class="invoice_info_span">Due Date</span>
			<input type="text" name="due_date" class="datepicker" style="width: 200px" />
		</li>
		<li>
			<span class="invoice_info_span">PO Number</span>
			<input type="text" name="po_number" />
		</li>
		<li>
			<span class="invoice_info_span">Tax</span>
			<select name="tax_id">
				<option value="">---- Select ----</option>
				<?php foreach ($tax as $id => $name): ?>
				<option value="<?php echo $id ?>"><?php echo $name ?></option>
				<?php endforeach ?>
			</select>
		</li>
		<li>
			<span class="invoice_info_span">Currency</span>
			<input type="text" name="currency" />
		</li>
	</ul>
</div>

<div id="invoice_item_list">
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
</div>

<div id="invoice_item_template" style="display: none;">
	<div class="invoice_item temp clear">
		<div>
			<input type="hidden" name="invoice_item_id[]" />
			<input type="hidden" name="product_id[]" class="product_id" />
			<input type="text" name="product_name[]" class="product_name" />
		</div>
		<div class="desc"><input type="text" name="description[]" /></div>
		<div><input type="text" name="unit_price[]" class="unit_price cal" /></div>
		<div><input type="text" name="qty[]" class="qty cal" /></div>
		<div><input type="text" name="discount[]" class="discount cal" /></div>
		<div><input type="text" name="tax[]" class="tax cal" /></div>
		<div><input type="text" name="total[]" class="item_total" /></div>
		<div class="act">
			<a href="#" class="more">m</a>
			<a href="#" class="add">+</a>
			<a href="#" class="remove">x</a>
		</div>
		<div class="invoice_item_sub header clear" style="display: none;">
			<div><span>Price Type</span></div>
			<div><span>From</span></div>
			<div><span>To</span></div>
			<div><span>Duration</span></div>
		</div>
		<div class="invoice_item_sub clear" style="display: none;">
			<div>
				<select name="price_type[]">
					<option value="">-- Select --</option>
					<?php foreach ($price_type as $r): ?>
					<option value="<?php echo $r->a_product_pricetype_id ?>"><?php echo $r->a_product_pricetype_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div><input type="text" name="from[]" class="item_datepicker" /></div>
			<div><input type="text" name="to[]" class="item_datepicker" /></div>
			<div>
				<select name="duration[]">
					<option value="">-- Select --</option>
					<?php foreach ($duration_type as $r): ?>
					<option value="<?php echo $r->a_product_durationtype_id ?>"><?php echo $r->a_product_durationtype_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	</div>
</div>

<div id="total" class="right">
	<span class="left">Sub Total</span><span id="sub_total" class="right"></span>
	<br />
	<span class="left">Tax Total</span><span id="tax_total" class="right"></span>
	<br />
	<br />
	<span class="left">Invoice Total</span><span id="invoice_total" class="right"></span>
	<br />
	<span class="left">Paid</span>
	<br />
	<br />
	<span class="left">Balance</span>
</div>

<div class="clear">
	<div class="terms left">
		<span>Terms</span>
		<select id="terms_id" name="terms_id" class="right">
			<option value="">Pre Define Terms</option>
			<?php foreach ($terms as $r): ?>
			<option value="<?php echo $r->id ?>"><?php echo $r->name ?></option>
			<?php endforeach ?>
		</select>
		<br />
		<div class="clear"></div>
		<textarea id="terms_content" name="terms_content"></textarea>
	</div>
	<div class="terms right">
		<span>Notes for Customer</span>
		<br />
		<textarea name="notes" class="notes"></textarea>
	</div>
</div>

<div class="div_btn clear">
	<input id="submit_btn" type="submit" class="btn" value="Submit" />
	<a href="/invoice"><input type="button" class="btn" value="Cancel" /></a>
</div>
</form>