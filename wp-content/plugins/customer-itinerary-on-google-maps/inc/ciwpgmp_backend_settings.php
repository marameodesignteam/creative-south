

<?php

/**

 * 

 *

 * @package Maps

 * @author Flipper Code <hello@flippercode.com>

 */



	$data['ciwpgmp_settings'] = maybe_unserialize( get_option( 'ciwpgmp_settings' ) );



	$productInfo = array(

		'productName'       => esc_html__('Customer Itinerary On Google Maps', 'ciwpgmp-google-maps' ),

		'productSlug'       => 'wp-user-location-on-map',

		'product_tag_line'  => esc_html__('(WGPMP Extension) Allow Member/Guest to add markers (locations) to google maps at frontend. Ability to import locations via CSV.','customer-itinerary-on-google-maps' ),

		'productTextDomain' => 'customer-itinerary-on-google-maps',

		'productVersion'    => CIWPGMP_VERSION,

		'productID'         => '#',

		'videoURL'          => 'https://www.wpmapspro.com/tutorials/',

		'docURL'            => 'https://www.wpmapspro.com/tutorials/',

		'demoURL'           => 'https://www.wpmapspro.com/tutorials/',

		'productSaleURL'    => 'https://www.wpmapspro.com/product/customer-itinerary-on-google-maps/',

		'multisiteLicence'  => 'https://www.wpmapspro.com/product/customer-itinerary-on-google-maps/',

	);



	//echo '<pre>'; print_r($data['ciwpgmp_settings']['ciwpgmp_map']);

	$form  = new FlipperCode_HTML_Markup($productInfo);

	$form->set_header( esc_html__( 'Select Map for Trips', 'ciwpgmp-google-maps' ), $response );





	$modelFactory = new WPGMP_Model();

    $map_obj = $modelFactory->create_object( 'map' );

    $wpgmp_maps   = $map_obj->fetch();

    

    $maps = array();



    if(is_array($wpgmp_maps) && !empty($wpgmp_maps)) {



    	foreach ($wpgmp_maps as $map) {

	    	$maps[''] = esc_html__( 'Please Select Map', 'ciwpgmp-google-maps' );

	    	$maps[$map->map_id] = $map->map_title;



	    }

	}


	$form->add_element(

		'select', 'ciwpgmp_settings[ciwpgmp_map]', array(

			'label'   => esc_html__( 'Select Map', 'ciwpgmp-google-maps' ),

			'current' => isset($data['ciwpgmp_settings']['ciwpgmp_map']) ? $data['ciwpgmp_settings']['ciwpgmp_map'] : '',

			'desc'    => esc_html__( 'Please choose map for trips.', 'ciwpgmp-google-maps' ),

			'options' => $maps,

			'before'  => '<div class="fc-4">',

			'after'   => '</div>',

		)

	);


	$color = ( empty( $data['ciwpgmp_settings']['route_stroke_color'] ) ) ? '8CAEF2' : sanitize_text_field( wp_unslash( $data['ciwpgmp_settings']['route_stroke_color'] ) );
	$form->add_element(
		'text', 'ciwpgmp_settings[route_stroke_color]', array(
			'label'       => esc_html__( 'Stroke Color', 'wpgmp-google-map' ),
			'value'       => $color,
			'class'       => 'color {pickerClosable:true} form-control',
			'id'          => 'route_stroke_color',
			'desc'        => esc_html__( 'Choose route direction stroke color.(Default is Blue)', 'wpgmp-google-map' ),
			'placeholder' => esc_html__( 'Route Stroke Color', 'wpgmp-google-map' ),
		)
	);


	$stroke_opacity = array(
		'1'   => '1',
		'0.9' => '0.9',
		'0.8' => '0.8',
		'0.7' => '0.7',
		'0.6' => '0.6',
		'0.5' => '0.5',
		'0.4' => '0.4',
		'0.3' => '0.3',
		'0.2' => '0.2',
		'0.1' => '0.1',
	);
	$form->add_element(
		'select', 'ciwpgmp_settings[route_stroke_opacity]', array(
			'label'   => esc_html__( 'Stroke Opacity', 'wpgmp-google-map' ),
			'current' => ( isset( $data['ciwpgmp_settings']['route_stroke_opacity'] ) and ! empty( $data['ciwpgmp_settings']['route_stroke_opacity'] ) ) ? sanitize_text_field( wp_unslash( $data['ciwpgmp_settings']['route_stroke_opacity'] ) ) : '',
			'desc'    => esc_html__( 'Please select route direction stroke opacity.', 'wpgmp-google-map' ),
			'options' => $stroke_opacity,
			'class'   => 'form-control-select',
		)
	);

	$stroke_weight = array();
	for ( $sw = 10; $sw >= 1; $sw-- ) {
		$stroke_weight[ $sw ] = $sw;
	}
	$form->add_element(
		'select', 'ciwpgmp_settings[route_stroke_weight]', array(
			'label'   => esc_html__( 'Stroke Weight', 'wpgmp-google-map' ),
			'current' => ( isset( $data['ciwpgmp_settings']['route_stroke_weight'] ) and ! empty( $data['ciwpgmp_settings']['route_stroke_weight'] ) ) ? sanitize_text_field( wp_unslash( $data['ciwpgmp_settings']['route_stroke_weight'] ) ) : '',
			'desc'    => esc_html__( 'Please select route stroke weight.', 'wpgmp-google-map' ),
			'options' => $stroke_weight,
			'class'   => 'form-control-select',
		)
	);

	$route_travel_mode = array(
		'DRIVING'   => 'DRIVING',
		'WALKING'   => 'WALKING',
		'BICYCLING' => 'BICYCLING',
		'TRANSIT'   => 'TRANSIT',
	);
	$form->add_element(
		'select', 'ciwpgmp_settings[route_travel_mode]', array(
			'label'   => esc_html__( 'Travel Modes', 'wpgmp-google-map' ),
			'current' => ( isset( $data['ciwpgmp_settings']['route_travel_mode'] ) and ! empty( $data['ciwpgmp_settings']['route_travel_mode'] ) ) ? sanitize_text_field( wp_unslash( $data['ciwpgmp_settings']['route_travel_mode'] ) ) : '',
			'desc'    => esc_html__( 'Please select travel mode.', 'wpgmp-google-map' ),
			'options' => $route_travel_mode,
			'class'   => 'form-control-select',
		)
	);

	$form->add_element(
		'select', 'ciwpgmp_settings[route_unit_system]', array(
			'label'   => esc_html__( 'Unit Systems', 'wpgmp-google-map' ),
			'current' => ( isset( $data['ciwpgmp_settings']['route_unit_system'] ) and ! empty( $data['ciwpgmp_settings']['route_unit_system'] ) ) ? sanitize_text_field( wp_unslash( $data['ciwpgmp_settings']['route_unit_system'] ) ) : '',
			'desc'    => esc_html__( 'Please select unit system.', 'wpgmp-google-map' ),
			'options' => array(
				'METRIC'   => 'METRIC',
				'IMPERIAL' => 'IMPERIAL',
			),
			'class'   => 'form-control-select',
		)
	);





	$form->add_element('submit','ciwpgmp_save_settings',array(

		'value' => esc_html__( 'Save Setting','ciwpgmp-google-maps' ),

	));



	$form->add_element('hidden','operation',array(

		'value' => 'save_settings',

	));

	



	$form->render();