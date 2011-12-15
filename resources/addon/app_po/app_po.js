function app_po_viewpost_start(lat,lng){

    $( "#tabs" ).tabs();

    $('#po_tabs_map').click(function() {
    app_po_viewpost_map(lat,lng);
    });

    $('#po_tabs_photo').click(function() {
        $("#divpo_photo").wtGallery({
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


$("ul.thumb li").hover(function() {
	$(this).css({'z-index' : '10'});
	$(this).find('img').addClass("hover").stop()
		.animate({
			marginTop: '-80px',
			marginLeft: '-100px',
			top: '50%',
			left: '50%',
			width: '160px',
			height: '120px',
			padding: '20px'
		}, 200);

	} , function() {
	$(this).css({'z-index' : '0'});
	$(this).find('img').removeClass("hover").stop()
		.animate({
			marginTop: '0',
			marginLeft: '0',
			top: '0',
			left: '0',
			width: '100px',
			height: '75px',
			padding: '5px'
		}, 400);
});

//Swap Image on Click
	$("ul.thumb li a").click(function() {

		var mainImage = $(this).attr("href"); //Find Image Name
		$("#divpo_photo_view img").attr({ src: mainImage });
		return false;
	});




}




function app_po_viewpost_map(lat,lng){

    var map;
    var marker;

    var infowindow = new google.maps.InfoWindow(
      {
        size: new google.maps.Size(150,50)
      });

    // A function to create the marker and set up the event window function
    function createMarker(latlng) {
        //var contentString = html;
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            zIndex: Math.round(latlng.lat()*-100000)<<5
            });

        //google.maps.event.addListener(marker, 'click', function() {
            //infowindow.setContent(contentString);
            //infowindow.open(map,marker);
        //    });
        //google.maps.event.trigger(marker, 'click');
        //alert(latlng);
        return marker;
    }

    var latlng = new google.maps.LatLng(lat,lng);

    var myOptions = {
      zoom: 12,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("div_viewpost_map"),
        myOptions);

    marker = createMarker(latlng);

};