<form id="frm_product" action="/product/save" method="post">
<input type="hidden" name="id" value="<?php echo $product['id'] ?>" />
<div class="dataF">
	<div class="dataFL clearfix">
		<div class="dataFT">Category</div>
		<div class="dataVD">
			<?php echo $parent_name ?>
			<input type="hidden" name="category_id" value="<?php echo $product['category_id'] ?>" />
		</div>
	</div>
	<div class="dataFL clearfix">
		<div class="dataFT">Name</div>
		<div class="dataVD"><input type="text" name="name" value="<?php echo $product['name'] ?>" /></div>
	</div>
	<div class="dataFL clearfix">
		<div class="dataFT">Description</div>
		<div class="dataVD">
			<?php if (isset($product['addon_desp'])): ?>
			<input type="hidden" name="addon_desp[0][id]" value="<?php echo $product['addon_desp'][0]['id'] ?>" />
			<input type="text" name="addon_desp[0][content]" value="<?php echo $product['addon_desp'][0]['content'] ?>" />
			<?php else: ?>
			<input type="hidden" name="addon_desp[0][id]" />
			<input type="text" name="addon_desp[0][content]" />
			<?php endif ?>
		</div>
	</div>
	<div class="dataFL clearfix">
		<div class="dataFT">SKU</div>
		<div class="dataVD"><input type="text" name="sku" value="<?php echo $product['sku'] ?>" /></div>
	</div>
	<div class="dataFL clearfix">
		<div class="dataFT">Price</div>
		<div class="dataVD">
			<?php if (isset($product['addon_price'])): ?>
			<input type="hidden" name="addon_price[0][id]" value="<?php echo $product['addon_price'][0]['id'] ?>" />
			<input type="text" name="addon_price[0][amount]" value="<?php echo $product['addon_price'][0]['amount'] ?>" />
			<?php else: ?>
			<input type="hidden" name="addon_price[0][id]" />
			<input type="text" name="addon_price[0][amount]" />
			<?php endif ?>
		</div>
	</div>
	<div class="dataFL clearfix">
		<div class="dataFT">&nbsp;</div>
		<div class="dataVD"><button type="button" id="btn_submit" class="btn btn-primary btn-mini">SUBMIT</button></div>
	</div>
</div>
</form>