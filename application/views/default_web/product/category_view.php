<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div class="subtitle"><span class="subtitle-arrow"></span>CATEGORY INFORMATION</div>
	<div class="subtitleContent">
		<div id="category" class="product_ul dataV">
			<div class="dataVL clearfix">
				<div class="dataVT">Category</div>
				<div class="dataVD"><?php echo $parent_name ?></div>
			</div>
			<div class="dataVL clearfix">
				<div class="dataVT">Name</div>
				<div class="dataVD"><?php echo $category['name'] ?></div>
			</div>
			<div class="dataVL clearfix">
				<div class="dataVT">&nbsp;</div>
				<div class="dataVD"><button class="btn btn-inverse btn-mini btn_edit" data-url="/product/category_edit/<?php echo $category['id'] ?>" data-container="category">EDIT DETAILS</button></div>
			</div>
		</div>
	</div>
</div>