<?php if ($invoice_list): ?>
<table class="table">
	<thead>
		<tr>
			<th></th>
			<th>Customer</th>
			<th>Invoice #</th>
			<th>Date</th>
			<th>Total</th>
			<th>Status</th>
			<th></th>
		<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($invoice_list as $invoice): ?>
		<tr>
			<td></td>
			<td><?php echo $invoice->nickname ?></td>
			<td><a href="/invoice/view/<?php echo $invoice->id ?>"><?php echo $invoice->id ?></a></td>
			<td><?php echo date('Y-m-d', strtotime($invoice->payment_due_stamp)) ?></td>
			<td style="text-align: right;"><?php echo (float)$invoice->total ?></td>
			<td></td>
			<td></td>
			<td><a href="/invoice/edit/<?php echo $invoice->id ?>">Edit</a></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php else: ?>
No invoice found
<?php endif ?>