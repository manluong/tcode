function invoice_ajax_content(url,divid) {
    $.get(
		url,
		function(loader) {
			//load full page
			//to fix with pjax
			if (divid == "page"){
				$.pjax({
					url: url,
					container: '#content-container'
				});
			} else {
				invoice_ajax_content_json(loader, divid);
			}
		},
		'json'
	);
}

function invoice_ajax_content_json(jarray,divid) {
	var json = '';
	
	if (typeof jarray != 'object') {
		json = jQuery.parseJSON( jarray );
	} else {
		json = jarray;	//no need to parseJSON because jQuery already did it at $.get/$.post
	}

	var links = { html: '', bu: ''};
	if (json['details']['links']){
		links = invoice_ajax_content_links(json['details']['links'],divid);
	}

	switch (json['type']) {
		case 'list':
		invoice_ajax_content_list(json,divid,links);
		break;

		case 'view':
		invoice_ajax_content_view(json,divid,links);
		break;

		case 'form':
		invoice_ajax_content_form(json,divid,links);
		break;

		case 'save':
		invoice_ajax_content_save(json,divid,links);
		break;

	}

}

function invoice_ajax_content_echo(json,divid,content){
	var view = {title: json['title'], content: content};
	if (!json['template']) {
		document.getElementById(divid).innerHTML = Mustache.to_html(tpl_c_stdwidget, view);
	} else {
		document.getElementById(divid).innerHTML = Mustache.to_html(window[json['template']], view);
	}
}

function invoice_ajax_content_list(json,divid,links){
	var tableid = divid+"_table";
	var thisid = "";
	//if (json['details']['setting']['hidetitle'] == 1){

	invoice_ajax_content_echo(json,divid,'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="'+tableid+'"></table>'+links.html);

	if (json['details']['listlinks']){
		lslink = ajax_content_links(json['details']['listlinks'],divid);
		var cl = json['details']['columns'].length;
		var thisLength = json['details']['data'].length;
		for(var i = 0; i < thisLength; i++) {
				rid = {id: json['details']['ids'][i]};
				json['details']['data'][i][cl] = Mustache.to_html(lslink.bu, rid);
		}
		json['details']['columns'][cl] = {sTitle: ''};
	}
	
	$('#'+tableid).dataTable( {
		"aoColumns": json['details']['columns'],
		"aaData": json['details']['data'],
		"sDom": "<<'pull-right'p>f>t<<'pull-right'p>li>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sSearch" : "<div class=\"input-prepend\"><span class=\"add-on\"><i class=\"icon-search\"></i></span></i>_INPUT_</div>",
			"sInfo": "Showing _START_ to _END_ of _TOTAL_",
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
	        '<option value="50">50</option>'+
	        '<option value="-1">All</option>'+
	        '</select> Rows'

		}
	});

}

function invoice_ajax_content_view(json,divid,links){

	var html = "";
	var thisLength = json['details']['data'].length;
	var jsonformat = {};

	for(var i = 0; i < thisLength; i++) {
		var view = {label: json['details']['data'][i]['label'], value: json['details']['data'][i]['value'], fieldname: json['details']['data'][i]['fieldname']};
		html = html + Mustache.to_html(tpl_content_view, view);

		jsonformat[json['details']['data'][i]['fieldname']+'_label'] = json['details']['data'][i]['label'];
		jsonformat[json['details']['data'][i]['fieldname']+'_value'] = json['details']['data'][i]['value'];

	}

	if (!json['template']) {
		html = html + links.html;
		var view = {content: html};
		invoice_ajax_content_echo(json,divid,Mustache.to_html(tpl_content_viewwarp, view));
	} else {
		jsonformat['links'] = links.bu;
		invoice_ajax_content_echo(json,divid,jsonformat);
	}

}

