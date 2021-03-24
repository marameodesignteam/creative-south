

jQuery( document ).ready (function($) {


        jQuery.fn.getMapData = function ( options ) { 


            let map_obj = jQuery(options.wpgmp_map_selector).data("wpgmp_maps");


            let mapId = map_obj.map_data.map_property.map_id;


            let routesdata = map_obj.map_data.routesdata;


            let wpindex = 0;


            let routes = routesdata;

            if(routes != undefined) {

                if(jQuery.isArray(routesdata)) {


               var actualStartPoint = routesdata[0]['start_location_data'];

            } else{

               var actualStartPoint = routesdata['start_location_data'];

            }

            let waypts = [];

            if(jQuery.isArray(routesdata)) {

                $.each(routesdata, function(index, routeobj) {

                        if (true) {

                        let start = routeobj.start_location_data;

                        waypts.push({'locdata' : start });

                        if(typeof routeobj.way_points != 'undefined' && routeobj.way_points.length > 0 ) {

                            $.each(routeobj.way_points, function(point_index, place) {

                                waypts.push({'locdata' : place });

                            });
                        }
                        let end   = routeobj.end_location_data;
                        waypts.push({'locdata' : end });
                    }
                });

            }else {

                let start = routesdata.start_location_data;

                waypts.push({'locdata' : start });

                if(typeof routesdata.way_points != 'undefined' && routesdata.way_points.length > 0 ) {

                    $.each(routesdata.way_points, function(point_index, place) {

                        waypts.push({'locdata' : place });
                    });
                }
                let end   = routesdata.end_location_data;
                waypts.push({'locdata' : end });
            }

            let start_point = actualStartPoint.split(',');

            var latitude1 = parseFloat(start_point[0]);

            var longitude1 = parseFloat(start_point[1]);

            for( i=0; i<waypts.length; i++ ) {

                if(waypts[i]['locdata'] != 'undefined') {

                    let loc_array = waypts[i]['locdata'].split(',');

                    var latitude2 = parseFloat(loc_array[0]);

                    var longitude2 = parseFloat(loc_array[1]);

                    var distance = google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(parseFloat(latitude1), parseFloat(longitude1)), new google.maps.LatLng(parseFloat(latitude2), parseFloat(longitude2) ) );

                    waypts[i].distance = distance;
                }
            }
            waypts.sort(function(obj1, obj2) {

                return obj1.distance - obj2.distance;
            });

            let counter = 0;
            $.each(routes, function(index, routeobj) {
                if(wpindex == 0){

                    routes[index]['start_location_data'] = waypts[wpindex]['locdata'];
                    wpindex++;
                }
                else{
                    routes[index]['start_location_data'] = waypts[wpindex-1]['locdata'];
                }
                let current_route_way_points = [];

                if(typeof routeobj.way_points != 'undefined' && routeobj.way_points.length > 0 ) {

                    if(counter == 0) {

                        if(routeobj.way_points.length >0){

                            for(i=0; i<routeobj.way_points.length; i++ ){

                                current_route_way_points.push(waypts[wpindex]['locdata']);
                                wpindex++;
                            }
                        }
                    }else{

                        if(routeobj.way_points.length >0){

                            for(i=0; i<routeobj.way_points.length+1; i++ ){

                                current_route_way_points.push(waypts[wpindex]['locdata']);
                                wpindex++;
                            }
                        }
                    }
                }else{
                    current_route_way_points.push(waypts[wpindex]['locdata']);
                    wpindex++;
                }

                if(waypts.length == 2) {

                    routes[index]['way_points'] = current_route_way_points;

                    routes[index]['end_location_data'] = waypts[index]['locdata'];

                    index++;
                } else {
                    routes[index]['way_points'] = current_route_way_points;
                    routes[index]['end_location_data'] = waypts[wpindex]['locdata'];
                    wpindex++;
                }
                counter++;

           });


           map_obj.map_data.routes = routes;
           map_obj.create_routes();

        };

            }

            
        jQuery("div.wpgmp_map_container").each(function (index, element) { 

            let wpgmp_map_selector = "#"+$(this).attr('rel');

            let wpgmp_layout_args = {'wpgmp_map_selector' : wpgmp_map_selector};

            jQuery(wpgmp_map_selector).getMapData(wpgmp_layout_args);

        });


        jQuery('#create_new_trip_btn_id').on('click', function(event) {

            jQuery('#display_add_existing_trip_div').hide();
            jQuery('#display_create_new_trip_div').toggle('show');
            

        });

        jQuery('#add_existing_trip_btn_id').on('click', function(event) {

            jQuery('#display_create_new_trip_div').hide();
            jQuery('#display_add_existing_trip_div').toggle('show');
            

        });

        jQuery('.fc-frontend-hide').parent().addClass('all_locations_div');



        //Add More Locations div

        jQuery('#add_more_locations').on('click', function(event) {



                



            jQuery('.all_locations_div').toggle('show');



        });





        jQuery(".trips_listings tr:odd").css({

            "background-color":"#f1f1f1"});

        

        //$('.all_locations_div').css({'display:none'});



  });



