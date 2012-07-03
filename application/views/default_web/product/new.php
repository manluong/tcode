<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div class="subtitle"><span class="subtitle-arrow"></span>ADD A NEW CATEGORY OR PRODUCT</div>
	<div class="subtitleContent">
		<div id="product_radio" class="dataF">
			<div class="dataFL clearfix">
				<div class="dataFT">&nbsp;</div>
				<div class="dataVD">
					<input type="radio" id="rad_category" name="type" checked="checked" data-name="category" /><label for="rad_category" style="display: inline;"> Category</label>
					<input type="radio" id="rad_product" name="type" data-name="product" style="margin-left: 15px;" /><label for="rad_product" style="display: inline;"> Product</label>
				</div>
			</div>
		</div>

		<div id="category" class="product_ul dataF">
			<form id="frm_category" action="/product/category_save" method="post">
			<div class="dataFL clearfix">
				<div class="dataFT">Category</div>
				<div class="dataVD">
					<?php echo $parent_name ?>
					<input type="hidden" name="parent_id" value="<?php echo $parent_id ?>" />
				</div>
			</div>
			<div class="dataFL clearfix">
				<div class="dataFT">Name</div>
				<div class="dataVD"><input type="text" name="name" /></div>
			</div>
			</form>
		</div>

		<div id="product" class="product_ul dataF" style="display: none;">
			<form id="frm_product" action="/product/save" method="post">
			<div class="dataFL clearfix">
				<div class="dataFT">Category</div>
				<div class="dataVD">
					<?php echo $parent_name ?>
					<input type="hidden" name="category_id" value="<?php echo $parent_id ?>" />
				</div>
			</div>
			<div class="dataFL clearfix">
				<div class="dataFT">Name</div>
				<div class="dataVD"><input type="text" name="name" /></div>
			</div>
			<div class="dataFL clearfix">
				<div class="dataFT">Description</div>
				<div class="dataVD"><input type="text" name="addon_desp[0][content]" /></div>
			</div>
			<div class="dataFL clearfix">
				<div class="dataFT">SKU</div>
				<div class="dataVD"><input type="text" name="sku" /></div>
			</div>
			<div class="dataFL clearfix">
				<div class="dataFT">Price</div>
				<div class="dataVD"><input type="text" name="addon_price[0][amount]" /></div>
			</div>
			</form>
		</div>

		<button type="button" id="btn_submit" class="btn btn-primary pull-right">SUBMIT</button>
	</div>
</div>