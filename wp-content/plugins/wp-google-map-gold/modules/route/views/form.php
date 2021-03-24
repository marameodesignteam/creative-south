<?php
/**
 * Template for Add & Edit Route
 *
 * @author  Flipper Code <hello@flippercode.com>
 * @package Maps
 */

if ( isset( $_REQUEST['_wpnonce'] ) ) {

	$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

		die( 'Cheating...' );

	} else {
		$data = $_POST;
	}
}
global $wpdb;
$form         = new WPGMP_Template();
$modelFactory = new WPGMP_Model();
$category     = $modelFactory->create_object( 'group_map' );
$location     = $modelFactory->create_object( 'location' );
$locations    = $location->fetch();
$categories   = $category->fetch();
if ( ! empty( $categories ) ) {
	$categories_data = array();
	foreach ( $categories as $cat ) {
		$categories_data[ $cat->group_map_id ] = $cat->group_map_title;
	}
}
$route = $modelFactory->create_object( 'route' );
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['route_id'] ) ) {
	$route_obj = $route->fetch( array( array( 'route_id', '=', intval( wp_unslash( $_GET['route_id'] ) ) ) ) );
	$data      = (array) $route_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $data );
}

$all_locations = array();

if ( ! empty( $locations ) ) {

	if ( ! isset( $data['route_way_points'] ) ) {
		$data['route_way_points'] = array();
	}


	foreach ( $locations as $loc ) {
		$assigned_categories = array();
		if ( isset( $loc->location_group_map ) and is_array( $loc->location_group_map ) ) {
			foreach ( $loc->location_group_map as $c => $cat ) {
				if ( isset( $categories_data[ $cat ] ) ) {
					$assigned_categories[] = $categories_data[ $cat ];
				}
			}
		}
		$assigned_categories = implode( ',', $assigned_categories );
		$loc_checkbox        = $form->field_checkbox(
			'select_route_way_points[]', array(
				'value'   => $loc->location_id,
				'current' => ( ( in_array( $loc->location_id, (array) $data['route_way_points'] ) ) ? $loc->location_id : '' ),
				'class'   => 'chkbox_class',
				'before'  => '<div class="fc-1">',
				'after'   => '</div>',
			)
		);
		$all_locations[]     = array( $loc_checkbox, $loc->location_title, $loc->location_address, $assigned_categories );
	}
}


$form->set_header( esc_html__( 'Route Information', 'wpgmp-google-map' ), $response, esc_html__( 'Manage Routes', 'wpgmp-google-map' ), 'wpgmp_manage_route' );

$form->add_element(
	'text', 'route_title', array(
		'label'       => esc_html__( 'Route Title', 'wpgmp-google-map' ),
		'value'       => ( isset( $data['route_title'] ) and ! empty( $data['route_title'] ) ) ? sanitize_text_field( wp_unslash( $data['route_title'] ) ) : '',
		'id'          => 'route_title',
		'desc'        => esc_html__( 'Please enter route title.', 'wpgmp-google-map' ),
		'placeholder' => esc_html__( 'Route Title', 'wpgmp-google-map' ),
		'required'    => true,
	)
);

