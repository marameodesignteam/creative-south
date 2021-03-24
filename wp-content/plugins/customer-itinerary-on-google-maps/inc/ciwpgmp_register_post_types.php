<?php

    /**

     * Post Type: Admin Register Trip Places.

     */



    $labels = [



        "name" 					=> esc_html__( "Trip Location", "ciwpgmp-google-maps" ),



        "singular_name" 		=> esc_html__( "Trip Location", "ciwpgmp-google-maps" ),



        "menu_name"				=> esc_html__( "Trip Locations", "ciwpgmp-google-maps" ),



        "all_items" 			=> esc_html__( "All Locations", "ciwpgmp-google-maps" ),



        "add_new" 				=> esc_html__( "Add New Location", "ciwpgmp-google-maps" ),



        "add_new_item" 			=> esc_html__( "Add New Location", "ciwpgmp-google-maps" ),



        "edit_item" 			=> esc_html__( "Edit Location", "ciwpgmp-google-maps" ),



        "new_item" 				=> esc_html__( "New Location", "ciwpgmp-google-maps" ),



        "view_item" 			=> esc_html__( "View Location", "ciwpgmp-google-maps" ),



        "view_items" 			=> esc_html__( "View Location", "ciwpgmp-google-maps" ),



        "search_items" 			=> esc_html__( "Search Location", "ciwpgmp-google-maps" ),



        "not_found" 			=> esc_html__( "No Location Found", "ciwpgmp-google-maps" ),



        "not_found_in_trash" 	=> esc_html__( "No Location Found in Trash", "ciwpgmp-google-maps" ),



    ];





    $args = [



        "label" 				=> esc_html__( "Trip Location", "ciwpgmp-google-maps" ),



        "labels" 				=> $labels,



        "description" 			=> "",



        "public" 				=> true,



        "publicly_queryable" 	=> true,



        "show_ui" 				=> true,



        "show_in_rest" 			=> true,



        "rest_base" 			=> "",



        "rest_controller_class" => "WP_REST_Posts_Controller",



        "has_archive" 			=> true,



        "show_in_menu" 			=> true,



        "show_in_nav_menus" 	=> true,



        "delete_with_user" 		=> false,



        "exclude_from_search" 	=> false,



        "capability_type" 		=> "post",



        "map_meta_cap" 			=> true,



        "hierarchical" 			=> false,



        "rewrite" 				=> [ "slug" => "trip_locations", "with_front" => true ],



        "query_var" 			=> true,



        "supports" 				=> [ "title", "editor", "thumbnail", "excerpt" ],



        "taxonomies" 			=> [ "category", "post_tag" ],



        "menu_icon" 			=> site_url().'/wp-content/plugins/wp-google-map-gold/assets/images/flippercode.png',



    ];



    register_post_type( "trip_locations", $args );







/**

* Post Type: User Registred Trips.

*/

    register_post_type('my_trips',



        array(



            'labels'      => array(



                'name'          => __('My Trips'),



                'singular_name' => __('My Trip'),



            ),



            'public'      => true,



            'has_archive' => true,



            'rewrite'     => array( 'slug' => 'my-trips' ),
            "menu_icon"   => site_url().'/wp-content/plugins/wp-google-map-gold/assets/images/flippercode.png',


        )



    );