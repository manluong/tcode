
function apps_action_ajax(app,an,aved,div,thisid,morevalue,replacefn) {

    if (thisid==undefined || thisid == '') {thisid = "0";}
    if (morevalue==undefined) {morevalue = "";}

    //if (thisid) {moreid = "&thisid="+thisid;} else { moreid = "&thisid="; }
    //if (morevalue) {moreid = moreid+"&"+morevalue;}

    if (aved == "a" || aved == "l" || aved == "ld" || aved == "s" || aved == "f" || aved == "v" || ((aved == "e" || aved == "d" || aved == "ds") && thisid)){
        dhtmlxAjax.get("/"+app+"/"+an+"/"+thisid+"/"+aved+"/"+morevalue, apps_action_ajax2);
    }

    function apps_action_ajax2(loader) {

    jsCkStr = loader.xmlDoc.responseText.substring(0,60)
    //jsCkStr.substring(0,4)

    if (jsCkStr.substring(0,4) == '//js'){
        //alert("is JS");
        jsCkFu = jsCkStr.split("//",3)
        //alert(jsCkFu[2]);

        if(replacefn == '1' || typeof window[jsCkFu[2]] !== 'function'){

          var head= document.getElementsByTagName('head')[0];
          var script= document.createElement('script');
          script.type= "text/javascript";script.text= loader.xmlDoc.responseText;
          head.appendChild(script);
          if (jsCkFu[2]) eval(jsCkFu[2]+"('"+app+"','"+an+"','"+aved+"','"+div+"','"+thisid+"','"+morevalue+"');");

        }else if(jsCkFu[2]) {
          eval(jsCkFu[2]+"('"+app+"','"+an+"','"+aved+"','"+div+"','"+thisid+"','"+morevalue+"');");

        }



    }else if (jsCkStr.substring(0,2) == '{"'){

			apps_action_json(loader.xmlDoc.responseText);

    }else{

          document.getElementById(div).innerHTML = loader.xmlDoc.responseText;

          if (jsCkStr.substring(0,12) == '<!-- //jsrun'){
            jsCkFu = jsCkStr.split("//",3)
            eval(jsCkFu[2]);
          }

    }

    }


}


function apps_action_json(jarray) {


	var json = jQuery.parseJSON( jarray );

	if (json['element_type'] == "dgroup"){
		switch (json['dgrouptype']) {
			case 'list':
			dgroup_list(json);
			break;

			case 'view':
			dgroup_view(json);
			break;

			case 'form':
			dgroup_form(json);
			break;

			case 'save':
			dgroup_save(json);
			break;

			case 'empty':
			dgroup_empty(json);
			break;
		}
	}

	//alert(json['element_id']);
}

function apps_action_pageload(url) {
    //alert(url);
    //window.location = url;
	$.pjax({
		url: url,
		container: '#content-container'
	});
}



