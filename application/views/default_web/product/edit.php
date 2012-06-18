<form id="frm_product" action="/product/save" method="post">
<input type="hidden" name="id" value="<?php echo $product['id'] ?>" />
<ul>
	<li class="clearfix">
		<span class="input_data_label">Category</span>
		<span class="fillter_input">
			<span style="line-height: 28px;"><?php echo $parent_name ?></span>
			<input type="hidden" name="category_id" value="<?php echo $product['category_id'] ?>" />
		</span>
	</li>
	<li class="clearfix">
		<span class="input_data_label">Name</span>
		<span class="fillter_input"><input type="text" name="name" value="<?php echo $product['name'] ?>" class="inv-field" /></span>
	</li>
	<li class="clearfix">
		<span class="input_data_label">Description</span>
		<span class="fillter_input">
			<?php if (isset($product['addon_desp'])): ?>
			<input type="hidden" name="addon_desp[0][id]" value="<?php echo $product['addon_desp'][0]['id'] ?>" class="inv-field" />
			<input type="text" name="addon_desp[0][content]" value="<?php echo $product['addon_desp'][0]['content'] ?>" class="inv-field" />
			<?php else: ?>
			<input type="hidden" name="addon_desp[0][id]" class="inv-field" />
			<input type="text" name="addon_desp[0][content]" class="inv-field" />
			<?php endif ?>
		</span>
	</li>
	<li class="clearfix">
		<span class="input_data_label">SKU</span>
		<span class="fillter_input"><input type="text" name="sku" value="<?php echo $product['sku'] ?>" class="inv-field" /></span>
	</li>
	<li class="clearfix">
		<span class="input_data_label">Price</span>
		<span class="fillter_input">
			<?php if (isset($product['addon_price'])): ?>
			<input type="hidden" name="addon_price[0][id]" value="<?php echo $product['addon_price'][0]['id'] ?>" class="inv-field" />
			<input type="text" name="addon_price[0][amount]" value="<?php echo $product['addon_price'][0]['amount'] ?>" class="inv-field" />
			<?php else: ?>
			<input type="hidden" name="addon_price[0][id]" class="inv-field" />
			<input type="text" name="addon_price[0][amount]" class="inv-field" />
			<?php endif ?>
		</span>
	</li>
</ul>
<button type="button" id="btn_submit" class="btn btn-primary btn-mini" style="margin-left: 120px;">SUBMIT</button>
</form>