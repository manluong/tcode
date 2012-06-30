function preview(img, selection) {
    if (!selection.width || !selection.height)
        return;

    var scaleX = 60 / selection.width;
    var scaleY = 60 / selection.height;

    $('img#profilepic_preview_photo').css({
        width: Math.round(scaleX * img.width),
        height: Math.round(scaleY * img.height),
        marginLeft: -Math.round(scaleX * selection.x1),
        marginTop: -Math.round(scaleY * selection.y1)
    });

    $('#x1').val(selection.x1);
    $('#y1').val(selection.y1);
    $('#x2').val(selection.x2);
    $('#y2').val(selection.y2);
    $('#w').val(selection.width);
    $('#h').val(selection.height);
}

var ias, pic_real_width, pic_real_height;

function setsel(img, selection) {

    if (img.width > img.height) usethis = img.height;
    else usethis = img.width;

    ias.setSelection(0, 0, usethis, usethis);
    ias.setOptions({ show: true });
    ias.update();

    selection.x1 = 0;
    selection.y1 = 0;
    selection.x2 = usethis;
    selection.y2 = usethis;
    selection.width = usethis;
    selection.height = usethis;

    $('#imgw').val(img.width);
    $('#imgh').val(img.height);

    preview(img, selection);
}


function imgupload(filename) {
      //var img = $("img#photo")[0]; // Get my img elem
      //$("<img/>") // Make in memory copy of image to avoid css issues
      //    .attr("src", $(img).attr("src"))
      //    .load(function() {
      //        pic_real_width = this.width;   // Note: $(this).width() will not
      //        pic_real_height = this.height; // work for in memory images.
      //})
      //alert(filename);

      //$("#profilepic_org").html();
      //$("#profilepic_org").append("<img id=\"photo\" src=\"html/addon/plupload/examples/uploads/"+filename+"\" />")
      //$("#profilepic_preview").append("<img id=\"preview\" src=\"html/addon/plupload/examples/uploads/"+filename+"\" />")
      var src = "userfiles/tmp/"+filename;

      $("#profilepic_photo").attr("src",src);
      $("#profilepic_preview_photo").attr("src",src);
      $('#imgfilename').val(filename);
      $('#imgpreload').val("-");

      //setTimeout(
      //$('#profilepic_upload').css({"display":"none"});
      $('#profilepic_preload_warp').css({"display":"none"});
      $('#profilepic_org_warp').css({"display":"inline"})
      //,1250);

      ias = $('img#profilepic_photo').imgAreaSelect({
      aspectRatio: '1:1',
      handles: true,
      fadeSpeed: 200,
      //imageHeight: pic_real_height,
      //imageWidth: pic_real_width,
      onInit: setsel,
      onSelectChange: preview,
      instance: true
      //x1: 0, y1: 0, x2: 100, y2: 100
      });

};

function profilepic_sel_cancel(){
    if (ias!=undefined) {ias.cancelSelection();}
}

function profilepic_preload(usethis,filepath,fileshort){
    //alert(filepath+"/"+usethis);
    $("#profilepic_preview_photo").attr("src",filepath+"/"+usethis);
    $('img#profilepic_preview_photo').css({
        width: 60,
        height: 60,
        marginLeft: 0,
        marginTop: 0
    });
    $('#imgfilename').val("-");
    $('#imgpreload').val(fileshort);
}

function profilepic_preload_view(){
    if (ias!=undefined) {ias.cancelSelection();}
    $('#profilepic_org_warp').css({"display":"none"});
    $('#profilepic_preload_warp').css({"display":"inline"});
}

// Custom example logic
function profilepic_uploader() {
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

















