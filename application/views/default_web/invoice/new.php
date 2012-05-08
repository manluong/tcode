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

<!-- <table class="table">
<thead>
	<tr>
		<th style="width: 5%"></th>
		<th style="width: 10%">Product</th>
		<th style="width: 30%">Description</th>
		<th style="width: 10%">Unit Price</th>
		<th style="width: 10%">Qty</th>
		<th style="width: 10%">Discount</th>
		<th style="width: 10%">Tax</th>
		<th style="width: 10%">Total</th>
		<th style="width: 5%"></th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><a href="#" class="add">+</a></td>
		<td><input type="text" name="product[]" class="input_table" /></td>
		<td><input type="text" name="description[]" class="input_table" /></td>
		<td><input type="text" name="unit_price[]" class="input_table" /></td>
		<td><input type="text" name="qty[]" class="input_table" /></td>
		<td><input type="text" name="discount[]" class="input_table" /></td>
		<td><input type="text" name="tax[]" class="input_table" /></td>
		<td><input type="text" name="total[]" class="input_table" /></td>
		<td><a href="#" class="remove">x</a></td>
	</tr>
	<tr>
		<td></td>
		<td colspan="3">
			<table class="default">
				<thead>
					<tr>
						<td>Price Type</td>
						<td>From</td>
						<td>To</td>
						<td>Duration</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Monthly Recur</td>
						<td>1 July 2012</td>
						<td>31 July 2012</td>
						<td>1 Month</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</tbody>
</table> -->

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
	<div class="invoice_item clear">
		<div><input type="text" name="product[]" class="input_table" /></div>
		<div class="desc"><input type="text" name="description[]" class="input_table" /></div>
		<div><input type="text" name="unit_price[]" class="input_table" /></div>
		<div><input type="text" name="qty[]" class="input_table" /></div>
		<div><input type="text" name="discount[]" class="input_table" /></div>
		<div><input type="text" name="tax[]" class="input_table" /></div>
		<div><input type="text" name="total[]" class="input_table" /></div>
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
			<div><input type="text" name="price_type[]" class="input_table" /></div>
			<div><input type="text" name="from[]" class="datepicker_temp input_table" /></div>
			<div><input type="text" name="to[]" class="datepicker_temp input_table" /></div>
			<div><input type="text" name="duration[]" class="input_table" /></div>
		</div>
	</div>
</div>

<div id="total" class="right">
	<span>Sub Total</span>
	<br />
	<span>Tax Total</span>
	<br />
	<br />
	<span>Invoice Total</span>
	<br />
	<span>Paid</span>
	<br />
	<br />
	<span>Balance</span>
</div>

<div class="clear">
	<div class="terms left">
		<span>Terms</span>
		<select name="terms_id" class="right">
			<option>Pre Define Terms</option>
			<?php foreach ($terms as $r): ?>
			<option value="<?php echo $r->id ?>"><?php echo $r->name ?></option>
			<?php endforeach ?>
		</select>
		<br />
		<div class="clear"></div>
		<textarea name="terms_content"></textarea>
	</div>
	<div class="terms right">
		<span>Notes for Customer</span>
		<br />
		<textarea name="notes" class="notes"></textarea>
	</div>
</div>

<div class="div_btn clear">
	<input id="submit_btn" type="submit" class="btn" value="Submit" />
</div>
</form>