function invoice_ajax_content_form(json,divid,links){

	var html = "";
	var thisLength = json['details']['data'].length;
	var jsonformat = { items: [] };
	var addclass = "input-xlarge";

	for(var i = 0; i < thisLength; i++) {

		//SET STYLE
		json['details']['data'][i]['addclass'] = 'input-xlarge';
		//json['details']['data'][i]['helpblk'] = 'help-block';
		if (!json['details']['data'][i]['form_type']) json['details']['data'][i]['form_type'] = "text";
		if (json['details']['data'][i]['required'] == "0") json['details']['data'][i]['required'] = null;

		template = tpl_form[json['details']['data'][i]['form_type']];

		switch (json['details']['data'][i]['form_type']) {

			case 'select':
			if(json['details']['data'][i]['form_type'] == "select" && json['details']['data'][i]['value']){
				var selLength = json['details']['data'][i]['select_options'].length;
				for(var si = 0; si < selLength; si++) {
					if (json['details']['data'][i]['select_options'][si]['value'] == json['details']['data'][i]['value']){
						json['details']['data'][i]['select_options'][si]['selected'] = ' SELECTED';
					}
				}
			}
			break;

		}

		if (!json['template']) {
		jsonformat.items[i] = {control: Mustache.to_html(template, json['details']['data'][i]), fieldname: json['details']['data'][i]['name'], label: json['details']['data'][i]['label'], helptext: json['details']['data'][i]['helptext']};
		} else {
		jsonformat[json['details']['data'][i]['name']] = {control: Mustache.to_html(template, json['details']['data'][i]), fieldname: json['details']['data'][i]['name'], label: json['details']['data'][i]['label'], helptext: json['details']['data'][i]['helptext']};
		}
	}


	if (!json['template']) {
		jsonformat.divid = divid;
		jsonformat.links = links.html;
		invoice_ajax_content_echo(json,divid,Mustache.to_html(tpl_form_ctlgroup, jsonformat));
	} else {
		jsonformat['links'] = links.bu;
		invoice_ajax_content_echo(json,divid,jsonformat);
	}


	//post action after form is renden
	for(var i = 0; i < thisLength; i++) {
		switch (json['details']['data'][i]['form_type']) {

			//case 'uniform':
			//$('#form_'+json['formsetup'][i]['field']).uniform();
			//break;

			//case 'combobox':
			//$('#form_'+json['formsetup'][i]['field']).combobox();
			//break;

			case 'date':
			$('#form_'+json['details']['data'][i]['name']).datepicker();
			//$('#form_'+json['details']['data'][i]['name']).datepicker({dateFormat: 'yy/mm/dd'});
			break;

			case 'datetime':
			//$('#form_'+json['details']['data'][i]['name']).datetimepicker({dateFormat: 'yy/mm/dd',timeFormat: 'hh:mm:ss'});
			break;

			case 'time':
			//$('#form_'+json['details']['data'][i]['name']).timepicker({timeFormat: 'hh:mm:ss'});
			break;

		}

		//if (json['formsetup'][i]['autocomplete']){
		//	dgroup_autocomplete(json['formsetup'][i]['field'],json['formsetup'][i]['autocomplete']);
		//}

	}
	invoice_ajax_content_errorstyle();
    invoice_ajax_content_submit(divid,links.submiturl,json);
}

function invoice_ajax_content_submit(divid,submiturl,json){

	//formui_removeerrmeg('formid_'+element_id);

	$('#formid_'+divid).validator({
		position : 'bottom left',
		offset : [5, 0],
		messageClass : 'form-error',
		message : '<div><em/></div>'// em element is the arrow
	}).submit(function(e) {

	  	var form = $(this);

	  	// client-side validation OK.
	  	if (!e.isDefaultPrevented()) {
			// prevent default form submission logic
			e.preventDefault();

			//dhtmlxAjax.post(submiturl.url, form.serialize(),function(loader){
			$.post(
				submiturl.url,
				form.serialize(),
				function(json) {

					if (json['success'] == 1)  {

						var buLength = json['details']['links'].length;
						//auto redirect if there is only 1 button, auto go the the page in the button
						if (buLength == 1){
							if (json['details']['links'][0]['target'] == "") json['details']['links'][0]['target'] = divid;
							invoice_ajax_content(json['details']['links'][0]['url'],json['details']['links'][0]['target']);
						}

					} else if (json['details']['data']) {
						//server-side validation failed. use invalidate() to show errors
						//return as {"fieldname":"message"}
						form.data("validator").invalidate(json['details']['data']);

					} else if (json['message']) {

					}
				},
				'json'
			);

		}

	});

}

