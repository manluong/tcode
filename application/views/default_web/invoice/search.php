<?php if ($invoice_list): ?>
<?php
	$min = max(1, $current_page - 2);
	$max = min($max_page, $min + 4);
	if ($max - $min < 4) {
		$min = ($max_page < 5) ? 1 : $max - 4;
	}
?>
<div class="widget">
	<div class="widget-body">
		<div class="dataTables_wrapper form-inline" id="invoice_list_table_wrapper">
			<div>
				<div class="pull-right">
					<div class="dataTables_paginate paging_bootstrap pagination">
						<ul>
							<li class="prev<?php echo ($current_page == 1) ? ' disabled' : '' ?>"><a href="#" data-page="<?php echo $current_page - 1 ?>">Previous</a></li>
							<?php for ($i = $min; $i <= $max; $i++): ?>
							<li<?php echo ($i == $current_page) ? ' class="active"' : '' ?>><a href="#" data-page="<?php echo $i ?>"><?php echo $i ?></a></li>
							<?php endfor; ?>
							<li class="next<?php echo ($current_page == $max_page) ? ' disabled' : '' ?>"><a href="#" data-page="<?php echo $current_page + 1 ?>">Next</a></li>
						</ul>
					</div>
				</div>
			</div>
			<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="invoice_list_table">
				<thead>
					<tr>
						<th class="sorting_asc" rowspan="1" colspan="1" style="width: 60px;"></th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 400px;">Customer</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 200px;">Invoice #</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 200px;">Date</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 200px;">Total</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 200px;">Status</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 60px; text-align: center;">Edit</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($invoice_list as $invoice): ?>
					<tr>
						<td style="text-align: center;"><input type="checkbox" /></td>
						<td><?php echo $invoice->nickname ?></td>
						<td><a href="/invoice/view/<?php echo $invoice->id ?>"><?php echo $invoice->id ?></a></td>
						<td><?php echo date('Y-m-d', strtotime($invoice->payment_due_stamp)) ?></td>
						<td style="text-align: right;"><?php echo (float)$invoice->total ?></td>
						<td></td>
						<td style="text-align: center;"><a href="/invoice/edit/<?php echo $invoice->id ?>">Edit</a></td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<div>
				<div class="pull-right">
					<div class="dataTables_paginate paging_bootstrap pagination">
						<ul>
							<li class="prev<?php echo ($current_page == 1) ? ' disabled' : '' ?>"><a href="#" data-page="<?php echo $current_page - 1 ?>">Previous</a></li>
							<?php for ($i = $min; $i <= $max; $i++): ?>
							<li<?php echo ($i == $current_page) ? ' class="active"' : '' ?>><a href="#" data-page="<?php echo $i ?>"><?php echo $i ?></a></li>
							<?php endfor; ?>
							<li class="next<?php echo ($current_page == $max_page) ? ' disabled' : '' ?>"><a href="#" data-page="<?php echo $current_page + 1 ?>">Next</a></li>
						</ul>
					</div>
				</div>
				<div id="invoice_list_table_length" class="dataTables_length">
					<label>
						<select>
							<option value="1"<?php echo ($row_per_page == 1) ? ' selected="selected"' : '' ?>>1</option>
							<option value="2"<?php echo ($row_per_page == 2) ? ' selected="selected"' : '' ?>>2</option>
							<option value="3"<?php echo ($row_per_page == 3) ? ' selected="selected"' : '' ?>>3</option>
							<option value="4"<?php echo ($row_per_page == 4) ? ' selected="selected"' : '' ?>>4</option>
							<option value="5"<?php echo ($row_per_page == 5) ? ' selected="selected"' : '' ?>>5</option>
							<option value="-1"<?php echo ($row_per_page == -1) ? ' selected="selected"' : '' ?>">All</option>
						</select> Rows
					</label>
				</div>
				<div class="dataTables_info" id="invoice_list_table_info">Showing <?php echo ($current_page-1)*$row_per_page+1 ?> to <?php echo ($row_per_page == -1) ? $total_record : min($current_page*$row_per_page, $total_record) ?> of <?php echo $total_record ?></div>
			</div>
		</div>
	</div>
</div>
<?php else: ?>
No invoice found
<?php endif ?>