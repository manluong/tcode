<?php if ($invoice_list): ?>
<?php
	$min = max(1, $current_page - 2);
	$max = min($max_page, $min + 4);
	if ($max - $min < 4) {
		$min = ($max_page < 5) ? 1 : $max - 4;
	}
?>

<div id="invoice_cases">
	<div id="top_cases">
		<div class="invoice_title"><span class="arrow_title"></span><span>Cases</span></div>
		<div class="invoice_pagination">
			<ul class="page">
				<li class="prev<?php echo ($current_page == 1) ? ' disabled' : '' ?>"><a href="#" data-page="<?php echo $current_page - 1 ?>">Previous</a></li>
				<?php for ($i = $min; $i <= $max; $i++): ?>
				<li<?php echo ($i == $current_page) ? ' class="active"' : '' ?>><a href="#" data-page="<?php echo $i ?>"><?php echo $i ?></a></li>
				<?php endfor; ?>
				<li class="next<?php echo ($current_page == $max_page) ? ' disabled' : '' ?>"><a href="#" data-page="<?php echo $current_page + 1 ?>">Next</a></li>
			</ul>
		</div>
	</div>
	<div id="main_cases">
		<table>
			<thead>
				<tr>
					<th style="width: 60px;"></th>
					<th style="width: 200px;">Customer</th>
					<th style="width: 100px;text-align: center;">Invoice #</th>
					<th style="width: 150px;text-align: center;">Date</th>
					<th style="width: 100px;text-align: right;">Total</th>
					<th style="width: 150px;text-align: center;">Status</th>
					<th style="width: 100px;text-align: center;">Edit</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($invoice_list as $invoice): ?>
				<tr>
					<td style="text-align: center;"><input type="checkbox" /></td>
					<td><?php echo $invoice->nickname ?></td>
					<td style="text-align: center;"><a href="/invoice/view/<?php echo $invoice->id ?>"><?php echo $invoice->id ?></a></td>
					<td style="text-align: center;"><?php echo date('Y-m-d', strtotime($invoice->payment_due_stamp)) ?></td>
					<td style="text-align: right;"><?php echo '$'.number_format($invoice->total, 2) ?></td>
					<td></td>
					<td style="text-align: center;"><a href="/invoice/edit/<?php echo $invoice->id ?>">Edit</a></td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?php else: ?>
No invoice found
<?php endif ?>
