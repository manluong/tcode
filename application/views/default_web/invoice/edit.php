<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="breadcrumb">
	<div id="module_name">
		<ul>
			<li><a href="/invoice" class="main">INVOICE</a></li>
			<li class="arrow"></li>
			<li class="curent_page">Edit</li>
			<li><a href="#" id="favoriteIcon" class="on" title="Remove from favorites"></a></li>
		</ul>
	</div>
	<div id="top_button">
		<ul>
			<li><a href="/invoice"><button class="btn btn-inverse">LIST</button></a></li>
			<li><a href="/invoice/add"><button class="btn btn-inverse">NEW</button></a></li>
		</ul>
	</div>
</div>

<div id="content_top">
	<div id="user_profile">
		<div id="user_avatar"><img alt="avatar" src="/resources/template/default_web/img/invoice/invoice-avatar.jpg"/></div>
		<div id="user_info">
			<ul>
				<li class="user_sex">Mr.</li>
				<li class="user_name">Albert Z</li>
				<li class="user_position">Facebook Inc. <span style="font-weight: normal;">CEO</span></li>
			</ul>
		</div>
	</div>
	<div id="customer_list">
		<div class="btn-group">
			<a href="#" class="btn btn-inverse">CUSTOMER</a>
			<a href="#" data-toggle="dropdown" class="btn btn-inverse dropdown-toggle"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#"><i class="icon-pencil"></i> Edit</a></li>
				<li><a href="#"><i class="icon-trash"></i> Delete</a></li>
				<li><a href="#"><i class="icon-ban-circle"></i> Ban</a></li>
			</ul>
		</div>
	</div>
</div>

<div id="title_item_edit"><span style="color:#2a9611;">Draft</span> Invoice #<?php echo $invoice['id'] ?></div>

