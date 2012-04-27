<table cellspacing="0" cellpadding="0" border="0" id="helloworld_list_table" class="table table-striped table-bordered">
	<thead>
		<tr>
			<th class="sorting_asc" rowspan="1" colspan="1" style="width: 57px;">Id</th>
			<th class="sorting" rowspan="1" colspan="1" style="width: 97px;">Title</th>
			<th class="sorting" rowspan="1" colspan="1" style="width: 199px;">First Name</th>
			<th class="sorting" rowspan="1" colspan="1" style="width: 240px;">Middle Name</th>
			<th class="sorting" rowspan="1" colspan="1" style="width: 197px;">Last Name</th>
			<th class="sorting" rowspan="1" colspan="1" style="width: 202px;">Nick123456</th>
		</tr>
	</thead>
	
	<tbody>
		<tr class="odd">
			<td class=" sorting_1">181</td>
			<td>0</td>
			<td>Micheal</td>
			<td>M.</td>
			<td>Jordan</td>
			<td></td>
		</tr>
		<tr class="even">
			<td class=" sorting_1">182</td>
			<td>0</td>
			<td>Micheal</td>
			<td>M.</td>
			<td>Jordan</td>
			<td></td>
		</tr>
		
		<tr class="odd">
		
	</tbody>
</table>
<script>
	$(document).ready(function() {
		ajax_content('/tcode/helpdesk/sendjson_list', 'helloworld_list');
	});
</script>