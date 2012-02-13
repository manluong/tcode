
function ajax_content(url,divid) {

	//temp
	//to change to Jquery ajax function
    dhtmlxAjax.get("/"+url, apps_action_ajax2);

    function apps_action_ajax2(loader) {

    jsCkStr = loader.xmlDoc.responseText.substring(0,60)

	//load full page
	//to fix with pjax
	if (divid == "page"){
		
		$.pjax({
			url: url,
			container: '#content-container'
		}); 		
	
	//append JavaScript
    }else if (jsCkStr.substring(0,4) == '//js'){
        //alert("is JS");
        jsCkFu = jsCkStr.split("//",3)
        //alert(jsCkFu[2]);

        if(typeof window[jsCkFu[2]] !== 'function'){

          var head= document.getElementsByTagName('head')[0];
          var script= document.createElement('script');
          script.type= "text/javascript";script.text= loader.xmlDoc.responseText;
          head.appendChild(script);
          if (jsCkFu[2]) eval(jsCkFu[2]+"('"+url+"');");

        }else if(jsCkFu[2]) {
          eval(jsCkFu[2]+"('"+url+"');");

        }

	//this is JSON respond
    }else if (jsCkStr.substring(0,2) == '{"'){

			ajax_content_json(loader.xmlDoc.responseText,divid);

	//this is just Plain HTML
    }else{

          document.getElementById(divid).innerHTML = loader.xmlDoc.responseText;

          if (jsCkStr.substring(0,12) == '<!-- //jsrun'){
            jsCkFu = jsCkStr.split("//",3)
            eval(jsCkFu[2]);
          }

    }

    }


}


function ajax_content_json(jarray,divid) {

	var json = jQuery.parseJSON( jarray );

	switch (json['type']) {
		case 'list':
		ajax_content_list(json,divid);
		break;

		case 'view':
		ajax_content_view(json,divid);
		break;

		case 'form':
		ajax_content_form(json,divid);
		break;

		case 'save':
		ajax_content_save(json,divid);
		break;

		case 'empty':
		ajax_content_empty(json,divid);
		break;
	}
	
}

function ajax_content_list(json,divid){

	var tableid = divid+"_table";

	if (json['details']['setting']['hidetitle'] == 1){

		document.getElementById(divid).innerHTML = '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed tpaneltable_notitle" id="'+tableid+'"></table>';

		$('#'+tableid).dataTable({
			"aoColumns": json['details']['columns'],
			"aaData": json['details']['data'],
			"bAutoWidth": false,
			"bJQueryUI": false,
			"bProcessing": true,
			"sPaginationType": "full_numbers",
			"fnDrawCallback": function() {
				//formui_reload();
			},
			"bPaginate": false,
			"bLengthChange": false,
			"bFilter": false,
			"bSort": false,
			"bInfo": false,
			"bAutoWidth": false

			,"sDom": "<'row'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>",
			"sPaginationType": "bootstrap"
		});



	}else{

		document.getElementById(divid).innerHTML = '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed tpaneltable" id="'+tableid+'"></table>';

		$('#'+tableid).dataTable({
			"aoColumns": json['details']['columns'],
			"aaData": json['details']['data'],
			"bAutoWidth": false,
			"bJQueryUI": true,
			"bProcessing": true,
			"sPaginationType": "full_numbers",
			"fnDrawCallback": function() {
				//formui_reload();
				}

			,"sDom": "<'row'<'span5'l><'span5'f>r>t<'row'<'span5'i><'span5'p>>",
			"sPaginationType": "bootstrap"
		});
	}

$.extend( $.fn.dataTableExt.oStdClasses, {
    "sSortAsc": "header headerSortDown",
    "sSortDesc": "header headerSortUp",
    "sSortable": "header"
} );


}

function ajax_content_view(json,divid){

	var html = "";
	var thisLength = json['details']['data'].length;
	var jsonformat = {};
	
	for(var i = 0; i < thisLength; i++) {
		var view = {label: json['details']['data'][i]['label'], value: json['details']['data'][i]['value'], fieldname: json['details']['data'][i]['fieldname']}
		var html = html + Mustache.to_html(tpl_content_view, view);
		
		jsonformat[json['details']['data'][i]['fieldname']+'_label'] = json['details']['data'][i]['label'];
		jsonformat[json['details']['data'][i]['fieldname']+'_value'] = json['details']['data'][i]['value'];
		
	}

	//where to store custom template
	//var template = "My {{firstname_label}} is {{firstname_value}}!";
	//var html = html + Mustache.to_html(template, jsonformat);
	
	var view = {content: html};
	document.getElementById(divid).innerHTML = Mustache.to_html(tpl_content_viewwarp, view);
          
}

