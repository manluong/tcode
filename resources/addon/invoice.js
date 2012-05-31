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

function add_row(object) {
	var html = $('#invoice_item_template').html();
	var item = $(object).closest('div.invoice_item');
	$(item).after(html);

	var new_item = $('#invoice_item_list .temp');
	new_item.removeClass('temp');
	bind_event_row(new_item);
}

function remove_row(object) {
	var item = $(object).closest('div.invoice_item');
	item.remove();

	if ($('#invoice_item_list div.invoice_item').length == 0) {
		add_last_row();
	}

	cal_invoice_total();
}

function cal_item_total(object) {
	var item = $(object).closest('div.invoice_item');
	var price = parseFloat(item.find('.unit_price').val()) || 0;
	var qty = parseInt(item.find('.qty').val()) || 0;
	var total = price*qty;
	item.find('.item_total').val(total.toFixed(2));
	item.find('.item_total_label').html(format_money(total));

	cal_invoice_total();
}

function cal_invoice_total() {
	var sub_total = 0;
	var discount_total = 0;
	//var tax_gst_total = 0;
	//var tax_vat_total = 0;
	var invoice_total = 0;
	$('#invoice_item_list .invoice_item').not('.header').each(function(index, item) {
		var price = parseFloat($(item).find('.unit_price').val()) || 0;
		var qty = parseInt($(item).find('.qty').val()) || 0;
		var discount = parseInt($(item).find('.discount').val()) || 0;

		//var tax_gst = 0;
		//if ($(item).find('.tax_gst').checked) {
		//	tax_gst = price*qty*5/100;
		//	tax_gst_total += tax_gst;
		//}

		//var tax_vat = 0;
		//if ($(item).find('.tax_vat').checked) {
		//	tax_vat = price*qty*10/100;
		//	tax_vat_total += tax_vat;
		//}

		sub_total += price*qty;
		discount_total += price*qty*discount/100;
		invoice_total += price*qty - price*qty*discount/100;
	});

	$('#lbl_sub_total').html(format_money(sub_total));
	$('#lbl_discount_total').html(format_money(discount_total));
	//$('#lbl_tax_total').html(tax_total);
	$('#lbl_invoice_total').html(format_money(invoice_total));
	$('#lbl_balance').html(format_money(invoice_total));
}

function bind_event_row(item) {
	$(item).find('.item_datepicker').datetimepicker({
		showTimepicker: false,
		dateFormat: 'yy-mm-dd'
	});

	$(item).find('.product_name').autocomplete({
		source: '/invoice/get_product',
		minLength: 2,
		select: function(e, ui) {
			var item = $(this).closest('div.invoice_item');
			item.find('.product_id').val(ui.item.product.id);
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
	d = d == undefined ? ',' : d;
	t = t == undefined ? '.' : t;
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
		success: function(resp) {
			//$('#invoice_list').html(resp);
			//$('#page').val(1);

			//$.extend($.fn.dataTableExt.oStdClasses, {
			//	"sWrapper": "dataTables_wrapper form-inline"
			//});

			var data = new Array();
			for (i in resp) {
				var item = resp[i];
				var row  = new Array();
				row[0] = '<input type="checkbox" />';
				row[1] = (item.first_name+' '+item.last_name).trim();
				row[2] = '<a href="/invoice/view/'+item.id+'">'+item.id+'</a>';
				var date = new Date((item.payment_due_stamp).substring(0, 10));
				row[3] = $.datepicker.formatDate('yy-mm-dd', date);
				row[4] = format_money(item.total);
				row[5] = '';
				row[6] = '<a href="/invoice/edit/'+item.id+'">Edit</a></td>';
				data.push(row);
			}

			$('#tbl_invoice').dataTable({
				"bDestroy" : true,
				"aaData": data,
				"aoColumns": [
					{ "sTitle": "" },
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
		source: '/invoice/get_customer',
		minLength: 2,
		select: function(e, ui) {
			$('#customer_id').val(ui.item.id);
		}
	});
	$('#customer_name').on('change', function(e) {
		$('#customer_id').val('');
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

	//$('#more_options').on('click', function(e) {
	//	$('#search_more table').toggle();
	//	if (!$('#search_more table').is(":visible")) {
	//		$('#search_more input').val('');
	//	}
	//});

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

	$('.datepicker').datetimepicker({
		showTimepicker: false,
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
		});

		cal_invoice_total();
	});

	$('#apply_all_tax').on('change', function() {
		var val = $(this).val();

		$('#invoice_item_list .tax').each(function(index, item) {
			$(this).val(val);
		});

		cal_invoice_total();
	});

	/*$('#all_discount input:checkbox').on('change', function() {
		var tax = $(this).data('tax');
		var checked = this.checked;

		$('#invoice_item_list input:checkbox').each(function(index, item) {
			if ($(this).hasClass('tax-'+tax)) {
				$(this).attr('checked', checked);
			}
		});

		cal_invoice_total();
	});*/

	$('#terms_id').on('change', function(e) {
		var id = $(this).val();
		if (id) {
			$.ajax({
				type: 'GET',
				url: '/invoice/get_terms/'+id,
				success: function(resp) {
					$('#terms_content').val(resp);
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

	$('#invoice_item_list .invoice_item').not('.header').each(function(index, item) {
		bind_event_row(item);
	});
	if ($('#invoice_item_list').length > 0) {
		if ($('#invoice_item_list div.invoice_item').length == 0) {
			add_last_row();
		}
		cal_invoice_total();
	}
	search_invoice();

	$('#btn_submit').click(function() {
		var frm = $(this).closest('form');
		$.ajax({
			type: 'POST',
			url: $(frm).attr('action'),
			data: $(frm).serialize(),
			dataType: 'json',
			success: function(resp) {
				if (resp.success) {
					document.location.href = resp.url;
				} else {
					alert('input error');
					console.log(resp.error);
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
