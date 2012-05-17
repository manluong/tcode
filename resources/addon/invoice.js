function add_last_row() {
	var html = $('#invoice_item_template').html();
	$('#invoice_item_list').append(html);

	var new_item = $('#invoice_item_list .temp');
	new_item.removeClass('temp');
	bind_event_row(new_item);
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

	if ($('#invoice_item_list div.invoice_item').length == 1) {
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

	$('#sub_total').html(format_money(sub_total));
	$('#discount_total').html(format_money(discount_total));
	//$('#tax_total').html(tax_total);
	$('#invoice_total').html(format_money(invoice_total));
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
	item.find('div.invoice_item_sub').toggle();
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
};

$(document).ready(function() {
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

	$('#slider').slider({
		range: true,
		min: parseInt($('#total_default_min').val()),
		max: parseInt($('#total_default_max').val()),
		values: [parseInt($('#total_default_min').val()), parseInt($('#total_default_max').val())],
		slide: function( event, ui ) {
			$('#total_min').val(ui.values[0]);
			$('#total_max').val(ui.values[1]);
			$('#lbl_total').html(ui.values[0]+' - '+ui.values[1]);
		}
	});
	$('#total_min').val($('#slider').slider('values', 0));
	$('#total_max').val($('#slider').slider('values', 1));

	$('#more_options').on('click', function(e) {
		$('#search_more table').toggle();
		if (!$('#search_more table').is(":visible")) {
			$('#search_more input').val('');
		}
	});

	$('#search_btn').on('click', function(e) {
		$.ajax({
			type: "POST",
			url: $('#frm_search').attr('action'),
			data: $('#frm_search').serialize(),
			success: function(resp) {
				$('#invoice_list').html(resp);
				$('#page').val(1);
			}
		});
		return false;
	});

	$('.pagination a').live('click', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (!$(li).hasClass('disabled') && !$(li).hasClass('active')) {
			$('#page').val($(this).data('page'));
			$('#search_btn').click();
		}
	});

	$('#invoice_list_table_length select').live('change', function(e) {
		$('#row_per_page').val($(this).val());
		$('#page').val(1);
		$('#search_btn').click();
	});

	$('#invoice_print').on('click', function(e) {
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
		dateFormat: 'yy-mm-dd'
	});

	$('a.remove').live('click', function(e) {
		e.preventDefault();
		remove_row(this);
	});
	$('a.more').live('click', function(e) {
		e.preventDefault();
		more(this);
	});
	$('#add_row').on('click', function(e) {
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

	$('.apply input:checkbox').on('change', function() {
		var tax = $(this).data('tax');
		var checked = this.checked;

		$('#invoice_item_list input:checkbox').each(function(index, item) {
			if ($(this).hasClass('tax-'+tax)) {
				$(this).attr('checked', checked);
			}
		});

		cal_invoice_total();
	});

	$('#terms_id').on('change', function(e) {
		var id = $(this).val();
		if (id) {
			$.ajax({
				type: "GET",
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
		handle: 'a.move',
		opacity: 0.7
	});

	$('#invoice_item_list .invoice_item').not('.header').each(function(index, item) {
		bind_event_row(item);
	});
	if ($('#invoice_item_list div.invoice_item').length == 1) {
		add_last_row();
	}
	cal_invoice_total();
	$('#search_btn').click();

	$('#submit_btn').click(function() {
		$.ajax({
			type: "POST",
			url: $('#invoice_form').attr('action'),
			data: $('#invoice_form').serialize(),
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
});