<div id="boxes">
	<form id="invoice_form" action="/invoice/edit_save" method="post">
	<input type="hidden" name="invoice_id" value="<?php echo $invoice['id'] ?>" />
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>INVOICE INFORMATION</span></div>
		<div id="input_data_info">
			<ul>
				<li>
					<span class="input_data_label">Customer</span>
					<select name="customer_id">
						<option value="">- - - Select - - -</option>
						<?php foreach ($customer as $r): ?>
						<option value="<?php echo $r->id ?>"<?php echo ($r->id == $invoice['customer_card_id']) ? ' selected="selected"' : '' ?>><?php echo $r->nickname ?></option>
						<?php endforeach ?>
					</select>
				</li>
				<li>
					<span class="input_data_label">Invoice Date</span>
					<span class="fillter_input"><input type="text" name="issue_date" value="<?php echo date('Y-m-d', strtotime($invoice['invoice_stamp'])) ?>" class="inv-field datepicker" /></span>
				</li>
				<li>
					<span class="input_data_label">Due Date</span>
					<span class="fillter_input"><input type="text" name="due_date" value="<?php echo date('Y-m-d', strtotime($invoice['payment_due_stamp'])) ?>" class="inv-field datepicker" /></span>
				</li>
				<li>
					<span class="input_data_label">Purchase Order</span>
					<span class="fillter_input"><input type="text" name="po_number" value="<?php echo $invoice['purchase_order_number'] ?>" class="inv-field" /></span>
				</li>
			</ul>
		</div>
	</div>
	<div id="invoice_detail">
		<div class="invoice_title"><span class="arrow_title"></span><span>DETAILS</span></div>
		<div id="main_cases">
			<div id="item_list_header">
				<div style="width: 28px;"></div>
				<div style="width: 216px;">Product</div>
				<div style="width: 360px;">Description</div>
				<div style="width: 85px;">Unit Price</div>
				<div style="width: 88px;">Qty</div>
				<div style="width: 80px;">Total</div>
			</div>
			<div id="invoice_item_list">
				<?php foreach ($invoice_items as $invoice_item): ?>
				<div class="invoice_item clearfix">
					<div class="col-1">
						<ul>
							<li class="row_delete"></li>
							<li class="row_move"></li>
							<li class="row_down"></li>
						</ul>
					</div>
					<div class="invoice_item_main">
						<div>
							<input type="hidden" name="invoice_item_id[]" value="<?php echo $invoice_item->id ?>" />
							<input type="hidden" name="product_id[]" value="<?php echo $invoice_item->product_id ?>" class="product_id" />
							<input type="text" name="product_name[]" value="<?php echo $invoice_item->a_product_name ?>" class="col-2 inv-field product_name" />
						</div>
						<div><input type="text" name="description[]" value="<?php echo $invoice_item->description ?>" class="col-3" /></div>
						<div><input type="text" name="unit_price[]" value="<?php echo (float)$invoice_item->unit_price ?>" class="col-4 unit_price cal" /></div>
						<div><input type="text" name="qty[]" value="<?php echo $invoice_item->quantity ?>" class="col-5 qty cal" /></div>
						<div>
							<input type="hidden" name="total[]" value="<?php echo (float)$invoice_item->total ?>" class="item_total" />
							<label class="col-6 item_total_label"><?php echo '$'.number_format($invoice_item->total, 2) ?></label>
						</div>
					</div>
					<div class="invoice_item_sub clear"<?php echo ($invoice_item->price_type) ? '' : ' style="display: none;"' ?>>
						<div><input type="text" name="from[]" value="<?php echo date('Y-m-d', strtotime($invoice_item->subscription_start_stamp)) ?>" class="col-7 item_datepicker" /></div>
						<div><input type="text" name="to[]" value="<?php echo date('Y-m-d', strtotime($invoice_item->subscription_end_stamp)) ?>" class="col-8 item_datepicker" /></div>
						<div>
							<select name="duration[]" class="col-9">
								<option value="">Period</option>
								<?php foreach ($duration_type as $r): ?>
								<option value="<?php echo $r->a_product_durationtype_id ?>"<?php echo ($r->a_product_durationtype_id == $invoice_item->duration_type) ? ' selected="selected"' : '' ?>><?php echo $r->a_product_durationtype_name ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div>
							<select name="price_type[]" class="col-10">
								<option value="">Price Type</option>
								<?php foreach ($price_type as $r): ?>
								<option value="<?php echo $r->a_product_pricetype_id ?>"<?php echo ($r->a_product_pricetype_id == $invoice_item->price_type) ? ' selected="selected"' : '' ?>><?php echo $r->a_product_pricetype_name ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div><input type="text" name="discount[]" value="<?php echo $invoice_item->discount ?>" class="col-11 discount cal" /></div>
						<div class="col-12">
							<input type="checkbox" class="tax-gst" /><span>GST</span>
							<input type="checkbox" class="tax-vat" /><span>VAT</span>
						</div>
					</div>
				</div>
				<?php endforeach ?>
			</div>
		</div>

		<div id="invoice_item_template" style="display: none;">
			<div class="invoice_item temp clearfix">
				<div class="col-1">
					<ul>
						<li class="row_delete"></li>
						<li class="row_move"></li>
						<li class="row_down"></li>
					</ul>
				</div>
				<div class="invoice_item_main">
					<div>
						<input type="hidden" name="invoice_item_id[]" />
						<input type="hidden" name="product_id[]" class="product_id" />
						<input type="text" name="product_name[]" class="col-2 inv-field product_name" />
					</div>
					<div><input type="text" name="description[]" class="col-3" /></div>
					<div><input type="text" name="unit_price[]" class="col-4 unit_price cal" /></div>
					<div><input type="text" name="qty[]" class="col-5 qty cal" /></div>
					<div>
						<input type="hidden" name="total[]" class="item_total" />
						<label class="col-6 item_total_label"></label>
					</div>
				</div>
				<div class="invoice_item_sub clear" style="display: none;">
					<div><input type="text" name="from[]" class="col-7 item_datepicker" /></div>
					<div><input type="text" name="to[]" class="col-8 item_datepicker" /></div>
					<div>
						<select name="duration[]" class="col-9">
							<option value="">Period</option>
							<?php foreach ($duration_type as $r): ?>
							<option value="<?php echo $r->a_product_durationtype_id ?>"><?php echo $r->a_product_durationtype_name ?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div>
						<select name="price_type[]" class="col-10">
							<option value="">Price Type</option>
							<?php foreach ($price_type as $r): ?>
							<option value="<?php echo $r->a_product_pricetype_id ?>"><?php echo $r->a_product_pricetype_name ?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div><input type="text" name="discount[]" class="col-11 discount cal" /></div>
					<div class="col-12">
						<input type="checkbox" class="tax-gst" /><span>GST</span>
						<input type="checkbox" class="tax-vat" /><span>VAT</span>
					</div>
				</div>
			</div>
		</div>

		<div>
			<div id="invoice_footer">
				<div id="add_row">
					<input type="button" class="btn btn-inverse" value="ADD ROW" />
				</div>
				<div id="all_discount">
					<ul>
						<li>Apply to all items</li>
						<li><input type="text" id="apply_all_discount" class="inv-field" /></li>
						<li><input type="checkbox" id="apply_all_gst" data-tax="gst" /> GST</li>
						<li><input type="checkbox" id="apply_all_vat" data-tax="vat" /> VAT</li>
					</ul>
				</div>
			</div>
			<div id="total_price">
				<ul>
					<li>
						<div class="total_label">Sub Total</div>
						<div class="total_price"><span id="sub_total">$0</span></div>
					</li>
					<li>
						<div class="total_label">Discount</div>
						<div class="total_price"><span id="discount_total">$0</span></div>
					</li>
					<li style="font-size:18px;">
						<div class="total_label">Invoice Total</div>
						<div class="total_price"><span id="invoice_total">$0</span></div>
					</li>
					<li>
						<div class="total_label">Paid</div>
						<div class="total_price">$0</div>
					</li>
					<li style="font-weight:bold;">
						<div class="total_label">Balance</div>
						<div class="total_price"><span id="balance">$0</span></div>
					</li>
				</ul>
			</div>
		</div>
		<div id="submit_all_page"><button id="submit_btn" class="btn btn-primary">SUBMIT</button></div>

		<div id="invoice_note">
			<div id="term_condition">
				<div id="term_condition_title">Terms &amp; Conditions</div>
				<div style="float:left;width:150px;">
					<select id="terms_id" name="terms_id" class="right">
						<option value="">Pre Define Terms</option>
						<?php foreach ($terms as $r): ?>
						<option value="<?php echo $r->id ?>"<?php echo ($r->id == $invoice['terms_id']) ? ' selected="selected"' : '' ?>><?php echo $r->name ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<div style="width:100%;"><textarea rows="3" id="terms_content" name="terms_content" class="input-xlarge"><?php echo $invoice_terms ?></textarea></div>
			</div>
			<div id="note_customer">
				<div style="width:100%;float:left;height:37px;">Notes for Customers</div>
				<div style="width:100%;"><textarea rows="3" name="notes" class="input-xlarge"><?php echo $invoice['memo'] ?></textarea></div>
			</div>
		</div>
	</div>
	</form>
</div>
