<div class="container-fluid">
	<table class="table table-bordered table-condensed table-striped">
		<thead>
			<tr>
				<td>ID</td>
				<td>Name</td>
				<td>Action</td>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($list AS $l) {
				echo '<tr>';
				echo '<td>',$l['id'],'</td>';
				echo '<td><a href="/card/view/',$l['id'],'">',$l['first_name'],' ',$l['last_name'],'</a></td>';
				echo '<td><a href="/card/edit/',$l['id'],'">Edit</a></td>';
				echo '</tr>';
			}
		?>
		</tbody>
	</table>
</div>