function ajax_content_empty(json){

	var buttontop = "";
	var buttonbottom = "";
	if (json['element_button_format']['top']) buttontop = '<div class="bu-div bu-formview">'+json['element_button_format']['top']+'</div>';
	if (json['element_button_format']['bottom']) buttonbottom = '<div class="bu-div bu-formview">'+json['element_button_format']['bottom']+'</div>';

	document.getElementById(json['divid']).innerHTML = buttontop+'<div class="formview '+json['divid']+'_inner"></div>'+buttonbottom;
	formui_reload();
 	//if ($aved == "fd"){   method="post" action="?app='.json['element_button']['buttons'][0]['targetapp'].'&an='.json['element_button']['buttons'][0]['targetan'].'">

}

function ajax_content_save(json){

	if (json['form']['save_success'] == 1)  {
//form.load("success.php");
// server-side validation failed. use invalidate() to show errors
	var buLength = json['savebutton']['buttons'].length;
	var targetthisid = "";
	//auto redirect if there is only 1 button, auto go the the page in the button
	if (buLength == 1){
		if (json['savebutton']['buttons'][0]['targetid'] == 'thisid') {
			targetthisid = json['form']['save_id'];
		} else if (json['savebutton']['buttons'][0]['targetid'] == 'listid') {
			targetthisid = json['form']['list_id'];
		} else {
			targetthisid = json['savebutton']['buttons'][0]['targetid'];
		}
		apps_action_ajax(json['savebutton']['buttons'][0]['targetapp'],json['savebutton']['buttons'][0]['targetan'],json['savebutton']['buttons'][0]['targetaved'],json['savebutton']['buttons'][0]['div'],targetthisid);
	}

	}

}

function ajax_content_form(json,divid){

	var html = "";
	var thisLength = json['details']['data'].length;
	var jsonformat = { items: [] };
	var addclass = "input-xlarge";
	//var template_input = "<div class=\"control-group\"><label class=\"control-label\" for=\"form_{{fieldname}}\">{{label}}</label><div class=\"controls\"><input type=\"text\" class=\"input-xlarge\" id=\"form_{{fieldname}}\" value=\"{{value}}\"><p class=\"help-block\">{{helptext}}</p></div></div>";

	
	//if not custom template
	for(var i = 0; i < thisLength; i++) {
		json['details']['data'][i]['addclass'] = 'input-xlarge';
		template = tpl_form[json['details']['data'][i]['form_type']];
		jsonformat.items[i] = {control: Mustache.to_html(template, json['details']['data'][i]), fieldname: json['details']['data'][i]['name'], label: json['details']['data'][i]['label'], helptext: json['details']['data'][i]['helptext']};
	}
		
	
	//to improve, wait for final design
	//warp up every row
	console.log(jsonformat);
	html = Mustache.to_html(tpl_form_ctlgroup, jsonformat)

	document.getElementById(divid).innerHTML = '<div class="formview"><form class="form-horizontal" id="formid_'+divid+'" name="formid_'+divid+'">'+html+'</form></div><div id="'+divid+'_submiturl" style="display:none;"></div><div id="'+divid+'_formsetup" style="display:none;"></div>';

    //formui_reload();
	//$(".form .form-input textarea").css({"max-width":"100%"})
	/*
    var thisLength = json['details']['data'].length;
    
    
	for(var i = 0; i < thisLength; i++) {
		if (json['formsetup'][i]['set']){
			switch (json['formsetup'][i]['set']) {

				case 'uniform':
				$('#form_'+json['formsetup'][i]['field']).uniform();
				break;

				case 'combobox':
				$('#form_'+json['formsetup'][i]['field']).combobox();
				break;

				case 'date':
				$('#form_'+json['formsetup'][i]['field']).datepicker({dateFormat: 'yy/mm/dd'});
				break;

				case 'datetime':
				$('#form_'+json['formsetup'][i]['field']).datetimepicker({dateFormat: 'yy/mm/dd',timeFormat: 'hh:mm:ss'});
				break;

				case 'time':
				$('#form_'+json['formsetup'][i]['field']).timepicker({timeFormat: 'hh:mm:ss'});
				break;

			}
		}

		if (json['formsetup'][i]['autocomplete']){
			dgroup_autocomplete(json['formsetup'][i]['field'],json['formsetup'][i]['autocomplete']);
		}

	}

    document.getElementById(divid+"_submiturl").innerHTML = json['dgroup_savejs']['base_url']+json['dgroup_savejs']['app']+'/'+json['dgroup_savejs']['an']+'/'+json['dgroup_savejs']['thisid']+'/'+json['dgroup_savejs']['aved'];

    var formsetup_text = JSON.stringify(json['formsetup'], json_replacer);
    document.getElementById(divid+"_formsetup").innerHTML = formsetup_text;
    //document.getElementById(json['element_id']+"_formsetup").innerHTML = json['formsetup'];

    dgroup_submit(divid);
    */

}

