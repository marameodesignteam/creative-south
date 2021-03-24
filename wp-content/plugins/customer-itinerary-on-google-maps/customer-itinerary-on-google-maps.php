<?php
/**
Plugin Name: Customer Itinerary On Google Maps
Plugin URI: http://www.flippercode.com/
Description:  An advanced google maps plugin extention that allows users to create holiday / travel trips on google maps and site admin can see and understand where they want to go in backend.
Author: flippercode
Author URI: http://www.flippercode.com/
Version: 1.0.0
Text Domain: customer-itinerary-on-google-maps
Domain Path: /lang/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! class_exists( 'CIWPGMP_On_GoogleMaps' ) ) {

	class CIWPGMP_On_GoogleMaps {

		private $googlemapsMissing = false;
		private $proceed = false;
		private $addonModal;
		private $pluginUrl = '';
		private $pluginDir = '';
		private $tripDeleted = false;
		private $response = array();
		private $responseMessage = array();
		private $updateTripResponse = array();
		private $displayForm = '';

		public function __construct() {

			$this->ciwpgmp_check_plugin_dependancy();

			if($this->proceed) {

				$this->ciwpgmp_define_constants();
				$this->ciwpgmp_register_hooks();

			}
		}


		function ciwpgmp_check_plugin_dependancy() {

			if ( ! function_exists( 'is_plugin_active_for_network' ) )

			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

			//GoogleMaps Dependency

			$is_google_maps_installed = in_array( 'wp-google-map-gold/wp-google-map-gold.php',get_option('active_plugins' ) ) ;

			$is_google_maps_active = ( is_plugin_active_for_network( 'wp-google-map-gold/wp-google-map-gold.php' ) ) ? true : false;

			$this->googlemapsMissing = (!$is_google_maps_installed && !$is_google_maps_active) ? true : false;

			$this->proceed = (!($this->googlemapsMissing)) ? true : false;

			if(!$this->proceed)
			add_action( 'admin_notices', array( $this, 'ciwpgmp_admin_notices' ) );	

		}

		function ciwpgmp_admin_notices() {

			if($this->googlemapsMissing)
			$this->ciwpgmp_google_maps_missing();

		}

		function ciwpgmp_google_maps_missing() { ?>

		    <div class="notice notice-error">

		    	<p><a target="_blank" href="https://codecanyon.net/item/advanced-google-maps-plugin-for-wordpress/5211638"><?php esc_html_e('WP Google Maps Pro','customer-itinerary-on-google-maps'); ?></a><?php esc_html_e( ' is required for Customer Itinerary On Google Maps. Please install and configure WP Google Maps Pro first.', 'customer-itinerary-on-google-maps' ); ?></p>

		    </div>
		   <?php

		}

		function ciwpgmp_register_hooks() {

			$this->ciwpgmp_model_class();

			add_action('init', array($this, 'ciwpgmp_register_required_post_type') );

			add_filter( 'the_content', array($this, 'ciwpgmp_add_location_map_after_content_trip_locations_post'), 1, 100 );

			add_filter( 'the_content', array($this, 'ciwpgmp_add_auto_shortcode_user_trips_listing_my_trips_post') );

			add_action('wp_enqueue_scripts', array($this, 'ciwpgmp_frontend_scripts'));

			add_filter('wpgmp_markers', array($this, 'ciwpgmp_filter_markers_view_trip'),1,2);

			add_action( 'init',array($this,'ciwpgmp_model_class'));

			add_shortcode('user_trips_list', array($this, 'ciwpgmp_user_trips_list'));

			add_filter('wpgmp_map_data', array($this, 'wpgmp_map_data' ), 10, 2);

			add_filter( 'fc_tabular_set_pagination_page',array($this,'ciwpgmp_set_pagination_location_listing'));

			add_action( 'admin_menu', array( $this, 'ciwpgmp_create_menu' ),100);

			add_action( 'init', array( $this, 'ciwpgmp_form_handling' ) );

			add_action( 'wp_footer', array( $this, 'ciwpgmp_display_from' ) );

			
			if( is_admin() ) {

				add_action('add_meta_boxes', array($this, 'ciwpgmp_add_custom_meta_box_my_trips'));

				add_action( 'admin_enqueue_scripts', array($this, 'ciwpgmp_register_admin_script'));

				add_filter( 'wpgmp_meta_boxes', array($this, 'ciwpgmp_hide_google_maps_metabox_from_my_trips_post'), 10, 1);

				add_action( 'admin_head', array($this, 'ciwpgmp_admin_styles'));
			}
			
			add_action( 'plugins_loaded', array( $this, 'ciwpgmp_load_plugin_languages' ) );
		}

		function ciwpgmp_load_plugin_languages() {

			load_plugin_textdomain( 'customer-itinerary-on-google-maps', false, plugin_dir_path( __FILE__ ).'/lang/' );
		}

		function ciwpgmp_form_handling() {

			if(isset($_GET['doaction']) && $_GET['doaction'] == 'delete') {

		        $post_id = isset($_GET['post']) ? $_GET['post'] : '';

		        $this->tripDeleted  = wp_delete_post($post_id);
			}

			if(isset( $_POST['wptp_add_location_in_trip']) ) {

		        $this->errorsMessage = $this->addonModal->add_locations_in_trip();

		        if(count($this->errorsMessage) > 0) {

		            $this->responseMessage['error'] = implode( '<br>', array_reverse($this->errorsMessage));

		            $data = $_POST;

		        }


		        if(empty($this->errorsMessage)) {

		            $this->responseMessage['success'] = esc_html__( 'Trip added successfully', 'customer-itinerary-on-google-maps' );

		            unset($_POST);
		        }

		        $this->displayForm = '#display_add_existing_trip_div';
		    }


		    if( isset( $_POST['wptp_create_trip']) ) {

		        $this->createTripValidationMsg = $this->addonModal->wptp_create_new_trip();

		        if(count($this->createTripValidationMsg) > 0) {

		                $this->response['error'] = implode( '<br>', array_reverse($this->createTripValidationMsg));

		                $data = $_POST;
		        }

		        if(empty($this->createTripValidationMsg)) {

		            $this->response['success'] = esc_html__( 'Trip Created Successfully', 'customer-itinerary-on-google-maps' );
		            unset($_POST);
		        }
		        else{

		            $data = $_POST;
		        }
		        $this->displayForm = '#display_create_new_trip_div';
		    }


		    if( isset($_POST['wptp_update_trip']) ) {

		        $this->updateTripValidationMsg = $this->addonModal->wptp_update_trip();

			    if(count($this->updateTripValidationMsg) > 0) {

			        $this->updateTripResponse['error'] = implode( '<br>', array_reverse($this->updateTripValidationMsg));

			        $data = $_POST;

			    }

			    if(empty($this->updateTripValidationMsg)) {

			        $this->updateTripResponse['success'] = esc_html__( 'Trip Updated Successfully', 'customer-itinerary-on-google-maps' );

			        unset($_POST);
			    }else{

			        $data = $_POST;
			    }
		    }

		}

		function ciwpgmp_display_from() { ?>

			<script>
				var fromId = '<?php echo $this->displayForm; ?>';
				if( jQuery(fromId).length > 0) {
					jQuery( window ).load(function() {
						jQuery(fromId).show();
					});
				}
			</script>
			<?php 
		}

		function ciwpgmp_hide_google_maps_metabox_from_my_trips_post($screens){ 

			unset($screens['my_trips']);
			return $screens;
		}

		function ciwpgmp_admin_styles() {

			$screen = get_current_screen();
			
			if($screen->post_type == 'my_trips') {

				?>
				<style> .wpgmp_map_container{ display:inline-block; } </style>
				<?php 
			}
		}

		function ciwpgmp_register_admin_script() {

			$screen = get_current_screen();

			if($screen->post_type == 'my_trips') {

				WPGMP_Helper::wpgmp_register_map_frontend_resources(); 

				wp_enqueue_script( 'ciwpgmp-backend-js', CIWPGMP_JS .'ciwpgmp_frontend.js',array ('jquery'),'',true );
			}

		}

		function ciwpgmp_create_menu() {

			global $navigations;

			$pagehook1 = add_submenu_page(

				WPGMP_SLUG,
				esc_html__( 'Customer Itinerary Settings','customer-itinerary-on-google-maps' ),
				esc_html__( 'Customer Itinerary Settings','customer-itinerary-on-google-maps' ),
				'manage_options',
				'ciwpgmp_settings',
				array( $this,'ciwpgmp_settings' )
			);
			add_action( 'load-'.$pagehook1, array( $this, 'wpuls_backend_scripts' ) );

		}

		function wpuls_backend_scripts() {


				$admin_styles = array(

				'font_awesome_minimised'   => WPGMP_CSS . 'font-awesome.min.css',
				'wpgmp-map-bootstrap'      => WPGMP_CSS . 'flippercode-ui.css',
				'wpgmp-backend-google-map' => WPGMP_CSS . 'backend.css',
			);







			if ( $admin_styles ) {



				foreach ( $admin_styles as $admin_style_key => $admin_style_value ) {



					wp_enqueue_style( $admin_style_key, $admin_style_value );



				}



			}

			wp_enqueue_style( 'thickbox' );

			wp_enqueue_style( 'wp-color-picker' );

			$wp_scripts = array( 'jQuery', 'thickbox', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-sortable' );



			if ( $wp_scripts ) {



				foreach ( $wp_scripts as $wp_script ) {



					wp_enqueue_script( $wp_script );

				}



			}



			$scripts = array();



			$scripts[] = array(



				'handle' => 'flippercode-ui',



				'src'    => WPGMP_JS . 'flippercode-ui.js',



				'deps'   => array(),



			);



			$scripts[] = array(



				'handle' => 'wpgmp-backend-google-maps',



				'src'    => WPGMP_JS . 'backend.js',



				'deps'   => array("flippercode-datatable","flippercode-webfont"),



			);



			if ( $scripts ) {



				foreach ( $scripts as $script ) {



					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], '2.3.4' );



				}



			}

		}





		function ciwpgmp_settings() {



			global $wpdb;

			$response = array();

			if ( isset( $_POST['ciwpgmp_save_settings'] ) ) {



				if ( isset( $_REQUEST['_wpnonce'] ) ) {



					$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );



					if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {



						die( 'Cheating...' );



					} else {



						$data = $_POST;



					}



				}



				$ciwpgmp_options = get_option( 'ciwpgmp_settings' );



				if ( false === $ciwpgmp_options ) {



					add_option( 'ciwpgmp_settings',serialize( wp_unslash( $_POST['ciwpgmp_settings'] ) ) );



				    $response['success'] = esc_html__( 'Settings saved.', 'customer-itinerary-on-google-maps' );



				} else {



					update_option( 'ciwpgmp_settings',serialize( wp_unslash( $_POST['ciwpgmp_settings'] ) ) );



					$response['success'] = esc_html__( 'Settings updated.', 'customer-itinerary-on-google-maps' );



				}



			}



			$admin_settings = CIWPGMP_DIR.'inc/ciwpgmp_backend_settings.php';



			return require_once($admin_settings);







			



		}



		function ciwpgmp_add_custom_meta_box_my_trips() {



			global $post;



			if($post->post_type == 'my_trips' ) {



				add_meta_box(



			       'ciwpgmp_trip_view',



			       esc_html__( 'Trip Created By Customer : Below Map Displays Locations Customer Wants To Visit', 'customer-itinerary-on-google-maps' ),



			       array( $this, 'ciwpgmp_add_meta_box' ),



			       'my_trips',



			       'normal',



			       'high'



			   );

			}

		}







		function ciwpgmp_add_meta_box() {



			$settings = maybe_unserialize( get_option( 'ciwpgmp_settings' ) );



			$map_id = '';



			if(isset($settings['ciwpgmp_map']) && !empty($settings['ciwpgmp_map']) ){



				$map_id = $settings['ciwpgmp_map'];



			}



			echo '<div class="wpgmp_metabox_container">'.do_shortcode( '[put_wpgm id='.$map_id.']').'</div>';



		}





		function ciwpgmp_get_way_points() {



			global $post;

			$way_points = array();

			$get_current_trip_locations = array();



			if(is_admin() ) {



				$screen = get_current_screen();



				if($screen->post_type == 'my_trips') {



					$post_id = $_GET['post'];



					$get_current_trip_locations = get_post_meta( $post_id, '_wptp_trip_locations', true );



				}



			}else{



				if(is_single() && $post->post_type == 'my_trips' ) {



					$get_current_trip_locations = get_post_meta( $post->ID, '_wptp_trip_locations', true );



				}



			}



			if(is_array($get_current_trip_locations) && !empty($get_current_trip_locations) ) {



				foreach($get_current_trip_locations as $key => $place){	



					$lat = get_post_meta( $place, '_wpgmp_metabox_latitude', true );



					$lng = get_post_meta( $place, '_wpgmp_metabox_longitude', true );



					$way_points[] = $lat.','.$lng;

				}



			}



			return $way_points;

		}



		function wpgmp_map_data($map_data, $map) {



			$data = maybe_unserialize( get_option( 'ciwpgmp_settings' ) );



			$way_points = $this->ciwpgmp_get_way_points();



			if( is_array($way_points) && count($way_points) > 0) {



				$routes = array();



				$route_data = array();



				$count = 0;



				$routes = 0;



				if(count($way_points) <= 10 ) {



					$remaining_way_points = array_slice($way_points, 1, -1);



					$route_data[] = array(



						'route_id' => 1,



						'route_title' => 'Trip1',



						'route_stroke_color' => isset($data['route_stroke_color']) ? $data['route_stroke_color'] : '#8CAEF2',



						'route_stroke_opacity' => isset($data['route_stroke_opacity']) ? $data['route_stroke_opacity'] : '1',



						'route_stroke_weight' => isset($data['route_stroke_weight']) ? $data['route_stroke_weight'] : 10,



						'route_travel_mode' => isset($data['route_travel_mode']) ? $data['route_travel_mode'] : 'DRIVING',



						'route_unit_system' => isset($data['route_unit_system']) ? $data['route_unit_system'] : 'METRIC',



						'route_marker_draggable' => '',



						'route_optimize_waypoints' => true,



						'way_points' => $remaining_way_points,



						'start_location_data' => $way_points[0],



						'end_location_data' => end($way_points)



					);



				}else {



					$x = count($way_points);



					$y = 10;



					$mod = ($x/100*$y);



					$path = explode('.', $mod);



					if(empty($path[1])) {



						$path[1] = 0;



					}



					if($path[1] > 0) {







						for($i = 0; $i < $path[0]+1; $i++) {



							$route_data[$i] = array(



								'route_id' => 1,



								'route_title' => 'Trip'.$i,



								'route_stroke_color' => isset($data['route_stroke_color']) ? $data['route_stroke_color'] : '#8CAEF2',



								'route_stroke_opacity' => isset($data['route_stroke_opacity']) ? $data['route_stroke_opacity'] : '1',



								'route_stroke_weight' => isset($data['route_stroke_weight']) ? $data['route_stroke_weight'] : 10,



								'route_travel_mode' => isset($data['route_travel_mode']) ? $data['route_travel_mode'] : 'DRIVING',



								'route_unit_system' => isset($data['route_unit_system']) ? $data['route_unit_system'] : 'METRIC',



								'route_marker_draggable' => '',



								'route_optimize_waypoints' => true,



								'way_points' => array(),



								'start_location_data' => '',



								'end_location_data' => '',



							);



						}



					}else{



						for($i = 0; $i < $path[0]; $i++) {



							$route_data[$i] = array(



								'route_id' => 1,



								'route_title' => 'Trip'.$i,



								'route_stroke_color' => isset($data['route_stroke_color']) ? $data['route_stroke_color'] : '#8CAEF2',



								'route_stroke_opacity' => isset($data['route_stroke_opacity']) ? $data['route_stroke_opacity'] : '1',



								'route_stroke_weight' => isset($data['route_stroke_weight']) ? $data['route_stroke_weight'] : 10,



								'route_travel_mode' => isset($data['route_travel_mode']) ? $data['route_travel_mode'] : 'DRIVING',



								'route_unit_system' => isset($data['route_unit_system']) ? $data['route_unit_system'] : 'METRIC',



								'route_marker_draggable' => '',



								'route_optimize_waypoints' => true,



								'way_points' => array(),



								'start_location_data' => '',



								'end_location_data' => '',



							);



						}



					}







					$count = 0;



					$i = 0;



					$only_one = false;



					while( $i <= count($way_points)) {







						$start_index = $count * 10;







						$current_route_waypt = array();







						for($j=1;$j<=8;$j++){







							if(!empty($way_points[$start_index + $j+1])) {



								$only_one = true;



								$current_route_waypt[] = $way_points[$start_index + $j];



							}else{



								break;



							}



						}







						$end_index = (10 * $count ) + $j;



						



						$route_data[$count]['start_location_data'] = isset($way_points[$start_index]) && !empty($way_points[$start_index]) ? $way_points[$start_index] : '';



						$route_data[$count]['way_points'] = $current_route_waypt;



						$route_data[$count]['end_location_data'] = isset($way_points[$end_index]) && !empty($way_points[$end_index]) ? $way_points[$end_index] : '';







						if(!$only_one) {



							$route_data[$count]['start_location_data'] = end($way_points);



							$route_data[$count]['way_points'] = array();



							$route_data[$count]['end_location_data'] = end($way_points);



						}







						if(empty($route_data[$count]['end_location_data']) ) {



							$route_data[$count]['end_location_data'] = $route_data[$count]['start_location_data'];



						}



						$i = $i+10;



						$count++;

					}

				}



				$map_data['routesdata'] = $route_data;

			}

			return $map_data;



		}







		function ciwpgmp_frontend_scripts() {



			wp_enqueue_script( 'jquery' );



			wp_enqueue_script( 'ciwpgmp-frontend-js', CIWPGMP_JS .'ciwpgmp_frontend.js',array ('jquery'),'',true );



			wp_enqueue_style( 'frontend-css', CIWPGMP_CSS .'frontend.css', false);





		}







		function ciwpgmp_set_pagination_location_listing($page){



			if(!(is_admin())){



				$page = (get_query_var('paged')) ? get_query_var('paged') : 1;



				$_REQUEST['paged'] = $page;



			}

			return $page;



		}

		function ciwpgmp_model_class() {



			require_once(CIWPGMP_DIR.'inc/class.model.php');



			$this->addonModal = new CIWPGMP_Model_User_Trip();



		}







		function ciwpgmp_register_required_post_type() {



			require_once(CIWPGMP_DIR.'inc/ciwpgmp_register_post_types.php');



		}







		function ciwpgmp_add_auto_shortcode_user_trips_listing_my_trips_post($content) {



			$settings = maybe_unserialize( get_option( 'ciwpgmp_settings' ) );



			$map_id = '';



			if(isset($settings['ciwpgmp_map']) && !empty($settings['ciwpgmp_map']) ){



				$map_id = $settings['ciwpgmp_map'];



			}



			global $post;



			if ( ! is_single() ) {



				return $content;



			}



			if($post->post_type == 'my_trips' && is_single()) {



				$content .= do_shortcode( '[put_wpgm id='.$map_id.']');



			}



			return $content;

		}



		public static function wptp_get_all_current_user_trips() {



			$user_id = get_current_user_id();



			$args = array(



			'post_type' 	=> 'my_trips',



			'posts_per_page' => -1,



			'author'		=> $user_id,



			);



			$all_trips = get_posts($args);



			return $all_trips;



		}



		function ciwpgmp_add_location_map_after_content_trip_locations_post($content) {


			global $post;

			if ( ! is_single() ) {

			    return $content;
			}

			if( $post->post_type == 'my_trips') {
				return $content;
			}

			if($post->post_type == 'trip_locations' && is_singular('trip_locations') ){
				//echo '<pre>'; print_r($post); exit;
				//echo get_post_type();
				$trip_form = CIWPGMP_DIR.'inc/ciwpgmp_display_map_trip_form_location_post.php';
				$form = require($trip_form);
				return $content.$form;
			} else {
				return $content;
			}
		}

		function ciwpgmp_filter_markers_view_trip($places,$map_id) {



			global $post;

			$settings = maybe_unserialize( get_option( 'ciwpgmp_settings' ) );



			$current_map_id = '';



			if(isset($settings['ciwpgmp_map']) && !empty($settings['ciwpgmp_map']) ){



				$current_map_id = $settings['ciwpgmp_map'];



			}

			if( is_admin() ) {



				$screen = get_current_screen();



				if($screen->post_type == 'my_trips') {



					$post_id = $_GET['post'];



				}



			}else{

				$post_id = $post->ID;



				if(is_single() && $post->post_type == 'my_trips' ) {

				}

			}



			if($map_id == $current_map_id) {



				$get_user_location = get_post_meta( $post_id, '_wptp_trip_locations', true );



				if(empty($get_user_location))



				$get_user_location = array();



				foreach($places as $key => $place){	



					if(!in_array($place['id'],$get_user_location)) {



						unset($places[$key]);



					}



				}



				$places = array_values($places);

			}

			return $places;

		}







		function ciwpgmp_user_trips_list($args) {



			require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );

    		require_once( ABSPATH . 'wp-admin/includes/screen.php' );

			require_once( ABSPATH.'wp-admin/includes/template.php' );



			$trips_listing = CIWPGMP_DIR.'inc/shortcodes/ciwpgmp_user_tips_list.php';



			return require($trips_listing);

		}

		private function ciwpgmp_define_constants() {



			global $wpdb;

			if ( ! defined( 'CIWPGMP_SLUG' ) ) {



				define( 'CIWPGMP_SLUG', 'ciwpgmp_view_overview' );



			}

			if ( ! defined( 'CIWPGMP_VERSION' ) ) {



				define( 'CIWPGMP_VERSION', '1.0.0' );



			}

			if ( ! defined( 'CIWPGMP_FOLDER' ) ) {



				define( 'CIWPGMP_FOLDER', basename( dirname( __FILE__ ) ) );



			}

			if ( ! defined( 'CIWPGMP_DIR' ) ) {



				define( 'CIWPGMP_DIR', plugin_dir_path( __FILE__ ) );



			}

			if ( ! defined( 'CIWPGMP_URL' ) ) {



				define( 'CIWPGMP_URL', plugin_dir_url( CIWPGMP_FOLDER ).CIWPGMP_FOLDER.'/' );



			}

			if ( ! defined( 'CIWPGMP_CSS' ) ) {



				define( 'CIWPGMP_CSS', CIWPGMP_URL.'assets/css/' );



			}

			if ( ! defined( 'CIWPGMP_JS' ) ) {



				define( 'CIWPGMP_JS', CIWPGMP_URL.'assets/js/' );



			}

		}

	}

	

	new CIWPGMP_On_GoogleMaps();

}
