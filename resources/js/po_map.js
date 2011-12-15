


function po_map_showmap(orglat,orglng){

    //Set button disabled
    //$("#button-savelocation").attr("disabled", "disabled");

    //Append a change event listener to you inputs

    //$('#latlng').change(function(){
          //Validate your form here, example:
     //     var validated = true;
    //      if($('#latlng').val().length === 0) validated = false;

          //If form is validated enable form
    //      if(validated) $("#button-savelocation").show;
    //});
    //alert(orglat+orglng);
    var newlatlng = document.getElementById('latlng').value;
    if (!newlatlng) {

    //Trigger change function once to check if the form is validated on page load
    $("#button-savelocation").hide();

     // global "map" variable
    //var map = null;
    //var marker = null;
    var geocoder;
    var map;
    var marker;

    var infowindow = new google.maps.InfoWindow(
      {
        size: new google.maps.Size(150,50)
      });

    // A function to create the marker and set up the event window function
    function createMarker(latlng,noshowbtn) {
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
        $("#latlng").val(latlng);
        if (!noshowbtn) $("#button-savelocation").show();
        return marker;
    }

    //var latlng = new google.maps.LatLng(-34.397, 150.644);

    //var newlatlng = document.getElementById('latlng').value;

    //if (newlatlng) {
    //    latlng = newlatlng;
    //}else {
        var latlng = new google.maps.LatLng(orglat, orglng);
    //}

    var myOptions = {
      zoom: 12,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("divpo_map_showmap"),
        myOptions);

    google.maps.event.addListener(map, 'click', function(event) {
  	//call function to create marker
           if (marker) {
              marker.setMap(null);
              marker = null;
           }
     //alert(event.latLng);
  	 marker = createMarker(event.latLng);
    });

    if (orglat && orglng) marker = createMarker(latlng,1);

    geocoder = new google.maps.Geocoder();

    $("#po_map_address").autocomplete({
      //This bit uses the geocoder to fetch address values
      source: function(request, response) {
        geocoder.geocode( {'address': request.term }, function(results, status) {
          response($.map(results, function(item) {
            return {
              label:  item.formatted_address,
              value: item.formatted_address,
              latitude: item.geometry.location.lat(),
              longitude: item.geometry.location.lng()
            }
          }));
        })
      },
      //This bit is executed upon selection of an address
      select: function(event, ui) {
        //$("#latitude").val(ui.item.latitude);
        //$("#longitude").val(ui.item.longitude);
        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
        marker.setPosition(location);
        map.setCenter(location);
      }
    });


    };
};






function po_map_savelatlng(thisid,lang_saved){

    var latlng = document.getElementById('latlng').value;

    dhtmlxAjax.get("?app=po&an=x_editpost_geo&aved=es&thisid="+thisid+"&latlng="+latlng,function(loader){
        //document.getElementById(divplace).innerHTML = loader.xmlDoc.responseText;
        //alert(loader.xmlDoc.responseText);
        if (loader.xmlDoc.responseText == 'ok') {
          $('#button-savelocation').hide();
          notification_show('xmsg',lang_saved,2,1)
        }

        });

    //alert('save'+latlng+'xxx'+thisid);

}













