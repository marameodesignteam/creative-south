<?php



global $post;

$category = array();
$post_meta = get_post_meta($post->ID);
$get_map_id = get_post_meta($post->ID, '_wpgmp_map_id', true);
$applied_on_map = maybe_unserialize($get_map_id);
$post_categories = get_the_category();
$modelFactory = new WPGMP_Model();
$category_obj = $modelFactory->create_object( 'group_map' );
$wpgmp_categories   = $category_obj->fetch();
$get_marker_categories = get_post_meta($post->ID, '_wpgmp_metabox_marker_id', true);
$get_marker_categories = maybe_unserialize($get_marker_categories);

if(isset($get_marker_categories) && !empty($get_marker_categories) ) {

    foreach ($wpgmp_categories as $wpgmp_category) {

        foreach ($get_marker_categories as $get_marker_category ) {

            if(($wpgmp_category->group_map_id == $get_marker_category) && !empty($wpgmp_category->group_marker)) {

                $result['cat_marker_title'] = $wpgmp_category->group_map_title;
                $result['cat_marker_url'] = $wpgmp_category->group_marker;
            }
            break;
        }
    }

}else{

    $result['cat_marker_title'] = '';
    $result['cat_marker_url'] = '';

}



if(is_array($post_categories) && count($post_categories) > 0) {

    foreach($post_categories as $post_category) {

        $post_category =  '<span>'.$post_category->cat_name .'</span>';

        $post_cat[] = $post_category;
    }

} else {

    $post_cat = '';
}


if(is_array($post_cat) && count($post_cat) > 0 ) {
    $post_cat = implode(', ', $post_cat);
}
    
