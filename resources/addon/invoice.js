function add_last_row() {
	var c = $('#invoice_item_count').val();
	var html = $('#invoice_item_template').html();
	while (html.indexOf('{xxxxx}') != -1) {
		html = html.replace('{xxxxx}', c);
	}
	$('#invoice_item_list').append(html);

	var new_item = $('#invoice_item_list .temp');
	new_item.removeClass('temp');
	bind_event_row(new_item);

	$('#invoice_item_count').val(parseInt(c) + 1);
}

/*function add_row(object) {
	var html = $('#invoice_item_template').html();
	var item = $(object).closest('div.invoice_item');
	$(item).after(html);

	var new_item = $('#invoice_item_list .temp');
	new_item.removeClass('temp');
	bind_event_row(new_item);
}*/

function remove_row(object) {
	var item = $(object).closest('div.invoice_item');
	item.remove();

	if ($('#invoice_item_list div.invoice_item').length == 0) {
		add_last_row();
	}

	cal_invoice_total();
}

function get_json(obj, id) {
	var i = null;
	$.each(obj, function(index, item) {
		if (item.id == id) {
			i = index;
		}
	});

	if (i == null) {
		return false;
	} else {
		return obj[i];
	}
}

function cal_tax(tax_use_id, amount) {
	var tax_use = get_json(ary_tax_use, tax_use_id);

	var result = [];
	var tax = null;
	var tax_1 = tax_2 = tax_3 = 0;

	if (tax_use['tax_id_1']) {
		tax = get_json(ary_tax, tax_use['tax_id_1']);
		tax_1 = amount * tax['percent'] / 100;
		result.push({id: tax['id'], name: tax['name'], amount: tax_1});
	}

	if (tax_use['tax_id_2']) {
		tax = get_json(ary_tax, tax_use['tax_id_2']);
		if (tax_use['tax_2_compound'] == 1) {
			tax_2 = (amount + tax_1) * tax['percent'] / 100;
		} else {
			tax_2 = amount * tax['percent'] / 100;
		}
		result.push({id: tax['id'], name: tax['name'], amount: tax_2});
	}

	if (tax_use['tax_id_3']) {
		tax = get_json(ary_tax, tax_use['tax_id_3']);
		if (tax_use['tax_3_compound'] == 1) {
			tax_3 = (amount + tax_1 + tax_2) * tax['percent'] / 100;
		} else {
			tax_3 = amount * tax['percent'] / 100;
		}
		result.push({id: tax['id'], name: tax['name'], amount: tax_3});
	}

	result.push({id: '0', name: 'Total', amount: tax_1 + tax_2 + tax_3});
	return result;
}

function cal_item_total(object) {
	var item = $(object).closest('div.invoice_item');
	var price = parseFloat(item.find('.unit_price').val()) || 0;
	var qty = parseInt(item.find('.qty').val()) || 0;
	var discount = parseFloat(item.find('.discount').val()) || 0;
	var total = price*qty*(100-discount)/100;
	item.find('.item_total').val(total.toFixed(2));
	item.find('.item_total_label').html(format_money(total));

	cal_invoice_total();
}

function cal_invoice_total() {
	var sub_total = 0;
	var invoice_total = 0;

	var tax_detail = [];
	$.each(ary_tax, function(index, tax) {
		tax_detail.push({id: tax['id'], name: tax['name'], amount: 0});
	});

	$('#invoice_item_list .invoice_item').each(function(index, item) {
		var price = parseFloat($(item).find('.unit_price').val()) || 0;
		var qty = parseInt($(item).find('.qty').val()) || 0;
		var discount = parseInt($(item).find('.discount').val()) || 0;

		var tax = 0;
		if ($(item).find('.tax').val()) {
			t = cal_tax($(item).find('.tax').val(), price*qty*(100-discount)/100);
			tax = t[t.length-1].amount;

			$.each(tax_detail, function(index_1, item_1) {
				$.each(t, function(index_2, item_2) {
					if (item_1.id == item_2.id) {
						item_1.amount += item_2.amount;
					}
				});
			});
		}

		sub_total += price*qty*(100-discount)/100;
		invoice_total += price*qty*(100-discount)/100+tax;
	});

	$('#invoice_total').val(invoice_total.toFixed(2));
	$('#lbl_sub_total').html(format_money(sub_total));
	$('#lbl_invoice_total').html(format_money(invoice_total));
	$('#lbl_balance').html(format_money(invoice_total));

	$.each(tax_detail, function(index, item) {
		$('#tax_'+item.id+'_total').val(item.amount.toFixed(2));
		$('#lbl_tax_'+item.id+'_total').html(format_money(item.amount));
	});

	hide_tax();
}

