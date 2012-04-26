<table cellspacing="0" cellpadding="0" border="0" id="helloworld_list_table" class="table table-striped table-bordered">
	<thead>
	<tr>
		<th class="sorting_asc" rowspan="1" colspan="1" style="width: 80px;">Id</th>
		<th class="sorting" rowspan="1" colspan="1" style="width: 200px;">Subject</th>
		<th class="sorting" rowspan="1" colspan="1" style="width: 207px;">Created</th>
		<th class="sorting" rowspan="1" colspan="1" style="width: 291px;">Last Update</th>
		<th class="sorting" rowspan="1" colspan="1" style="width: 231px;">Assigned</th>
	</tr>
	</thead>
	
	<tbody>
	<tr class="odd">
		<td class=" sorting_1">1</td>
		<td>Help me</td>
		<td>2012-04-13 22:20:29</td>
		<td>2012-04-13 22:20:29</td>
		<td>222</td>
	</tr>
	</tbody>
</table>
<script>
	$(document).ready(function() {
		ajax_content('/tcode/helpdesk/sendjson_list', 'helloworld_list');
	});
</script>