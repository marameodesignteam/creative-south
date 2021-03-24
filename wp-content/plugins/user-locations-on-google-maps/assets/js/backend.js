
(function($) {

    "use strict";

	jQuery( document ).ready (function($) {

    function init() {

      var input = document.getElementById('wpgmp_autocomplete_control_id');
      var autocomplete = new google.maps.places.Autocomplete(input);
      google.maps.event.addListener(autocomplete, 'place_changed', function() {
      
        var place = autocomplete.getPlace();
            if (!place.geometry) {
              console.log("No details available for input: '" + place.name + "'");
              return;
            }else{

              function wpgmp_finddata(result, type) {
            var component_name = "";
            for (var i = 0; i < result.address_components.length; ++i) {
              var component = result.address_components[i];
              jQuery.each(component.types, function(index, value) {
                if (value == type) {
                  component_name = component.long_name;
                }
              });
            }
            return component_name;
          }

          var place = autocomplete.getPlace();
          var address = '';
          if (place.address_components) {
            var address = place.formatted_address;
          }
          if ( place.geometry ) {
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
          }
          var country = wpgmp_finddata(place, 'country');
          var state = wpgmp_finddata(place, 'administrative_area_level_1');
          var city = wpgmp_finddata(place, 'administrative_area_level_2') || wpgmp_finddata(place, 'administrative_area_level_3') || wpgmp_finddata(place, 'locality');
          jQuery(".wpgmp_autocomplete_control").val(address);
          jQuery(".wpgmp_autocomplete_city").val(city);
          jQuery(".wpgmp_autocomplete_state").val(state);
          jQuery(".wpgmp_autocomplete_country").val(country);
          jQuery(".wpgmp_autocomplete_lat").val(lat);
          jQuery(".wpgmp_autocomplete_lng").val(lng);
          
        }
      });
    }

    if($('#wpgmp_autocomplete_control_id').length > 0) {
       google.maps.event.addDomListener(window, 'load', init);      
    }
        
		
		jQuery("#wpul_reset_message").on("click", function(event){
		    
		    var defaultMessage = $('#wpul_infomessage')[0];
            if (confirm('Are you sure you want to reset your changes?')) {
              defaultMessage.value = defaultMessage.defaultValue;
            } else {
                // Do nothing!
            }

		});

     	jQuery('#wpul_show_placeholders').on('click', function(event) {
     		
        	jQuery('#wpul_placeholders_div').toggle('show');
        	if ($.trim($(this).val()) === 'Show Placeholders') {
        		$(this).val('Hide Placeholders');
		    } else {
		        $(this).val('Show Placeholders');
		    }
    	});
    	
	});

})(jQuery);
