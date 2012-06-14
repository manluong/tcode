<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/product.css" />
<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>CATEGORY INFORMATION</span></div>
		<div id="input_data_info">
			<div id="category" class="product_ul">
				<form id="frm_category" action="/product/category_save" method="post">
				<input type="hidden" name="id" value="<?php echo $category['id'] ?>" />
				<ul>
					<li>
						<span class="input_data_label">Category</span>
						<span class="fillter_input">
							<span class="clearfix" style="line-height: 28px;"><?php echo $parent_name ?></span>
							<input type="hidden" name="parent_id" value="<?php echo $category['parent_id'] ?>" />
						</span>
					</li>
					<li>
						<span class="input_data_label">Name</span>
						<span class="fillter_input"><input type="text" name="name" value="<?php echo $category['name'] ?>" class="inv-field" /></span>
					</li>
				</ul>
				<button type="button" id="btn_submit" class="btn btn-primary pull-right">SUBMIT</button>
				</form>
			</div>
		</div>
	</div>
</div>