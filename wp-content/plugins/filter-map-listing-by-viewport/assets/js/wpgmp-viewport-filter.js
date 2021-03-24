jQuery( document ).ready(function($) {
			    
    jQuery.fn.filterListingByViewport = function ( options ) { 

    	let settings = $.extend( { mapWidth: "50" }, options );

		let map_obj = jQuery(settings.wpgmp_map_selector).data("wpgmp_maps");


		let enable_viewport_filter = function(){

			var all_places = map_obj.places;
			google.maps.event.addListener(map_obj.map, 'zoom_changed', function() {
				filterListViewport();
			});

			google.maps.event.addListener(map_obj.map, 'dragend', function() { 
				filterListViewport();
			} );

			function filterListViewport(){

				var bounds = map_obj.map.getBounds();
			    var bound_markers = [];
				for(var i = 0; i< all_places.length; i++){ // looping through my Markers Collection    
					if(bounds.contains(all_places[i].marker.getPosition())){
					  bound_markers.push(all_places[i]);
					}
				}
				map_obj.show_places = bound_markers;
	            map_obj.map_data.places = map_obj.show_places;
	            map_obj.update_places_listing();

			}

			
	 	}

	 	if (map_obj !== 'undefined' && map_obj.map_data.listing && map_obj.map_data.viewportfilter) { 
	 		enable_viewport_filter(); 		
	 	}

	 	return this;
	};

	jQuery("div.wpgmp_map_container").each(function (index, element) { 

		let wpgmp_map_selector = "#"+$(this).attr('rel');
		let wpgmp_layout_args = {'wpgmp_map_selector' : wpgmp_map_selector};
		jQuery(wpgmp_map_selector).filterListingByViewport(wpgmp_layout_args);
	    
	});
		
});
