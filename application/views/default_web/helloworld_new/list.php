<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th>Name</th>
			<th>Nickname</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach($list AS $l) {
		echo '<tr>';
		echo '<td>',$l['title_options'][$l['title']],' ',$l['first_name'],' ',$l['last_name'],'</td>';
		echo '<td>',$l['nickname'],'</td>';
		echo '</tr>';
	}
?>
	</tbody>
</table>