<script src="/resources/addon/jquery.tagsinput.min.js" type="text/javascript"></script>






<style>
	div.tagsinput { border:1px solid #CCC; background: #FFF; padding:5px; width:300px; height:100px; overflow-y: auto;}
	div.tagsinput span.tag { border: 1px solid #a5d24a; -moz-border-radius:2px; -webkit-border-radius:2px; display: block; float: left; padding: 5px; text-decoration:none; background: #cde69c; color: #638421; margin-right: 5px; margin-bottom:5px;font-family: helvetica;  font-size:13px;}
	div.tagsinput span.tag a { font-weight: bold; color: #82ad2b; text-decoration:none; font-size: 11px;  }
	div.tagsinput input { width:80px; margin:0px; font-family: helvetica; font-size: 13px; border:1px solid transparent; padding:5px; background: transparent; color: #000; outline:0px;  margin-right:5px; margin-bottom:5px; }
	div.tagsinput div { display:block; float: left; }
	.tags_clear { clear: both; width: 100%; height: 0px; }
	.not_valid {background: #FBD8DB !important; color: #90111A !important;}
</style>





<input type="hidden" name="app_id" id="app_id" value="<?=$app_id?>" />
<input type="hidden" name="app_data_id" id="app_data_id" value="<?=$app_data_id?>" />
<input name="tags" id="tags" value="<?=implode(',', $tags)?>" />






<script>
	$(document).ready(function(){
		$('#tags').tagsInput({
			'height': '100%',
			'width': '100%',
			'defaultText': 'Add New Tag',
			'onAddTag': function(tag) {
				$.post(
					'/tags/ajax_add',
					{ app_id:$('#app_id').val(), app_data_id:$('#app_data_id').val(), tag:tag },
					function(response) {

					},
					'json'
				);
			},
			'onRemoveTag': function(tag) {
				$.post(
					'/tags/ajax_remove',
					{ app_id:$('#app_id').val(), app_data_id:$('#app_data_id').val(), tag:tag },
					function(response) {

					},
					'json'
				);
			},
			'removeWithBackspace': false
		});




	});
</script>