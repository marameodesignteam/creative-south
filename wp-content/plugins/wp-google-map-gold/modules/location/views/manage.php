<?php
  global $wpdb;
  $objects       = $wpdb->get_results( 'select location_id, location_address,location_country,location_postal_code,location_state from ' . TBL_LOCATION . " where location_latitude IS NULL OR location_latitude = '' or location_longitude IS NULL OR location_longitude = '' " );
  $geo_locations = array();

  $geocode_limit = apply_filters( 'wpgmp_geocode_limit', 1000 );

  $objects_1000 = array_slice( $objects, 0, $geocode_limit );

if ( is_array( $objects_1000 ) ) {
	foreach ( $objects_1000 as $object ) {
		$geo_locations[ $object->location_id ] = array(
			'address'     => strtolower( trim( $object->location_address ) ),
			'country'     => strtolower( trim( $object->location_country ) ),
			'postal_code' => strtolower( trim( $object->location_postal_code ) ),
			'state'       => strtolower( trim( $object->location_state ) ),
		);
	}
}

  $json = json_encode( $geo_locations );
  $form = new WPGMP_Template();
  $form->show_header();
if ( count( $objects ) > 0 ) {
	$modalArgs = array(
		'fc_modal_header'    => esc_html__( 'Start Geocoding Process', 'wpgmp-google-map' ),
		'fc_modal_content'   => '<div class="fc-msg fc-danger">' . esc_html__( 'Total', 'wpgmp-google-map' ) . ' ' . count( $objects ) . ' ' . esc_html__( 'locations do not have latitude & longitude', 'wpgmp-google-map' ) . '.</div><p>' . esc_html__( 'Max 1000 locations will be geocoded at a time. You can start geocoding process by clicking below link. and whole process may takes few minutes. Please do not close or refresh the window meanwhile', 'wpgmp-google-map' ) . '.</p> <p class="wpgmp_geo_adv"><input type="checkbox" name="wpgmp_geo_adv"  value="true" />&nbsp ' . esc_html__( 'Advanced Geocoding. Country, Region, Postal Code will be counted in this process.', 'wpgmp-google-map' ) . '</p> <p><input type="button" name="fc-geocoding" class="fc-btn fc-btn-green fc-geocoding" value="' . esc_html__( 'Start Geocoding', 'wpgmp-google-map' ) . '" /><div class="fcdoc-loader">
                             <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
							 <span class="sr-only">Loading...</span>
							</div> <input type="button" name="fc-geocoding-abort" class="fc-btn fc-danger fc-geocoding-abort" value="' . esc_html__( 'Abort', 'wpgmp-google-map' ) . '" /> <span class="fc-geocoding-progress"></span> <textarea class="fc-location-data-set">' . $json . '</textarea><form enctype="multipart/form-data" action="" name="wpgmp-new-loc" method="post">' . wp_nonce_field( 'wpgmp-nonce' ) . '<input type="hidden" value="update_loc" name="operation" /><textarea name="fc-location-new-set" class="fc-location-new-set"></textarea><span class="wpgmp-status"></span><input type="submit" name="fc-geocoding-updates" class="fc-btn fc-btn-green fc-geocoding-updates" value="' . esc_html__( 'Update Locations', 'wpgmp-google-map' ) . '" /></form></p>',
		'fc_modal_initiator' => '.fc-open-modal',
		'class'              => 'fc-modal fc-modal-show fc-12',
	);

	echo WPGMP_Template::field_fc_modal( 'fc_import_modal', $modalArgs );
}



if ( class_exists( 'FlipperCode_List_Table_Helper' ) and ! class_exists( 'Wpgmp_Location_Table' ) ) {

	class Wpgmp_Location_Table extends FlipperCode_List_Table_Helper {
		public function __construct( $tableinfo ) {
			parent::__construct( $tableinfo ); }  }

	// Minimal Configuration :)
	global $wpdb;
	$columns   = array(
		'location_title'     => esc_html__( 'Title', 'wpgmp-google-map' ),
		'location_address'   => esc_html__( 'Address', 'wpgmp-google-map' ),
		'location_city'      => esc_html__( 'City', 'wpgmp-google-map' ),
		'location_latitude'  => esc_html__( 'Latitude', 'wpgmp-google-map' ),
		'location_longitude' => esc_html__( 'Longitude', 'wpgmp-google-map' ),
	);
	$sortable  = array( 'location_title', 'location_address', 'location_city', 'location_latitude', 'location_longitude' );
	$tableinfo = array(
		'table'                   => $wpdb->prefix . 'map_locations',
		'textdomain'              => 'wpgmp-google-map',
		'singular_label'          => esc_html__( 'location', 'wpgmp-google-map' ),
		'plural_label'            => esc_html__( 'locations', 'wpgmp-google-map' ),
		'admin_listing_page_name' => 'wpgmp_manage_location',
		'admin_add_page_name'     => 'wpgmp_form_location',
		'primary_col'             => 'location_id',
		'columns'                 => $columns,
		'sortable'                => $sortable,
		'per_page'                => 200,
		'actions'                 => array( 'edit', 'delete' ),
		'bulk_actions'            => array(
			'delete' => esc_html__( 'Delete', 'wpgmp-google-map' ),
			'export_location_csv' => esc_html__( 'Export as CSV', 'wpgmp-google-map' ),
		),
		'col_showing_links'       => 'location_title',
		'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Locations', 'wpgmp-google-map' ),
			'add_button'          => esc_html__( 'Add Location', 'wpgmp-google-map' ),
			'delete_msg'          => esc_html__( 'Location(s) deleted successfully', 'wpgmp-google-map' ),
			'insert_msg'          => esc_html__( 'Location added successfully', 'wpgmp-google-map' ),
			'update_msg'          => esc_html__( 'Location updated successfully', 'wpgmp-google-map' ),
		),
	);
	return new Wpgmp_Location_Table( $tableinfo );

}

