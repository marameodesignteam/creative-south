<?php
/**
 * Display Tabs over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_tabs_setting', array(
		'value'  => esc_html__( 'Tabs Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[display_marker_category]', array(
		'label'   => esc_html__( 'Display Tabs', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_display_marker_category',
		'current' => isset( $data['map_all_control']['display_marker_category'] ) ? $data['map_all_control']['display_marker_category'] : '',
		'desc'    => esc_html__( 'Display various tabs on the map.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.map_tabs_setting' ),
	)
);

$form->add_element(
	'checkbox', 'map_all_control[hide_tabs_default]', array(
		'label'   => esc_html__( 'Hide Tabs on Load', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_hide_tabs_default',
		'current' => isset( $data['map_all_control']['hide_tabs_default'] ) ? $data['map_all_control']['hide_tabs_default'] : '',
		'desc'    => esc_html__( 'Hide tabs by default.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_category_tab]', array(
		'label'   => esc_html__( 'Display Categories Tab', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_category_tab',
		'current' => isset( $data['map_all_control']['wpgmp_category_tab'] ) ? $data['map_all_control']['wpgmp_category_tab'] : '',
		'desc'    => esc_html__( 'Display Categories/Locations Tab.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class map_tabs_setting switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpgmp_category_tab_setting' ),

	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_category_tab_title]', array(
		'label'         => esc_html__( 'Category Tab Title', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_all_control']['wpgmp_category_tab_title'] ) ? $data['map_all_control']['wpgmp_category_tab_title'] : '',
		'id'            => 'wpgmp_category_tab_title',
		'desc'          => esc_html__( 'Title of the category tab.', 'wpgmp-google-map' ),
		'class'         => 'form-control wpgmp_category_tab_setting',
		'show'          => 'false',
		'default_value' => esc_html__( 'Categories', 'wpgmp-google-map' ),
	)
);

$form->add_element(
	'select', 'map_all_control[wpgmp_category_order]', array(
		'label'   => esc_html__( 'Sort Category By', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['wpgmp_category_order'] ) ? $data['map_all_control']['wpgmp_category_order'] : '',
		'desc'    => esc_html__( 'Select Sort Criteria For Categories Tab.', 'wpgmp-google-map' ),
		'options' => array(
			'title'    => esc_html__( 'Title', 'wpgmp-google-map' ),
			'count'    => esc_html__( 'Location Count.', 'wpgmp-google-map' ),
			'category' => esc_html__( 'Category Order', 'wpgmp-google-map' ),
		),
		'class'   => 'form-control wpgmp_category_tab_setting',
		'show'    => 'false',
		'before'  => '<div class="fc-4">',
		'after'   => '</div>',
	)
);

$form->add_element(
	'select', 'map_all_control[wpgmp_category_location_sort_order]', array(
		'label'   => esc_html__( 'Sort Order Of Locations', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['wpgmp_category_location_sort_order'] ) ? $data['map_all_control']['wpgmp_category_location_sort_order'] : '',
		'desc'    => esc_html__( 'Specify Sort Order For Locations/Places Under Categories In Tab.', 'wpgmp-google-map' ),
		'options' => array(
			'asc'  => esc_html__( 'Ascending', 'wpgmp-google-map' ),
			'desc' => esc_html__( 'Descending', 'wpgmp-google-map' ),
		),
		'class'   => 'form-control wpgmp_category_tab_setting',
		'show'    => 'false',
		'before'  => '<div class="fc-4">',
		'after'   => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_category_tab_show_count]', array(
		'label'   => esc_html__( 'Show Location Count', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_category_tab_show_count',
		'current' => isset( $data['map_all_control']['wpgmp_category_tab_show_count'] ) ? $data['map_all_control']['wpgmp_category_tab_show_count'] : '',
		'desc'    => esc_html__( 'Display location count next to category name.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_category_tab_setting',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_category_tab_hide_location]', array(
		'label'   => esc_html__( 'Hide Locations', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_category_tab_hide_location',
		'current' => isset( $data['map_all_control']['wpgmp_category_tab_hide_location'] ) ? $data['map_all_control']['wpgmp_category_tab_hide_location'] : '',
		'desc'    => esc_html__( 'Hide locations below category selection.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_category_tab_setting',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_category_tab_show_all]', array(
		'label'   => esc_html__( 'Select All', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_category_tab_show_all',
		'current' => isset( $data['map_all_control']['wpgmp_category_tab_show_all'] ) ? $data['map_all_control']['wpgmp_category_tab_show_all'] : '',
		'desc'    => esc_html__( 'Display select all checkbox to select all categories at once.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_category_tab_setting',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_direction_tab]', array(
		'label'   => esc_html__( 'Display Directions Tab', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_direction_tab',
		'current' => isset( $data['map_all_control']['wpgmp_direction_tab'] ) ? $data['map_all_control']['wpgmp_direction_tab'] : '',
		'desc'    => esc_html__( 'Display Direction Tab.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff map_tabs_setting',
		'data'    => array( 'target' => '.wpgmp_direction_tab' ),
		'show'    => 'false',
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_direction_tab_title]', array(
		'label'         => esc_html__( 'Direction Tab Title', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_all_control']['wpgmp_direction_tab_title'] ) ? $data['map_all_control']['wpgmp_direction_tab_title'] : '',
		'id'            => 'wpgmp_direction_tab_title',
		'desc'          => esc_html__( 'Title of the route tab.', 'wpgmp-google-map' ),
		'class'         => 'form-control wpgmp_direction_tab',
		'show'          => 'false',
		'default_value' => esc_html__( 'Directions', 'wpgmp-google-map' ),
	)
);

$form->add_element(
	'select', 'map_all_control[wpgmp_unit_selected]', array(
		'label'         => esc_html__( 'Select Unit', 'wpgmp-google-map' ),
		'options'       => array(
			'km'    => esc_html__( 'KM', 'wpgmp-google-map' ),
			'miles' => esc_html__( 'miles', 'wpgmp-google-map' ),
		),
		'current'       => isset( $data['map_all_control']['wpgmp_unit_selected'] ) ? $data['map_all_control']['wpgmp_unit_selected'] : '',
		'class'         => 'chkbox_class wpgmp_direction_tab',
		'show'          => 'false',
		'default_value' => 'km',
	)
);
$form->add_element(
	'radio', 'map_all_control[wpgmp_direction_tab_start]', array(
		'label'           => esc_html__( 'Start Location', 'wpgmp-google-map' ),
		'radio-val-label' => array(
			'textbox'   => esc_html__( 'Auto Search Textbox', 'wpgmp-google-map' ),
			'selectbox' => esc_html__( 'Location Dropdown', 'wpgmp-google-map' ),
		),
		'current'         => isset( $data['map_all_control']['wpgmp_direction_tab_start'] ) ? $data['map_all_control']['wpgmp_direction_tab_start'] : '',
		'class'           => 'chkbox_class wpgmp_direction_tab',
		'show'            => 'false',
		'default_value'   => 'textbox',
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_direction_tab_start_default]', array(
		'label' => esc_html__( 'Default Start Location', 'wpgmp-google-map' ),
		'value' => isset( $data['map_all_control']['wpgmp_direction_tab_start_default'] ) ? $data['map_all_control']['wpgmp_direction_tab_start_default'] : '',
		'id'    => 'wpgmp_direction_tab_start_default',
		'desc'  => esc_html__( 'Set default start location.', 'wpgmp-google-map' ),
		'class' => 'form-control wpgmp_direction_tab wpgmp_auto_suggest',
		'show'  => 'false',
	)
);

$form->add_element(
	'radio', 'map_all_control[wpgmp_direction_tab_end]', array(
		'label'           => esc_html__( 'End Location', 'wpgmp-google-map' ),
		'radio-val-label' => array(
			'textbox'   => esc_html__( 'Auto Search Textbox', 'wpgmp-google-map' ),
			'selectbox' => esc_html__( 'Location Dropdown', 'wpgmp-google-map' ),
		),
		'current'         => isset( $data['map_all_control']['wpgmp_direction_tab_end'] ) ? $data['map_all_control']['wpgmp_direction_tab_end'] : '',
		'class'           => 'chkbox_class wpgmp_direction_tab',
		'show'            => 'false',
		'default_value'   => 'textbox',
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_direction_tab_end_default]', array(
		'label' => esc_html__( 'Default End Location', 'wpgmp-google-map' ),
		'value' => isset( $data['map_all_control']['wpgmp_direction_tab_end_default'] ) ? $data['map_all_control']['wpgmp_direction_tab_end_default'] : '',
		'id'    => 'wpgmp_direction_tab_end_default',
		'desc'  => esc_html__( 'Set default end location.', 'wpgmp-google-map' ),
		'class' => 'form-control wpgmp_direction_tab wpgmp_auto_suggest',
		'show'  => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_direction_tab_suppress_markers]', array(
		'label'   => esc_html__( 'Suppress Markers', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_direction_tab_suppress_markers',
		'current' => isset( $data['map_all_control']['wpgmp_direction_tab_suppress_markers'] ) ? $data['map_all_control']['wpgmp_direction_tab_suppress_markers'] : '',
		'desc'    => esc_html__( 'Check the suppressMarkers property to hide directions markers', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_direction_tab',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_nearby_tab]', array(
		'label'   => esc_html__( 'Display Nearby Tab', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_nearby_tab',
		'current' => isset( $data['map_all_control']['wpgmp_nearby_tab'] ) ? $data['map_all_control']['wpgmp_nearby_tab'] : '',
		'desc'    => esc_html__( 'Display nearby tab.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff map_tabs_setting',
		'show'    => 'false',
		'data'    => array( 'target' => '.nearby_tabs_setting' ),
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_nearby_tab_title]', array(
		'label'         => esc_html__( 'Nearby Tab Title', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_all_control']['wpgmp_nearby_tab_title'] ) ? $data['map_all_control']['wpgmp_nearby_tab_title'] : '',
		'id'            => 'wpgmp_nearby_tab_title',
		'desc'          => esc_html__( 'Title of the nearby tab.', 'wpgmp-google-map' ),
		'class'         => 'form-control nearby_tabs_setting',
		'show'          => 'false',
		'default_value' => esc_html__( 'Nearby Places', 'wpgmp-google-map' ),
	)
);

$form->add_element(
	'message', 'amenities_instruction', array(
		'value' => esc_html__( 'You can select amenities to display in nearby tab to be searchable in the below list.', 'wpgmp-google-map' ),
		'class' => 'alert alert-success nearby_tabs_setting',
		'show'  => 'false',
	)
);

$amenities_options = array(
	'accounting',
	'airport',
	'amusement_park',
	'aquarium',
	'art_gallery',
	'atm',
	'bakery',
	'bank',
	'bar',
	'beauty_salon',
	'bicycle_store',
	'book_store',
	'bowling_alley',
	'bus_station',
	'cafe',
	'campground',
	'car_dealer',
	'car_rental',
	'car_repair',
	'car_wash',
	'casino',
	'cemetery',
	'church',
	'city_hall',
	'clothing_store',
	'convenience_store',
	'courthouse',
	'dentist',
	'department_store',
	'doctor',
	'electrician',
	'electronics_store',
	'embassy',
	'establishment',
	'finance',
	'fire_station',
	'florist',
	'food',
	'funeral_home',
	'furniture_store',
	'gas_station',
	'general_contractor',
	'grocery_or_supermarket',
	'gym',
	'hair_care',
	'hardware_store',
	'health',
	'hindu_temple',
	'home_goods_store',
	'hospital',
	'insurance_agency',
	'jewelry_store',
	'laundry',
	'lawyer',
	'library',
	'liquor_store',
	'local_government_office',
	'locksmith',
	'lodging',
	'meal_delivery',
	'meal_takeaway',
	'mosque',
	'movie_rental',
	'movie_theater',
	'moving_company',
	'museum',
	'night_club',
	'painter',
	'park',
	'parking',
	'pet_store',
	'pharmacy',
	'physiotherapist',
	'place_of_worship',
	'plumber',
	'police',
	'post_office',
	'real_estate_agency',
	'restaurant',
	'roofing_contractor',
	'rv_park',
	'school',
	'shoe_store',
	'shopping_mall',
	'spa',
	'stadium',
	'storage',
	'store',
	'subway_station',
	'synagogue',
	'taxi_stand',
	'train_station',
	'travel_agency',
	'university',
	'veterinary_care',
	'zoo',
);
$amenities         = array();
if ( ! empty( $amenities_options ) ) {
	$count  = 0;
	$column = 1;
	foreach ( $amenities_options as $place_type => $amenity ) {

		$amenities[ $count ][] = $form->field_checkbox(
			'map_all_control[wpgmp_nearby_amenities][' . $amenity . ']', array(
				'desc'    => str_replace( '_', ' ', $amenity ),
				'value'   => $amenity,
				'current' => isset( $data['map_all_control']['wpgmp_nearby_amenities'][ $amenity ] ) ? $data['map_all_control']['wpgmp_nearby_amenities'][ $amenity ] : '',
				'before'  => '<div class="fc-1">',
				'after'   => '</div>',
				'class'   => 'chkbox_class',
			)
		);
		if ( 0 == $column % 7 ) {
			$count++; }

		$column++;
	}
}
$form->add_element(
	'table', 'wpgmp_amenities_table', array(
		'heading' => array( '', '', '', '', '', '', '', '' ),
		'data'    => $amenities,
		'before'  => '<div class="fc-11">',
		'after'   => '</div>',
		'class'   => ' nearby_tabs_setting',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[show_nearby_circle]', array(
		'label'   => esc_html__( 'Display Circle around amenities', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'show_nearby_circle',
		'current' => isset( $data['map_all_control']['show_nearby_circle'] ) ? $data['map_all_control']['show_nearby_circle'] : '',
		'desc'    => esc_html__( 'Display a circle around the nearby locations.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff nearby_tabs_setting',
		'show'    => 'false',
		'data'    => array( 'target' => '.nearby_circle_settings' ),
	)
);
$form->set_col( 5 );
$color = ( empty( $data['map_all_control']['nearby_circle_fillcolor'] ) ) ? '8CAEF2' : sanitize_text_field( wp_unslash( $data['map_all_control']['nearby_circle_fillcolor'] ) );
$form->add_element(
	'text', 'map_all_control[nearby_circle_fillcolor]', array(
		'value'  => $color,
		'class'  => 'color {pickerClosable:true} form-control nearby_circle_settings',
		'id'     => 'nearby_circle_fillcolor',
		'desc'   => esc_html__( 'Circle fill color.', 'wpgmp-google-map' ),
		'show'   => 'false',
		'before' => '<div class="fc-2">',
		'after'  => '</div>',
	)
);
$form->add_element(
	'text', 'map_all_control[nearby_circle_fillopacity]', array(
		'value'         => isset( $data['map_all_control']['nearby_circle_fillopacity'] ) ? $data['map_all_control']['nearby_circle_fillopacity'] : '',
		'class'         => 'form-control nearby_circle_settings',
		'id'            => 'nearby_circle_fillopacity',
		'desc'          => esc_html__( 'Circle fill opacity.', 'wpgmp-google-map' ),
		'show'          => 'false',
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '.5',
	)
);
$color = ( empty( $data['map_all_control']['nearby_circle_strokecolor'] ) ) ? '8CAEF2' : sanitize_text_field( wp_unslash( $data['map_all_control']['nearby_circle_strokecolor'] ) );
$form->add_element(
	'text', 'map_all_control[nearby_circle_strokecolor]', array(
		'value'  => $color,
		'class'  => 'color {pickerClosable:true} form-control nearby_circle_settings',
		'id'     => 'nearby_circle_strokecolor',
		'desc'   => esc_html__( 'Circle stroke color.', 'wpgmp-google-map' ),
		'show'   => 'false',
		'before' => '<div class="fc-2">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'map_all_control[nearby_circle_strokeopacity]', array(
		'value'         => isset( $data['map_all_control']['nearby_circle_strokeopacity'] ) ? $data['map_all_control']['nearby_circle_strokeopacity'] : '',
		'class'         => 'form-control nearby_circle_settings',
		'id'            => 'nearby_circle_strokeopacity',
		'desc'          => esc_html__( 'Circle stroke opacity.', 'wpgmp-google-map' ),
		'show'          => 'false',
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '.5',
	)
);

$form->add_element(
	'text', 'map_all_control[nearby_circle_strokeweight]', array(
		'value'         => isset( $data['map_all_control']['nearby_circle_strokeweight'] ) ? $data['map_all_control']['nearby_circle_strokeweight'] : '',
		'class'         => 'form-control nearby_circle_settings',
		'id'            => 'nearby_circle_strokeweight',
		'desc'          => esc_html__( 'Circle stroke weight.', 'wpgmp-google-map' ),
		'show'          => 'false',
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '1',
	)
);
$form->set_col( 1 );
$zoom_level = array();
for ( $i = 1; $i < 20; $i++ ) {
	$zoom_level[ $i ] = $i;
}
$form->add_element(
	'select', 'map_all_control[nearby_circle_zoom]', array(
		'label'         => esc_html__( 'Circle Zoom Level', 'wpgmp-google-map' ),
		'current'       => isset( $data['map_all_control']['nearby_circle_zoom'] ) ? $data['map_all_control']['nearby_circle_zoom'] : '',
		'desc'          => esc_html__( 'Available options 1 to 19.', 'wpgmp-google-map' ),
		'class'         => 'form-control  nearby_circle_settings',
		'options'       => $zoom_level,
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '8',
		'show'          => 'false',
	)
);


$form->add_element(
	'checkbox', 'map_all_control[wpgmp_route_tab]', array(
		'label'   => esc_html__( 'Display Route Tab', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_route_tab',
		'current' => isset( $data['map_all_control']['wpgmp_route_tab'] ) ? $data['map_all_control']['wpgmp_route_tab'] : '',
		'desc'    => esc_html__( 'Display route tab.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class map_tabs_setting switch_onoff',
		'data'    => array( 'target' => '.wpgmp_route_tab_setting' ),
		'show'    => 'false',
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_route_tab_title]', array(
		'label'         => esc_html__( 'Route Tab Title', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_all_control']['wpgmp_route_tab_title'] ) ? $data['map_all_control']['wpgmp_route_tab_title'] : '',
		'id'            => 'wpgmp_route_tab_title',
		'desc'          => esc_html__( 'Title of the route tab.', 'wpgmp-google-map' ),
		'class'         => 'form-control wpgmp_route_tab_setting',
		'show'          => 'false',
		'default_value' => esc_html__( 'Routes', 'wpgmp-google-map' ),
	)
);

$form->set_col( 1 );
