<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="breadcrumb">
	<div id="module_name">
		<ul>
			<li><a href="/invoice" class="main">INVOICE</a></li>
			<li class="arrow"></li>
			<li class="curent_page">List</li>
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

<div id="boxes">
	<form id="frm_search" action="/invoice/search" method="post">
	<div id="invoice_fillter">
		<div class="invoice_title"><span class="arrow_title"></span><span>Fillter</span></div>
		<div id="items_fillter">
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
						<div data-toggle="buttons-radio" class="btn-group">
							<button type="button" class="btn">All</button>
							<button type="button" class="btn">Paid</button>
							<button type="button" class="btn">Unpaid</button>
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
		<div class="ClearLeft"></div>
		<div id="input_data_fillter">
			<ul>
				<li>
					<span class="input_data_label">Customer</span>
					<span class="fillter_input">
						<input type="hidden" id="customer_id" name="customer_id" value="<?php echo isset($customer_card_id) ? $customer_card_id : '' ?>" />
						<input type="text" id="customer_name" name="customer_name" value="<?php echo isset($customer_card_name) ? $customer_card_name : '' ?>" class="inv-field" />
					</span>
				</li>
				<li>
					<span class="input_data_label">Invoice #</span>
					<span class="fillter_input"><input type="text" name="invoice_id" class="inv-field" /></span>
				</li>
				<li>
					<span class="input_data_label">Purchase Order</span>
					<span class="fillter_input"><input type="text" name="po_number" class="inv-field" /></span>
				</li>
				<li>
					<span class="input_data_label">Notes</span>
					<span class="fillter_input"><input type="text" name="notes" class="inv-field" /></span>
				</li>
				<li>
					<span class="input_data_label"></span>
					<span class="fillter_input"><button type="button" id="search_btn" class="btn btn-primary">SUBMIT</button></span>
				</li>
			</ul>
		</div>
	</div>

	<input type="hidden" id="page" name="page" />
	<input type="hidden" id="row_per_page" name="row_per_page" />
	</form>

	<div id="invoice_cases">
		<div class="invoice_title"><span class="arrow_title"></span><span>Cases</span></div>
		<div id="invoice_list">
			<table id="tbl_invoice" cellpadding="0" cellspacing="0" border="0" class="table table-striped">
			</table>
		</div>
	</div>
</div>
