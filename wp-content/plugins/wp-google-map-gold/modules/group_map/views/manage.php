<?php
/**
 * Manage Marker Categories
 *
 * @package Maps
 */

  $form = new WPGMP_Template();
  $form->show_header();

if ( class_exists( 'FlipperCode_List_Table_Helper' ) and ! class_exists( 'Wpgmp_Manage_Group_Table' ) ) {

	/**
	 * Display categories manager.
	 */
	class Wpgmp_Manage_Group_Table extends FlipperCode_List_Table_Helper {

		/**
		 * Intialize manage category table.
		 *
		 * @param array $tableinfo Table's properties.
		 */
		public function __construct( $tableinfo ) {
			parent::__construct( $tableinfo ); }
		/**
		 * Show marker image assigned to category.
		 *
		 * @param  array $item Category row.
		 * @return html       Image tag.
		 */
		public function column_group_marker( $item ) {
			if ( strstr( $item->group_marker, 'wp-google-map-pro/icons/' ) !== false ) {
				$item->group_marker = str_replace( 'icons', 'assets/images/icons', $item->group_marker );
			}
			return sprintf( '<img src="' . $item->group_marker . '" name="group_image[]" value="%s" />', $item->group_map_id );
		}
		/**
		 * Show category's parent name.
		 *
		 * @param  [type] $item Category row.
		 * @return string       Category name.
		 */
		public function column_group_parent( $item ) {

			 global $wpdb;
			 $parent = $wpdb->get_col( $wpdb->prepare( 'SELECT group_map_title FROM ' . $this->table . ' where group_map_id = %d', $item->group_parent ) );
			 $parent = ( ! empty( $parent ) ) ? ucwords( $parent[0] ) : '---';
			 return $parent;

		}

		public function column_extensions_fields( $item ) {

			 global $wpdb;
			 $order = unserialize( $item->extensions_fields );
			 return $order['cat_order'];

		}

	}
	global $wpdb;
	$columns   = array(
		'group_map_title'   => esc_html__( 'Category Title', 'wpgmp-google-map' ),
		'group_marker'      => esc_html__( 'Marker Image', 'wpgmp-google-map' ),
		'group_parent'      => esc_html__( 'Parent Category', 'wpgmp-google-map' ),
		'extensions_fields' => esc_html__( 'Priority Order', 'wpgmp-google-map' ),
		'group_added'       => esc_html__( 'Updated On', 'wpgmp-google-map' ),
	);
	$sortable  = array( 'group_map_title', 'extensions_fields' );
	$tableinfo = array(
		'table'                   => $wpdb->prefix . 'group_map',
		'textdomain'              => 'wpgmp-google-map',
		'singular_label'          => esc_html__( 'marker category', 'wpgmp-google-map' ),
		'plural_label'            => esc_html__( 'Categories', 'wpgmp-google-map' ),
		'admin_listing_page_name' => 'wpgmp_manage_group_map',
		'admin_add_page_name'     => 'wpgmp_form_group_map',
		'primary_col'             => 'group_map_id',
		'columns'                 => $columns,
		'sortable'                => $sortable,
		'per_page'                => 20,
		'col_showing_links'       => 'group_map_title',
		'searchExclude'           => array( 'group_parent' ),
		'bulk_actions'            => array( 'delete' => esc_html__( 'Delete', 'wpgmp-google-map' ) ),
		'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Categories', 'wpgmp-google-map' ),
			'add_button'          => esc_html__( 'Add Category', 'wpgmp-google-map' ),
			'delete_msg'          => esc_html__( 'Category deleted successfully', 'wpgmp-google-map' ),
			'insert_msg'          => esc_html__( 'Category added successfully', 'wpgmp-google-map' ),
			'update_msg'          => esc_html__( 'Category updated successfully', 'wpgmp-google-map' ),
		),
	);
	return new Wpgmp_Manage_Group_Table( $tableinfo );

}