function hide_tax() {
	$.each($('#total_price .total_hide'), function(index, item) {
		if ($(this).find('span').html() != '$0.00') {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
}

function bind_event_row(item) {
	$(item).find('.item_datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$(item).find('.product_name').autocomplete({
		source: '/invoice/get_product',
		minLength: 2,
		select: function(e, ui) {
			var item = $(this).closest('div.invoice_item');
			item.find('.product_id').val(ui.item.product.id);
			if (item.find('.qty').val() == '' || item.find('.qty').val() == 0) {
				item.find('.qty').val(1);
			}
			item.find('.unit_price').val(ui.item.product.price).change();
		}
	});

	$(item).find('.cal').on('change', function() {
		var val = parseFloat($(this).val()) || 0;
		$(this).val(val);
		cal_item_total(this);
	});
}

function more(object) {
	var item = $(object).closest('div.invoice_item');
	item.find('div.invoice_item_sub').slideToggle();
}

function format_money(n, c, d, t, q) {
	c = isNaN(c = Math.abs(c)) ? 2 : c;
	d = d == undefined ? '.' : d;
	t = t == undefined ? ',' : t;
	q = q == undefined ? '$' : q;
	s = n < 0 ? '-' : '';
	i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '';
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + q + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
}

var req_search;
function search_invoice() {
	if ($('#frm_search').length == 0) return;
	if (req_search) req_search.abort();

	req_search = $.ajax({
		type: 'POST',
		url: $('#frm_search').attr('action'),
		data: $('#frm_search').serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#loader').show();
			$('#invoice_list').hide();
		},
		complete: function() {
			$('#loader').hide();
			$('#invoice_list').show();
			$('#tbl_invoice').css('width', '100%');
		},
		success: function(resp) {
			if (!resp.success) return;

			var data = new Array();
			for (i in resp.details) {
				var item = resp.details[i];
				var name = '';
				if (item.display_name) {
					name = item.display_name;
				} else {
					name = item.first_name+' '+item.last_name;
				}
				var row = new Array();
				row[0] = name;
				row[1] = '<a href="/invoice/view/'+item.id+'">'+item.id+'</a>';
				var date = new Date((item.payment_due_stamp).substring(0, 10));
				row[2] = $.datepicker.formatDate('yy-mm-dd', date);
				row[3] = format_money(item.total);
				row[4] = (item.paid_status == 1) ? 'Paid' : 'Unpaid';
				row[5] = '<a href="/invoice/edit/'+item.id+'">Edit</a></td>';
				data.push(row);
			}

			$('#tbl_invoice').dataTable({
				"bDestroy" : true,
				"aaData": data,
				"aoColumns": [
					{ "sTitle": "Customer" },
					{ "sTitle": "Invoice #" },
					{ "sTitle": "Date" },
					{ "sTitle": "Total" },
					{ "sTitle": "Status" },
					{ "sTitle": "Edit" }
				],
				"sDom": "<<'pull-right'p>>t<<'pull-right'p>lfi>",
				"sPaginationType": "bootstrap",
				"iDisplayLength": 10,
				"oLanguage": {
					"sSearch" : "<div class=\"input-prepend\"><span class=\"add-on\"><i class=\"icon-filter\"></i></span></i>_INPUT_</div>",
					"sInfo": "_START_ to _END_ of _TOTAL_",
					"sLengthMenu": "_MENU_ Rows per Page",
					"sInfoFiltered": " - filtering from _MAX_ records",
					"oPaginate": {
						"sPrevious": "Previous",
						"sNext": "Next"
				},
				"sLengthMenu": '<select>'+
					'<option value="10">10</option>'+
					'<option value="20">20</option>'+
					'<option value="30">30</option>'+
					'<option value="40">40</option>'+
					'<option value="50">50 Rows</option>'+
					'<option value="-1">All</option>'+
					'</select>'
			}});
		}
	});
}

function cal_pay_total() {
	var pay_total = 0;
	$('#tbl_pay .tr_pay').each(function(index, item) {
		var pay_amount = parseFloat($(item).find('.pay_amount').val()) || 0;
		pay_total += pay_amount;
	});

	$('#pay_total').val(pay_total);
	$('#lbl_pay_total').html(format_money(pay_total));
}

$(document).ready(function() {
	$('#date_range').on('change', function(e) {
		if ($(this).val() == 1) {
			now = new Date();
			$('#date_range_from').val($.datepicker.formatDate('yy-mm-dd', new Date(now.getFullYear(), now.getMonth() - 1, 1)));
			$('#date_range_to').val($.datepicker.formatDate('yy-mm-dd', new Date(new Date(now.getFullYear(), now.getMonth(), 1) - 1)));
		} else {
			$('#date_range_from').val('');
			$('#date_range_to').val('');
		}
		search_invoice();
	});

	$('#customer_name').autocomplete({
		source: '/card/ajax_auto_customer',
		minLength: 2,
		select: function(e, ui) {
			$('#customer_id').val(ui.item.id);
		}
	});
	$('#customer_name').on('change', function(e) {
		$('#customer_id').val('');
	});

	$('#status-group button').on('click', function() {
		$('#status').val($(this).data('value'));
		search_invoice();
	});

	$('#slider-range').slider({
		range: true,
		min: parseInt($('#total_default_min').val()),
		max: parseInt($('#total_default_max').val()),
		values: [parseInt($('#total_default_min').val()), parseInt($('#total_default_max').val())],
		slide: function(event, ui) {
			$('#total_min').val(ui.values[0]);
			$('#total_max').val(ui.values[1]);
			$('#lbl_total').html('$'+ui.values[0]+' - '+'$'+ui.values[1]);
		},
		change: function(event, ui) {
			search_invoice();
		}
	});
	$('#total_min').val($('#slider-range').slider('values', 0));
	$('#total_max').val($('#slider-range').slider('values', 1));

	$('#arrow').click(function() {
		$('#input_data_fillter').slideToggle();

		if ($('#arrow').attr('class') == 'down_arrow') {
			$('#arrow').removeClass('down_arrow');
			$('#arrow').addClass('up_arrow');
		} else {
			$('#arrow').removeClass('up_arrow');
			$('#arrow').addClass('down_arrow');
			$('#input_data_fillter input').val('');
		}
	});

	$('#search_btn').on('click', function(e) {
		search_invoice();
	});

	$('#btn_print').on('click', function(e) {
		if ($('#iframe_print').attr('src') == '') {
			$('#iframe_print').attr('src', $(this).data('url'));
			$('#iframe_print').load(function() {
				document.getElementById('iframe_print').contentWindow.print();
			});
		} else {
			document.getElementById('iframe_print').contentWindow.print();
		}
	});

	$('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		onSelect: function() {
			search_invoice();
		}
	});

	$('.row_delete').live('click', function(e) {
		e.preventDefault();
		remove_row(this);
	});
	$('.row_more').live('click', function(e) {
		e.preventDefault();
		more(this);

		if ($(this).hasClass('row_down')){
			$(this).removeClass('row_down');
			$(this).addClass('row_up');
		} else {
			$(this).removeClass('row_up');
			$(this).addClass('row_down');
		}
	});
	$('#add_row input').on('click', function(e) {
		add_last_row();
	});

	$('#apply_all_discount').on('change', function() {
		var val = parseInt($(this).val()) || '';
		$(this).val(val);

		$('#invoice_item_list .discount').each(function(index, item) {
			$(this).val(val);
			cal_item_total(this);
		});

		cal_invoice_total();
	});

	$('#apply_all_tax').on('change', function() {
		var val = $(this).val();
		if (val != -1) {
			$('#invoice_item_list .tax').each(function(index, item) {
				$(this).val(val);
			});

			cal_invoice_total();
		}
	});

	$('#terms_id').on('change', function(e) {
		var id = $(this).val();
		if (id) {
			$.ajax({
				type: 'GET',
				url: '/invoice/get_terms/'+id,
				success: function(resp) {
					if (resp.success) {
						$('#terms_content').val(resp.details);
					}
				}
			});
		}
	});
	$('#terms_content').on('change', function(e) {
		$('#terms_id').val('');
	});

	$('#invoice_item_list').sortable({
		axis: 'y',
		handle: '.row_move',
		opacity: 0.7
	});

	$('#invoice_item_list .invoice_item').each(function(index, item) {
		bind_event_row(item);
	});
	if ($('#invoice_item_list').length > 0) {
		if ($('#invoice_item_list div.invoice_item').length == 0) {
			add_last_row();
		}
		//cal_invoice_total();
		hide_tax();
	}
	search_invoice();

	$('#btn_submit').click(function() {
		$('#invoice_item_list .invoice_item').each(function(index, item) {
			$(this).find('.sort_order').val(index);
		});

		var frm = $(this).closest('form');
		$.ajax({
			type: 'POST',
			url: $(frm).attr('action'),
			data: $(frm).serialize(),
			dataType: 'json',
			success: function(resp) {
				if (resp.success) {
					document.location.href = resp.details;
				} else {
					alert(resp.message);
				}
			}
		});
		return false;
	});

	$('#btn_more_pay').on('click', function() {
		var c = $('#pay_item_count').val();
		var html = $('#tr_pay_template').html();
		while (html.indexOf('{xxxxx}') != -1) {
			html = html.replace('{xxxxx}', c);
		}
		$('#tr_pay_total').before(html);

		$('#pay_item_count').val(parseInt(c) + 1);
	});

	$('#tbl_pay .pay_amount').live('change', function(e) {
		var val = parseFloat($(this).val()) || 0;
		$(this).val(val);
		cal_pay_total();
	});
});
