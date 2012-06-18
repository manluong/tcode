<link rel="stylesheet" href="/resources/template/<?php echo get_template() ?>/css/product.css" />
<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div>
		<div class="invoice_title"><span class="arrow_title"></span><span>CATEGORY INFORMATION</span></div>
		<div id="input_data_info">
			<div id="category" class="product_ul">
				<ul>
					<li class="clearfix">
						<span class="input_data_label">Category</span>
						<span class="fillter_input">
							<span style="line-height: 28px;"><?php echo $parent_name ?></span>
						</span>
					</li>
					<li class="clearfix">
						<span class="input_data_label">Name</span>
						<span class="fillter_input">
							<span style="line-height: 28px;"><?php echo $category['name'] ?></span>
						</span>
					</li>
				</ul>
				<button class="btn btn-inverse btn-mini btn_edit" style="margin-left: 120px;" data-url="/product/category_edit/<?php echo $category['id'] ?>" data-container="category">EDIT DETAILS</button>
			</div>
		</div>
	</div>
</div>