if(!empty($post_meta['_wpgmp_metabox_latitude'][0]) && !empty($applied_on_map) ) {

    $lat = $post_meta['_wpgmp_metabox_latitude'][0];
    $lng = $post_meta['_wpgmp_metabox_longitude'][0];
    $city = $post_meta['_wpgmp_location_city'][0];
    $state = $post_meta['_wpgmp_location_state'][0];
    $country = $post_meta['_wpgmp_location_country'][0];
    $address = $post_meta['_wpgmp_location_address'][0];

    $message_content = $address.'<br/>City:'.$city.' <br/>State:'.$state.' <br/>Country:'.$country.' <br/><b>speciality:</b> '.$post_cat;

    $trip_form_html = '<br><br>';


    $trip_form_html .= do_shortcode( '[display_map height="250" zoom="10" language="en" map_type="ROADMAP" map_draggable="true" marker1=" '.$lat.' | '.$lng.' | title | '.$message_content.' | '.$result['cat_marker_title'].' "]');


    if(is_user_logged_in() ) {

        $create_trip_link = '<div class="fc-form-group trips-btn"><button href="#" id="create_new_trip_btn_id" class="fc-btn fc-btn-submit fc-btn-big">'. esc_html__( "Create New Trip", "ciwpgmp-google-maps" ).'</button>';

        $create_trip_link .= '<button href="#" id="add_existing_trip_btn_id" class="fc-btn fc-btn-submit fc-btn-big">'. esc_html__( "Add To Existing Trip", "ciwpgmp-google-maps" ).'</button> </div>';

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

        $form->add_element(
            'checkbox', 'add_current_location_to_trip', array(
            'label'   => esc_html__( 'Add location to trip?', 'wpgmp-google-map' ),
            'value'   => 'true',
            'current' => isset( $data['add_current_location_to_trip'] ) ? $data['add_current_location_to_trip'] : '',
            'desc'    => esc_html__( 'please enable this checkbox to add current location into this new trip.', 'wpgmp-google-map' ),
            'class'   => 'chkbox_class',
            'before' => '<div class="fc-8 trips-checkbox">',
            'after'  => '</div>',
            )
        );


        $form->add_element('hidden','current_post_id',array(

            'value' => get_the_ID(),
        ));

        $form->add_element('hidden','_nonce',array(

            'value' => wp_create_nonce('submit-trip-name-nonce'),
        ));



        $form->add_element('submit','wptp_create_trip',array(

            'value' => esc_html__( 'Create Trip','ciwpgmp-google-maps' ),

            'class' => 'fc-btn fc-btn-submit fc-btn-big',

            'before'      => '<div class="fc-12">',

            'after'       => '</div>',
        ));



        ob_start();
        $form->render();
        $trip_form_html .= '<div class="create-trip-btn">'.$create_trip_button.'</div>';
        $trip_form_html .= '<div class="ciwpgmp-update-trip" id="display_create_new_trip_div" style="display:none;">'.ob_get_contents().'</div>';
        ob_clean();

        $trips_option_list = array();
        $args = array(
        'post_type'     => 'my_trips',
        'numberposts'   => -1,
        'author'    => get_current_user_id(),
        );

        $all_created_trips = get_posts($args);

        if(is_array($all_created_trips) && count($all_created_trips) > 0 ) {

            foreach($all_created_trips as $all_created_trip ) {

                $trips_option_list[''] = esc_html__( "Please Select", "ciwpgmp-google-maps" );

                $trips_option_list[$all_created_trip->ID] = $all_created_trip->post_title;
            }

        }
        
        if(is_array($trips_option_list) && count($trips_option_list) > 0 ) {

        $post_title = get_the_title();
        $form_heading = sprintf(esc_html__( 'Add %s into Trip', 'ciwpgmp-google-maps'),$post_title );
        $form_heading = apply_filters('ciwpgmp_selected_form_heading', $form_heading);
        $form  = new WPGMP_Template(array('no_header'=>true));
        $form->set_header($form_heading, $this->responseMessage);


        $form->add_element(

            'select', 'trip_post_id', array(

            'label'   => esc_html__( 'Choose Trip', 'ciwpgmp-google-maps' ),

            'current' => ( isset( $data['trip_post_id'] ) and ! empty( $data['trip_post_id'] ) ) ? sanitize_text_field( wp_unslash( $data['trip_post_id'] ) ) : '',

            'desc'    => esc_html__( 'Please select trip to add this location.', 'ciwpgmp-google-maps' ),

            'required' => true,

            'options' => $trips_option_list,

            'class'   => 'form-control-select',

            )

        );

        $form->add_element('hidden','_nonce',array(

            'value' => wp_create_nonce('add-trip-place-nonce'),

        ));



        $form->add_element('submit','wptp_add_location_in_trip',array(

            'value' => esc_html__( 'Add to Trip','ciwpgmp-google-maps' ),

            'before'      => '<div class="fc-12">',

            'after'       => '</div>',

        ));


        ob_start();
        $form->render();
        $trip_form_html .= '<div class="ciwpgmp-update-trip" id="display_add_existing_trip_div" style="display:none;">'.ob_get_contents().'</div>';
        ob_clean();
        } else{

            $no_trips = FlipperCode_HTML_Markup::field_message('no_trips',array(
                'value' => esc_html__( 'Sorry you have no trips.', 'wpgmp-google-map' ),
                'class' => 'fc-msg fc-info',
            ));

            $no_trips = apply_filters('ciwpgmp_no_trips_message', $no_trips);
            $trip_form_html .= '<div class="please-loggedin" id="display_add_existing_trip_div" style="display:none;">'.$no_trips.'</div>';
        }

    } else {


        $single_posts_loggedin_message = FlipperCode_HTML_Markup::field_message('no_login_found',array(
            'value' => esc_html__( 'Please login to add this location into trip.', 'wpgmp-google-map' ),
            'class' => 'fc-msg fc-info',
        ));

        $trip_list_loggedin_message = apply_filters('ciwpgmp_location_loggedin_message', $single_posts_loggedin_message);
        $trip_form_html .= '<div class="please-loggedin">'.$trip_list_loggedin_message.'</div>';
    }

}

return $trip_form_html;