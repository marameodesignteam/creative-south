<?php
/**
 * WP Google Map Pro
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 * @copyright 2019 flippercode
 * 
 * @wordpress-plugin
 * Plugin Name: WP Google Map Pro
 * Plugin URI: http://www.flippercode.com/
 * Description: (Gold Version) World's most advanced google maps plugin. Location, Category, Layers, Controls, Shapes,Routes, Directions, Marker clusters, Listing, Places and many more...
 * Version: 5.2.8
 * Author: flippercode
 * Author URI: http://www.flippercode.com/
 * Text Domain: wpgmp_google_map
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! class_exists( 'WPGMP_Google_Maps_Pro' ) ) {

	/**
	 * Main plugin class
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @package Maps
	 */
	class WPGMP_Google_Maps_Pro {

		/**
		 * List of Modules.
		 *
		 * @var array
		 */
		private $modules = array();
		/**
		 * Intialize variables, files and call actions.
		 *
		 * @var array
		 */
		public function __construct() {

			$this->wpgmp_define_constants();
			$this->wpgmp_load_files();
			$this->wpgmp_register_hooks();
			
		}

		function wpgmp_register_hooks(){

			register_activation_hook( __FILE__, 				array( $this, 'wpgmp_plugin_activation' ) );
			register_deactivation_hook( __FILE__, 				array( $this, 'wpgmp_plugin_deactivation' ) );

			if ( is_multisite() ) {
				add_action( 'wpmu_new_blog', 					array( $this, 'wpgmp_on_blog_new_generate' ), 10, 6 );
				add_filter( 'wpmu_drop_tables', 				array( $this, 'wpgmp_on_blog_delete' ) );
			}
			add_action( 'plugins_loaded', 						array( $this, 'wpgmp_load_plugin_languages' ) );
			add_action( 'widgets_init', 						array( $this, 'wpgmp_google_map_widget' ) );
			add_action( 'wp_enqueue_scripts', 					array( $this, 'wpgmp_frontend_scripts' ) );
			add_action( 'wp_ajax_wpgmp_ajax_call', 				array( $this, 'wpgmp_ajax_call' ) );
			add_action( 'wp_ajax_nopriv_wpgmp_ajax_call', 		array( $this, 'wpgmp_ajax_call' ) );
			add_filter( 'media_upload_tabs', 					array( $this, 'wpgmp_google_map_tabs_filter' ) );
			add_filter( 'fc-dummy-placeholders', 				array( $this, 'wpgmp_apply_placeholders' ) );

			add_shortcode( 'put_wpgm', 							array( $this, 'wpgmp_show_location_in_map' ) );
			add_shortcode( 'display_map', 						array( $this, 'wpgmp_display_map' ) );
			
			if ( is_admin() ) {
				
				add_action( 'admin_head', 						array( $this, 'wpgmp_customizer_font_family' ));
				add_action( 'admin_menu', 						array( $this, 'wpgmp_create_menu' ) );
				add_action( 'media_upload_ell_insert_gmap_tab', array( $this, 'wpgmp_google_map_media_upload_tab' ) );
				add_action( 'admin_print_scripts', 				array( $this, 'wpgmp_backend_styles' ) );
				add_action( 'admin_init', 						array( $this, 'wpgmp_export_data' ) );
				add_action( 'add_meta_boxes', 					array( $this, 'wpgmp_call_meta_box' ) );
				add_action( 'save_post', 						array( $this, 'wpgmp_save_meta_box_data' ) );

				$this->wpgmp_create_vc_component();

			}
		}

		function wpgmp_create_vc_component() {

			if ( defined( 'WPB_VC_VERSION' ) ) {
				
				$vcComponent = new WPGMP_VC_Builder();
				$vcComponent->wpgmp_register_vc_component();
				
			}
		}

		/**
		 * Export data into csv,xml,json or excel file
		 */
		function wpgmp_export_data() {

			if ( isset( $_POST['action'] ) && isset( $_REQUEST['_wpnonce'] ) && $_POST['action'] == 'export_location_csv' ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

				if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
					die( 'Cheating...' );
				}

				if ( isset( $_POST['action'] ) and false != strstr( $_POST['action'], 'export_' ) ) {
					$export_action = explode( '_', sanitize_text_field( $_POST['action'] ) );
					if ( 3 == count( $export_action ) and 'export' == $export_action[0] ) {
						$model_class = 'WPGMP_Model_' . ucwords( $export_action[1] );
						$entity      = new $model_class();
						$entity->export( $export_action[2] );
					}
				}
			}

		}

		function wpgmp_apply_placeholders( $content ) {
			 
			$content = WPGMP_Helper::wpgmp_apply_placeholders( $content );
			return $content;
		}
		
		function wpgmp_customizer_font_family() {

			if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {

			    $modelFactory = new WPGMP_Model();
				$map_obj      = $modelFactory->create_object( 'map' );
				$styles_and_scripts = $map_obj->get_map_customizer_style();
				$font_families   = $styles_and_scripts['font_families'];
				$fc_skin_styles  = $styles_and_scripts['fc_skin_styles']; 
				if ( ! empty( $fc_skin_styles ) ) {
					echo '<style>' . $fc_skin_styles . '</style>';
				}
				if ( ! empty( $font_families ) ) {
					$font_families = array_unique($font_families);
					?>
					<script type="text/javascript">
						var google_customizer_fonts = <?php echo json_encode($font_families,JSON_FORCE_OBJECT);?>;
					</script>
				<?php }

			}

		}

		/**
		 * Register WP Google Map Widget
		 */
		function wpgmp_google_map_widget() { register_widget( 'WPGMP_Google_Map_Widget_Class' ); }


		/**
		 * Display WP Google Map meta box on pages/posts and custom post type(s).
		 */
		function wpgmp_call_meta_box() {

			
			$wpgmp_settings = get_option( 'wpgmp_settings', true );
			$screens = WPGMP_Helper::wpgmp_get_all_post_types();
			$screens = apply_filters( 'wpgmp_meta_boxes', $screens );
			$selected_values = unserialize( $wpgmp_settings['wpgmp_allow_meta'] );
			foreach ( $screens as $screen ) {

				if ( is_array( $selected_values ) ) {

					if ( in_array( $screen, $selected_values ) or in_array( 'all', $selected_values ) ) {
						continue;
					}
				}

				add_meta_box(
					'wpgmp_google_map_metabox',
					esc_html__( 'WP Google Map Pro', 'wpgmp-google-map' ),
					array( $this, 'wpgmp_add_meta_box' ),
					$screen
				);
			}
		}
		/**
		 * Callback to display  wp google map pro meta box.
		 *
		 * @param  string $post Post Type.
		 */
		function wpgmp_add_meta_box( $post ) {

			global $wpdb;

			$wpgmp_settings = get_option( 'wpgmp_settings', true );

			$modelFactory                   = new WPGMP_Model();
			$category_obj                   = $modelFactory->create_object( 'group_map' );
			$categories                     = $category_obj->fetch();
			$map_obj                        = $modelFactory->create_object( 'map' );
			$all_maps                       = $map_obj->fetch();
			$wpgmp_location_address         = get_post_meta( $post->ID, '_wpgmp_location_address', true );
			$wpgmp_metabox_location_city    = get_post_meta( $post->ID, '_wpgmp_location_city', true );
			$wpgmp_metabox_location_state   = get_post_meta( $post->ID, '_wpgmp_location_state', true );
			$wpgmp_metabox_location_country = get_post_meta( $post->ID, '_wpgmp_location_country', true );

			$wpgmp_map_ids = get_post_meta( $post->ID, '_wpgmp_map_id', true );
			$wpgmp_map_id  = unserialize( $wpgmp_map_ids );
			if ( ! is_array( $wpgmp_map_id ) ) {
				$wpgmp_map_id = array( $wpgmp_map_ids );
			}
			$wpgmp_metabox_marker_id         = get_post_meta( $post->ID, '_wpgmp_metabox_marker_id', true );
			$wpgmp_metabox_latitude          = get_post_meta( $post->ID, '_wpgmp_metabox_latitude', true );
			$wpgmp_metabox_longitude         = get_post_meta( $post->ID, '_wpgmp_metabox_longitude', true );
			$wpgmp_metabox_location_redirect = get_post_meta( $post->ID, '_wpgmp_metabox_location_redirect', true );
			$wpgmp_metabox_custom_link       = get_post_meta( $post->ID, '_wpgmp_metabox_custom_link', true );
			$wpgmp_apilocation = WPGMP_Helper::wpgmp_get_server_protocol();
			
			if ( isset( $wpgmp_settings['wpgmp_country_specific'] ) ) {
				$wpgmp_country_specific = ( $wpgmp_settings['wpgmp_country_specific'] == 'true' );
			} else {
				$wpgmp_country_specific = false;
			}

			if ( isset( $wpgmp_settings['wpgmp_countries'] ) ) {
				$wpgmp_countries = $wpgmp_settings['wpgmp_countries'];
			} else {
				$wpgmp_countries = false;
			}
			$language = $wpgmp_settings['wpgmp_language'];

			if ( $language == '' ) {
				$language = 'en';
			}

			$language = apply_filters( 'wpgmp_map_lang', $language );

			if ( isset( $wpgmp_settings['wpgmp_language'] ) && $wpgmp_settings['wpgmp_language'] != '' ) {
				$wpgmp_apilocation .= '://maps.google.com/maps/api/js?key=' . $wpgmp_settings['wpgmp_api_key'] . '&libraries=geometry,places,weather,panoramio,drawing&language=' . $language;
			} else {
				$wpgmp_apilocation .= '://maps.google.com/maps/api/js?libraries=geometry,places,weather,panoramio,drawing&language=' . $language;
			}

			$hide_map = $wpgmp_settings['wpgmp_metabox_map'];

			$center_lat = '38.555475';
			$center_lng = '-95.665';

			if ( $wpgmp_metabox_latitude != '' ) {	$center_lat = $wpgmp_metabox_latitude;	}

			if ( $wpgmp_metabox_longitude != '' ) {	$center_lng = $wpgmp_metabox_longitude;	}

			$center_lat = apply_filters( 'wpgmp_metabox_lat', $center_lat );
			$center_lng = apply_filters( 'wpgmp_metabox_lng', $center_lng );

			?>
		<script src="<?php echo esc_url( $wpgmp_apilocation ); ?>"></script>
		<script>
		jQuery(document).ready(function($) {
			try {


			  var wpgmp_input = $("#wpgmp_metabox_location").val();
			var wpgmp_geocoder = new google.maps.Geocoder();

			<?php if ( $hide_map != 'true' ) { ?>
			var center = new google.maps.LatLng(<?php echo esc_html( $center_lat ); ?>, <?php echo esc_html( $center_lng ); ?>);
			var wpgmp_map = new google.maps.Map($(".wpgmp_meta_map")[0],{
				zoom: 5,
				center: center,
			});	

			var infowindow = new google.maps.InfoWindow();
			var wpgmp_marker = new google.maps.Marker({
			  map: wpgmp_map,
			  position: center,
			  draggable : true,
			});

			google.maps.event.addListener(wpgmp_marker, 'drag', function() {

				var position = wpgmp_marker.getPosition();

				wpgmp_geocoder.geocode({
					latLng: position
				}, function(results, status) {

					if (status == google.maps.GeocoderStatus.OK) {

						$("#wpgmp_metabox_location").val(results[0].formatted_address);
						$("#wpgmp_metabox_location_hidden").val(results[0].formatted_address);
						$("#wpgmp_metabox_location_city").val(wpgmp_get_exact_names(results[0], 'administrative_area_level_3') || wpgmp_get_exact_names(results[0], 'locality'));
						$("#wpgmp_metabox_location_state").val(wpgmp_get_exact_names(results[0], "administrative_area_level_1"));
						$("#wpgmp_metabox_location_country").val(wpgmp_get_exact_names(results[0], "country"));
						
					}
				});
				
				$(".wpgmp_metabox_latitude").val(position.lat());
				$(".wpgmp_metabox_longitude").val(position.lng());
			});
			<?php } ?>
			
			function wpgmp_get_exact_names(result, type){
					
					var component_name = "";
					for (i = 0; i < result.address_components.length; ++i) {
						var component = result.address_components[i];
						$.each(component.types, function(index, value) {
							if (value == type) {
								component_name = component.long_name;
							}
						});


					}
					return component_name;
				}
				
			var wpgmp_metabox_autocomplete = new google.maps.places.Autocomplete(wpgmp_metabox_location);

			<?php if ($wpgmp_country_specific && $wpgmp_countries != '') { 

					$js_array = json_encode($wpgmp_countries);

				?>
                    wpgmp_metabox_autocomplete.setComponentRestrictions({
                        'country': <?php echo $js_array; ?>
                    });
             <?php } ?>

			<?php if ( $hide_map != 'true' ) { ?>
			wpgmp_metabox_autocomplete.bindTo('bounds', wpgmp_map);
			<?php } ?>
			google.maps.event.addListener(wpgmp_metabox_autocomplete, 'place_changed', function() {
			var metabox_place = wpgmp_metabox_autocomplete.getPlace();
			<?php if ( $hide_map != 'true' ) { ?>
			wpgmp_map.setCenter(metabox_place.geometry.location);
			wpgmp_marker.setPosition(metabox_place.geometry.location);
			
			<?php } ?>
			$(".wpgmp_metabox_latitude").val(metabox_place.geometry.location.lat());
			$(".wpgmp_metabox_longitude").val(metabox_place.geometry.location.lng());
			$("#wpgmp_metabox_location_hidden").val(metabox_place.formatted_address);
			
			$("#wpgmp_metabox_location_city").val(wpgmp_get_exact_names(metabox_place, 'administrative_area_level_3') || wpgmp_get_exact_names(metabox_place, 'locality'));
			$("#wpgmp_metabox_location_state").val(wpgmp_get_exact_names(metabox_place, "administrative_area_level_1"));
			$("#wpgmp_metabox_location_country").val(wpgmp_get_exact_names(metabox_place, "country"));

			
						
			});

			$(".wpgmp_mcurrent_loction").click(function() {

				navigator.geolocation.getCurrentPosition(function(position) {
				
				var position = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
				<?php if ( $hide_map != 'true' ) { ?>
				wpgmp_map.setCenter(position);
				wpgmp_marker.setPosition(position);
			
				<?php } ?>
				wpgmp_geocoder.geocode({
					latLng: position
				}, function(results, status) {

					if (status == google.maps.GeocoderStatus.OK) {

						$("#wpgmp_metabox_location").val(results[0].formatted_address);
						$("#wpgmp_metabox_location_hidden").val(results[0].formatted_address);
						$("#wpgmp_metabox_location_city").val(get_exact_names(results[0], 'administrative_area_level_3') || get_exact_names(results[0], 'locality'));
						$("#wpgmp_metabox_location_state").val(get_exact_names(results[0], "administrative_area_level_1"));
						$("#wpgmp_metabox_location_country").val(get_exact_names(results[0], "country"));

					}
				});

				$(".wpgmp_metabox_latitude").val(position.lat());
				$(".wpgmp_metabox_longitude").val(position.lng());

				}, function(ErrorPosition) {

					
				}, {
					enableHighAccuracy: true,
					timeout: 5000,
					maximumAge: 0
				});
			});

			$("select[name='wpgmp_metabox_location_redirect']").change(function() {
			var rval = $(this).val();
			if(rval=="custom_link")
			{
			$("#wpgmp_toggle_custom_link").show("slow");
			}
			else
			{
				$("#wpgmp_toggle_custom_link").hide("slow");
			}
			});

			} catch(err) {
				console.log("wpgmp-exception - " + err.message);
			}
	});
		</script>

		<div class="wpgmp_metabox_container">
			<?php if ( $hide_map != 'true' ) { ?>
		<div class="row_metabox">
			<div class="wpgmp_meta_map"></div>
		</div>
		<?php } ?>
		<div class="row_metabox">
		<div class="wpgmp_metabox_left">
		<label for="wpgmp_metabox_location"><?php esc_html_e( 'Enter Location :', 'wpgmp-google-map' ); ?></label>
	</div>
	<div class="wpgmp_metabox_right">
	<input type="text" id="wpgmp_metabox_location" class="wpgmp_metabox_location wpgmp_auto_suggest" name="wpgmp_metabox_location" value="<?php echo htmlspecialchars( stripslashes( $wpgmp_location_address ) ); ?>" size="25" />
	<input type="hidden" id="wpgmp_metabox_location_hidden" name="wpgmp_metabox_location_hidden" value="<?php echo htmlspecialchars( stripslashes( $wpgmp_location_address ) ); ?>" />
	<input type="hidden" id="wpgmp_metabox_location_city" name="wpgmp_metabox_location_city" value="<?php echo htmlspecialchars( stripslashes( $wpgmp_metabox_location_city ) ); ?>" />
	<input type="hidden" id="wpgmp_metabox_location_state" name="wpgmp_metabox_location_state" value="<?php echo htmlspecialchars( stripslashes( $wpgmp_metabox_location_state ) ); ?>" />
	<input type="hidden" id="wpgmp_metabox_location_country" name="wpgmp_metabox_location_country" value="<?php echo htmlspecialchars( stripslashes( $wpgmp_metabox_location_country ) ); ?>" />
	<span class="wpgmp_mcurrent_loction" title="Take Current Location">&nbsp;</span>
	</div>
	</div>
	<div class="row_metabox">
	<div class="wpgmp_metabox_left">
	<label for="wpgmp_enter_location"><?php esc_html_e( 'Latitude', 'wpgmp-google-map' ); ?>&nbsp;/&nbsp;<?php esc_html_e( 'Longitude', 'wpgmp-google-map' ); ?>&nbsp;:</label>
	</div>
	<div class="wpgmp_metabox_right">
	<input type="text" class="wpgmp_metabox_latitude" id="wpgmp_metabox_latitude" name="wpgmp_metabox_latitude" value="<?php echo esc_attr( $wpgmp_metabox_latitude ); ?>" placeholder="Latitude" />
	<input type="text" class="wpgmp_metabox_longitude" id="wpgmp_metabox_longitude" name="wpgmp_metabox_longitude" value="<?php echo esc_attr( $wpgmp_metabox_longitude ); ?>" placeholder="Longitude" />
	</div>
	</div>
	<div class="row_metabox">
	<div class="wpgmp_metabox_left">
	<label><?php esc_html_e( 'Select Categories:', 'wpgmp-google-map' ); ?></label>
	</div>
	<div class="wpgmp_metabox_right">
			<?php
			$selected_categories = unserialize( $wpgmp_metabox_marker_id );

			if ( ! is_array( $selected_categories ) ) {
				$selected_categories = array( $wpgmp_metabox_marker_id );
			}

			if ( $categories ) {
				foreach ( $categories as $category ) {
					if ( in_array( $category->group_map_id, $selected_categories ) ) {
						$s = "checked='checked'";
					} else {
						$s = '';
					}
					?>
		<span class="wpgmp_check">
		<input type="checkbox" id="wpgmp_location_group_map<?php echo esc_attr( $category->group_map_id ); ?>" <?php echo esc_attr( $s ); ?> name="wpgmp_metabox_marker_id[]" value="<?php echo esc_attr( $category->group_map_id ); ?>">
					<?php echo esc_html( $category->group_map_title ); ?>
	</span>
					<?php
				}
			} else {
				echo '<p class="description">';

				$link = "<a href='" . esc_url( admin_url( 'admin.php?page=wpgmp_form_group_map' ) ) . "' target='_blank'>" . esc_html__( 'Here', 'wpgmp-google-map' ) . '</a>';

				printf(
					/* translators: %s: Add Category Link */
						esc_html__( 'Do you want to assign a category? Please create category %s.', 'wpgmp-google-map' ),
					$link
				);

				echo '</p>';
			}
			?>
		</div>
		</div>
		<div class="row_metabox">
		</div>
		<div class="row_metabox">
		<div class="wpgmp_metabox_left">
		<label for="wpgmp_enter_location"><?php esc_html_e( 'Location Redirect :', 'wpgmp-google-map' ); ?></label>
	</div>
	<div class="wpgmp_metabox_right">
	<select name="wpgmp_metabox_location_redirect" id="wpgmp_metabox_location_redirect">
	<option value="marker"<?php selected( $wpgmp_metabox_location_redirect, 'marker' ); ?>>Marker</option>
	<option value="post"<?php selected( $wpgmp_metabox_location_redirect, 'post' ); ?>>Post</option>
	<option value="custom_link"<?php selected( $wpgmp_metabox_location_redirect, 'custom_link' ); ?>>Custom Link</option>
	</select>
	</div>
	</div>

			<?php
			if ( ! empty( $wpgmp_metabox_custom_link ) && 'custom_link' == $wpgmp_metabox_location_redirect ) {
				$display_custom_link = 'display:block';
			} else {
				$display_custom_link = 'display:none';
			}
			?>

		<div class="row_metabox" style="<?php echo esc_attr( $display_custom_link ); ?>" id="wpgmp_toggle_custom_link">
	<div class="wpgmp_metabox_left">
	<label for="wpgmp_metabox_custom_link">&nbsp;</label>
	</div>
	<div class="wpgmp_metabox_right">
	<input type="textbox" value="<?php echo esc_attr( $wpgmp_metabox_custom_link ); ?>" name="wpgmp_metabox_custom_link" class="wpgmp_metabox_location" />
	<p class="description"><?php esc_html_e( 'Please enter link.', 'wpgmp-google-map' ); ?></p>
	</div>
	</div>
			<?php do_action( 'wpgmp_meta_box_fields' ); ?>
	<div class="row_metabox">
	<div class="wpgmp_metabox_left">
	<label><?php esc_html_e( 'Select Map :', 'wpgmp-google-map' ); ?></label>
	</div>
	<div class="wpgmp_metabox_right">
	
			<?php

			if ( count( $all_maps ) > 0 ) {
				foreach ( $all_maps as $map ) :

					if ( is_array( $wpgmp_map_id ) and in_array( $map->map_id, $wpgmp_map_id ) ) {
						$c = 'checked=checked';
					} else {
						$c = ''; }

					?>
	   
		 <span class="wpgmp_check"><input <?php echo esc_attr( $c ); ?> type="checkbox" name="wpgmp_metabox_mapid[]" value="<?php echo esc_attr( $map->map_id ); ?>">&nbsp; <?php echo esc_html( $map->map_title ); ?></span>
	
					<?php
	endforeach;
			} else {

				$link = "<a href='" . admin_url( 'admin.php?page=wpgmp_create_map' ) . "'>" . esc_html__( 'create a map', 'wpgmp-google-map' ) . '</a>';

				printf(
					/* translators: %s: Add Map Link */
						esc_html__( 'Please %s first.', 'wpgmp-google-map' ),
					$link
				);

			}
			?>
   
	<input type="hidden" name="wpgmp_hidden_flag" value="true" />
	
	</div>
	</div>

	</div>
			<?php
		}
		/**
		 * Save meta box data
		 *
		 * @param  int $post_id Post ID.
		 */
		function wpgmp_save_meta_box_data( $post_id ) {

			$modelFactory = new WPGMP_Model();
			$postObj = $modelFactory->create_object( 'post' );
			$postObj->wpgmp_handle_metabox_submission($post_id);
			
		}

		/**
		 * Eneque scripts at frontend.
		 */
		
		function wpgmp_frontend_scripts() {  WPGMP_Helper::wpgmp_register_map_frontend_resources();  }
		/**
		 * Display map at the frontend using put_wpgmp shortcode.
		 *
		 * @param  array  $atts   Map Options.
		 * @param  string $content Content.
		 */
		function wpgmp_show_location_in_map( $atts, $content = null ) {

			try {
				$factoryObject = new WPGMP_Controller();
				$viewObject    = $factoryObject->create_object( 'shortcode' );
				$output        = $viewObject->display( 'put-wpgmp', $atts );
				 return $output;

			} catch ( Exception $e ) {
				echo WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) );

			}

		}
		/**
		 * Display map at the frontend using display_map shortcode.
		 *
		 * @param  array $atts    Map Options.
		 */
		function wpgmp_display_map( $atts ) {

			try {
				$factoryObject = new WPGMP_Controller();
				$viewObject    = $factoryObject->create_object( 'shortcode' );
				 $output       = $viewObject->display( 'display-map', $atts );
				 return $output;

			} catch ( Exception $e ) {
				echo WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) );

			}

		}
		/**
		 * Ajax Call
		 */
		function wpgmp_ajax_call() {

			check_ajax_referer( 'fc-call-nonce', 'nonce' );
			$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
			$value     = wp_unslash( $_POST );
			if ( isset( $operation ) ) {
				$this->$operation( $value );
			}
			exit;
		}

		/**
		 * Process slug and display view in the backend.
		 */
		function wpgmp_processor() {

			$return = '';
			$page = ( isset( $_GET['page'] ) ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'wpgmp_view_overview';
			$pageData      = explode( '_', $page );
			$obj_type      = $pageData[2];
			$obj_operation = $pageData[1];

			if ( count( $pageData ) < 3 ) {	die( 'Cheating!' );	}

			try {

				if ( count( $pageData ) > 3 ) {
					$obj_type = $pageData[2] . '_' . $pageData[3];
				}
				$factoryObject = new WPGMP_Controller();
				$viewObject    = $factoryObject->create_object( $obj_type );
				$viewObject->display( $obj_operation );

			} catch ( Exception $e ) {
				echo WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) );

			}

		}
		/**
		 * Create backend navigation.
		 */
		function wpgmp_create_menu() {

			global $navigations;

			$pagehook1 = add_menu_page(
				esc_html__( 'WP Google Map Pro', 'wpgmp-google-map' ),
				esc_html__( 'WP Google Map Pro', 'wpgmp-google-map' ),
				'wpgmp_admin_overview',
				WPGMP_SLUG,
				array( $this, 'wpgmp_processor' ),
				WPGMP_IMAGES . '/flippercode.png'
			);

			if ( current_user_can( 'manage_options' ) ) {
				$role = get_role( 'administrator' );
				$role->add_cap( 'wpgmp_admin_overview' );
			}

			$this->wpgmp_load_modules_menu();
			add_action( 'load-' . $pagehook1, array( $this, 'wpgmp_backend_scripts' ) );

		}
		/**
		 * Read models and create backend navigation.
		 */
		function wpgmp_load_modules_menu() {

			$modules   = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {

						$object = new $module();

					if ( method_exists( $object, 'navigation' ) ) {

						if ( ! is_array( $object->navigation() ) ) {
							continue;
						}

						foreach ( $object->navigation() as $nav => $title ) {

							if ( current_user_can( 'manage_options' ) && is_admin() ) {
								$role = get_role( 'administrator' );
								$role->add_cap( $nav );

							}

							$pagehooks[] = add_submenu_page(
								WPGMP_SLUG,
								$title,
								$title,
								$nav,
								$nav,
								array( $this, 'wpgmp_processor' )
							);

						}
					}
				}
			}

			if ( is_array( $pagehooks ) ) {

				foreach ( $pagehooks as $key => $pagehook ) {
					add_action( 'load-' . $pagehooks[ $key ], array( $this, 'wpgmp_backend_scripts' ) );
				}
			}

		}
		/**
		 * Eneque scripts in the backend.
		 */
		function wpgmp_backend_scripts() { 

			WPGMP_Helper::wpgmp_register_map_backend_resources();
		}
		/**
		 * Metabox stylesheet.
		 */
		function wpgmp_backend_styles() {

			wp_enqueue_style( 'wpgmp-backend-metabox', WPGMP_CSS . 'wpgmp-metabox-css.css' );
		}
		/**
		 * Load plugin language file.
		 */
		function wpgmp_load_plugin_languages() {

			$this->modules = apply_filters( 'wpgmp_extensions', $this->modules );
			load_plugin_textdomain( 'wpgmp-google-map', false, WPGMP_FOLDER . '/lang/' );
		}
		/**
		 * Call hook on plugin activation for both multi-site and single-site.
		 */
		function wpgmp_plugin_activation( $network_wide ) {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated   = array();
				$sql         = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids    = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpgmp_activation();
					$activated[] = $blog_id;
				}

				switch_to_blog( $currentblog );
				update_site_option( 'op_activated', $activated );

			} else {
				$this->wpgmp_activation();
			}
		}
		/**
		 * Call hook on plugin deactivation for both multi-site and single-site.
		 */
		function wpgmp_plugin_deactivation() {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated   = array();
				$sql         = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids    = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpgmp_deactivation();
					$activated[] = $blog_id;
				}

				switch_to_blog( $currentblog );
				update_site_option( 'op_activated', $activated );

			} else {
				$this->wpgmp_deactivation();
			}
		}

		/**
		 * Perform tasks on new blog create and table install.
		 */

		function wpgmp_on_blog_new_generate( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

			if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				switch_to_blog( $blog_id );
				$this->wpgmp_activation();
				restore_current_blog();
			}

		}

		/**
		 * Perform tasks on when blog deleted and remove plugin tables.
		 */

		function wpgmp_on_blog_delete( $tables ) {
			global $wpdb;
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_LOCATION );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_GROUPMAP );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_MAP );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_ROUTES );
			return $tables;
		}
		/**
		 * Create choose icon tab in media manager.
		 *
		 * @param  array $tabs Current Tabs.
		 * @return array       New Tabs.
		 */
		function wpgmp_google_map_tabs_filter( $tabs ) {

			$newtab = array( 'ell_insert_gmap_tab' => esc_html__( 'Choose Icons', 'wpgmp-google-map' ) );
			return array_merge( $tabs, $newtab );
		}
		/**
		 * Intialize wp_iframe for icons tab
		 *
		 * @return [type] [description]
		 */
		function wpgmp_google_map_media_upload_tab() {

			return wp_iframe( array( $this, 'media_wpgmp_google_map_icon' ), array() );
		}
		/**
		 * Read images/icons folder.
		 */
		function media_wpgmp_google_map_icon() {

			wp_enqueue_style( 'media' );
			media_upload_header();
			$form_action_url = site_url( "wp-admin/media-upload.php?type={$GLOBALS['type']}&tab=ell_insert_gmap_tab", 'admin' );
			?>

			<style type="text/css">
			#select_icons .read_icons {width: 32px;height: 32px;}
			#select_icons .active img {border: 3px solid #000;width: 26px;}
			</style>

			<script type="text/javascript">

			jQuery(document).ready(function($) {

				$(".read_icons").click(function () {
					$(".read_icons").removeClass('active');
					$(this).addClass('active');
				});

				$('input[name="wpgmp_search_icon"]').keyup(function() {
					if($(this).val() == '')
					$('.read_icons').show();
				else {
					$('.read_icons').hide();
					$('img[title^="' + $(this).val() + '"]').parent().show();
				}

			});

		});

		function wpgmp_add_icon_to_images(target) {

			if(jQuery('.read_icons').hasClass('active')) {
				imgsrc = jQuery('.active').find('img').attr('src');
				var win = window.dialogArguments || opener || parent || top;
				win.send_icon_to_map(imgsrc,target);
			}else{
				alert('<?php esc_html_e( 'Choose marker icon', 'wpgmp-google-map' ); ?>');
			}
		}
		</script>
		<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $form_action_url ); ?>" class="media-upload-form" id="library-form">
	<h3 class="media-title" style="color: #5A5A5A; font-family: Georgia, 'Times New Roman', Times, serif; font-weight: normal; font-size: 1.6em; margin-left: 10px;"><?php esc_html_e( 'Choose icon', 'wpgmp-google-map' ); ?> 	<input name="wpgmp_search_icon" id="wpgmp_search_icon" type='text' value="" placeholder="<?php esc_html_e( 'Search icons', 'wpgmp-google-map' ); ?>" />
