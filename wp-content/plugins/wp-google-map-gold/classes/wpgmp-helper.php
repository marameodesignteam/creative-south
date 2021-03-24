<?php

class WPGMP_Helper{

	public static function wpgmp_get_all_post_types(){

		$screens        = array( 'post', 'page' );
		$args = array( 'public'   => true,'_builtin' => false );
		$custom_post_types = get_post_types( $args, 'names' );
		$screens = array_merge( $screens, $custom_post_types );
		return $screens;

	}

	public static function wpgmp_register_map_backend_resources(){

		$wpgmp_settings = get_option( 'wpgmp_settings', true );

		$wpgmp_apilocation = self::wpgmp_get_server_protocol();

		if ( $wpgmp_settings['wpgmp_api_key'] != '' ) {
			$wpgmp_apilocation .= '://maps.google.com/maps/api/js?key=' . $wpgmp_settings['wpgmp_api_key'] . '&libraries=geometry,places,weather,panoramio,drawing&language=en';
		} else {
			$wpgmp_apilocation .= '://maps.google.com/maps/api/js?libraries=geometry,places,weather,panoramio,drawing&language=en';
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
			'handle' => 'flippercode-datatable',
			'src'    => WPGMP_JS . 'vendor/datatables/datatables.js',
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'flippercode-webfont',
			'src'    => WPGMP_JS . 'vendor/webfont/webfont.js',
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'flippercode-select2',
			'src'    => WPGMP_JS . 'vendor/select2/select2.js',
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'flippercode-slick',
			'src'    => WPGMP_JS . 'vendor/slick/slick.js',
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'wpgmp-backend-google-maps',
			'src'    => WPGMP_JS . 'backend.js',
			'deps'   => array("flippercode-datatable","flippercode-webfont"),
		);

		$scripts[] = array(
			'handle' => 'wpgmp-backend-google-api',
			'src'    => $wpgmp_apilocation,
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'wpgmp-markercluster',
			'src'    => WPGMP_JS . 'vendor/markerclustererplus/markerclustererplus.js',
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'wpgmp-infobox',
			'src'    => WPGMP_JS . 'vendor/infobox/infobox.js',
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'wpgmp-accordion',
			'src'    => WPGMP_JS . 'vendor/accordion/accordion.js',
			'deps'   => array(),
			);

		$scripts[] = array(
			'handle' => 'wpgmp-map',
			'src'    => WPGMP_JS . 'maps.js',
			'deps'   => array("wpgmp-markercluster","wpgmp-infobox","wpgmp-accordion"),
		);

		$scripts[] = array(
			'handle' => 'flippercode-ui',
			'src'    => WPGMP_JS . 'flippercode-ui.js',
			'deps'   => array(),
		);


		if ( $scripts ) {
			foreach ( $scripts as $script ) {
				wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], '2.3.4' );
			}
		}

		$wpgmp_js_lang                    = array();
		$wpgmp_js_lang['ajax_url']        = admin_url( 'admin-ajax.php' );
		$wpgmp_js_lang['nonce']           = wp_create_nonce( 'fc-call-nonce' );
		$wpgmp_js_lang['confirm']         = esc_html__( 'Are you sure to delete item?', 'wpgmp-google-map' );
		$wpgmp_js_lang['text_editable']   = array( '.fc-text', '.fc-post-link', '.place_title', '.fc-item-content', '.wpgmp_locations_content' );
		$wpgmp_js_lang['bg_editable']     = array( '.fc-bg', '.fc-item-box', '.fc-pagination', '.wpgmp_locations' );
		$wpgmp_js_lang['margin_editable'] = array( '.fc-margin', '.fc-item-title', '.wpgmp_locations_head', '.fc-item-content', '.fc-item-meta' );
		$wpgmp_js_lang['full_editable']   = array( '.fc-css', '.fc-item-title', '.wpgmp_locations_head', '.fc-readmore-link', '.fc-item-meta', 'a.page-numbers', '.current', '.wpgmp_location_meta' );
		$wpgmp_js_lang['image_path']      = WPGMP_IMAGES;

		$wpgmp_js_lang['geocode_stats']   = esc_html__( 'locations geocoded', 'wpgmp-google-map' );
		$wpgmp_js_lang['geocode_success'] = esc_html__( 'Click below to save geocoded locations', 'wpgmp-google-map' );

		wp_localize_script( 'flippercode-ui', 'settings_obj', $wpgmp_js_lang );

		$wpgmp_local               = array();
		$wpgmp_local['language']   = 'en';
		$wpgmp_local['urlforajax'] = admin_url( 'admin-ajax.php' );
		$wpgmp_local['hide']       = esc_html__( 'Hide', 'wpgmp-google-map' );
		$wpgmp_local['nonce']      = wp_create_nonce( 'fc_communication' );

		if ( isset( $wpgmp_settings['wpgmp_country_specific'] ) ) {
			$wpgmp_local['wpgmp_country_specific'] = ( $wpgmp_settings['wpgmp_country_specific'] == 'true' );
		} else {
			$wpgmp_local['wpgmp_country_specific'] = false;
		}

		if ( isset( $wpgmp_settings['wpgmp_countries'] ) ) {
			$wpgmp_local['wpgmp_countries'] = $wpgmp_settings['wpgmp_countries'];
		} else {
			$wpgmp_local['wpgmp_countries'] = false;
		}

		wp_localize_script( 'wpgmp-map', 'wpgmp_local', $wpgmp_local );
		wp_localize_script( 'flippercode-ui', 'wpgmp_local', $wpgmp_local );

		$wpgmp_js_lang            = array();
		$wpgmp_js_lang['confirm'] = esc_html__( 'Are you sure to delete item?', 'wpgmp-google-map' );
		wp_localize_script( 'wpgmp-backend-google-maps', 'wpgmp_js_lang', $wpgmp_js_lang );
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
	}

	public static function wpgmp_register_map_frontend_resources(){

		$wpgmp_settings = get_option( 'wpgmp_settings', true );

		$auto_fix = $wpgmp_settings['wpgmp_auto_fix'];

		$scripts = array();

		if ( $auto_fix == 'true' ) {
			wp_enqueue_script( 'jquery' );
		}

		$wpgmp_apilocation = self::wpgmp_get_server_protocol();

		$language = $wpgmp_settings['wpgmp_language'];

		if ( $language == '' ) {
			$language = 'en';
		}

		$language = apply_filters( 'wpgmp_map_lang', $language );

		if ( isset( $wpgmp_settings['wpgmp_api_key'] ) and $wpgmp_settings['wpgmp_api_key'] != '' ) {
			$wpgmp_apilocation .= '://maps.google.com/maps/api/js?key=' . $wpgmp_settings['wpgmp_api_key'] . '&libraries=geometry,places,weather,panoramio,drawing&language=' . $language;
		} else {
			$wpgmp_apilocation .= '://maps.google.com/maps/api/js?libraries=geometry,places,weather,panoramio,drawing&language=' . $language;
		}

		$scripts[] = array(
			'handle' => 'wpgmp-google-api',
			'src'    => $wpgmp_apilocation,
			'deps'   => array(),
		);


		if( isset( $wpgmp_settings['wpgmp_scripts_minify']) && $wpgmp_settings['wpgmp_scripts_minify'] == 'yes') {

			$scripts[] = array(
				'handle' => 'wpgmp-frontend',
				'src'    => WPGMP_JS . 'frontend.min.js',
				'deps'   => array( 'jquery-masonry', 'imagesloaded' ),
			);

		} else {
			
			$scripts[] = array(
				'handle' => 'wpgmp-jscrollpane',
				'src'    => WPGMP_JS . 'vendor/jscrollpane/jscrollpane.js',
				'deps'   => array(),
			);

			$scripts[] = array(
			'handle' => 'wpgmp-accordion',
			'src'    => WPGMP_JS . 'vendor/accordion/accordion.js',
			'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'wpgmp-frontend',
				'src'    => WPGMP_JS . 'frontend.js',
				'deps'   => array( 'wpgmp-jscrollpane','jquery-masonry', 'imagesloaded','wpgmp-accordion', ),
			);

		}

		
		$where = $wpgmp_settings['wpgmp_scripts_place'];

		if ( $where == 'header' ) {
			$where = false;
		} else {
			$where = true;
		}

		if ( $wpgmp_settings['wpgmp_gdpr'] == true ) {
			$auto_fix = apply_filters( 'wpgmp_accept_cookies', false );
		}

		wp_enqueue_script('webfont', WPGMP_JS.'vendor/webfont/webfont.js', array(), WPGMP_VERSION, true );


		if ( $scripts ) {
			foreach ( $scripts as $script ) {
				if ( $auto_fix == 'true' ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], WPGMP_VERSION, $where );
				} else {
					wp_register_script( $script['handle'], $script['src'], $script['deps'], WPGMP_VERSION, $where );
				}
			}
		}

		$wpgmp_fjs_lang                     = array();
		$wpgmp_fjs_lang['ajax_url']         = admin_url( 'admin-ajax.php' );
		$wpgmp_fjs_lang['nonce']            = wp_create_nonce( 'fc-call-nonce' );
		if ( isset( $wpgmp_settings['wpgmp_days_to_remember'] ) ) {
			$wpgmp_fjs_lang['days_to_remember'] = $wpgmp_settings['wpgmp_days_to_remember'];
		} else {
			$wpgmp_fjs_lang['days_to_remember'] = '';
		}

		wp_localize_script( 'wpgmp-frontend', 'wpgmp_flocal', $wpgmp_fjs_lang );

		$wpgmp_local = self::wpgmp_get_localised_data();

		$wpgmp_local['nonce']  = wp_create_nonce( 'fc-call-nonce' );
		
		if ( isset( $wpgmp_settings['wpgmp_country_specific'] ) ) {
			$wpgmp_local['wpgmp_country_specific'] = ( $wpgmp_settings['wpgmp_country_specific'] == 'true' );
		} else {
			$wpgmp_local['wpgmp_country_specific'] = false;
		}

		if ( isset( $wpgmp_settings['wpgmp_countries'] ) ) {
			$wpgmp_local['wpgmp_countries'] = $wpgmp_settings['wpgmp_countries'];
		} else {
			$wpgmp_local['wpgmp_countries'] = false;
		}

		$wpgmp_local  = apply_filters( 'wpgmp_text_settings', $wpgmp_local );


		$scripts = array();

		if( isset( $wpgmp_settings['wpgmp_scripts_minify']) && $wpgmp_settings['wpgmp_scripts_minify'] == 'yes') {

			$scripts[] = array(
				'handle' => 'wpgmp-google-map-main',
				'src'    => WPGMP_JS . 'maps.min.js',
				'deps'   => array( 'wpgmp-google-api', 'jquery-masonry', 'imagesloaded' ),
			);	

		} else {

			$scripts[] = array(
			'handle' => 'wpgmp-markercluster',
			'src'    => WPGMP_JS . 'vendor/markerclustererplus/markerclustererplus.js',
			'deps'   => array(),
		);

		$scripts[] = array(
			'handle' => 'wpgmp-print',
			'src'    => WPGMP_JS . 'vendor/print/print.js',
			'deps'   => array(),
		);

		$scripts[] = array(
				'handle' => 'wpgmp-infobox',
				'src'    => WPGMP_JS . 'vendor/infobox/infobox.js',
				'deps'   => array(),
			);
		
		$scripts[] = array(
			'handle' => 'wpgmp-google-map-main',
			'src'    => WPGMP_JS . 'maps.js',
			'deps'   => array( 'wpgmp-google-api', 'jquery-masonry', 'imagesloaded', 'wpgmp-markercluster','wpgmp-print', 'wpgmp-infobox' ),
		);

		}

		

		if ( $scripts ) {
			foreach ( $scripts as $script ) {
				if ( $auto_fix == 'true' ) {
					wp_register_script( $script['handle'], $script['src'], $script['deps'], WPGMP_VERSION, $where );
				} else {
					wp_register_script( $script['handle'], $script['src'], $script['deps'], WPGMP_VERSION, $where );
				}
			}
		}

		wp_localize_script( 'wpgmp-google-map-main', 'wpgmp_local', $wpgmp_local );

		wp_enqueue_style( 'masonry' );

		if( isset( $wpgmp_settings['wpgmp_scripts_minify']) && $wpgmp_settings['wpgmp_scripts_minify'] == 'yes') {
						$frontend_styles = array(
							'wpgmp-frontend' => WPGMP_CSS . 'frontend.min.css',
						);
		} else {
						$frontend_styles = array(
						'wpgmp-frontend' => WPGMP_CSS . 'frontend.css',
					);	
		}	
	
		if ( $frontend_styles ) {
			foreach ( $frontend_styles as $frontend_style_key => $frontend_style_value ) {
				wp_register_style( $frontend_style_key, $frontend_style_value );
			}
		}

	}

	public static function wpgmp_get_localised_data(){

		$wpgmp_local                              = array();
		$wpgmp_local['select_radius']             = esc_html__( 'Select Radius', 'wpgmp-google-map' );
		$wpgmp_local['search_placeholder']        = esc_html__( 'Enter address or latitude or longitude or title or city or state or country or postal code here...', 'wpgmp-google-map' );
		$wpgmp_local['select']                    = esc_html__( 'Select', 'wpgmp-google-map' );
		$wpgmp_local['select_all']                = esc_html__( 'Select All', 'wpgmp-google-map' );
		$wpgmp_local['select_category']           = esc_html__( 'Select Category', 'wpgmp-google-map' );
		$wpgmp_local['all_location']              = esc_html__( 'All', 'wpgmp-google-map' );
		$wpgmp_local['show_locations']            = esc_html__( 'Show Locations', 'wpgmp-google-map' );
		$wpgmp_local['sort_by']                   = esc_html__( 'Sort by', 'wpgmp-google-map' );
		$wpgmp_local['wpgmp_not_working']         = esc_html__( 'not working...', 'wpgmp-google-map' );
		$wpgmp_local['place_icon_url']            = WPGMP_ICONS;
		$wpgmp_local['wpgmp_location_no_results'] = esc_html__( 'No results found.', 'wpgmp-google-map' );
		$wpgmp_local['wpgmp_route_not_avilable']  = esc_html__( 'Route is not available for your requested route.', 'wpgmp-google-map' );
		$wpgmp_local['img_grid']                  = "<span class='span_grid'><a class='wpgmp_grid'><img src='" . WPGMP_IMAGES . "grid.png'></a></span>";
		$wpgmp_local['img_list']                  = "<span class='span_list'><a class='wpgmp_list'><img src='" . WPGMP_IMAGES . "list.png'></a></span>";
		$wpgmp_local['img_print']                 = "<span class='span_print'><a class='wpgmp_print' data-action='wpgmp-print'><img src='" . WPGMP_IMAGES . "print.png'></a></span>";
		$wpgmp_local['hide']                      = esc_html__( 'Hide', 'wpgmp-google-map' );
		$wpgmp_local['show']                      = esc_html__( 'Show', 'wpgmp-google-map' );
		$wpgmp_local['start_location']            = esc_html__( 'Start Location', 'wpgmp-google-map' );
		$wpgmp_local['start_point']               = esc_html__( 'Start Point', 'wpgmp-google-map' );
		$wpgmp_local['radius']                    = esc_html__( 'Radius', 'wpgmp-google-map' );
		$wpgmp_local['end_location']              = esc_html__( 'End Location', 'wpgmp-google-map' );
		$wpgmp_local['take_current_location']     = esc_html__( 'Take Current Location', 'wpgmp-google-map' );
		$wpgmp_local['center_location_message']   = esc_html__( 'Your Location', 'wpgmp-google-map' );
		$wpgmp_local['center_location_message']   = esc_html__( 'Your Location', 'wpgmp-google-map' );
		$wpgmp_local['driving']                   = esc_html__( 'Driving', 'wpgmp-google-map' );
		$wpgmp_local['bicycling']                 = esc_html__( 'Bicycling', 'wpgmp-google-map' );
		$wpgmp_local['walking']                   = esc_html__( 'Walking', 'wpgmp-google-map' );
		$wpgmp_local['transit']                   = esc_html__( 'Transit', 'wpgmp-google-map' );
		$wpgmp_local['metric']                    = esc_html__( 'Metric', 'wpgmp-google-map' );
		$wpgmp_local['imperial']                  = esc_html__( 'Imperial', 'wpgmp-google-map' );
		$wpgmp_local['find_direction']            = esc_html__( 'Find Direction', 'wpgmp-google-map' );
		$wpgmp_local['miles']                     = esc_html__( 'Miles', 'wpgmp-google-map' );
		$wpgmp_local['km']                        = esc_html__( 'KM', 'wpgmp-google-map' );
		$wpgmp_local['show_amenities']            = esc_html__( 'Show Amenities', 'wpgmp-google-map' );
		$wpgmp_local['find_location']             = esc_html__( 'Find Locations', 'wpgmp-google-map' );
		$wpgmp_local['locate_me']                 = esc_html__( 'Locate Me', 'wpgmp-google-map' );
		$wpgmp_local['prev']                      = esc_html__( 'Prev', 'wpgmp-google-map' );
		$wpgmp_local['next']                      = esc_html__( 'Next', 'wpgmp-google-map' );
		$wpgmp_local['ajax_url']                  = admin_url( 'admin-ajax.php' );

		$wpgmp_local = apply_filters('wpgmp_update_default_placeholder', $wpgmp_local);
		
		return $wpgmp_local;


	}

	public static function wpgmp_get_server_protocol(){

		if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}  

		return $protocol;

	}

	public static function wpgmp_apply_placeholders( $content ){  

		 $data['marker_id']                 = 1;
		 $data['marker_title']              = 'New York, NY, United States';
		 $data['marker_address']            = 'New York, NY, United States';
		 $data['marker_message']            = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.';
		 $data['marker_category']           = 'Real Estate';
		 $data['marker_icon']               = WPGMP_IMAGES . 'default_marker.png';
		 $data['marker_latitude']           = '40.7127837';
		 $data['marker_longitude']          = '-74.00594130000002';
		 $data['marker_city']               = 'New York';
		 $data['marker_state']              = 'NY';
		 $data['marker_country']            = 'United States';
		 $data['marker_zoom']               = '5';
		 $data['marker_postal_code']        = '10002';
		 $data['extra_field_slug']          = 'color';
		 $data['marker_featured_image_src'] = WPGMP_IMAGES . 'sample.jpg';
		 $data['marker_image']              = '<img class="fc-item-featured_image  fc-item-large" src="' . WPGMP_IMAGES . 'sample.jpg' . '" />';
		 $data['marker_featured_image']     = '<img class="fc-item-featured_image  fc-item-large" src="' . WPGMP_IMAGES . 'sample.jpg' . '" />';
		 $data['post_title']                = 'Lorem ipsum dolor sit amet, consectetur';
		 $data['post_link']                 = '#';
		 $data['post_excerpt']              = 'Lorem ipsum dolor sit amet, consectetur';
		 $data['post_content']              = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
		 $data['post_categories']           = 'city tour';
		 $data['post_tags']                 = 'WordPress, plugins, google maps';
		 $data['post_featured_image']       = '<img class="fc-item-featured_image  fc-item-large" src="' . WPGMP_IMAGES . 'sample.jpg' . '" />';
		 $data['post_author']               = 'FlipperCode';
		 $data['post_comments']             = '<i class="fci fci-comment"></i> 10';
		 $data['view_count']                = '<i class="fci fci-heart"></i> 1';

		foreach ( $data as $key => $value ) {
			if ( strstr( $key, 'marker_featured_image_src' ) === false and strstr( $key, 'marker_icon' ) === false and strstr( $key, 'post_link' ) === false and strstr( $key, 'marker_zoom' ) === false and strstr( $key, 'marker_id' ) === false ) {
				$content = str_replace( "{{$key}}", $value . '<span class="fc-hidden-placeholder">{' . $key . '}</span>', $content );
			} else {
				$content = str_replace( "{{$key}}", $value, $content );
			}
		}

		return $content;

	}
	

}