<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div class="subtitle"><span class="subtitle-arrow"></span>PRODUCT INFORMATION</div>
	<div class="subtitleContent">
		<div id="product" class="product_ul dataV">
			<div class="dataVL clearfix">
				<div class="dataVT">Category</div>
				<div class="dataVD"><?php echo $parent_name ?></div>
			</div>
			<div class="dataVL clearfix">
				<div class="dataVT">Name</div>
				<div class="dataVD"><?php echo $product['name'] ?></div>
			</div>
			<div class="dataVL clearfix">
				<div class="dataVT">Description</div>
				<div class="dataVD"><?php echo isset($product['addon_desp']) ? $product['addon_desp'][0]['content'] : '' ?></div>
			</div>
			<div class="dataVL clearfix">
				<div class="dataVT">SKU</div>
				<div class="dataVD"><?php echo $product['sku'] ?></div>
			</div>
			<div class="dataVL clearfix">
				<div class="dataVT">Price</div>
				<div class="dataVD"><?php echo isset($product['addon_price']) ? '$'.number_format($product['addon_price'][0]['amount'], 2) : '' ?></div>
			</div>
			<div class="dataVL clearfix">
				<div class="dataVT">&nbsp;</div>
				<div class="dataVD"><button class="btn btn-inverse btn-mini btn_edit" data-url="/product/edit/<?php echo $product['id'] ?>" data-container="product">EDIT DETAILS</button></div>
			</div>
		</div>
	</div>
</div>