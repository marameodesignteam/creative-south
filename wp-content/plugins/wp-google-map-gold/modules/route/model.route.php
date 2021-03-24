<?php
/**
 * Class: WPGMP_Model_Route
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Route' ) ) {

	/**
	 * Route model for CRUD operation.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Route extends FlipperCode_Model_Base {
		/**
		 * Validations on route properies.
		 *
		 * @var array
		 */
		protected $validations;
		/**
		 * Intialize route object.
		 */
		function __construct() {

			$this->validations = array(
				'route_title' => array( 'req' => esc_html__( 'Please enter route title.', 'wpgmp-google-map' ) ),
			);

			$this->table  = TBL_ROUTES;
			$this->unique = 'route_id';

		}
		/**
		 * Admin menu for CRUD Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_form_route'   => esc_html__( 'Add Route', 'wpgmp-google-map' ),
				'wpgmp_manage_route' => esc_html__( 'Manage Routes', 'wpgmp-google-map' ),
			);
		}
		/**
		 * Install table associated with Route entity.
		 *
		 * @return string SQL query to install map_routes table.
		 */
		function install() {
			global $wpdb;
			$map_routes = 'CREATE TABLE ' . $wpdb->prefix . 'map_routes (
			route_id int(11) NOT NULL AUTO_INCREMENT,
			route_title varchar(255) DEFAULT NULL,
			route_stroke_color varchar(255) DEFAULT NULL,
			route_stroke_opacity varchar(255) DEFAULT NULL,
			route_stroke_weight int(11) DEFAULT NULL,
			route_travel_mode varchar(255) DEFAULT NULL,
			route_unit_system varchar(255) DEFAULT NULL,
			route_marker_draggable varchar(255) DEFAULT NULL,
			route_optimize_waypoints varchar(255) DEFAULT NULL,
			route_start_location int(11) DEFAULT NULL,
			route_end_location int(11) DEFAULT NULL,
			route_way_points text DEFAULT NULL,
			extensions_fields text DEFAULT NULL,
			PRIMARY KEY  (route_id)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;';
			return $map_routes;
		}
		/**
		 * Get Route(s)
		 *
		 * @param  array $where  Conditional statement.
		 * @return array         Array of Route object(s).
		 */
		public function fetch( $where = array() ) {

			$objects = $this->get( $this->table, $where );

			if ( isset( $objects ) ) {
				foreach ( $objects as $index => $object ) {
					$object->route_way_points  = unserialize( $object->route_way_points );
					$object->extensions_fields = unserialize( $object->extensions_fields );
				}
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
			
			$this->errors = apply_filters('wpgmp_route_validation',$this->errors,$_POST);
			
			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}
			if ( sanitize_text_field( $_POST['route_way_points'] ) != '' ) {
				$route_way_points = explode( ',', $_POST['route_way_points'] );
			} else {
				$route_way_points = array();
			}

			$data['route_way_points']          = serialize( wp_unslash( $route_way_points ) );
			$data['route_title']               = sanitize_text_field( wp_unslash( $_POST['route_title'] ) );
			$data['route_stroke_color']        = sanitize_text_field( wp_unslash( $_POST['route_stroke_color'] ) );
			$data['route_stroke_opacity']      = sanitize_text_field( wp_unslash( $_POST['route_stroke_opacity'] ) );
			$data['route_stroke_weight']       = sanitize_text_field( wp_unslash( $_POST['route_stroke_weight'] ) );
			$data['route_travel_mode']         = sanitize_text_field( wp_unslash( $_POST['route_travel_mode'] ) );
			$data['route_unit_system']         = sanitize_text_field( wp_unslash( $_POST['route_unit_system'] ) );

			if ( isset( $_POST['route_marker_draggable'] ) ) {
				$data['route_marker_draggable']    = sanitize_text_field( wp_unslash( $_POST['route_marker_draggable'] ) );
			} else {
				$data['route_marker_draggable'] = '';
			}

			if ( isset( $_POST['route_optimize_waypoints'] ) ) {
				$data['route_optimize_waypoints']  = sanitize_text_field( wp_unslash( $_POST['route_optimize_waypoints'] ) );
			} else {
				$data['route_optimize_waypoints'] = '';
			}

			if( isset( $_POST['route_start_location'] ) ) {
				$data['route_start_location']      = sanitize_text_field( wp_unslash( $_POST['route_start_location'] ) );
			} else {
				$data['route_start_location']      = '';
			}

			if( isset( $_POST['route_end_location'] ) ) {
				$data['route_end_location']      = sanitize_text_field( wp_unslash( $_POST['route_end_location'] ) );
			} else {
				$data['route_end_location']      = '';
			}


			if ( $entityID > 0 ) {
				$where[ $this->unique ] = $entityID;
			} else {
				$where = '';
			}

			$data = apply_filters('fc_save_route_data',$data,$where);
			$result = FlipperCode_Database::insert_or_update( $this->table, $data, $where );

			if ( false === $result ) {
				$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wpgmp-google-map' );
			} elseif ( $entityID > 0 ) {
				$response['success'] = esc_html__( 'Route updated successfully', 'wpgmp-google-map' );
			} else {
				$response['success'] = esc_html__( 'Route added successfully.', 'wpgmp-google-map' );
			}
			
			$response['last_db_id'] = $result;
			
			return $response;
		}

		/**
		 * Delete route object by id.
		 */
		function delete() {
			if ( isset( $_GET['route_id'] ) ) {
				$id          = intval( wp_unslash( $_GET['route_id'] ) );
				$connection  = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}

	}
}