function invoice_ajax_content_links(links,divid){

	var linkhtml = "";
	var linkre = {};
	var linkLength = links.length;
	for(var iln = 0; iln < linkLength; iln++) {

		if (links[iln]['type'] == "ajax" && (links[iln]['target'] == "" || links[iln]['target'] == null)) {
			links[iln]['target'] = divid;
		}

		template = tpl_link[links[iln]['type']];
		linkhtml = linkhtml + Mustache.to_html(template, links[iln]);

		//set submit url
		if (links[iln]['type'] == "submit") {
			linkre.submiturl = links[iln];
		}

	}
	//button only html
	linkre.bu = linkhtml;

	//warp the button
	var linkarray = {links: linkhtml};
	linkre.html = Mustache.to_html(tpl_link.warp, linkarray);

	return linkre;
}

function invoice_ajax_content_errorstyle(){
// Replace Jquery Tool error message style with twitter-bootstrap, directly cut and paste code from:
// http://wezfurlong.org/blog/2011/dec/jquery-tools-form-validator-and-twitter-bootstrap/
    $(function () {
        function find_container(input) {
            return input.parent().parent();
        }
        function remove_validation_markup(input) {
            var cont = find_container(input);
            cont.removeClass('error success warning');
            $('.help-inline.error, .help-inline.success, .help-inline.warning',
                cont).remove();
        }
        function add_validation_markup(input, cls, caption) {
            var cont = find_container(input);
            cont.addClass(cls);
            input.addClass(cls);

            if (caption) {
                var msg = $('<span class="help-inline"/>');
                msg.addClass(cls);
                msg.text(caption);
                input.after(msg);
            }
        }
        function remove_all_validation_markup(form) {
            $('.help-inline.error, .help-inline.success, .help-inline.warning',
                form).remove();
            $('.error, .success, .warning', form)
                .removeClass('error success warning');
        }
        $('form').each(function () {
            var form = $(this);

            form
                .validator({
                })
                .bind('reset.validator', function () {
                    remove_all_validation_markup(form);
                })
                .bind('onSuccess', function (e, ok) {
                    $.each(ok, function() {
                        var input = $(this);
                        remove_validation_markup(input);
                        // uncomment next line to highlight successfully
                        // validated fields in green
                        //add_validation_markup(input, 'success');
                    });
                })
                .bind('onFail', function (e, errors) {
                    $.each(errors, function() {
                        var err = this;
                        var input = $(err.input);
                        remove_validation_markup(input);
                        add_validation_markup(input, 'error',
                            err.messages.join(' '));
                    });
                    return false;
                });
        });
    });
//end Replace Jquery Tool error message style
}

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
	var discount = parseInt(item.find('.discount').val()) || 0;
	var tax = parseInt(item.find('.tax').val()) || 0;
	item.find('.item_total').val((price*qty-discount)*(100+tax)/100);
	
	cal_invoice_total();
}

function cal_invoice_total() {
	var sub_total = 0;
	var tax_total = 0;
	var invoice_total = 0;
	$('#invoice_item_list .invoice_item').not('.header').each(function(index, item) {
		var price = parseFloat($(item).find('.unit_price').val()) || 0;
		var qty = parseInt($(item).find('.qty').val()) || 0;
		var discount = parseInt($(item).find('.discount').val()) || 0;
		var tax = parseInt($(item).find('.tax').val()) || 0;
		
		sub_total += price*qty-discount;
		tax_total += (price*qty-discount)*tax/100;
		invoice_total += (price*qty-discount)*(100+tax)/100;
	});
	
	$('#sub_total').html(sub_total);
	$('#tax_total').html(tax_total);
	$('#invoice_total').html(invoice_total);
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
		min: $('#total_default_min').val(),
		max: $('#total_default_max').val(),
		values: [$('#total_default_min').val(), $('#total_default_max').val()],
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
			}
		});
		return false;
	});
	
	$('.datepicker').datetimepicker({
		showTimepicker: false,
		dateFormat: 'yy-mm-dd'
	});
	
	$('a.add').live('click', function(e) {
		e.preventDefault();
		add_row(this);
	});	
	$('a.remove').live('click', function(e) {
		e.preventDefault();
		remove_row(this);
	});	
	$('a.more').live('click', function(e) {
		e.preventDefault();
		more(this);
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
	
	$('#invoice_item_list .invoice_item').not('.header').each(function(index, item) {
		bind_event_row(item);
	});
	add_last_row();
	cal_invoice_total();
});
