<form id="frm_category" action="/product/category_save" method="post">
<input type="hidden" name="id" value="<?php echo $category['id'] ?>" />
<div class="dataF">
	<div class="dataFL clearfix">
		<div class="dataFT">Category</div>
		<div class="dataVD">
			<?php echo $parent_name ?>
			<input type="hidden" name="parent_id" value="<?php echo $category['parent_id'] ?>" />
		</div>
	</div>
	<div class="dataFL clearfix">
		<div class="dataFT">Name</div>
		<div class="dataVD"><input type="text" name="name" value="<?php echo $category['name'] ?>" /></div>
	</div>
	<div class="dataFL clearfix">
		<div class="dataFT">&nbsp;</div>
		<div class="dataVD"><button type="button" id="btn_submit" class="btn btn-primary btn-mini">SUBMIT</button></div>
	</div>
</div>
</form>