
function helpdesk_ajax_content(url,divid) {
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
				helpdesk_ajax_content_json(loader, divid);
			}
		},
		'json'
	);
}

function helpdesk_ajax_content_json(jarray,divid) {
	var json = '';
	
	if (typeof jarray != 'object') {
		json = jQuery.parseJSON( jarray );
	} else {
		json = jarray;	//no need to parseJSON because jQuery already did it at $.get/$.post
	}

	var links = { html: '', bu: ''};
	if (json['details']['links']){
		links = helpdesk_ajax_content_links(json['details']['links'],divid);
	}

	switch (json['type']) {
		case 'list':
		helpdesk_ajax_content_list(json,divid,links);
		insert_edit_button();
		add_btn_insert();
		break;

		case 'view':
		helpdesk_ajax_content_view(json,divid,links);
		break;

		case 'form':
		helpdesk_ajax_content_form(json,divid,links);
		$('#form_a_helpdesk_created_stamp').parent().parent().hide();
		break;

		case 'save':
		helpdesk_ajax_content_save(json,divid,links);
		break;

	}

}

function add_btn_insert(){
	$('h4').html('<div style="float:left;width:1010px;height:10px;">HelpDesk List</div><div><a onclick="load_insert_helpdesk_form()","helpdesk_view");" href="#" >New</a></div>');
}

function load_insert_form(){
	helpdesk_ajax_content('/helpdesk/sendjson_insert_form', 'helpdesk_list');
}

function insert_edit_button(){
	$('<th class="sorting" rowspan="1" colspan="1" style="width: 45px;">Edit</th>').insertAfter('.sorting:last');
	$.each($("tbody tr"),function(index,value){
		var i = $(this).children(":first-child").html();
		$(this).append('<td><a onclick="load_comment_form('+i+')","helpdesk_view");" href="#" class="btn btn-default">Edit</a></td>');
	})
}

function load_edit_form(id){
	helpdesk_ajax_content('/helpdesk/sendjson_form/'+id, 'helpdesk_list');
}

function load_comment_form(id){
	var url = '/helpdesk/sendjson_comment_form/';
	$.post(url,{
				id: id
			},function(data){
				$('#helpdesk_list').html(data);
			}
		);
}

function load_insert_helpdesk_form(){
	var url = '/helpdesk/insert_helpdesk_form/';
	$.post(url,function(data){
				$('#helpdesk_list').html(data);
			}
		);
}

function helpdesk_ajax_content_echo(json,divid,content){
	var view = {title: json['title'], content: content};
	if (!json['template']) {
		document.getElementById(divid).innerHTML = Mustache.to_html(tpl_c_stdwidget, view);
	} else {
		document.getElementById(divid).innerHTML = Mustache.to_html(window[json['template']], view);
	}
}


function helpdesk_ajax_content_list(json,divid,links){
	var tableid = divid+"_table";
	var thisid = "";
	//if (json['details']['setting']['hidetitle'] == 1){

	helpdesk_ajax_content_echo(json,divid,'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="'+tableid+'"></table>'+links.html);

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

function helpdesk_ajax_content_view(json,divid,links){

	var html = "";
	var thisLength = json['details']['data'].length;
	var jsonformat = {};

	for(var i = 0; i < thisLength; i++) {
		var view = {label: json['details']['data'][i]['label'], value: json['details']['data'][i]['value'], fieldname: json['details']['data'][i]['fieldname']}
		html = html + Mustache.to_html(tpl_content_view, view);

		jsonformat[json['details']['data'][i]['fieldname']+'_label'] = json['details']['data'][i]['label'];
		jsonformat[json['details']['data'][i]['fieldname']+'_value'] = json['details']['data'][i]['value'];

	}

	if (!json['template']) {
		html = html + links.html;
		var view = {content: html};
		helpdesk_ajax_content_echo(json,divid,Mustache.to_html(tpl_content_viewwarp, view));
	} else {
		jsonformat['links'] = links.bu;
		helpdesk_ajax_content_echo(json,divid,jsonformat);
	}

}

function helpdesk_ajax_content_form(json,divid,links){

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
		helpdesk_ajax_content_echo(json,divid,Mustache.to_html(tpl_form_ctlgroup, jsonformat));
	} else {
		jsonformat['links'] = links.bu;
		helpdesk_ajax_content_echo(json,divid,jsonformat);
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
	helpdesk_ajax_content_errorstyle();
    helpdesk_ajax_content_submit(divid,links.submiturl,json);
}

function helpdesk_ajax_content_submit(divid,submiturl,json){

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
							helpdesk_ajax_content(json['details']['links'][0]['url'],json['details']['links'][0]['target']);
							console.log(json['details']['links'][0]);
						}

					} else if (json['details']['data']) {
						//server-side validation failed. use invalidate() to show errors
						//return as {"fieldname":"message"}
						//console.log(json['details']['data']);
						form.data("validator").invalidate(json['details']['data']);

					} else if (json['message']) {

					}
				},
				'json'
			);

		}

	});

}

function helpdesk_ajax_content_links(links,divid){

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
	var linkarray = {links: linkhtml}
	linkre.html = Mustache.to_html(tpl_link.warp, linkarray);

	return linkre;
}


function helpdesk_ajax_content_errorstyle(){
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



