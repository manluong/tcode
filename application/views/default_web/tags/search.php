<div style="padding:5px;" id="search">
	Search Tag: <input type="text" name="tag" id="search_field" value="<?=$search?>" />
	To search for more than 1 tag, separate your tags with commas.
</div>

<table border="1" cellspacing="2" cellpadding="2" id="results_table">
	<tr id="header_row">
		<th>Application</th>
		<th>Item</th>
		<th>Tags</th>
	</tr>

</table>



<script>
	$(document).ready(function(){

		if ($('#search_field').val()!='') ajax_search_tag($('#search_field').val());

		$('#search').on('keypress', '#search_field', function(e){
			if (e.which == 13) {	//if enter key is pressed
				var textbox = $('#search_field');
				var tag = textbox.val();

				if (tag.length == 0) {
					$('.search_result').remove();

					e.preventDefault();
					return false;
				}

				ajax_search_tag(tag);

				//prevent enter key from submitting the form
				e.preventDefault();
				return false;
			}
		});


		function ajax_search_tag(tag) {
			$.post(
				'/tags/ajax_search',
				{ tag: tag },
				function(response) {
					$('.search_result').remove();

					if (response.success) {
						$.each(response.data, function(k, v) {
							$('#results_table').append(
								'<tr class="search_result">'+
									'<td>'+v.app_name+'</td>'+
									'<td><a href="/'+v.app_name+'/view/'+v.app_data_id_encoded+'">'+v.app_data_name+'</a></td>'+
									'<td>'+v.tags.join(', ')+'</td>'+
								'</tr>'
							);
						});
					} else {
						alert('Server Error: '+response.message);
					}
				},
				'json'
			);
		}
	});
</script>