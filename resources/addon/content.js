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

function ajax_content_form(json,divid){

	var html = "";
	var thisLength = json['details']['data'].length;
	var jsonformat = { items: [] };
	var addclass = "input-xlarge";

	for(var i = 0; i < thisLength; i++) {
		
		//SET STYLE
		json['details']['data'][i]['addclass'] = 'input-xlarge';
		//json['details']['data'][i]['helpblk'] = 'help-block';
		
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
		
		jsonformat.items[i] = {control: Mustache.to_html(template, json['details']['data'][i]), fieldname: json['details']['data'][i]['name'], label: json['details']['data'][i]['label'], helptext: json['details']['data'][i]['helptext']};

	}
		
	
	//to improve, wait for final design
	//warp up every row
	var linkre = ajax_content_links(json['details']['links'],divid);
	jsonformat.divid = divid;
	jsonformat.links = linkre.html;
	document.getElementById(divid).innerHTML = Mustache.to_html(tpl_form_ctlgroup, jsonformat);
	
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
	ajax_content_errorstyle();
    ajax_content_submit(divid,linkre.submiturl,json);
}

function ajax_content_submit(divid,submiturl,json){

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
		
		dhtmlxAjax.post(submiturl.url, form.serialize(),function(loader){

			var json = jQuery.parseJSON(loader.xmlDoc.responseText);

			if (json['success'] == 1)  {
			
				var buLength = json['details']['links'].length;
				//auto redirect if there is only 1 button, auto go the the page in the button
				if (buLength == 1){
					if (json['details']['links'][0]['target'] == "") { json['details']['links'][0]['target'] = divid; }
					ajax_content(json['details']['links'][0]['url'],json['details']['links'][0]['target']);	
				}

			} else if (json['details']['data']) {
				//server-side validation failed. use invalidate() to show errors
				//return as {"fieldname":"message"}
				console.log(json['details']['data']);
				form.data("validator").invalidate(json['details']['data']);

			} else if (json['message']) {

			}
		});

		}

	});

}

function ajax_content_links(links,divid){

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
	var linkarray = {links: linkhtml}
	linkre.html = Mustache.to_html(tpl_link.warp, linkarray);
	
	return linkre;
}


function ajax_content_errorstyle(){
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



