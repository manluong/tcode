function po_imageupload_loaduploader(thisid) {

    //var divplace = 'divpo_imageupload';
    //document.getElementById(divplace).innerHTML = '<form id="po_imageupload_uploader"><div id="divpo_imageupload_uploader"></div><div class="bu-div bu-formview"><span class="fr"><button type="submit" class="button" data-icon-primary="ui-icon-circle-check">Save</button></span></div></form>';

    //$('#tablink-divpo_imageupload').click(function() {
    //$('#button-poimgupload').click(function() {

    var divplace = 'divpo_imageupload';
    document.getElementById(divplace).innerHTML = '<div style="width:100%;padding:10px;background:#eee;"><div class="bu-div bu-formview"><button type="button" class="button" data-icon-primary="ui-icon-circle-check" id="button-poimgview" onClick="app_element_po_imageupload_showviewer();">View</button></div></div><div id="divpo_imageupload_vieworuplaod"></div>';
    formui_reload();
    var divplace = "divpo_imageupload_vieworuplaod";

    //var uploader = new plupload.Uploader({
	var uploader = $("#"+divplace).plupload({
		// General settings
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		url : '?app=po&an=upload_pophoto&aved=e&thisid='+thisid,
		max_file_size : '5mb',
		chunk_size : '1mb',
		unique_names : true,

		// Resize images on clientside if we can
		resize : {width : 1024, height : 1024, quality : 90},

		flash_swf_url : 'html/addon/plupload/js/plupload.flash.swf',
		silverlight_xap_url : 'html/addon/plupload/js/plupload.silverlight.xap',

		// Specify what files to browse for
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"}
		]

	});

    var uploader = $("#"+divplace).plupload('getUploader');
   //I noticed, that in chrome, it has problems to set z-index correctly for input container. To workaround that, just add another line after previous two
    //$('#'+divplace+' > div.plupload').css('z-index','99999');

	uploader.bind('FileUploaded', function(up, file, info) {
	  var returnresult1 = jQuery.parseJSON(info.response);
        //alert(returnresult1.error.message);
        if (returnresult1.error) {
            console.log('[Error] ' + file.id + ' : ' + returnresult1.error.message);
            file.status = plupload.FAILED;
            up.trigger('QueueChanged');
            return false;
        }

   	});


    //});

};


function po_imageupload_loadupviwer(thisid){

    var divplace = 'divpo_imageupload';
    document.getElementById(divplace).innerHTML = '<div style="width:100%;padding:10px;background:#eee;"><div class="bu-div bu-formview"><button type="button" class="button" data-icon-primary="ui-icon-circle-check" id="button-poimgupload" onClick="app_element_po_imageupload_showupload();">Upload</button></div></div><div id="divpo_imageupload_setting"></div><div id="divpo_imageupload_vieworuplaod"></div>';
    formui_reload();
    var divplace = "divpo_imageupload_vieworuplaod";

    dhtmlxAjax.get("?app=po&an=x_editpost_image&aved=e&thisid="+thisid,function(loader){
        document.getElementById(divplace).innerHTML = loader.xmlDoc.responseText;
    });
    $("divpo_imageupload_editdesp").hide();

}

function po_imageupload_editpost_imagedb(thisid){

        apps_action_ajax('po','x_editpost_imagedb','e','divpo_imageupload_editdesp',thisid,'','1');
        $('#divpo_imageupload_editdesp').fadeIn(1000);
        $(".po_gallery_photo").css("border","0px");
        $("#po_gallery_photo_"+thisid).css("border","2px solid #ff0000");

		//$( "#divpo_imageupload_gallery_popup" ).dialog({
        //    title: "123",
        //    height: 400,
        //    width: 600,
        //    modal: true
		//});

};















function po_imageupload_loadupviwer_gallery2(thisid) {

    var divplace = 'divpo_imageupload';
    document.getElementById(divplace).innerHTML = '<div style="width:100%;padding:10px;background:#eee;"><div class="bu-div bu-formview"><button type="button" class="button" data-icon-primary="ui-icon-circle-check" id="button-poimgupload" onClick="app_element_po_imageupload_showupload();">Upload</button></div></div><div id="divpo_imageupload_setting"></div><div id="divpo_imageupload_vieworuplaod"></div>';
    formui_reload();
    var divplace = "divpo_imageupload_vieworuplaod";



    dhtmlxAjax.get("?app=po&an=x_editpost_image&aved=e&thisid="+thisid,function(loader){
        document.getElementById(divplace).innerHTML = loader.xmlDoc.responseText;
        $('#gallery').imgScroll({mouseNavigation:true, mouseWheelSpeed:155, keyboardNavigation:true,  transition:'slide',
                                animateScroll:true, autoplay:true, autoplayInterval:10000, cycle:true, arrowButtonSpeed:155,
                                imagePreloader:'snowman1/html/addon/gallery2/images/6-32.gif', lightbox:true, thumbCaption:false, transitionEasing:'swing',
                                animationSpeed:500});
    });



};



