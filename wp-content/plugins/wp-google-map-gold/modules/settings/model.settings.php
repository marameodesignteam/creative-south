<?php
/**
 * Class: WPGMP_Model_Settings
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Settings' ) ) {

	/**
	 * Setting model for Plugin Options.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Settings extends FlipperCode_Model_Base {
		/**
		 * Intialize Backup object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_manage_settings' => esc_html__( 'Settings', 'wpgmp-google-map' ),
			);
		}
		/**
		 * Add or Edit Operation.
		 */
		function save() {
			global $_POST;
			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

				die( 'Cheating...' );

			}

			$this->verify( $_POST );

			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}
			$extra_fields = array();
			if ( isset( $_POST['location_extrafields'] ) ) {
				foreach ( $_POST['location_extrafields'] as $index => $label ) {
					if ( $label != '' ) {
						$extra_fields[ $index ] = sanitize_text_field( wp_unslash( $label ) );
					}
				}
			}

			$meta_hide = array();
			if ( isset( $_POST['wpgmp_allow_meta'] ) && !empty( $_POST['wpgmp_allow_meta'] ) && is_array($_POST['wpgmp_allow_meta']) ) {
				foreach ( $_POST['wpgmp_allow_meta'] as $index => $label ) {
					if ( $label != '' ) {
						$meta_hide[ $index ] = sanitize_text_field( wp_unslash( $label ) );
					}
				}
			}
			$wpgmp_settings = array();

			$wpgmp_settings['wpgmp_language']         = sanitize_text_field( wp_unslash( $_POST['wpgmp_language'] ) );
			$wpgmp_settings['wpgmp_api_key']          = sanitize_text_field( wp_unslash( $_POST['wpgmp_api_key'] ) );
			$wpgmp_settings['wpgmp_scripts_place']    = sanitize_text_field( wp_unslash( $_POST['wpgmp_scripts_place'] ) );
			$wpgmp_settings['wpgmp_version']    = sanitize_text_field( wp_unslash( $_POST['wpgmp_version'] ) );

			$wpgmp_settings['wpgmp_scripts_minify']    = sanitize_text_field( wp_unslash( $_POST['wpgmp_scripts_minify'] ) );

			$wpgmp_settings['wpgmp_allow_meta']       = serialize( $meta_hide );

			if ( isset( $_POST['wpgmp_metabox_map'] ) ) {
				$wpgmp_settings['wpgmp_metabox_map']      = sanitize_text_field( wp_unslash( $_POST['wpgmp_metabox_map'] ) );
			} else {
				$wpgmp_settings['wpgmp_metabox_map'] = '';
			}

			if ( isset( $_POST['wpgmp_auto_fix'] ) ) {

				$wpgmp_settings['wpgmp_auto_fix']         = sanitize_text_field( wp_unslash( $_POST['wpgmp_auto_fix'] ) );
			} else {
				$wpgmp_settings['wpgmp_auto_fix']         = '';
			}

			if ( isset( $_POST['wpgmp_debug_mode'] ) ) {
				$wpgmp_settings['wpgmp_debug_mode']             = sanitize_text_field( wp_unslash( $_POST['wpgmp_debug_mode'] ) );
			} else {
				$wpgmp_settings['wpgmp_debug_mode']             = '';
			}

			if ( isset( $_POST['wpgmp_gdpr'] ) ) {
				$wpgmp_settings['wpgmp_gdpr']             = sanitize_text_field( wp_unslash( $_POST['wpgmp_gdpr'] ) );
			} else {
				$wpgmp_settings['wpgmp_gdpr']             = '';
			}

			$wpgmp_settings['wpgmp_gdpr_msg']         = wp_unslash( $_POST['wpgmp_gdpr_msg'] );

			if ( isset( $_POST['wpgmp_country_specific'] ) ) {
				$wpgmp_settings['wpgmp_country_specific'] = sanitize_text_field( wp_unslash( $_POST['wpgmp_country_specific'] ) );
			} else {
				$wpgmp_settings['wpgmp_country_specific'] = '';
			}

			if( isset($_POST['wpgmp_countries']) ) {
				$wpgmp_settings['wpgmp_countries']        = wp_unslash( $_POST['wpgmp_countries'] );
			}
			

			update_option( 'wpgmp_settings', $wpgmp_settings );
			update_option( 'wpgmp_location_extrafields', serialize( $extra_fields ) );

			$response['success'] = esc_html__( 'Setting(s) saved successfully.', 'wpgmp-google-map' );
			return $response;

		}
	}
}
