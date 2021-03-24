<?php



/**

 * Parse Shortcode and display Trips Listing.

 *

 * @package Maps

 * @author Flipper Code <hello@flippercode.com>

 *

 **/



$admin_styles = array(

    'font_awesome_minimised'   => WPGMP_CSS . 'font-awesome.min.css',

);



foreach ( $admin_styles as $admin_style_key => $admin_style_value ) {

    wp_enqueue_style( $admin_style_key, $admin_style_value );

}



if(is_user_logged_in() ) {

    global $current_user;

    $map_id = '';

    $all_trips = self::wptp_get_all_current_user_trips();



    if($this->tripDeleted) {

               

        echo '<div class="fc-12 fc-msg fc-success fade in">'.esc_html__( "Trip Deleted Successfully", "ciwpgmp-google-maps" ).' </div>'; 

    }





    if(!isset($_GET['action'])) { 





        $create_trip_link = '<div class="fc-form-group"><button href="#" id="create_new_trip_btn_id" class="fc-btn fc-btn-submit fc-btn-big">'. esc_html__( "Create New Trip", "ciwpgmp-google-maps" ).'</button> </div>';



        $create_trip_button = FlipperCode_HTML_Markup::field_html('create_trip_html', array(



                'html'   => $create_trip_link,



                'before' => '<div class="fc-4">',



                'after'  => '</div>',



            )



        );



        $form_heading = esc_html__( 'Create a New Trip', 'ciwpgmp-google-maps' );



        $form_heading = apply_filters('ciwpgmp_create_form_heading', $form_heading);



        $form  = new WPGMP_Template(array('no_header'=>true));



        $form->set_header($form_heading, $this->response);







        $form->add_element( 'text', 'wptp_trip_name', array(

            'label' => esc_html__('Trip Name','ciwpgmp-google-maps'),

            'value' => (isset( $data['wptp_trip_name'] ) && ! empty( $data['wptp_trip_name'] )) ? $data['wptp_trip_name'] : '',

            'required' => true,

            'class' => 'form-control user-trip-input',

            'placeholder' => esc_html__( 'Trip Name', 'ciwpgmp-google-maps' ),



        ));





        $form->add_element('hidden','_nonce',array(



            'value' => wp_create_nonce('submit-trip-name-nonce'),



        ));



        $form->add_element('submit','wptp_create_trip',array(



                'value' => esc_html__( 'Create Trip','ciwpgmp-google-maps' ),



                'before'      => '<div class="fc-12">',



                'after'       => '</div>',



            ));





        $actions = array();

        $columns   = array( 



            'trip_id' => esc_html__('ID', 'ciwpgmp-google-maps' ),



            'trip_name' => esc_html__('Trip Name', 'ciwpgmp-google-maps' ),



            'trip_locations' => esc_html__('Locations', 'ciwpgmp-google-maps' ),



            'view_trip' => esc_html__('View Trip', 'ciwpgmp-google-maps' ),



            'edit' => esc_html__('Edit', 'ciwpgmp-google-maps' ),



            'delete' => esc_html__('Delete', 'ciwpgmp-google-maps' ),



        );







        $all_trips = array();







        $all_trips = self::wptp_get_all_current_user_trips();





        global $wpdb;







        if(is_array($all_trips) && count($all_trips) > 0) {







            foreach ($all_trips as $trips ) {







                $added_trip_ids = get_post_meta( $trips->ID, '_wptp_trip_locations', true );



                $trip_url = get_post_permalink($trips->ID);



                $view_trip = '<a target="_blank" href="'.$trip_url.'">View</a>';



                $delete = "<a href='" . wp_nonce_url("?doaction=delete&amp;post=" . $trips->ID, 'delete-post_' . $trips->ID) . "'>Delete</a>";



                $share = '<div><a href="https://www.facebook.com/sharer/sharer.php?u={post_link}" class="facebook wpgmp-social-share"><i class="fa fa-facebook-square" aria-hidden="true"></i></a><a href="https://twitter.com/intent/tweet/?text={post_title}&url={post_link}" class="twitter wpgmp-social-share"><i class="fab fa-twitter-square"></i></a></div>';







                $trip_location_title = array();



                if(is_array($added_trip_ids) && !empty($added_trip_ids)) {







                    foreach($added_trip_ids as $added_trip_id) {



                        $trip_location_title[] = '<a target="_blank" href="'.get_the_permalink($added_trip_id).'">'. get_the_title($added_trip_id).' ';



                    }



                }



        



                if( is_array($trip_location_title) && count($trip_location_title) > 0 )



                    $location_link = implode("| ", $trip_location_title);



                else 



                    $location_link = esc_html__( "No Locations", "ciwpgmp-google-maps" );







                $edit_url = "<a href='" . wp_nonce_url("?doaction=edit&amp;post=" . $trips->ID, 'edit-post_' . $trips->ID) . "'>Edit</a>";



            



                $location_data[] = (object)array('trip_id' => $trips->ID, 'trip_name' => $trips->post_title, 'trip_locations' => $location_link, 'view_trip' =>$view_trip, 'edit' => $edit_url, 'delete' => $delete);



            }



        }



        if( isset($location_data) && count($location_data) > 0 ) {



            $tableinfo = array(



            'noSql'          => true,



            'external'       =>  $location_data,



            'textdomain'     => 'ciwpgmp-google-maps', 



            'singular_label' => 'location',



            'plural_label'   => 'locations',



            'admin_listing_page_name' => 'wpgmp_manage_location',



            'admin_add_page_name'     => 'edit-map-frontend-listing',



            'primary_col'             => 'trip_id',



            'columns'                 => $columns,



            'per_page'                => 10,



            'actions'                 => $actions,



            'col_showing_links'       => 'trip_name',



            'translation'             => array(



                'manage_heading'        => esc_html__( 'Manage Trips', 'ciwpgmp-google-maps' ),



                'add_button'            => esc_html__( 'Add Location', 'ciwpgmp-google-maps' ),



                'delete_msg'            => esc_html__( 'Trip deleted successfully', 'ciwpgmp-google-maps' ),



                'insert_msg'            => esc_html__( 'Trip added successfully', 'ciwpgmp-google-maps' ),



                'update_msg'            => esc_html__( 'Trip updated successfully', 'ciwpgmp-google-maps' ),



                ),



            );



            $GLOBALS['hook_suffix'] = '';



            ob_start();



            $form->render();

            

            $args = '<div class="create-trip-btn">'.$create_trip_button.'</div>';

            $args .= '<div class="ciwpgmp-update-trip" id="display_create_new_trip_div" style="display:none;">'.ob_get_contents().'</div>';

            ob_clean();

            

            ob_start();

            new FlipperCode_List_Table_Helper( $tableinfo );

            $args .= '<div class="fc-main trips_listings">'.ob_get_contents().'</div>';

            ob_clean();



        } else {



            ob_start();

            $form->render();

            

            $args = '<div class="create-trip-btn">'.$create_trip_button.'</div>';

            $args .= '<div class="ciwpgmp-update-trip" id="display_create_new_trip_div" style="display:none;">'.ob_get_contents().'</div>';



            $no_trips = FlipperCode_HTML_Markup::field_message('no_trips_available_msg',array(

                'value' => esc_html__( 'Trips not available, Please create your first trip.', 'wpgmp-google-map' ),

            'class' => 'fc-msg fc-info',

            ));



            $args .=

            '<div class="fc-form-group ">

                <div class="fc-8 no-trips">'

                    .$no_trips.

                '</div>

            </div>';



            ob_clean();



        }



    }







//Edit Work



if(isset($_GET['doaction']) && $_GET['doaction'] == 'edit') {





    $post_id = isset($_GET['post']) ? $_GET['post'] : '';



    $post_author_id = get_post_field( 'post_author', $post_id );



    if( $post_author_id == get_current_user_id() ) {





        $trip_id = $_GET['post'];



        $title = get_the_title($trip_id);



        $get_trip_locations = get_post_meta($trip_id, '_wptp_trip_locations', true);



        



        if(empty($get_trip_locations)) {



            $get_trip_locations = array();



            $edit_trip_current_loc = esc_html('No Locations Found in this trip', 'ciwpgmp-google-maps');



        } else{



            $edit_trip_current_loc = esc_html('Current Locations in Trip', 'ciwpgmp-google-maps');



        }



        $form_heading = esc_html__( 'Update Trip', 'ciwpgmp-google-maps' );

        $form_heading = apply_filters('fngmp_update_form_heading', $form_heading);

        $form  = new WPGMP_Template(array('no_header'=>true));

        $form->set_header($form_heading, $this->updateTripResponse);

        $form->add_element( 'text', 'wptp_trip_name', array(
            'label' => esc_html__( 'Trip Name', 'ciwpgmp-google-maps' ),
            'value' => (isset( $title ) && ! empty( $title )) ? $title : '',
            'required' => true,
            'id'        => 'location_title_id',
            'placeholder' => esc_html__( 'Location Title', 'ciwpgmp-google-maps' ),
            'before'    => '<div class="fc-9">',
            'after'    => '</div>'
        ));



        $loc_title = array();



        foreach($get_trip_locations as $locations) { 

            $loc_title[$locations] = get_the_title($locations);

            $location_id[] = $locations;

        }


            $form->add_element( 'multiple_checkbox', 'locations[]', array(

                'label' => esc_html__( 'Current Locations', 'ciwpgmp-google-maps' ),
                'value' => $loc_title,
                'current' => isset($location_id) ? $location_id :'',
                'class' => 'chkbox_class',
                'before' => '<div class="fc-8 trips-checkbox">',
                'after'  => '</div>',
            ));

        
            $args = array(



                'post_type'     => 'trip_locations',



                'post_status'   => 'publish',



                'numberposts'   => -1,



            );



            $get_all_locations = get_posts($args);







            foreach($get_all_locations as $get_locations) {



                $loc_id[] = $get_locations->ID;



            }



            



            $get_unique_loc_array = array_diff($loc_id, $get_trip_locations);



            



            if(!empty($get_unique_loc_array)) {

                if(is_array($loc_title) && count($loc_title) > 0 ) { 
                    $generate_link = '<span id="add_more_locations" class="fc-btn fc-btn-default">'.esc_html__( 'Add Locations', 'ciwpgmp-google-maps' ).'</span>';
                }else{
                    $generate_link = '<span id="add_more_locations" class="fc-btn fc-btn-default">'.esc_html__( 'Add More', 'ciwpgmp-google-maps' ).'</span>';
                }
            	



		        $form->add_element(



		            'html', 'add_more_locations', array(



		                'label' => esc_html__( '&nbsp;', 'ciwpgmp-google-maps' ),



		                'html'   => $generate_link,



		                'before' => '<div class="fc-4 add-more-btn">',



		                'after'  => '</div>',



		            )



		        );



                foreach($get_unique_loc_array as $unique) { 







                    $remaining_locs[$unique] = get_the_title($unique);



                    $rem_location_id[] = $unique;



                }







                $form->add_element( 'multiple_checkbox', 'remaining_locations[]', array(
                    'label' => esc_html__( '&nbsp;', 'ciwpgmp-google-maps' ),
                    'value' => $remaining_locs,
                    'current' => isset($unique) ? $unique :'',
                    'class' => 'chkbox_class',
                    'before' => '<div class="fc-8 fc-frontend-hide trips-checkbox">',
                    'after'  => '</div>',
                ));



            }







            $form->add_element('hidden','post_id',array(



                'value' => $trip_id,



            ));







            $form->add_element('submit','wptp_update_trip',array(



                'value' => esc_html__( 'Update Trip','ciwpgmp-google-maps' ),



                'before'      => '<div class="fc-12">',



                'after'       => '</div>',



            ));











            ob_start();



                $form->render();



                $args = '<div class="ciwpgmp-update-trip">'.ob_get_contents().'</div>';



            ob_clean();



    }else{



        ob_start();



            $args = '<div class="wrong-user">You are not able to update this trip.</div>';



        ob_clean();



    }

}



} else {



    ob_start();



        $trip_list_loggedin_message = FlipperCode_HTML_Markup::field_message('no_loggedin_user_found',array(
            'value' => esc_html__( 'Please login to view your created trips.', 'wpgmp-google-map' ),
            'class' => 'fc-msg fc-info',
        ));



        $trip_list_loggedin_message = apply_filters('ciwpgmp_trip_list_loggedin_message', $trip_list_loggedin_message);



        $args = '<div class="please-loggedin">'.$trip_list_loggedin_message.'</div>';



    ob_clean();



}



return $args;