$color = ( empty( $data['route_stroke_color'] ) ) ? '8CAEF2' : sanitize_text_field( wp_unslash( $data['route_stroke_color'] ) );
$form->add_element(
	'text', 'route_stroke_color', array(
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
	'select', 'route_stroke_opacity', array(
		'label'   => esc_html__( 'Stroke Opacity', 'wpgmp-google-map' ),
		'current' => ( isset( $data['route_stroke_opacity'] ) and ! empty( $data['route_stroke_opacity'] ) ) ? sanitize_text_field( wp_unslash( $data['route_stroke_opacity'] ) ) : '',
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
	'select', 'route_stroke_weight', array(
		'label'   => esc_html__( 'Stroke Weight', 'wpgmp-google-map' ),
		'current' => ( isset( $data['route_stroke_weight'] ) and ! empty( $data['route_stroke_weight'] ) ) ? sanitize_text_field( wp_unslash( $data['route_stroke_weight'] ) ) : '',
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
	'select', 'route_travel_mode', array(
		'label'   => esc_html__( 'Travel Modes', 'wpgmp-google-map' ),
		'current' => ( isset( $data['route_travel_mode'] ) and ! empty( $data['route_travel_mode'] ) ) ? sanitize_text_field( wp_unslash( $data['route_travel_mode'] ) ) : '',
		'desc'    => esc_html__( 'Please select travel mode.', 'wpgmp-google-map' ),
		'options' => $route_travel_mode,
		'class'   => 'form-control-select',
	)
);

$form->add_element(
	'select', 'route_unit_system', array(
		'label'   => esc_html__( 'Unit Systems', 'wpgmp-google-map' ),
		'current' => ( isset( $data['route_unit_system'] ) and ! empty( $data['route_unit_system'] ) ) ? sanitize_text_field( wp_unslash( $data['route_unit_system'] ) ) : '',
		'desc'    => esc_html__( 'Please select unit system.', 'wpgmp-google-map' ),
		'options' => array(
			'METRIC'   => 'METRIC',
			'IMPERIAL' => 'IMPERIAL',
		),
		'class'   => 'form-control-select',
	)
);

$current = ( empty( $data['route_marker_draggable'] ) ) ? '' : sanitize_text_field( wp_unslash( $data['route_marker_draggable'] ) );
$form->add_element(
	'checkbox', 'route_marker_draggable', array(
		'label'   => esc_html__( 'Draggable', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => $current,
		'id'      => 'route_marker_draggable',
		'desc'    => esc_html__( 'Please check to enable route draggable.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$current = ( empty( $data['route_optimize_waypoints'] ) ) ? '' : sanitize_text_field( wp_unslash( $data['route_optimize_waypoints'] ) );
$form->add_element(
	'checkbox', 'route_optimize_waypoints', array(
		'label'   => esc_html__( 'Optimize Waypoints', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => $current,
		'id'      => 'route_optimize_waypoints',
		'desc'    => esc_html__( 'Please check to enable optimize waypoints.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$res = array();
if ( ! empty( $locations ) ) {

	for ( $i = 0; $i < count( $locations ); $i++ ) {
		$res[ $locations[ $i ]->location_id ] = $locations[ $i ]->location_title;
	}
}

$form->add_element(
	'select', 'route_start_location', array(
		'label'   => esc_html__( 'Start Location', 'wpgmp-google-map' ),
		'current' => ( isset( $data['route_start_location'] ) and ! empty( $data['route_start_location'] ) ) ? sanitize_text_field( wp_unslash( $data['route_start_location'] ) ) : '',
		'desc'    => esc_html__( 'Please select start location.', 'wpgmp-google-map' ),
		'options' => $res,
	)
);

$form->add_element(
	'select', 'route_end_location', array(
		'label'   => esc_html__( 'End Location', 'wpgmp-google-map' ),
		'current' => ( isset( $data['route_end_location'] ) and ! empty( $data['route_end_location'] ) ) ? sanitize_text_field( wp_unslash( $data['route_end_location'] ) ) : '',
		'desc'    => esc_html__( 'Please select end location.', 'wpgmp-google-map' ),
		'options' => $res,
	)
);


$form->add_element(
	'group', 'location_extra_fields', array(
		'value'  => esc_html__( 'Way Point(s)', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);


$form->add_element(
	'message', 'route_notes', array(
		'value'  => esc_html__( 'You can select maximum 8 way point(s).', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'class'  => 'fc-msg fc-info',
		'after'  => '</div>',
	)
);

$form->add_element(
	'table', 'route_selected_way_points', array(
		'heading' => array( 'Select', 'Title', 'Address', 'Categories' ),
		'data'    => $all_locations,
		'id'      => 'wpgmp_google_map_data_table',
		'before'  => '<div class="fc-12">',
		'after'   => '</div>',
	)
);


$form->add_element(
	'submit', 'save_route_data', array(
		'value' => 'Save Route',
	)
);

$form->add_element(
	'hidden', 'route_way_points', array(
		'value' => '',
	)
);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);

if ( isset( $_GET['doaction'] ) and 'edit' == 'edit' and isset( $_GET['route_id'] ) ) {

	$form->add_element(
		'hidden', 'entityID', array(
			'value' => intval( wp_unslash( $_GET['route_id'] ) ),
		)
	);

}

$form->render();
