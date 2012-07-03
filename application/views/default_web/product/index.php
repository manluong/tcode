<script type="text/javascript" src="/resources/addon/product.js"></script>

<div id="boxes">
	<div class="subtitleContent">
		<form id="frm_search" action="/product/search" method="post">
			<input type="hidden" name="parent_id" value="<?php echo $parent_id ?>" />
		</form>

		<div id="loader" style="display: none;"><img src="/resources/template/default_web/img/invoice/loading.gif" /></div>
		<div id="product_list">
			<table id="tbl_product" cellpadding="0" cellspacing="0" border="0" class="table table-striped">
			</table>
		</div>
	</div>
</div>