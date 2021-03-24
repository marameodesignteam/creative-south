<?php
/**
 * Class: WPGMP_Model_Group_Map
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Group_Map' ) ) {

	/**
	 * Category model for CRUD operation.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Group_Map extends FlipperCode_Model_Base {
		/**
		 * Validations on category properies.
		 *
		 * @var array
		 */
		protected $validations;
		/**
		 * Intialize location object.
		 */
		function __construct() {

			$this->validations = array(
				'group_map_title' => array( 'req' => esc_html__( 'Please enter category title.', 'wpgmp-google-map' ) ),
				'group_marker'      => array( 'req' => esc_html__( 'Please upload marker image.', 'wpgmp-google-map' ) ),
			);

			$this->table  = TBL_GROUPMAP;
			$this->unique = 'group_map_id';
		}

		/**
		 * Admin menu for CRUD Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_form_group_map'   => esc_html__( 'Add Marker Category', 'wpgmp-google-map' ),
				'wpgmp_manage_group_map' => esc_html__( 'Marker Categories', 'wpgmp-google-map' ),
			);

		}
		/**
		 * Install table associated with Location entity.
		 *
		 * @return string SQL query to install map_locations table.
		 */
		function install() {
			global $wpdb;
			$group_map = 'CREATE TABLE ' . $wpdb->prefix . 'group_map (
			group_map_id int(11) NOT NULL AUTO_INCREMENT,
			group_map_title varchar(255) DEFAULT NULL,
			group_marker text DEFAULT NULL,
			extensions_fields text DEFAULT NULL,
			group_parent int(11) DEFAULT 0,
			group_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (group_map_id)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;';
			return $group_map;
		}
		/**
		 * Get Categories
		 *
		 * @param  array $where  Conditional statement.
		 * @return array         Array of Category object(s).
		 */
		public function fetch( $where = array() ) {

			$objects = $this->get( $this->table, $where );
			foreach ( $objects as $object ) {
				if ( strstr( $object->group_marker, 'wp-google-map-pro/icons/' ) !== false ) {
					$object->group_marker = str_replace( 'icons', 'assets/images/icons', $object->group_marker ); }
					$object->extensions_fields = unserialize( $object->extensions_fields );

			}
			if ( isset( $objects ) ) {
				return $objects;
			}
		}

		/**
		 * Add or Edit Operation.
		 */
		function save() {
			global $_POST;
			$data     = array();
			$entityID = '';
			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

				die( 'Cheating...' );

			}

			$this->verify( $_POST );

			if ( isset( $_POST['entityID'] ) ) {
				$entityID = intval( wp_unslash( $_POST['entityID'] ) );
			}
			
			$this->errors = apply_filters('wpgmp_category_validation',$this->errors,$_POST);
				
			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}

			$data['group_map_title']   = sanitize_text_field( wp_unslash( $_POST['group_map_title'] ) );
			$data['group_parent']      = intval( wp_unslash( $_POST['group_parent'] ) );
			$data['extensions_fields'] = serialize( wp_unslash( $_POST['extensions_fields'] ) );
			$data['group_marker']      = wp_unslash( $_POST['group_marker'] );

			if ( $entityID > 0 ) {
				$where[ $this->unique ] = $entityID;
			} else {
				$where = '';
			}

			$data = apply_filters('fc_save_categories_data',$data,$where);

			$result = FlipperCode_Database::insert_or_update( $this->table, $data, $where );
			if ( false === $result ) {
				$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wpgmp-google-map' );
			} elseif ( $entityID > 0 ) {
				$response['success'] = esc_html__( 'Category updated successfully', 'wpgmp-google-map' );
			} else {
				$response['success'] = esc_html__( 'Category added successfully.', 'wpgmp-google-map' );
			}
			$response['last_db_id'] = $result;
			
			return $response;
		}
		/**
		 * Delete location object by id.
		 */
		function delete() {
			if ( isset( $_GET['group_map_id'] ) ) {
				$id          = intval( wp_unslash( $_GET['group_map_id'] ) );
				$connection  = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}

	}
}
