<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/product.css" />
<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>ADD A NEW CATEGORY OR PRODUCT</span></div>
		<div id="input_data_info">
			<div id="product_radio">
				<input type="radio" id="rad_category" name="type" data-name="category" checked="checked" /><label for="rad_category">Category</label>
				<input type="radio" id="rad_product" name="type" data-name="product" style="margin-left: 15px;"  /><label for="rad_product">Product</label>
			</div>

			<div id="category" class="product_ul">
				<form id="frm_category" action="/product/category_save" method="post">
				<ul>
					<li class="clearfix">
						<span class="input_data_label">Category</span>
						<span class="fillter_input">
							<span style="line-height: 28px;"><?php echo $parent_name ?></span>
							<input type="hidden" name="parent_id" value="<?php echo $parent_id ?>" />
						</span>
					</li>
					<li class="clearfix">
						<span class="input_data_label">Name</span>
						<span class="fillter_input"><input type="text" name="name" class="inv-field" /></span>
					</li>
				</ul>
				</form>
			</div>

			<div id="product" class="product_ul" style="display: none;">
				<form id="frm_product" action="/product/save" method="post">
				<ul>
					<li class="clearfix">
						<span class="input_data_label">Category</span>
						<span class="fillter_input">
							<span style="line-height: 28px;"><?php echo $parent_name ?></span>
							<input type="hidden" name="category_id" value="<?php echo $parent_id ?>" />
						</span>
					</li>
					<li class="clearfix">
						<span class="input_data_label">Name</span>
						<span class="fillter_input"><input type="text" name="name" class="inv-field" /></span>
					</li>
					<li class="clearfix">
						<span class="input_data_label">Description</span>
						<span class="fillter_input"><input type="text" name="addon_desp[0][content]" class="inv-field" /></span>
					</li>
					<li class="clearfix">
						<span class="input_data_label">SKU</span>
						<span class="fillter_input"><input type="text" name="sku" class="inv-field" /></span>
					</li>
					<li class="clearfix">
						<span class="input_data_label">Price</span>
						<span class="fillter_input"><input type="text" name="addon_price[0][amount]" class="inv-field" /></span>
					</li>
				</ul>
				</form>
			</div>
		</div>

		<button type="button" id="btn_submit" class="btn btn-primary pull-right">SUBMIT</button>
	</div>
</div>