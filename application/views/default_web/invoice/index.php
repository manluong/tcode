<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="content_top">
	<?php echo $quickjump ?>
</div>

<div id="boxes">
	<div class="subtitle"><span class="subtitle-arrow"></span>FILLTER</div>
	<div class="subtitleContent">
		<form id="frm_search" action="/invoice/search" method="post">
		<div id="invoice_main_fillter" class="clearfix">
			<ul>
				<li>
					<span class="fillter_label">Date</span>
					<span class="fillter_input">
						<select id="date_range" name="date_range">
							<option value="">- - - Select - - -</option>
							<option value="1">Last Month</option>
						</select>
					</span>
				</li>
				<li>
					<span class="fillter_label">From</span>
					<span class="fillter_input"><input type="text" id="date_range_from" name="date_range_from" class="inv-field datepicker" /></span>
				</li>
				<li>
					<span class="fillter_label">To</span>
					<span class="fillter_input"><input type="text" id="date_range_to" name="date_range_to" class="inv-field datepicker" /></span>
				</li>
				<li style="width:150px;margin-left:10px;">
					<span class="fillter_label">Payment</span>
					<span class="fillter_input">
						<div id="status-group" data-toggle="buttons-radio" class="btn-group">
							<button type="button" class="btn active" data-value="-1">All</button>
							<button type="button" class="btn" data-value="1">Paid</button>
							<button type="button" class="btn" data-value="0">Unpaid</button>
							<input type="hidden" id="status" name="status" value="-1" />
						</div>
					</span>
				</li>
				<li style="width:210px; margin-left:10px;">
					<span class="fillter_label">Amount</span>
					<span class="fillter_input" id="invoice_amount" >
						<input type="hidden" id="total_default_min" value="<?php echo $total_min ?>" />
						<input type="hidden" id="total_default_max" value="<?php echo $total_max ?>" />
						<input type="hidden" id="total_min" name="total_min" />
						<input type="hidden" id="total_max" name="total_max" />
						<label id="lbl_total"><?php echo '$'.$total_min.' - '.'$'.$total_max ?></label>
						<div id="slider-range"></div>
					</span>
				</li>
				<li style="width: 25px; margin: 36px 0 0 28px;"><div id="arrow" class="down_arrow"></div></li>
			</ul>
		</div>
		<div id="invoice_sub_fillter" class="dataF" style="display: none;">
			<?php if (!$is_client): ?>
			<div class="dataFL">
				<div class="dataFT">Customer</div>
				<div class="dataVD">
					<input type="hidden" id="customer_id" name="customer_id" value="<?php echo isset($customer_card_id) ? $customer_card_id : '' ?>" />
					<input type="text" id="customer_name" name="customer_name" value="<?php echo isset($customer_card_name) ? $customer_card_name : '' ?>" class="inv-field" />
				</div>
			</div>
			<?php endif ?>
			<div class="dataFL">
				<div class="dataFT">Invoice #</div>
				<div class="dataVD"><input type="text" name="invoice_id" class="inv-field" /></div>
			</div>
			<div class="dataFL">
				<div class="dataFT">Purchase Order</div>
				<div class="dataVD"><input type="text" name="po_number" class="inv-field" /></div>
			</div>
			<div class="dataFL">
				<div class="dataFT">Notes</div>
				<div class="dataVD"><input type="text" name="notes" class="inv-field" /></div>
			</div>
			<div class="dataFL">
				<div class="dataFT">&nbsp;</div>
				<div class="dataVD"><button type="button" id="search_btn" class="btn btn-primary">SUBMIT</button></div>
			</div>
		</div>
		<input type="hidden" id="page" name="page" />
		<input type="hidden" id="row_per_page" name="row_per_page" />
		</form>
	</div>

	<div class="subtitle-noshadow"><span class="subtitle-arrow"></span>CASES</div>
	<div class="subtitleContent">
		<div id="loader" style="display: none;"><img src="/resources/template/default_web/img/invoice/loading.gif" /></div>
		<div id="invoice_list">
			<table id="tbl_invoice" cellpadding="0" cellspacing="0" border="0" class="table table-striped">
			</table>
		</div>
	</div>
</div>