function po_imageupload_loadupviwer_gallery1(thisid) {

    var divplace = 'divpo_imageupload';
    document.getElementById(divplace).innerHTML = '<div style="width:100%;padding:10px;background:#eee;"><div class="bu-div bu-formview"><button type="button" class="button" data-icon-primary="ui-icon-circle-check" id="button-poimgupload" onClick="app_element_po_imageupload_showupload();">Upload</button></div></div><div id="divpo_imageupload_setting"></div><div id="divpo_imageupload_vieworuplaod"></div>';
    formui_reload();
    var divplace = "divpo_imageupload_vieworuplaod";
    //apps_action_ajax('po','x_editpost_image','e',divplace,thisid);
    //dhtmlxAjax.get("?app=po&an=x_editpost_image&aved=e&thisid="+thisid);

    dhtmlxAjax.get("?app=po&an=x_editpost_image&aved=e&thisid="+thisid,function(loader){
        document.getElementById(divplace).innerHTML = loader.xmlDoc.responseText;
        $("#divpo_imageupload_gallery").wtGallery({
                num_display:5,
                screen_width:480,
                screen_height:360,
                padding:10,
                thumb_width:100,
                thumb_height:75,
                thumb_margin:5,
                text_align:"top",
                caption_align:"bottom",
                auto_rotate:true,
                delay:5000,
                rotate_once:false,
                auto_center:true,
                cont_imgnav:true,
                cont_thumbnav:true,
                display_play:true,
                display_imgnav:true,
                display_imgnum:false,
                display_thumbnav:true,
                display_thumbnum:false,
                display_arrow:true,
                display_tooltip:false,
                display_timer:false,
                display_indexes:true,
                mouseover_pause:true,
                mouseover_text:false,
                mouseover_info:false,
                mouseover_caption:false,
                mouseover_buttons:true,
                transition:"h.slide",
                transition_speed:800,
                scroll_speed:600,
                vert_size:45,
                horz_size:45,
                vstripe_delay:100,
                hstripe_delay:100,
                move_one:false,
                shuffle:false
        });
    });



};




function po_imageupload_loaduploader2() {


	// Client side form validation
	$('#po_imageupload_uploader').submit(function(e) {
        var uploader = $("#"+divplace).plupload('getUploader');
        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });

            uploader.start();
        } else
            alert('You must at least upload one file.');

        return false;
    });

	var uploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		browse_button : 'pickfiles',
		container : 'profilepic_upload',
		max_file_size : '10mb',
        unique_names: true,
		url : 'upload.php',
		flash_swf_url : 'html/addon/plupload/js/plupload.flash.swf',
		silverlight_xap_url : 'html/addon/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		],
		//resize : {width : 320, height : 240, quality : 90}
	});

	uploader.bind('Init', function(up, params) {
	//	$('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
	});

	$('#uploadfiles').click(function(e) {
		uploader.start();
		e.preventDefault();
	});

	uploader.init();

	uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$('#filelist').html(
				'<div id="' + file.id + '">' +
				file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
        up.start();
	});

	uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
	});

	uploader.bind('Error', function(up, err) {
		$('#filelist').append("<div>Error: " + err.code +
			", Message: " + err.message +
			(err.file ? ", File: " + err.file.name : "") +
			"</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	uploader.bind('FileUploaded', function(up, file, info) {
		$('#' + file.id + " b").html("100%");
        //alert(JSON.stringify(info));
        //var obj = jQuery.parseJSON(JSON.stringify(info));
       // alert(JSON.stringify(info));
        //var returnresult = jQuery.parseJSON(JSON.stringify(info));
        //if (info.status == "200"){
            var returnresult1 = jQuery.parseJSON(info.response);
            //alert(returnresult1.result);
            //alert("success");
            imgupload(returnresult1.result);
        //}

	});

    //uploader.bind('UploadComplete', function(up, file) {
    	//$(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
        //alert(file[0].name);
    //});

};