function json_replacer(key, value) {
if (typeof value === 'number' && !isFinite(value)) {
    return String(value);
}
return value;
}

function ajax_content_submit(divid){

	//formui_removeerrmeg('formid_'+element_id);


	$('#formid_'+divid).validator({
		position : 'bottom left',
		offset : [5, 0],
		messageClass : 'form-error',
		message : '<div><em/></div>'// em element is the arrow
	}).submit(function(e) {

	  	var form = $(this);

		var formsetup = document.getElementById(divid+"_formsetup").innerHTML;
		var submiturl = document.getElementById(divid+"_submiturl").innerHTML;
		var submiturl = $('<div/>').html(submiturl).text();
		//var submiturl = $(divid+"_submiturl").html(encodedStr).text();



	  	// client-side validation OK.
	  	if (!e.isDefaultPrevented()) {

		// prevent default form submission logic
		e.preventDefault();

		//alert(divid);
		//alert(formsetup.[0].field);

		var ajaxvalue = "";
		var formsetupobj = JSON.parse(formsetup);
		$.each(formsetupobj, function(index, value) {

			if (value.valuebycheck){
                if (document.getElementById('form_'+value.field).checked){
                ajaxvalue = ajaxvalue+value.field+'=1&';
                }else{
                ajaxvalue = ajaxvalue+value.field+'=0&';
                };
			}else if (value.valuebyradio){
                ajaxvalue = ajaxvalue+value.field+'='+form_radio_getvalue(document.forms['formid_'+divid].elements['form_'+value.field])+'&';

			}else{
				ajaxvalue = ajaxvalue+value.field+'='+document.getElementById('form_'+value.field).value+'&';
			}

		});

		//$.getJSON(submiturl+form.serialize(), function(json) {
		dhtmlxAjax.post(submiturl, encodeURI(ajaxvalue),function(loader){

			//console.log(loader.xmlDoc.responseText);

			var json = jQuery.parseJSON(loader.xmlDoc.responseText);

			//console.log(json['form']['save_success']);

			if (json['form']['save_success'] == 1)  {
			//form.load("success.php");
			// server-side validation failed. use invalidate() to show errors
				var buLength = json['savebutton']['buttons'].length;
				var targetthisid = "";
				//auto redirect if there is only 1 button, auto go the the page in the button
				if (buLength == 1){
					if (json['savebutton']['buttons'][0]['targetid'] == 'thisid') {
						targetthisid = json['form']['save_id'];
					} else if (json['savebutton']['buttons'][0]['targetid'] == 'listid') {
						targetthisid = json['form']['list_id'];
					} else {
						targetthisid = json['savebutton']['buttons'][0]['targetid'];
					}
					apps_action_ajax(json['savebutton']['buttons'][0]['targetapp'],json['savebutton']['buttons'][0]['targetan'],json['savebutton']['buttons'][0]['targetaved'],json['savebutton']['buttons'][0]['div'],targetthisid);
				}

				//for(var i = 0; i < formLength; i++) {}

			} else if (json['form']['save_error_json']) {
				//alert(decodeURIComponent(json['form']['save_error_json']));
				form.data("validator").invalidate(jQuery.parseJSON(json['form']['save_error_json']));

			} else if (json['form']['save_error_msg']) {

			}
		});

		// submit with AJAX
		//var ajaxvalue = '';
		//'.$ajax_getvalue.'
		//dhtmlxAjax.post('?app='+app+'&an='+an+'&thisid='+thisid+'&aved='+aved, encodeURI(ajaxvalue),function(loader){

			  //alert(loader.xmlDoc.responseText);
			  //jsCkStr = loader.xmlDoc.responseText.substring(0,4)

			  //if (jsCkStr == '//js'){
			  //if return js
			  //var head= document.getElementsByTagName('head')[0];
			  //var script= document.createElement('script');
			  //script.type= "text/javascript";script.text= loader.xmlDoc.responseText;
			  //head.appendChild(script);

			  //} else {
			  //var json = jQuery.parseJSON(loader.xmlDoc.responseText);
			  //form.data("validator").invalidate(json);

			  //}
		//});

		}

	});

}

