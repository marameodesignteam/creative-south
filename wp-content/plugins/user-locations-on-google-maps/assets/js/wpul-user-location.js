

(function($) {
    "use strict";

	$(document).ready(function(){
	    if(navigator.geolocation){
	        navigator.geolocation.getCurrentPosition(getUserLocation);
	    }else{ 
	        alert('location Error');
	    }
	});

	function getUserLocation(position){

		var geocoder = new google.maps.Geocoder(); 
	    var latitude = position.coords.latitude;
	    var longitude = position.coords.longitude;
	    var time = position.timestamp;
	  
	    var latlng 	= new google.maps.LatLng(latitude, longitude);
	    
	    geocoder.geocode({'latLng': latlng}, function(results, status) {
	        if(status == google.maps.GeocoderStatus.OK) {

	            if(results[0]) {

	            var current_address = results[0]["formatted_address"];

	            if (results[0].address_components) {

                    for (var i = 0; i < results[0].address_components.length; i++) {
                        for (var j = 0; j < results[0].address_components[i].types.length; j++) {

                            if (results[0].address_components[i].types[j] == "locality") {
                                var city = results[0].address_components[i].long_name;
                            }
                            if (results[0].address_components[i].types[j] == "administrative_area_level_2") {
                                var city1 = results[0].address_components[i].long_name;
                            }
                            if (results[0].address_components[i].types[j] == "administrative_area_level_3") {
                                var city2 = results[0].address_components[i].long_name;
                            }
                            if (results[0].address_components[i].types[j] == "administrative_area_level_1") {
                                var state = results[0].address_components[i].long_name;
                            }
                            if (results[0].address_components[i].types[j] == "country") {
                                var country = results[0].address_components[i].long_name;
                                
                            }
                        }
                    }
                }

                var data =  {action: 'wpul_ajax_call', 'formatted_address' : current_address, 'city' : city || city1 || city2, 'state' : state, 'country' : country, 'latitude': latitude, 'longitude': longitude};

			    jQuery.ajax({

			        type : "POST",
			        dataType : "json",
			        url : myAjax.ajaxurl,
			        data :data,
			    });

	            } else {
	                console.log('No results found');
	            }
	        } else {
	            var error = {
	                'ZERO_RESULTS': 'No Address Found'
	            }
	            console.log(error[status]);
	        }
	    });

	}

	

})(jQuery);
