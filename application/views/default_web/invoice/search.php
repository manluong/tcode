<table id="tbl_invoice" cellpadding="0" cellspacing="0" border="0" class="table table-striped">
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
			<td><?php echo trim($invoice->first_name.' '.$invoice->last_name) ?></td>
			<td style="text-align: center;"><a href="/invoice/view/<?php echo $invoice->id ?>"><?php echo $invoice->id ?></a></td>
			<td style="text-align: center;"><?php echo date('Y-m-d', strtotime($invoice->payment_due_stamp)) ?></td>
			<td style="text-align: right;"><?php echo '$'.number_format($invoice->total, 2) ?></td>
			<td></td>
			<td style="text-align: center;"><a href="/invoice/edit/<?php echo $invoice->id ?>">Edit</a></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
