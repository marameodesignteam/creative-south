<?php
/**
 * Location listings for maps.
 *
 * @package Maps
 */

global $wpdb;
$modelFactory = new WPGMP_Model();
$category     = $modelFactory->create_object( 'group_map' );
$location     = $modelFactory->create_object( 'location' );
$locations    = $location->fetch();
$categories   = $category->fetch();
$categories_data = array();
if ( ! empty( $categories ) ) {
	
	foreach ( $categories as $cat ) {
		$categories_data[ $cat->group_map_id ] = $cat->group_map_title;
	}
}

if ( ! isset( $data['map_locations'] ) ) {
	$data['map_locations'] = array();
}

$all_locations = array();

if ( ! empty( $locations ) ) {

	foreach ( $locations as $loc ) {
		$assigned_categories = array();

		if ( isset( $loc->location_group_map ) and !empty( $loc->location_group_map ) ) {
			foreach ( $loc->location_group_map as $c => $cat ) {

				if ( isset($categories_data[ $cat ]) ) {
					$assigned_categories[] = $categories_data[ $cat ];
				}
			}
		}
		$assigned_categories = implode( ',', $assigned_categories );
		$loc_checkbox        = $form->field_checkbox(
			'map_locations[]', array(
				'value'   => $loc->location_id,
				'current' => ( ( in_array( $loc->location_id, (array) $data['map_locations'] ) ) ? $loc->location_id : '' ),
				'class'   => 'chkbox_class',
				'before'  => '<div class="fc-1">',
				'after'   => '</div>',
			)
		);
		$all_locations[]     = array( $loc_checkbox, $loc->location_title, $loc->location_address, $assigned_categories );
	}
}


$table_group = $form->field_html(
	'map_location_listing', array(
		'html' => '<h4>' . esc_html__( 'Choose Locations', 'wpgmp-google-map' ) . '</h4>',
	)
);

$table_group .= $form->field_select(
	'select_all', array(
		'options' => array(
			''             => esc_html__( 'Choose', 'wpgmp-google-map' ),
			'select_all'   => esc_html__( 'Select All', 'wpgmp-google-map' ),
			'deselect_all' => esc_html__( 'Deselect All', 'wpgmp-google-map' ),
		),
	)
);

$form->add_element(
	'html', 'map_location_listing_div', array(
		'html'   => $table_group,
		'before' => '<div class="fc-12 wpgmp_location_selection fc-title-blue">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'table', 'map_selected_locations', array(
		'heading' => array( 'Select', 'Title', 'Address', 'Category' ),
		'data'    => $all_locations,
		'before'  => '<div class="fc-12">',
		'after'   => '</div>',
		'id'      => 'wpgmp_google_map_data_table',
		'current' => $data['map_locations'],
	)
);
