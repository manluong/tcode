<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/invoice.css" />
<script type="text/javascript" src="/resources/addon/invoice.js"></script>

<div id="invoice_nav">
	<ul class="nav_left">
		<li class="main">Invoice</li>
		<li>&gt; Edit</li>
	</ul>
	<ul class="nav_right">
		<li><a href="/invoice">List</a></li>
		<li><a href="/invoice/add">New</a></li>
	</ul>
</div>

<div id="invoice_info">
	<ul id="form_show">
		<li><span class="invoice_info_span">Customer</span><a href="#">Customer A</a></li>
		<li><span class="invoice_info_span">Date of Issue</span>05 03 2012</li>
		<li><span class="invoice_info_span">Due Date</span>25 03 2012</li>
		<li><span class="invoice_info_span">PO Number</span></li>
		<li><a class="invoice_info_link" href="#">Change</a></li>
	</ul>

	<!-- <ul style="" id="form_change">
		<li><span class="invoice_info_span">Subject</span> <span class="input_change">: <input type="text" id="subject" name="subject" value="aaaaa"></span></li>
		<li><span class="invoice_info_span">Creator</span> : <a href="#">Customer A</a></li>
		<li><span class="invoice_info_span">Assigned</span> : <a href="#">Staff A</a></li>
		<li><span class="invoice_info_span">CC</span> <span class="input_change">: <input type="text" id="cc_email" value="aaaa" name="cc_email"></span></li>
		<li><div href="#" class="btn btn-inverse" onclick="return show_form_show();">Save</div></li>
	</ul> -->
</div>

<table class="table">
<thead>
	<tr>
		<th></th>
		<th>Product</th>
		<th>Description</th>
		<th>Unit Price</th>
		<th>Qty</th>
		<th>Discount</th>
		<th>Tax</th>
		<th>Total</th>
		<th></th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><a href="#" class="add">+</a></td>
		<td>iPhone Case</td>
		<td>Xxxxo okoaoxa kokoxax</td>
		<td>$5</td>
		<td>100</td>
		<td></td>
		<td>GST</td>
		<td>$500</td>
		<td><a href="#" class="remove">x</a></td>
	</tr>
	<tr>
		<td><a href="#" class="add">+</a></td>
		<td>iPhone Case</td>
		<td>Xxxxo okoaoxa kokoxax</td>
		<td>$5</td>
		<td>100</td>
		<td></td>
		<td>GST</td>
		<td>$500</td>
		<td><a href="#" class="remove">x</a></td>
	</tr>
	<tr>
		<td><a href="#" class="add">+</a></td>
		<td>News Paper</td>
		<td>Xxxxo okoaoxa kokoxax</td>
		<td>$5</td>
		<td>100</td>
		<td></td>
		<td>GST</td>
		<td>$500</td>
		<td><a href="#" class="remove">x</a></td>
	</tr>
	<!-- <tr>
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
	</tr> -->
	<tr>
		<td><a href="#" class="add">+</a></td>
		<td>iPhone Case</td>
		<td>Xxxxo okoaoxa kokoxax</td>
		<td>$5</td>
		<td>100</td>
		<td></td>
		<td>GST</td>
		<td>$500</td>
		<td><a href="#" class="remove">x</a></td>
	</tr>
</tbody>
</table>

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
<div class="clear"></div>
<div id="terms" class="left">
	<span>Terms</span>
	<select class="right">
		<option>Pre Define Terms</option>
	</select>
	<br />
	<div class="clear"></div>
	<textarea></textarea>
</div>
<div id="terms" class="right">
	<span>Notes for Customer</span>
	<br />
	<textarea></textarea>
</div>
<input type="submit" class="btn" value="Submit" />
