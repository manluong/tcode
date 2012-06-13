<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/product.css" />
<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>PRODUCT INFORMATION</span></div>
		<div id="input_data_info">
			<div id="product" class="product_ul">
				<form id="frm_product" action="/product/save" method="post">
				<ul>
					<li>
						<span class="input_data_label">Category</span>
						<span class="fillter_input">
							<span class="clearfix" style="line-height: 28px;"><?php echo $parent_name ?></span>
							<input type="hidden" name="category_id" value="<?php echo $product['category_id'] ?>" />
						</span>
					</li>
					<li>
						<span class="input_data_label">Name</span>
						<span class="fillter_input"><input type="text" name="name" value="<?php echo $product['name'] ?>" class="inv-field" /></span>
					</li>
					<li>
						<span class="input_data_label">Description</span>
						<span class="fillter_input"><input type="text" name="description" class="inv-field" /></span>
					</li>
					<li>
						<span class="input_data_label">SKU</span>
						<span class="fillter_input"><input type="text" name="sku" value="<?php echo $product['sku'] ?>" class="inv-field" /></span>
					</li>
					<li>
						<span class="input_data_label">Price</span>
						<span class="fillter_input"><input type="text" name="price" class="inv-field" /></span>
					</li>
				</ul>
				</form>
			</div>
		</div>

		<button type="button" id="btn_submit" class="btn btn-primary pull-right">SUBMIT</button>
	</div>
</div>