<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/product.css" />
<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>PRODUCT INFORMATION</span></div>
		<div id="input_data_info">
			<div id="product" class="product_ul">
				<ul>
					<li>
						<span class="input_data_label">Category</span>
						<span class="fillter_input">
							<span class="clearfix" style="line-height: 28px;"><?php echo $parent_name ?></span>
						</span>
					</li>
					<li>
						<span class="input_data_label">Name</span>
						<span class="fillter_input">
							<span class="clearfix" style="line-height: 28px;"><?php echo $product['name'] ?></span>
						</span>
					</li>
					<li>
						<span class="input_data_label">Description</span>
						<span class="fillter_input">
							<span class="clearfix" style="line-height: 28px;"></span>
						</span>
					</li>
					<li>
						<span class="input_data_label">SKU</span>
						<span class="fillter_input">
							<span class="clearfix" style="line-height: 28px;"><?php echo $product['sku'] ?></span>
						</span>
					</li>
					<li>
						<span class="input_data_label">Price</span>
						<span class="fillter_input">
							<span class="clearfix" style="line-height: 28px;"></span>
						</span>
					</li>
				</ul>
				<a href="/product/edit/<?php echo $product['id'] ?>" class="btn btn-inverse pull-right">EDIT</a>
			</div>
		</div>
	</div>
</div>