<?php
/**
 * @wordpress-plugin
 * Plugin Name: Filter Map Listing By ViewPort
 * Plugin URI: http://www.flippercode.com/
 * Description: A premium extention of advance google maps pro plugin that allows users to filter location based on map viewport.
 * Version: 1.0.0
 * Author: flippercode
 * Author URI: http://www.flippercode.com/
 * Text Domain: filter-map-listing-by-viewport
 * Domain Path: /lang/
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! class_exists( 'Filter_Map_Listing_By_ViewPort' ) ) {

	class Filter_Map_Listing_By_ViewPort {

		private $pluginUrl = '';
		private $pluginDir = '';
		private $proceed = false;
		
		public function __construct() { 

			$this->pluginUrl = plugin_dir_url( __FILE__ );
			$this->pluginDir = plugin_dir_path( __FILE__ );
			$this->fmlv_check_plugin_dependancy();
			if($this->proceed)
			$this->fmlv_register_hooks();
			
		}

		function fmlv_register_hooks() {

			add_action( 'plugins_loaded', array( $this, 'fmlv_load_plugin_languages' ) );
			add_action(	'wp_footer',array($this,'fmlv_hook_in_footer'));
			if(is_admin() ) {
				add_filter( 'wpgmp_input_field_save_entity_data',array($this,'fmlv_addon_custom_settings_html'));
			}			
			add_filter('wpgmp_map_data',array($this,'fmlv_enabling_layouts'),10,2);
		}


		function fmlv_enabling_layouts($map_data, $map){
			$mapExtentionData = $this->fmlv_get_extention_data($map->map_id);
			if( !empty($mapExtentionData['extensions_fields']['viewport_filter']['enable']) && $mapExtentionData['extensions_fields']['viewport_filter']['enable'] == 'true'){
				$map_data['viewportfilter'] = true;	
			}			
			return $map_data;
		}

		function fmlv_load_plugin_languages() {

			load_plugin_textdomain( 'filter-map-listing-by-viewport', false, $this->pluginDir.'/lang/' );
		}
		function fmlv_check_plugin_dependancy() {
				
			if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			
			//Advance GoogleMaps Pro Dependency
			$is_google_maps_installed = in_array( 'wp-google-map-gold/wp-google-map-gold.php',get_option('active_plugins' ) ) ;
			$is_google_maps_active = ( is_plugin_active_for_network( 'wp-google-map-gold/wp-google-map-gold.php' ) ) ? true : false;
			$this->googlemapsMissing = (!$is_google_maps_installed && !$is_google_maps_active) ? true : false;

			$this->proceed = (!($this->googlemapsMissing) ) ? true : false;
			if(!$this->proceed)
			add_action( 'admin_notices', array( $this, 'fmlv_admin_notices' ) );	
			
		}

		function fmlv_admin_notices() {
			
			if($this->googlemapsMissing)
			$this->fmlv_google_maps_missing();
			
		}

		function fmlv_google_maps_missing() { ?>
		    
		    <div class="notice notice-error">
		    	<p><a target="_blank" href="https://codecanyon.net/item/advanced-google-maps-plugin-for-wordpress/5211638"><?php esc_html_e('WP Google Maps Pro','filter-map-listing-by-viewport'); ?></a><?php esc_html_e( ' is required for Filter Map Listing By ViewPort. Please install and configure WP Google Maps Pro first.', 'filter-map-listing-by-viewport' ); ?></p>
		    </div>
		   <?php

		}


		function fmlv_get_extention_data( $current_map_id ) {

			$model_factory = new WPGMP_Model();
			$map_obj = $model_factory->create_object( 'map' );
			$map_data = $map_obj->fetch( array( array( 'map_id', '=', $current_map_id ) ) );
			$map_control_settings = $map_data[0]->map_all_control;
			$map_control_settings = maybe_unserialize($map_control_settings);
			$extention_data = $map_control_settings;
			return $extention_data;
		}


		function fmlv_addon_custom_settings_html($markup){

			$addon_settings_file = $this->pluginDir.'views/addon-settings.php';
			if ( file_exists( $addon_settings_file ) ) { 
				return require_once $addon_settings_file;
			}
			
		}
		
		function fmlv_hook_in_footer(){ 		
		  ?> <script src="<?php echo esc_url($this->pluginUrl . 'assets/js/wpgmp-viewport-filter.js'); ?>"></script>
		   <?php 
 		}

	}

	return new Filter_Map_Listing_By_ViewPort();

}




