<?php
/**
 * Class: WPGMP_Model_Post
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 5.2.1
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Post' ) ) {

	/**
	 * Create metabox views and related operations.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Post extends FlipperCode_Model_Base {
		/**
		 * Intialize Shortcode object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {	return array();	}

		function wpgmp_handle_metabox_submission($post_id){

			if ( isset( $_POST['wpgmp_hidden_flag'] ) ) { 

				$wpgmp_enter_location = $_POST['wpgmp_metabox_location_hidden'];

				$wpgmp_enter_city    = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_location_city'] ) );
				$wpgmp_enter_state   = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_location_state'] ) );
				$wpgmp_enter_country = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_location_country'] ) );

				$wpgmp_metabox_latitude          = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_latitude'] ) );
				$wpgmp_metabox_longitude         = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_longitude'] ) );
				$wpgmp_map_id                    = serialize( wp_unslash( $_POST['wpgmp_metabox_mapid'] ) );
				$wpgmp_metabox_marker_id         = serialize( wp_unslash( $_POST['wpgmp_metabox_marker_id'] ) );
				$wpgmp_metabox_location_redirect = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_location_redirect'] ) );
				$wpgmp_metabox_custom_link       = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_custom_link'] ) );
				$wpgmp_metabox_taxomomies_terms  = serialize( wp_unslash( $_POST['wpgmp_metabox_taxomomies_terms'] ) );
				$wpgmp_extensions_fields         = serialize( wp_unslash( $_POST['wpgmp_extensions_fields'] ) );

				// Update the meta field in the database.
				update_post_meta( $post_id, '_wpgmp_location_address', $wpgmp_enter_location );
				update_post_meta( $post_id, '_wpgmp_location_city', $wpgmp_enter_city );
				update_post_meta( $post_id, '_wpgmp_location_state', $wpgmp_enter_state );
				update_post_meta( $post_id, '_wpgmp_location_country', $wpgmp_enter_country );

				update_post_meta( $post_id, '_wpgmp_metabox_latitude', $wpgmp_metabox_latitude );
				update_post_meta( $post_id, '_wpgmp_metabox_longitude', $wpgmp_metabox_longitude );
				update_post_meta( $post_id, '_wpgmp_metabox_location_redirect', $wpgmp_metabox_location_redirect );
				update_post_meta( $post_id, '_wpgmp_metabox_custom_link', $wpgmp_metabox_custom_link );
				update_post_meta( $post_id, '_wpgmp_map_id', $wpgmp_map_id );
				update_post_meta( $post_id, '_wpgmp_metabox_marker_id', $wpgmp_metabox_marker_id );
				update_post_meta( $post_id, '_wpgmp_metabox_taxomomies_terms', $wpgmp_metabox_taxomomies_terms );
				update_post_meta( $post_id, '_wpgmp_extensions_fields', $wpgmp_extensions_fields );
			}

		}

	}
}