function dgroup_list(json){
	var buttontop = "";
	var buttonbottom = "";
	if (json['element_button_format']){
	if (json['element_button_format']['top']) buttontop = '<div class="bu-div bu-tabletop">'+json['element_button_format']['top']+'</div>';
	if (json['element_button_format']['bottom']) buttonbottom = '<div class="bu-div bu-tablebottom">'+json['element_button_format']['bottom']+'</div>';
	}

	var tableid = json['element_id']+"_table";


	if (json['list']['listnotitle'] == 1){

		document.getElementById(json['element_id']).innerHTML = buttontop+'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed tpaneltable_notitle" id="'+tableid+'"></table>'+buttonbottom;

		$('#'+tableid).dataTable({
			"aoColumns": json['list']['aoColumns'],
			"aaData": json['list']['aaData'],
			"bAutoWidth": false,
			"bJQueryUI": false,
			"bProcessing": true,
			"sPaginationType": "full_numbers",
			"fnDrawCallback": function() {
				formui_reload();
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

		document.getElementById(json['element_id']).innerHTML = buttontop+'<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed tpaneltable" id="'+tableid+'"></table>'+buttonbottom;

		$('#'+tableid).dataTable({
			"aoColumns": json['list']['aoColumns'],
			"aaData": json['list']['aaData'],
			"bAutoWidth": false,
			"bJQueryUI": true,
			"bProcessing": true,
			"sPaginationType": "full_numbers",
			"fnDrawCallback": function() {
				formui_reload();
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

function dgroup_view(json){

	var buttontop = "";
	var buttonbottom = "";
	if (json['element_button_format']['top']) buttontop = '<div class="bu-div bu-formview">'+json['element_button_format']['top']+'</div>';
	if (json['element_button_format']['bottom']) buttonbottom = '<div class="bu-div bu-formview">'+json['element_button_format']['bottom']+'</div>';

	var allnolablecss = "";
	var formhtml = "";
	var formLength = json['form']['field'].length;

	if (!json['form']['allnolabel']){
		for(var i = 0; i < formLength; i++) {
			formhtml = formhtml+'<div class="field f_'+json['form']['field'][i]['fieldname']+'"><div class="label">'+json['form']['field'][i]['label']+'</div><div class="value">'+json['form']['field'][i]['value']+'</div></div>';
		}
	} else {
		for(var i = 0; i < formLength; i++) {
			formhtml = formhtml+'<div class="field f_'+json['form']['field'][i]['fieldname']+'"><div class="value">'+json['form']['field'][i]['value']+'</div></div>';
		}
		allnolablecss = " allnolabel";
	}

	document.getElementById(json['element_id']).innerHTML = buttontop+'<div class="formview '+json['element_id']+'_inner'+allnolablecss+'"><div style="padding: 5px;"></div>'+formhtml+'</div>'+buttonbottom;
	formui_reload();
 	//if ($aved == "fd"){   method="post" action="?app='.json['element_button']['buttons'][0]['targetapp'].'&an='.json['element_button']['buttons'][0]['targetan'].'">

}

function dgroup_empty(json){

	var buttontop = "";
	var buttonbottom = "";
	if (json['element_button_format']['top']) buttontop = '<div class="bu-div bu-formview">'+json['element_button_format']['top']+'</div>';
	if (json['element_button_format']['bottom']) buttonbottom = '<div class="bu-div bu-formview">'+json['element_button_format']['bottom']+'</div>';

	document.getElementById(json['element_id']).innerHTML = buttontop+'<div class="formview '+json['element_id']+'_inner"></div>'+buttonbottom;
	formui_reload();
 	//if ($aved == "fd"){   method="post" action="?app='.json['element_button']['buttons'][0]['targetapp'].'&an='.json['element_button']['buttons'][0]['targetan'].'">

}

function dgroup_save(json){

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

function dgroup_form(json){

	var buttontop = "";
	var buttonbottom = "";
	if (json['element_button_format']['top']) buttontop = '<div class="bu-div bu-formview">'+json['element_button_format']['top']+'</div>';
	if (json['element_button_format']['bottom']) buttonbottom = '<div class="bu-div bu-formview">'+json['element_button_format']['bottom']+'</div>';

	document.getElementById(json['element_id']).innerHTML = buttontop+'<div class="formview '+json['element_id']+'_inner"><form class="form" id="formid_'+json['element_id']+'" name="formid_'+json['element_id']+'" style="margin: -2px;"><div style="padding: 5px;"></div>'+json['form']+buttonbottom+'</form></div><div id="'+json['element_id']+'_submiturl" style="display:none;"></div><div id="'+json['element_id']+'_formsetup" style="display:none;"></div>';
	//if ($aved == "fd"){   method="post" action="?app='.json['element_button']['buttons'][0]['targetapp'].'&an='.json['element_button']['buttons'][0]['targetan'].'">

    formui_reload();
	$(".form .form-input textarea").css({"max-width":"100%"})

    var thisLength = json['formsetup'].length;
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

    document.getElementById(json['element_id']+"_submiturl").innerHTML = json['dgroup_savejs']['base_url']+json['dgroup_savejs']['app']+'/'+json['dgroup_savejs']['an']+'/'+json['dgroup_savejs']['thisid']+'/'+json['dgroup_savejs']['aved'];

    var formsetup_text = JSON.stringify(json['formsetup'], json_replacer);
    document.getElementById(json['element_id']+"_formsetup").innerHTML = formsetup_text;
    //document.getElementById(json['element_id']+"_formsetup").innerHTML = json['formsetup'];

    dgroup_submit(json['element_id']);

}

function json_replacer(key, value) {
if (typeof value === 'number' && !isFinite(value)) {
    return String(value);
}
return value;
}

function dgroup_submit(element_id){

	//formui_removeerrmeg('formid_'+element_id);


	$('#formid_'+element_id).validator({
		position : 'bottom left',
		offset : [5, 0],
		messageClass : 'form-error',
		message : '<div><em/></div>'// em element is the arrow
	}).submit(function(e) {

	  	var form = $(this);

		var formsetup = document.getElementById(element_id+"_formsetup").innerHTML;
		var submiturl = document.getElementById(element_id+"_submiturl").innerHTML;
		var submiturl = $('<div/>').html(submiturl).text();
		//var submiturl = $(element_id+"_submiturl").html(encodedStr).text();



	  	// client-side validation OK.
	  	if (!e.isDefaultPrevented()) {

		// prevent default form submission logic
		e.preventDefault();

		//alert(element_id);
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
                ajaxvalue = ajaxvalue+value.field+'='+form_radio_getvalue(document.forms['formid_'+element_id].elements['form_'+value.field])+'&';

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


function dgroup_autocomplete(this_field,this_ackey){

    var cache = {},
    	lastXhr;
    $('#form_'+this_field).autocomplete({
    	minLength: 2,
    	source: function( request, response ) {
    		var term = request.term;
    		if ( term in cache ) {
    			response( cache[ term ] );
    			return;
    		}

    		lastXhr = $.getJSON('?app=core_shared&an=ac_'+this_ackey, request, function( data, status, xhr ) {
    			cache[ term ] = data;
    			if ( xhr === lastXhr ) {
    				response( data );
    			}
    		});
    	},
		select: function( event, ui ) {
			$('#form_'+this_field).val( ui.item.value );
			//$( "#project-id" ).val( ui.item.value );
			//$( "#project-description" ).html( ui.item.desc );
			//$( "#project-icon" ).attr( "src", "images/" + ui.item.icon );

			return false;
		}


    });

}


    function notification_show(divValue,megValue,typeValue,autoclsValue){

        var randomnumber=Math.floor(Math.random()*1001);
        var newdivid = "notification"+randomnumber;

        switch(typeValue)
        {
        case '2':
          var uitype="ui-state-success";
          var uiicon="ui-icon-circle-check";
          break;
        case '3':
          var uitype="ui-state-error";
          var uiicon="ui-icon-alert";
          break;
        default:
          var uitype="ui-state-highlight";
          var uiicon="ui-icon-info";
        }

        var thenotice = '<div id="'+newdivid+'" class="ui-widget message info closeable notification_meg" style="display:none;"><div class="'+uitype+' ui-corner-all"><p><span class="ui-icon '+uiicon+'"></span>'+megValue+'</p></div></div>';

        setTimeout(function() {
            $(thenotice).prependTo('#'+divValue);
            $('#'+newdivid).fadeIn(500);
        }, 500);


        //setTimeout("timer('go',newCount)",1000)
        //setTimeout("noticeautoclose()",5000);
        //setTimeout("",3500);
        //var div;
        if (autoclsValue){
            setTimeout(function (a,b) {
                //alert(newdivid);
                $('#'+newdivid).fadeOut(500, function () {
                        $('#'+newdivid).remove();
                });
            },7000);
        }

    }



//                $("button", ".bu-div").button();
//                $("input, textarea, select").uniform();
//                $(".form-dateinput").datepicker({ dateFormat: \'yy-mm-dd\' });



function formui_removeerrmeg(divid){
    var form = $("#"+divid).validator();
    form.data("validator").destroy();
    //e.preventDefault();
    //$( "#"+divid ).validator().clear(function(e) {
    //e.preventDefault();
    //var form = $(this);
    //form.data("validator").reset();
    //});
}



// perform JavaScript after a new form is loaded from ajax
function formui_reload() {

    /**
     * Buttons
     */
    $('.button').each(function () {
            $(this).button({
                    icons : {
                        primary : $(this).attr('data-icon-primary') ? $(this).attr('data-icon-primary') : null,
                        secondary : $(this).attr('data-icon-primary') ? $(this).attr('data-icon-secondary') : null
                    },
                    text : $(this).attr('data-icon-only') === 'true' ? false : true
                });
        });

    /**
     * Toolbar Buttons
     */
    $(".buttonset input").addClass('no-uniform');
    $(".buttonset").addClass('ui-corner-all').buttonset();

    /**
     * Skin select, file, checkbox and radio input elements
     */
    //$(":checkbox:not(.no-uniform), :radio:not(.no-uniform), select:not(.no-uniform), :file:not(.no-uniform)").uniform();
    //$().uniform("#formid_divcard_email");
    //$().uniform.update();
    //alert("me");
    //$("#divcard_socia").uniform();
    //$("select, input:checkbox, input:radio, input:file").uniform();
    //$.uniform.update();



    /**
     * attach calendar to date inputs
     */
    //$(":date").datepicker({
    //    dateFormat : 'yy-mm-dd'
    //});



    /**
     * setup the validators
    $(".has-validation").validator({
        position : 'bottom left',
        offset : [5, 0],
        messageClass : 'form-error',
        message : '<div><em/></div>'// em element is the arrow
    }).attr('novalidate', 'novalidate');
    */

   /**
    * Character Counter for inputs and text areas
    */
   $('.word_count120').each(function(){
       // get current number of characters
       var length = 120 - $(this).val().length;
       // get current number of words
       //var length = $(this).val().split(/\b[\s,\.-:;]*/).length;
       // update characters
       $(this).parent().find('.counter').html( length + ' remaining');
       // bind on key up event
       $(this).keyup(function(){
           // get new length of characters
           var new_length = 120 - $(this).val().length;
           // get new length of words
           //var new_length = $(this).val().split(/\b[\s,\.-:;]*/).length;
           // update
           $(this).parent().find('.counter').html( new_length + ' remaining');
       });
   });

   $('.word_count1').each(function(){
       // get current number of characters
       var length = $(this).val().length;
       // get current number of words
       //var length = $(this).val().split(/\b[\s,\.-:;]*/).length;
       // update characters
       $(this).parent().find('.counter').html( length + ' characters');
       // bind on key up event
       $(this).keyup(function(){
           // get new length of characters
           var new_length = $(this).val().length;
           // get new length of words
           //var new_length = $(this).val().split(/\b[\s,\.-:;]*/).length;
           // update
           $(this).parent().find('.counter').html( new_length + ' characters');
       });
   });

};


function form_radio_getvalue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}


function form_popup(app,an,aved,div,thisid,morevalue) {

		$( "#"+div ).dialog({
			height: 140,
			modal: true
		});
        apps_action_ajax(app,an,aved,div,thisid,morevalue);
}

function form_popup_invoiceitemprice(title) {

        var thisproductid = document.getElementById('form_a_invoice_item_productid').value;

        if (thisproductid > 0){

		$( "#div_popup_a_invoice_item_priceunit" ).dialog({
            title: title,
            height: 400,
            width: 600,
            modal: true
		});
        apps_action_ajax('invoice','x_invoice_item_price','l','div_popup_a_invoice_item_priceunit',thisproductid,'','1');

        } else {

        apps_action_ajax('core_shared','xmsg_show','l','','','xmsg_name=invoice_item_price_noproduct');
        //document.getElementById("div_popup_a_invoice_item_priceunit").innerHTML = 'No Product';

        }

        form_invoiceitemprice_cal();

}

function form_popup_invoiceitemprice_select(priceunit) {

        $("#form_a_invoice_item_priceunit").val(priceunit);
        form_invoiceitemprice_cal_now();
        $("#div_popup_a_invoice_item_priceunit").dialog("close");

}

function form_invoiceitemprice_cal(){

        $('#form_a_invoice_item_priceunit').change(function () {
        form_invoiceitemprice_cal_now();
        });

        $('#form_a_invoice_item_qty').change(function () {
        form_invoiceitemprice_cal_now();
        });

        $('#form_a_invoice_item_discount').change(function () {
        form_invoiceitemprice_cal_now();
        });
}

function form_invoiceitemprice_cal_now(){

          var thispriceunit = document.getElementById('form_a_invoice_item_priceunit').value;
          var thisqty = document.getElementById('form_a_invoice_item_qty').value;
          var thisdiscount = document.getElementById('form_a_invoice_item_discount').value;

          if (isNaN(thispriceunit)) thispriceunit = 0;
          if (isNaN(thisqty)) thisqty = 0;

          if (thisdiscount >= 1 && thisdiscount <= 100) {
            var pricetotal = thispriceunit * thisqty * (1-(thisdiscount * 0.01));
          } else {
            var pricetotal = thispriceunit * thisqty;
            $("#form_a_invoice_item_pricetotal").val('');
          }

          //alert(thispriceunit+'-'+thisqty+'-'+thisdiscount);

          $("#form_a_invoice_item_pricetotal").val(pricetotal);

}

function form_dgroup_po_post_country() {
    $("#form_a_po_lcountry").change(function() {
      lcountry = document.getElementById('form_a_po_lcountry').value;
      //form_dgroup_po_post_lcountry(lcountry);
      form_dgroup_po_post_lselect("state",lcountry);
    });

    var this_divbracketid = "form_a_po_lcountry_append_country";
    var this_parentdivbracketid = "fl_a_po_lcountry";
    var thisaddsel = '<div id="'+this_divbracketid+'"></div>';
    $(thisaddsel).appendTo('#'+this_parentdivbracketid);

    lcountry = document.getElementById('form_a_po_lcountry').value;
    lstate = document.getElementById('form_a_po_lstate').value;
    lcity = document.getElementById('form_a_po_lcity').value;

    if (lcountry) form_dgroup_po_post_lselect("state",lcountry);
    if (lstate) form_dgroup_po_post_lselect("city",lstate);
    if (lcity) form_dgroup_po_post_lselect("area",lcity);
    //form_dgroup_po_post_lcountry(lcountry,lstate);
}

function form_dgroup_po_post_lcountry(lcountry,lstate) {
    //countryiso = document.getElementById('form_a_po_lcountry').value;

    //to clear all field value for - state/city/area
    //if no id = form_a_po_lcountry_appendTo > add, else clear
    if (document.getElementById("form_a_po_lcountry_append_state")) {
      document.getElementById("form_a_po_lcountry_append_state").innerHTML = "";
    } else {
      var thisaddsel = '<div id="form_a_po_lcountry_append_state"></div>';
      $(thisaddsel).appendTo('#fl_a_po_lcountry');
    }

    //[{"value":"KA","label":"Katong"},{"value":"SA","label":"Sentosa"}]
    //?app=core_shared&an=ac_state
    //alert(countryiso);
    $.getJSON("?app=core_shared&an=ac_state&countries_state_countryiso="+lcountry, function(data) {
        thissel = '<div class="buttonset form-input"><select id="append_form_a_po_lstate" name="append_a_po_lstate">';
        thissel = thissel + '<option value="">-----</option>';
        thisgotresult = 0;
        $.each(data, function(i, item) {
            thissel = thissel + '<option value="'+item.value+'"';
            if (lstate == item.value) thissel = thissel + ' SELECTED';
            thissel = thissel + '>'+item.label+'</option>';
            thisgotresult = 1;
        });
        thissel = thissel + '</select></div>';
        if (thisgotresult) {
          $(thissel).appendTo('#form_a_po_lcountry_append_state');
          $("#append_form_a_po_lstate").uniform();
          $("#append_form_a_po_lstate").change(function() {
            value_state = document.getElementById('append_form_a_po_lstate').value;
            $("#form_a_po_lstate").val(value_state);
            form_dgroup_po_post_lstate(value_state,lcountry,lstate,lcity,larea);
          });
        }

    });
}


function form_dgroup_po_post_lselect(whichlist,value_new) {

    lcountry = document.getElementById('form_a_po_lcountry').value;
    lstate = document.getElementById('form_a_po_lstate').value;
    lcity = document.getElementById('form_a_po_lcity').value;
    larea = document.getElementById('form_a_po_larea').value;

    if (whichlist == "state"){
    var this_nextlist = "city";
    var this_divbracketid = "form_a_po_lcountry_append_state";
    var this_parentdivbracketid = "form_a_po_lcountry_append_country";
    var this_jsonurl = "?app=core_shared&an=ac_state&countries_state_countryiso="+value_new;
    var this_selectid = "append_form_a_po_lstate";
    var this_tochangefield = "form_a_po_lstate";
    var this_selected = lstate;

    } else if (whichlist == "city"){
    var this_nextlist = "area";
    var this_divbracketid = "form_a_po_lcountry_append_city";
    var this_parentdivbracketid = "form_a_po_lcountry_append_state";
    var this_jsonurl = "?app=core_shared&an=ac_city&countries_city_countryiso="+lcountry+"&countries_city_stateshort="+value_new;
    var this_selectid = "append_form_a_po_lcity";
    var this_tochangefield = "form_a_po_lcity";
    var this_selected = lcity;

    } else if (whichlist == "area"){
    var this_divbracketid = "form_a_po_lcountry_append_area";
    var this_parentdivbracketid = "form_a_po_lcountry_append_city";
    var this_jsonurl = "?app=core_shared&an=ac_area&countries_area_countryiso="+lcountry+"&countries_area_stateshort="+lstate+"&countries_area_cityshort="+value_new;
    var this_selectid = "append_form_a_po_larea";
    var this_tochangefield = "form_a_po_larea";
    var this_selected = larea;

    }

    if (document.getElementById(this_divbracketid)) {
      document.getElementById(this_divbracketid).innerHTML = "";
    } else {
      var thisaddsel = '<div id="'+this_divbracketid+'"></div>';
      $(thisaddsel).appendTo('#'+this_parentdivbracketid);
    }

    if (value_new){
      $.getJSON(this_jsonurl, function(data) {
          thissel = '<div class="buttonset form-input"><select id="'+this_selectid+'" name="'+this_selectid+'">';
          thissel = thissel + '<option value="">-----</option>';
          thisgotresult = 0;
          $.each(data, function(i, item) {
              thissel = thissel + '<option value="'+item.value+'"';
              if (this_selected == item.value) thissel = thissel + ' SELECTED';
              thissel = thissel + '>'+item.label+'</option>';
              thisgotresult = 1;
          });
          thissel = thissel + '</select></div>';
          if (thisgotresult) {
            $(thissel).prependTo('#'+this_divbracketid);
            $("#"+this_selectid).uniform();
            $("#"+this_selectid).change(function() {
              value_new = document.getElementById(this_selectid).value;

              if (whichlist == "state") {$("#form_a_po_lstate").val(''); $("#form_a_po_lcity").val(''); $("#form_a_po_larea").val('');}
              if (whichlist == "city") {$("#form_a_po_lcity").val(''); $("#form_a_po_larea").val('');}
              if (whichlist == "area") $("#form_a_po_larea").val('');

              $("#"+this_tochangefield).val(value_new);
              if (this_nextlist) form_dgroup_po_post_lselect(this_nextlist,value_new);

            });
          }

      });
    };

}





















