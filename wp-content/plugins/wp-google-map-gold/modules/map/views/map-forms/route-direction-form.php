<?php
/**
 * Route Direction setting for google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_route_settings', array(
		'value'  => esc_html__( 'Route Direction Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_route_direction_setting[route_direction]', array(
		'label'   => esc_html__( 'Turn On Map Route Directions', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_route_direction',
		'current' => isset( $data['map_route_direction_setting']['route_direction'] ) ? $data['map_route_direction_setting']['route_direction'] : '',
		'desc'    => esc_html__( 'Please check to enable map route directions.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '#map_route_direction_setting, #no_route_message' ),
	)
);

$routeobj       = $modelFactory->create_object( 'route' );

if ( ! isset( $data['map_route_direction_setting']['specific_routes'] ) ) {
	$data['map_route_direction_setting']['specific_routes'] = array();
}

$routes_results = $routeobj->fetch();

$all_routes = array();

if ( ! empty( $routes_results ) ) {
	for ( $i = 0; $i < count( $routes_results ); $i++ ) {
		$route_checkbox = $form->field_checkbox(
			'map_route_direction_setting[specific_routes][]', array(
				'value'   => $routes_results[ $i ]->route_id,
				'current' => ( ( in_array( $routes_results[ $i ]->route_id, $data['map_route_direction_setting']['specific_routes'] ) ) ? $routes_results[ $i ]->route_id : '' ),
				'class'   => 'chkbox_class',
				'before'  => '<div class="fc-1">',
				'after'   => '</div>',
			)
		);
		$all_routes[]   = array( $route_checkbox, $routes_results[ $i ]->route_title, $routes_results[ $i ]->route_travel_mode, $routes_results[ $i ]->route_unit_system );
	}
}
$form->add_element(
	'table', 'map_route_direction_setting[specific_routes]', array(
		'heading' => array( esc_html__( 'Select', 'wpgmp-google-map' ), esc_html__( 'Route Title', 'wpgmp-google-map' ), esc_html__( 'Travel Mode', 'wpgmp-google-map' ), esc_html__( 'Unit System', 'wpgmp-google-map' ) ),
		'data'    => $all_routes,
		'id'      => 'map_route_direction_setting',
		'before'  => '<div class="fc-12">',
		'after'   => '</div>',
		'current' => $data['map_route_direction_setting']['specific_routes'],
		'show'    => 'false',
	)
);
if ( empty( $all_routes ) ) {
	$link = sprintf(
		wp_kses(
			esc_html__( 'No route found. <a target="_blank" href="%s">Click here</a> to create a route.', 'wpgmp-google-map' ), array(
				'a' => array(
					'href'   => array(),
					'target' => '_blank',
				),
			)
		), esc_url( $url )
	);

	$link = '<a target="_blank" href="'.admin_url( 'admin.php?page=wpgmp_form_route' ).'">'.esc_html__("Click here","wpgmp-google-map").'</a>';

	$form->add_element(
		'message', 'no_route_message', array(
			'value'  => sprintf( esc_html__( 'no routes found. %1$s to create a route.', 'wpgmp-google-map' ), $link),
			'class'  => 'fc-msg false',
			'before' => '<div class="fc-12 no_route_message">',
			'after'  => '</div>',
		)
	);
}