</h3>
	<div style="margin-bottom:20px; float:left; width:100%;">
	<ul style="float:left; width:100%;" id="select_icons">
			<?php
			$dir          = WPGMP_ICONS_DIR;
			$file_display = array( 'jpg', 'jpeg', 'png', 'gif' );

			if ( file_exists( $dir ) == false ) {
				echo 'Directory \'', $dir, '\' not found!';

			} else {
				$dir_contents = scandir( $dir );
				foreach ( $dir_contents as $file ) {
					$image_data = explode( '.', $file );
					$file_type  = strtolower( end( $image_data ) );
					if ( '.' !== $file && '..' !== $file && true == in_array( $file_type, $file_display ) ) {
						?>
			<li class="read_icons" style="float:left;">
			<img alt="<?php echo esc_attr( $image_data[0] ); ?>" title="<?php echo esc_attr( $image_data[0] ); ?>" src="<?php echo esc_url( WPGMP_ICONS . $file ); ?>" style="cursor:pointer;" />
		</li>
						<?php
					}
				}
			}

			if ( isset( $_GET['target'] ) ) {
				$target = esc_js( $_GET['target'] );
			} else {
				$target = '';
			}

			?>
		</ul>
		<button type="button" class="button" style="margin-left:10px;" value="1" onclick="wpgmp_add_icon_to_images('<?php echo esc_attr( $target ); ?>');" name="send[<?php echo esc_attr( $picid ); ?>]"><?php esc_html_e( 'Insert into Post', 'wpgmp-google-map' ); ?></button>
	</div>
	</form>
			<?php
		}
		/**
		 * Perform tasks on plugin deactivation.
		 */
		function wpgmp_deactivation() {}

		/**
		 * Perform tasks on plugin deactivation.
		 */
		function wpgmp_activation() {

			global $wpdb;

			// migrate options data from previous version.
			if ( ! get_option( 'wpgmp_settings' ) and get_option( 'wpgmp_language' ) ) {
				$wpgmp_settings['wpgmp_language']      = get_option( 'wpgmp_language', 'en' );
				$wpgmp_settings['wpgmp_api_key']       = get_option( 'wpgmp_api_key', '' );
				$wpgmp_settings['wpgmp_scripts_place'] = get_option( 'wpgmp_scripts_place', true );
				$wpgmp_settings['wpgmp_allow_meta']    = get_option( 'wpgmp_allow_meta', true );
				$wpgmp_settings['wpgmp_scripts_minify']    = get_option( 'wpgmp_scripts_minify', true );
				$wpgmp_settings['wpgmp_version']    = get_option( 'wpgmp_version', WPGMP_VERSION );
				
				update_option( 'wpgmp_settings', $wpgmp_settings );
			}


			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$modules   = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {
					$object = new $module();
					if ( method_exists( $object, 'install' ) ) {
								$tables[] = $object->install();
					}
				}
			}

			if ( is_array( $tables ) ) {
				foreach ( $tables as $i => $sql ) {
					dbDelta( $sql );
				}
			}

		}
		/**
		 * Define all constants.
		 */
		private function wpgmp_define_constants() {

			global $wpdb;

			if ( ! defined( 'ALLOW_UNFILTERED_UPLOADS' ) && is_admin() ) {
				define( 'ALLOW_UNFILTERED_UPLOADS', true );
			}

			if ( ! defined( 'WPGMP_SLUG' ) ) {
				define( 'WPGMP_SLUG', 'wpgmp_view_overview' );
			}

			if ( ! defined( 'WPGMP_VERSION' ) ) {
				define( 'WPGMP_VERSION', '5.2.8' );
			}

			if ( ! defined( 'WPGMP_FOLDER' ) ) {
				define( 'WPGMP_FOLDER', basename( dirname( __FILE__ ) ) );
			}

			if ( ! defined( 'WPGMP_DIR' ) ) {
				define( 'WPGMP_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'WPGMP_ICONS_DIR' ) ) {
				define( 'WPGMP_ICONS_DIR', WPGMP_DIR . '/assets/images/icons/' );
			}

			if ( ! defined( 'WPGMP_CORE_CLASSES' ) ) {
				define( 'WPGMP_CORE_CLASSES', WPGMP_DIR . 'core/' );
			}

			if ( ! defined( 'WPGMP_PLUGIN_CLASSES' ) ) {
				define( 'WPGMP_PLUGIN_CLASSES', WPGMP_DIR . 'classes/' );
			}

			if ( ! defined( 'WPGMP_TEMPLATES' ) ) {
				define( 'WPGMP_TEMPLATES', WPGMP_DIR . 'templates/' );
			}

			if ( ! defined( 'WPGMP_MODEL' ) ) {
				define( 'WPGMP_MODEL', WPGMP_DIR . 'modules/' );
			}

			if ( ! defined( 'WPGMP_CONTROLLER' ) ) {
				define( 'WPGMP_CONTROLLER', WPGMP_CORE_CLASSES );
			}

			if ( ! defined( 'WPGMP_CORE_CONTROLLER_CLASS' ) ) {
				define( 'WPGMP_CORE_CONTROLLER_CLASS', WPGMP_CORE_CLASSES . 'class.controller.php' );
			}

			if ( ! defined( 'WPGMP_MODEL' ) ) {
				define( 'WPGMP_MODEL', WPGMP_DIR . 'modules/' );
			}

			if ( ! defined( 'WPGMP_URL' ) ) {
				define( 'WPGMP_URL', plugin_dir_url( WPGMP_FOLDER ) . WPGMP_FOLDER . '/' );
			}

			if ( ! defined( 'WPGMP_TEMPLATES_URL' ) ) {
				define( 'WPGMP_TEMPLATES_URL', WPGMP_URL . 'templates/' );
			}

			if ( ! defined( 'FC_CORE_URL' ) ) {
				define( 'FC_CORE_URL', plugin_dir_url( WPGMP_FOLDER ) . WPGMP_FOLDER . '/core/' );
			}

			if ( ! defined( 'WPGMP_INC_URL' ) ) {
				define( 'WPGMP_INC_URL', WPGMP_URL . 'includes/' );
			}

			if ( ! defined( 'WPGMP_CSS' ) ) {
				define( 'WPGMP_CSS', WPGMP_URL . 'assets/css/' );
			}

			if ( ! defined( 'WPGMP_JS' ) ) {
				define( 'WPGMP_JS', WPGMP_URL . 'assets/js/' );
			}

			if ( ! defined( 'WPGMP_IMAGES' ) ) {
				define( 'WPGMP_IMAGES', WPGMP_URL . 'assets/images/' );
			}

			if ( ! defined( 'WPGMP_FONTS' ) ) {
				define( 'WPGMP_FONTS', WPGMP_URL . 'fonts/' );
			}

			if ( ! defined( 'WPGMP_ICONS' ) ) {
				define( 'WPGMP_ICONS', WPGMP_URL . 'assets/images/icons/' );
			}
			$upload_dir = wp_upload_dir();

			if ( ! defined( 'TBL_LOCATION' ) ) {
				define( 'TBL_LOCATION', $wpdb->prefix . 'map_locations' );
			}

			if ( ! defined( 'TBL_GROUPMAP' ) ) {
				define( 'TBL_GROUPMAP', $wpdb->prefix . 'group_map' );
			}

			if ( ! defined( 'TBL_MAP' ) ) {
				define( 'TBL_MAP', $wpdb->prefix . 'create_map' );
			}

			if ( ! defined( 'TBL_ROUTES' ) ) {
				define( 'TBL_ROUTES', $wpdb->prefix . 'map_routes' );
			}

		}
		
		public static function wpgmp_get_version_number(){	return WPGMP_VERSION; }
		
		
		/**
		 * Load all required core classes.
		 */
		private function wpgmp_load_files() {

			$coreInitialisationFile = plugin_dir_path( __FILE__ ) . 'core/class.initiate-core.php';
			if ( file_exists( $coreInitialisationFile ) ) {
				require_once $coreInitialisationFile;
			}

			// Load Plugin Files
			$plugin_files_to_include = array(
				'wpgmp-helper.php',
				'wpgmp-template.php',
				'wpgmp-controller.php',
				'wpgmp-model.php',
				'wpgmp-map-widget.php',
				'wpgmp-visual-composer.php'
			);

			foreach ( $plugin_files_to_include as $file ) {

				if ( file_exists( WPGMP_PLUGIN_CLASSES . $file ) ) {
					require_once WPGMP_PLUGIN_CLASSES . $file;
				}
			}
			// Load all modules.
			$core_modules = array( 'overview', 'location', 'map', 'group_map', 'drawing', 'route', 'permissions', 'settings', 'tools', 'post', 'extentions' );
			if ( is_array( $core_modules ) ) {
				foreach ( $core_modules as $module ) {

					$file = WPGMP_MODEL . $module . '/model.' . $module . '.php';

					if ( file_exists( $file ) ) {
						include_once $file;
						$class_name = 'WPGMP_Model_' . ucwords( $module );
						array_push( $this->modules, $class_name );
					}
				}
			}

		}
	}
}

new WPGMP_Google_Maps_Pro();
