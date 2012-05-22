<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/invoice.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="breadcrumb">
	<div id="module_name">
		<ul>
			<li><a href="/invoice" class="main">INVOICE</a></li>
			<li class="arrow"></li>
			<li class="curent_page">Pay</li>
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

<div id="title_item_edit">Invoice #<?php echo $invoice_id ?></div>

<div id="boxes">
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>PAYMENT INFORMATION</span></div>
		<div id="input_data_info">
			<ul>
				<li>
					<span class="input_data_label">Amount</span>
					<span class="fillter_input"><input type="text" class="inv-field" /></span>
				</li>
				<li>
					<span class="input_data_label">Payment Type</span>
					<span class="fillter_input"><input type="text" class="inv-field" /></span>
				</li>
				<li>
					<span class="input_data_label">Transaction No.</span>
					<span class="fillter_input"><input type="text" class="inv-field" /></span>
				</li>
			</ul>
		</div>
	</div>
</div>