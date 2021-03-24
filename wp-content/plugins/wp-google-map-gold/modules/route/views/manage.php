<?php
/**
 * Manage Route(s)
 *
 * @package Maps
 */
  $form = new WPGMP_Template();
  $form->show_header();
if ( class_exists( 'FlipperCode_List_Table_Helper' ) and ! class_exists( 'Wpgmp_Route_Table' ) ) {

	/**
	 * Display route(s) manager.
	 */
	class Wpgmp_Route_Table extends FlipperCode_List_Table_Helper {

		/**
		 * Intialize manage category table.
		 *
		 * @param array $tableinfo Table's properties.
		 */
		public function __construct( $tableinfo ) {
			parent::__construct( $tableinfo );
		}
		/**
		 * Output for Start Location column.
		 *
		 * @param array $item Route Row.
		 */
		public function column_route_start_location( $item ) {
			$modelFactory = new WPGMP_Model();
			$location_obj = $modelFactory->create_object( 'location' );
			$location     = $location_obj->fetch( array( array( 'location_id', '=', intval( wp_unslash( $item->route_start_location ) ) ) ) );
			if ( isset( $location[0]->location_title ) ) {
				echo esc_html( $location[0]->location_title );
			}
		}
		/**
		 * Output for End Location column.
		 *
		 * @param array $item Route Row.
		 */
		public function column_route_end_location( $item ) {
			$modelFactory = new WPGMP_Model();
			$location_obj = $modelFactory->create_object( 'location' );
			$location     = $location_obj->fetch( array( array( 'location_id', '=', intval( wp_unslash( $item->route_end_location ) ) ) ) );

			if ( isset( $location[0]->location_title ) ) {
				echo esc_html( $location[0]->location_title );
			}
		}
	}
	global $wpdb;
	$columns = array(
		'route_title'          => esc_html__( 'Title', 'wpgmp-google-map' ),
		'route_start_location' => esc_html__( 'Start Location', 'wpgmp-google-map' ),
		'route_end_location'   => esc_html__( 'End Location', 'wpgmp-google-map' ),

	);
	$sortable  = array( 'route_title', 'route_start_location', 'route_end_location' );
	$tableinfo = array(
		'table'                   => $wpdb->prefix . 'map_routes',
		'textdomain'              => 'wpgmp-google-map',
		'singular_label'          => esc_html__( 'route', 'wpgmp-google-map' ),
		'plural_label'            => esc_html__( 'routes', 'wpgmp-google-map' ),
		'admin_listing_page_name' => 'wpgmp_manage_route',
		'admin_add_page_name'     => 'wpgmp_form_route',
		'primary_col'             => 'route_id',
		'columns'                 => $columns,
		'sortable'                => $sortable,
		'per_page'                => 20,
		'actions'                 => array( 'edit', 'delete' ),
		'col_showing_links'       => 'route_title',
		'bulk_actions'            => array( 'delete' => esc_html__( 'Delete', 'wpgmp-google-map' ) ),
		'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Routes', 'wpgmp-google-map' ),
			'add_button'          => esc_html__( 'Add Route', 'wpgmp-google-map' ),
			'delete_msg'          => esc_html__( 'Route(s) deleted successfully', 'wpgmp-google-map' ),
			'insert_msg'          => esc_html__( 'Route added successfully', 'wpgmp-google-map' ),
			'update_msg'          => esc_html__( 'Route updated successfully', 'wpgmp-google-map' ),
		),
	);
	$obj       = new Wpgmp_Route_Table( $tableinfo );

}

