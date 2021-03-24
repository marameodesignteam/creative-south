<?php
/**
 * Parse Shortcode and display maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

if ( isset( $options['id'] ) ) {
	$map_id = $options['id'];
} else {
	return '';
}

// Fetch map information.
$modelFactory = new WPGMP_Model();
$map_obj      = $modelFactory->create_object( 'map' );
$map_record   = $map_obj->fetch( array( array( 'map_id', '=', $map_id ) ) );

if ( ! is_array( $map_record ) || empty( $map_record ) ) {
	return '';
} else {
	$map = $map_record[0];
}

$wpgmp_settings = get_option( 'wpgmp_settings', true );

// Hook accept cookies
if ( $wpgmp_settings['wpgmp_gdpr'] == true ) {

	$auto_fix = apply_filters( 'wpgmp_accept_cookies', false );

	if ( $auto_fix == false ) {

		if ( isset( $wpgmp_settings['wpgmp_gdpr_msg'] ) and $wpgmp_settings['wpgmp_gdpr_msg'] != '' ) {
			return $wpgmp_settings['wpgmp_gdpr_msg'];
		} else {
			return apply_filters( 'wpgmp_nomap_notice', '', $map_id );
		}
	}
}



// End
if ( isset( $options['show'] ) ) {
	$show_option = $options['show'];
} else {
	$show_option = 'default';
}
$shortcode_filters = array();
if ( isset( $options['category'] ) ) {
	$shortcode_filters['category'] = $options['category'];
}


if ( ! empty( $map ) ) {
	$map->map_street_view_setting     = unserialize( $map->map_street_view_setting );
	$map->map_route_direction_setting = unserialize( $map->map_route_direction_setting );
	$map->map_all_control             = unserialize( $map->map_all_control );
	$map->map_info_window_setting     = unserialize( $map->map_info_window_setting );
	$map->style_google_map            = unserialize( $map->style_google_map );
	$map->map_locations               = unserialize( $map->map_locations );
	$map->map_layer_setting           = unserialize( $map->map_layer_setting );
	$map->map_polygon_setting         = unserialize( $map->map_polygon_setting );
	$map->map_polyline_setting        = unserialize( $map->map_polyline_setting );
	$map->map_cluster_setting         = unserialize( $map->map_cluster_setting );
	$map->map_overlay_setting         = unserialize( $map->map_overlay_setting );
	$map->map_infowindow_setting      = unserialize( $map->map_infowindow_setting );
	$map->map_geotags                 = unserialize( $map->map_geotags );
}

$category_obj          = $modelFactory->create_object( 'group_map' );
$categories            = $category_obj->fetch();
$all_categories        = array();
$all_child_categories  = array();
$all_parent_categories = array();
$all_categories_name   = array();
$route_obj             = $modelFactory->create_object( 'route' );
$all_routes            = $route_obj->fetch();
$location_obj          = $modelFactory->create_object( 'location' );
$marker_category_icons = array();


if ( ! empty( $categories ) ) {
	foreach ( $categories as $category ) {
		$all_categories[ $category->group_map_id ]                           = $category;
		$all_categories_name[ sanitize_title( $category->group_map_title ) ] = $category;
		$marker_category_icons[ $category->group_map_id ] = $category->group_marker;
		if ( $category->group_parent > 0 ) {
			$all_child_categories[ $category->group_map_id ]    = $category->group_parent;
			$all_parent_categories[ $category->group_parent ][] = $category->group_map_id;
		}
	}
}

if ( ! empty( $map->map_locations ) ) {
	$map_locations = $location_obj->fetch( array( array( 'location_id', 'IN', implode( ',', $map->map_locations ) ) ) );
}

$location_criteria = array(
	'show_all_locations' => false,
	'category__in'       => false,
	'limit'              => 0,
);

$location_criteria = apply_filters( 'wpgmp_location_criteria', $location_criteria, $map );

if ( isset( $options['show_all_locations'] ) and $options['show_all_locations'] == 'true' ) {
	$location_criteria['show_all_locations'] = true;
}

if ( isset( $options['limit'] ) and $options['limit'] > 0 ) {
	$location_criteria['limit'] = $options['limit'];
} elseif ( isset( $_GET['limit'] ) and $map->map_all_control['url_filter'] == 'true' ) {
	$location_criteria['limit'] = sanitize_text_field( $_GET['limit'] );
}

if ( isset( $location_criteria['show_all_locations'] ) and $location_criteria['show_all_locations'] == true ) {
	$map_locations = $location_obj->fetch();
}


if ( isset( $location_criteria['category__in'] ) and is_array( $location_criteria['category__in'] ) ) {
	$shortcode_filters['category'] = implode( ',', $location_criteria['category__in'] );
}

// Routes data.
if ( ! empty( $all_routes ) ) {
	$routes_data = array();
	foreach ( $all_routes as $route ) {
		$routes_data[ $route->route_id ] = $route;
	}
}
$map_data = array();
// Set map options.
$map_data['places'] = array();
if ( $map->map_all_control['infowindow_openoption'] == 'mouseclick' ) {
	$map->map_all_control['infowindow_openoption'] = 'click';
} elseif ( $map->map_all_control['infowindow_openoption'] == 'mousehover' ) {
	$map->map_all_control['infowindow_openoption'] = 'mouseover';
} elseif ( $map->map_all_control['infowindow_openoption'] == 'mouseover' ) {
	$map->map_all_control['infowindow_openoption'] = 'mouseover';
} else {
	$map->map_all_control['infowindow_openoption'] = 'click';
}

$infowindow_setting = isset($map->map_all_control['infowindow_setting'])? $map->map_all_control['infowindow_setting']:array();

$infowindow_sourcecode = apply_filters( 'wpgmp_infowindow_message',$infowindow_setting , $map );

$infowindow_geotags_setting = isset($map->map_all_control['infowindow_geotags_setting'])? $map->map_all_control['infowindow_geotags_setting']:array();


$infowindow_post_view_source = apply_filters( 'wpgmp_infowindow_post_message',$infowindow_geotags_setting , $map );

$wpgmp_categorydisplayformat = isset($map->map_all_control['wpgmp_categorydisplayformat'])? $map->map_all_control['wpgmp_categorydisplayformat']:array();


$listing_placeholder_content = apply_filters( 'wpgmp_listing_html', $wpgmp_categorydisplayformat, $map );


if ( ( is_single() or is_page() ) && isset( $map->map_all_control['current_post'] ) && $map->map_all_control['current_post'] == 'true' ) {
	global $post;

	$post_center_lat = get_post_meta( $post->ID, '_wpgmp_metabox_latitude', true );
	$post_center_lng = get_post_meta( $post->ID, '_wpgmp_metabox_longitude', true );

	if ( $post_center_lat != '' ) {
		$map->map_all_control['map_center_latitude'] = $post_center_lat;
	}

	if ( $post_center_lng != '' ) {
		$map->map_all_control['map_center_longitude'] = $post_center_lng;
	}
}

if ( isset( $_GET['zoom'] ) and $map->map_all_control['url_filter'] == 'true' ) {
	$options['zoom'] = sanitize_text_field( $_GET['zoom'] );
}

if ( ! isset( $map->map_all_control['nearest_location'] ) ) {
	$map->map_all_control['nearest_location'] = false;
}

if ( ! isset( $map->map_all_control['fit_bounds'] ) ) {
	$map->map_all_control['fit_bounds'] = false;
}

if ( ! isset( $map->map_all_control['show_center_circle'] ) ) {
	$map->map_all_control['show_center_circle'] = false;
}

if ( ! isset( $map->map_all_control['show_center_marker'] ) ) {
	$map->map_all_control['show_center_marker'] = false;
}

if ( ! isset( $map->map_all_control['map_draggable'] ) ) {
	$map->map_all_control['map_draggable'] = true;
}

if ( ! isset( $map->map_all_control['infowindow_bounce_animation'] ) ) {
	$map->map_all_control['infowindow_bounce_animation'] = '';
}

if ( ! isset( $map->map_all_control['infowindow_drop_animation'] ) ) {
	$map->map_all_control['infowindow_drop_animation'] = false;
}

if ( ! isset( $map->map_all_control['infowindow_close'] ) ) {
	$map->map_all_control['infowindow_close'] = false;
}

if ( ! isset( $map->map_all_control['infowindow_open'] ) ) {
	$map->map_all_control['infowindow_open'] = false;
}


if ( ! isset( $map->map_all_control['infowindow_filter_only'] ) ) {
	$map->map_all_control['infowindow_filter_only'] = false;
}

if ( ! isset( $map->map_all_control['infowindow_iscenter'] ) ) {
	$map->map_all_control['infowindow_iscenter'] = false;
}


if ( ! isset( $map->map_all_control['full_screen_control'] ) ) {
	$map->map_all_control['full_screen_control'] = false;
}


if ( ! isset( $map->map_all_control['search_control'] ) ) {
	$map->map_all_control['search_control'] = false;
}

if ( ! isset( $map->map_all_control['zoom_control'] ) ) {
	$map->map_all_control['zoom_control'] = false;
}

if ( ! isset( $map->map_all_control['map_type_control'] ) ) {
	$map->map_all_control['map_type_control'] = false;
}

if ( ! isset( $map->map_all_control['street_view_control'] ) ) {
	$map->map_all_control['street_view_control'] = false;
}


if ( ! isset( $map->map_all_control['locateme_control'] ) ) {
	$map->map_all_control['locateme_control'] = false;
}


if ( ! isset( $map->map_all_control['mobile_specific'] ) ) {
	$map->map_all_control['mobile_specific'] = false;
}

if ( ! isset( $map->map_all_control['mobile_specific'] ) ) {
	$map->map_all_control['mobile_specific'] = false;
}

if ( ! isset( $map->map_all_control['map_zoom_level_mobile'] ) ) {
	$map->map_all_control['map_zoom_level_mobile'] = 5;
}

if ( ! isset( $map->map_all_control['map_draggable_mobile'] ) ) {
	$map->map_all_control['map_draggable_mobile'] = true;
}

if ( ! isset( $map->map_all_control['map_scrolling_wheel_mobile'] ) ) {
	$map->map_all_control['map_scrolling_wheel_mobile'] = true;
}

if ( ! isset( $map->map_all_control['map_custom_control'] ) ) {
	$map->map_all_control['map_custom_control'] = false;
}

if ( ! isset( $map->map_all_control['map_infowindow_customisations'] ) ) {
	$map->map_all_control['map_infowindow_customisations'] = false;
}

if ( ! isset( $map->map_all_control['show_infowindow_header'] ) ) {
	$map->map_all_control['show_infowindow_header'] = false;
}

if ( ! isset( $map->map_all_control['url_filter'] ) ) {
	$map->map_all_control['url_filter'] = false;
}

if ( isset( $map->map_all_control['doubleclickzoom'] ) ) {
	$map->map_all_control['doubleclickzoom'] = true;
}

if ( ! isset( $map->map_all_control['bound_map_after_filter'] ) ) {
	$map->map_all_control['bound_map_after_filter'] = false;
}

if ( ! isset( $map->map_all_control['display_reset_button'] ) ) {
	$map->map_all_control['display_reset_button'] = false;
}




$map_data['map_options'] = array(
	'center_lat'                     => sanitize_text_field( $map->map_all_control['map_center_latitude'] ),
	'center_lng'                     => sanitize_text_field( $map->map_all_control['map_center_longitude'] ),
	'zoom'                           => ( isset( $options['zoom'] ) ) ? intval( $options['zoom'] ) : intval( $map->map_zoom_level ),
	'map_type_id'                    => sanitize_text_field( $map->map_type ),
	'center_by_nearest'              => ( 'true' == sanitize_text_field( $map->map_all_control['nearest_location'] ) ),
	'fit_bounds'                     => ( 'true' == sanitize_text_field( $map->map_all_control['fit_bounds'] ) ),
	'center_circle_fillcolor'        => sanitize_text_field( $map->map_all_control['center_circle_fillcolor'] ),
	'center_circle_fillopacity'      => sanitize_text_field( $map->map_all_control['center_circle_fillopacity'] ),
	'center_circle_strokecolor'      => sanitize_text_field( $map->map_all_control['center_circle_strokecolor'] ),
	'center_circle_strokeopacity'    => sanitize_text_field( $map->map_all_control['center_circle_strokeopacity'] ),
	'center_circle_radius'           => sanitize_text_field( $map->map_all_control['center_circle_radius'] ),
	'show_center_circle'             => ( sanitize_text_field( $map->map_all_control['show_center_circle'] ) == 'true' ),
	'show_center_marker'             => ( sanitize_text_field( $map->map_all_control['show_center_marker'] ) == 'true' ),
	'center_marker_icon'             => esc_url( $map->map_all_control['marker_center_icon'] ),
	'center_marker_infowindow'       => wpautop( wp_unslash( $map->map_all_control['show_center_marker_infowindow'] ) ),
	'center_circle_strokeweight'     => sanitize_text_field( $map->map_all_control['center_circle_strokeweight'] ),
	'draggable'                      => ( sanitize_text_field( $map->map_all_control['map_draggable'] ) != 'false' ),
	'scroll_wheel'                   => ( isset($map->map_scrolling_wheel) ? $map->map_scrolling_wheel : false ),

	'display_45_imagery'             => sanitize_text_field( $map->map_45imagery ),
	'gesture'                        => sanitize_text_field( $map->map_all_control['gesture'] ),
	'marker_default_icon'            => esc_url( $map->map_all_control['marker_default_icon'] ),
	'infowindow_setting'             => wpautop( wp_unslash( $infowindow_sourcecode ) ),
	'infowindow_geotags_setting'     => wpautop( wp_unslash( $infowindow_post_view_source ) ),
	'infowindow_skin'                => ( isset( $map->map_all_control['location_infowindow_skin'] ) ) ? $map->map_all_control['location_infowindow_skin'] : array(
		'name'       => 'default',
		'type'       => 'infowindow',
		'sourcecode' => $infowindow_sourcecode,
	),
	'infowindow_post_skin'           => ( isset( $map->map_all_control['post_infowindow_skin'] ) ) ? $map->map_all_control['post_infowindow_skin'] : array(
		'name'       => 'default',
		'type'       => 'post',
		'sourcecode' => $infowindow_post_view_source,
	),
	'infowindow_bounce_animation'    => $map->map_all_control['infowindow_bounce_animation'],
	'infowindow_drop_animation'      => ( 'true' == $map->map_all_control['infowindow_drop_animation'] ),
	'close_infowindow_on_map_click'  => ( 'true' == $map->map_all_control['infowindow_close'] ),
	'default_infowindow_open'        => ( 'true' == $map->map_all_control['infowindow_open'] ),
	'infowindow_open_event'          => ( $map->map_all_control['infowindow_openoption'] ) ? $map->map_all_control['infowindow_openoption'] : 'click',
	'infowindow_filter_only'         => ( $map->map_all_control['infowindow_filter_only'] == 'true' ),
	'infowindow_click_change_zoom'   => (int) $map->map_all_control['infowindow_zoomlevel'],
	'infowindow_click_change_center' => ( 'true' == $map->map_all_control['infowindow_iscenter'] ),
	'full_screen_control'            => ( $map->map_all_control['full_screen_control'] != 'false' ),
	'search_control'                 => ( $map->map_all_control['search_control'] != 'false' ),
	'zoom_control'                   => ( $map->map_all_control['zoom_control'] != 'false' ),
	'map_type_control'               => ( $map->map_all_control['map_type_control'] != 'false' ),
	'street_view_control'            => ( $map->map_all_control['street_view_control'] != 'false' ),
	'locateme_control'               => ( $map->map_all_control['locateme_control'] == 'true' ),
	'mobile_specific'                => ( $map->map_all_control['mobile_specific'] == 'true' ),
	'zoom_mobile'                    => intval( $map->map_all_control['map_zoom_level_mobile'] ),
	'draggable_mobile'               => ( sanitize_text_field( $map->map_all_control['map_draggable_mobile'] ) != 'false' ),
	'scroll_wheel_mobile'            => ( sanitize_text_field( $map->map_all_control['map_scrolling_wheel_mobile'] ) != 'false' ),
	'full_screen_control_position'   => $map->map_all_control['full_screen_control_position'],
	'search_control_position'        => $map->map_all_control['search_control_position'],
	'locateme_control_position'      => $map->map_all_control['locateme_control_position'],
	'zoom_control_position'          => $map->map_all_control['zoom_control_position'],
	'map_type_control_position'      => $map->map_all_control['map_type_control_position'],
	'map_type_control_style'         => $map->map_all_control['map_type_control_style'],
	'street_view_control_position'   => $map->map_all_control['street_view_control_position'],
	'map_control'                    => ( $map->map_all_control['map_custom_control'] == 'true' ),
	'map_control_settings'           => $map->map_all_control['map_control_settings'],
	'screens'                        => $map->map_all_control['screens'],
	'map_infowindow_customisations'  => ( $map->map_all_control['map_infowindow_customisations'] == 'true' ),
	'infowindow_width'               => ( empty( $map->map_all_control['infowindow_width'] ) || $map->map_all_control['infowindow_width'] == '0' ) ? '100%' : $map->map_all_control['infowindow_width'] . 'px',
	'infowindow_border_color'        => ( $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) : 'rgba(0, 0, 0, 0.0980392)',
	'infowindow_bg_color'            => ( $map->map_all_control['infowindow_bg_color'] != '' && $map->map_all_control['infowindow_bg_color'] != '#' ) ? sanitize_text_field( $map->map_all_control['infowindow_bg_color'] ) : '#fff',
	'show_infowindow_header'         => ( $map->map_all_control['show_infowindow_header'] == 'true' ),
	'min_zoom'                       => $map->map_all_control['map_minzoom_level'],
	'max_zoom'                       => $map->map_all_control['map_maxzoom_level'],
	'zoom_level_after_search'        => isset($map->map_all_control['zoom_level_after_search']) ? $map->map_all_control['zoom_level_after_search'] : 10,
	'url_filters'                    => ( $map->map_all_control['url_filter'] == 'true' ),
	'doubleclickzoom' 				 => (isset($map->map_all_control['doubleclickzoom']) ? $map->map_all_control['doubleclickzoom'] : false
	),
);

if ( $map->map_all_control['map_infowindow_customisations'] == 'true' ) {
	?>
<style type="text/css">
#map<?php echo esc_attr( $map_id ); ?> .wpgmp_infowindow .wpgmp_iw_head, #map<?php echo esc_attr( $map_id ); ?> .post_body .geotags_link, #map<?php echo esc_attr( $map_id ); ?> .post_body .geotags_link a{
height: 28px;
font-weight: 600;
line-height: 27px;
font-size:16px;
	<?php echo esc_attr( ( $map->map_all_control['infowindow_header_font_color'] != '' && $map->map_all_control['infowindow_header_font_color'] != '#' ) ? 'color: ' . sanitize_text_field( $map->map_all_control['infowindow_header_font_color'] ) . ';' : 'color:#fff;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_header_bgcolor'] != '' && $map->map_all_control['infowindow_header_bgcolor'] != '#' ) ? 'background-color: ' . sanitize_text_field( $map->map_all_control['infowindow_header_bgcolor'] ) . ';' : 'background-color:#3498db;' ); ?>
}
#map<?php echo esc_attr( $map_id ); ?> .wpgmp_infowindow .wpgmp_iw_head_content, .wpgmp_infowindow .wpgmp_iw_content, #map<?php echo esc_attr( $map_id ); ?> .post_body .geotags_link{padding-left:5px;}
#map<?php echo esc_attr( $map_id ); ?> .wpgmp_infowindow .wpgmp_iw_content{
min-height: 50px!important;
min-width: 150px!important;
padding-top:5px;
}
#map<?php echo esc_attr( $map_id ); ?> .wpgmp_infowindow, #map<?php echo esc_attr( $map_id ); ?> .post_body{
float: left;
position: relative;
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? 'box-shadow: ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) . ' 0px 1px 4px -1px;' : 'box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? 'border: 1px solid ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) . ';' : 'border: 1px solid rgba(0, 0, 0, 0);' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_bg_color'] != '' && $map->map_all_control['infowindow_bg_color'] != '#' ) ? 'background-color: ' . sanitize_text_field( $map->map_all_control['infowindow_bg_color'] ) . ';' : 'background-color:#fff;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_radius'] != '' ) ? 'border-radius: ' . sanitize_text_field( $map->map_all_control['infowindow_border_radius'] ) . 'px;' : 'border-radius:3px;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_width'] != '' ) ? 'width: ' . sanitize_text_field( $map->map_all_control['infowindow_width'] ) . 'px;' : '' ); ?>
}
#map<?php echo esc_attr( $map_id ); ?> .wpgmp_infowindow{float:none;}

#map<?php echo esc_attr( $map_id ); ?> .infoBoxTail:after{
<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && ($map->map_all_control['infowindow_border_color'] != '#') ) ? 'border-top-color : ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) : 'border-top-color: '.sanitize_text_field($map->map_all_control['infowindow_bg_color'] ) ); ?>
}

</style>
	<?php
}

$map_data['map_options']['bound_map_after_filter'] = ( 'true' == $map->map_all_control['bound_map_after_filter'] );
$map_data['map_options']['display_reset_button']   = ( 'true' == $map->map_all_control['display_reset_button'] );
$map_data['map_options']['map_reset_button_text']  = $map->map_all_control['map_reset_button_text'];

$map_data['map_options']['width'] = sanitize_text_field( $map->map_width );

$map_data['map_options']['width'] = sanitize_text_field( $map->map_width );

$map_data['map_options']['height'] = sanitize_text_field( $map->map_height );

$map_data['map_options'] = apply_filters( 'wpgmp_maps_options', $map_data['map_options'], $map );

if ( isset( $options['width'] ) and $options['width'] != '' ) {
	$map_data['map_options']['width'] = $options['width'];
}

if ( isset( $options['height'] ) and $options['height'] != '' ) {
	$map_data['map_options']['height'] = $options['height'];
}


if ( isset( $map_data['map_options']['width'] ) ) {
	$width = $map_data['map_options']['width'];
} else {
	$width = '100%'; }

if ( isset( $map_data['map_options']['height'] ) ) {
	$height = $map_data['map_options']['height'];
} else {
	$height = '300px'; }

if ( '' != $width and strstr( $width, '%' ) === false ) {
	$width = str_replace( 'px', '', $width ) . 'px';
}

if ( '' == $width ) {
	$width = '100%';
}
if ( strstr( $height, '%' ) === false ) {
	$height = str_replace( 'px', '', $height ) . 'px';
} else {
	$height = str_replace( '%', '', $height ) . 'px';
}


wp_enqueue_script( 'wpgmp-google-api' );
wp_enqueue_script( 'wpgmp-google-map-main' );
wp_enqueue_script( 'wpgmp-frontend' );
wp_enqueue_script( 'wpgmp-infobox' );
wp_enqueue_style( 'wpgmp-frontend' );
do_action( 'wpgmp_load_scripts_styles' );

if ( !empty( $map->map_all_control['location_infowindow_skin'] ) and is_array( $map->map_all_control['location_infowindow_skin'] )  ) {
	$skin_data = $map->map_all_control['location_infowindow_skin'];
	$css_file  = WPGMP_URL . 'templates/' . $skin_data['type'] . '/' . $skin_data['name'] . '/' . $skin_data['name'] . '.css';
	wp_enqueue_style( 'fc-wpgmp-' . $skin_data['type'] . '-' . $skin_data['name'], $css_file );
}

if ( !empty( $map->map_all_control['post_infowindow_skin'] ) and is_array( $map->map_all_control['post_infowindow_skin'] ) ) {
	$skin_data = $map->map_all_control['post_infowindow_skin'];
	$css_file  = WPGMP_URL . 'templates/' . $skin_data['type'] . '/' . $skin_data['name'] . '/' . $skin_data['name'] . '.css';
	wp_enqueue_style( 'fc-wpgmp-' . $skin_data['type'] . '-' . $skin_data['name'], $css_file );
}

if ( !empty( $map->map_all_control['item_skin'] ) and is_array( $map->map_all_control['item_skin'] ) ) {
	$skin_data = $map->map_all_control['item_skin'];
	$css_file  = WPGMP_URL . 'templates/' . $skin_data['type'] . '/' . $skin_data['name'] . '/' . $skin_data['name'] . '.css';
	wp_enqueue_style( 'fc-wpgmp-' . $skin_data['type'] . '-' . $skin_data['name'], $css_file );
}

$map_custom_filters = array();
if ( isset( $map->map_all_control['wpgmp_display_custom_filters'] ) && $map->map_all_control['wpgmp_display_custom_filters'] == 'true' ) {
	$map_custom_filters = array_map( array( $map_obj, 'wpgmp_array_map' ), $map->map_all_control['custom_filters'] );
	$map_custom_filters = array_map( 'trim', $map_custom_filters );
}

if ( isset( $map_locations ) && is_array( $map_locations ) ) {

	$added_extra_fields = unserialize( get_option( 'wpgmp_location_extrafields' ) );
	$loc_count          = 0;
	foreach ( $map_locations as $location ) {
		$location_categories = array();
		$is_continue         = true;
		if ( empty( $location->location_group_map ) ) {
			$location_categories[] = array(
				'id'               => '',
				'name'             => 'Uncategories',
				'type'             => 'category',
				'extension_fields' => $loc_category->extensions_fields,
				'icon'             => WPGMP_ICONS . 'marker_default_icon.png',
			);
		} else {

			foreach ( $location->location_group_map as $key => $loc_category_id ) {
				
				if( isset($all_categories[ $loc_category_id ]) ) {
					$loc_category = $all_categories[ $loc_category_id ];
					$location_categories[] = array(
						'id'               => $loc_category->group_map_id,
						'name'             => $loc_category->group_map_title,
						'type'             => 'category',
						'extension_fields' => $loc_category->extensions_fields,
						'icon'             => $loc_category->group_marker,
					);	
				}
			}
		}


		// Extra Fields in location.
		$extra_fields          = array();
		$location_extra_fields = array();
		$extra_fields_filters  = array();
		if ( isset( $added_extra_fields ) ) {
			foreach ( $added_extra_fields as $i => $label ) {
				$field_name                  = sanitize_title( $label );
				if( isset($location->location_extrafields[ $field_name ]) ) {
					$extra_fields[ $field_name ] = $location->location_extrafields[ $field_name ];
				}
				
				if ( array_search( '{' . $field_name . '}', $map_custom_filters ) !== false ) {
					$values = array();
					if ( isset($location->location_extrafields[ $field_name ]) && strpos( $location->location_extrafields[ $field_name ], ',' ) !== false ) {
						$values = explode( ',', $location->location_extrafields[ $field_name ] );
					}
					if ( ! empty( $values ) ) {
						foreach ( $values as $k => $val ) :
							if ( isset($extra_fields_filters[ $field_name ]) && is_array($extra_fields_filters[ $field_name ]) && ! in_array( trim( $val ), $extra_fields_filters [ $field_name ] ) && trim( $val ) != '' && trim( $val ) != null ) {
								$extra_fields_filters[ $field_name ][] = trim( $val );
							}
							$location_extra_fields[ $field_name ][] = trim( $val );
			endforeach;
					} elseif ( isset($extra_fields_filters[ $field_name ]) && is_array($extra_fields_filters[ $field_name ]) && ! in_array( trim( $location->location_extrafields[ $field_name ] ), $extra_fields_filters [ $field_name ] ) ) {
						$extra_fields_filters[ $field_name ][]  = trim( $location->location_extrafields[ $field_name ] );
						$location_extra_fields[ $field_name ][] = trim( $location->location_extrafields[ $field_name ] );
					} else {
						$location_extra_fields[ $field_name ][] = (isset($location->location_extrafields[ $field_name ])) ? trim( $location->location_extrafields[ $field_name ] ) : '';
					}
				}
			}
		}
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $name => $term ) {
				$name = trim( $name );
				foreach ( $term as $t ) {
					if ( array_search( '{' . $name . '}', $map_custom_filters ) !== false && isset( $location->location_settings[ 'teaxonomy_' . $name . '_terms' ] ) && in_array( $t[0], $location->location_settings[ 'teaxonomy_' . $name . '_terms' ] ) ) {
						$extra_fields_filters[ $name ][] = trim( $t[1] );
					}
				}
			}
		}


		ksort( $extra_fields_filters );
		if ( is_array( $location_categories ) ) {
			$high_order = 0;
			foreach ( $location_categories as $cat_order ) {
				if ( $cat_order['extension_fields']['cat_order'] ) {
					if ( $cat_order['extension_fields']['cat_order'] > $high_order ) {
						$high_order = $cat_order['extension_fields']['cat_order'];
					}
				}
			}
			$extra_fields['listorder'] = $high_order;
		} else {
			$extra_fields['listorder'] = 0;
		}

		$onclick = isset( $location->location_settings['onclick'] ) ? $location->location_settings['onclick'] : 'marker';

		if ( isset( $location->location_settings['featured_image'] ) and $location->location_settings['featured_image'] != '' ) {
			$marker_image = "<div class='fc-feature-img'><img alt='" . esc_attr( $location->location_title ) . "' src='" . $location->location_settings['featured_image'] . "' class='wpgmp_marker_image fc-item-featured_image fc-item-large' /></div>";
		} else {
			$marker_image = '';
		}

		if( !isset($location->location_settings['hide_infowindow']) ) {
			$location->location_settings['hide_infowindow'] = false;
		}

		$cats_with_order_id = array();

		$c_icon = isset( $location_categories[0]['icon'] ) ? $location_categories[0]['icon'] : $map_data['map_options']['marker_default_icon'];

		foreach($location_categories as $key1 => $cat) {

			if(!empty($cat['extension_fields']['cat_order'])){
				$cats_with_order_id[$key1] = $cat['extension_fields']['cat_order'];	
			}
		}

		if(!empty($cats_with_order_id) && count($cats_with_order_id)>0){
			$top_priority_key = min(array_keys($cats_with_order_id, min($cats_with_order_id)));
			$c_icon = isset( $location_categories[$top_priority_key]['icon'] ) ? $location_categories[$top_priority_key]['icon'] : $map_data['map_options']['marker_default_icon'];

		}

		$map_data['places'][ $loc_count ] = array(
			'id'             => $location->location_id,
			'title'          => $location->location_title,
			'address'        => $location->location_address,
			'source'         => 'manual',
			'content'        => ( '' != $location->location_messages ) ? do_shortcode( stripcslashes( $location->location_messages ) ) : '',
			'location'       => array(
				'icon'                    => $c_icon ,
				'lat'                     => $location->location_latitude,
				'lng'                     => $location->location_longitude,
				'city'                    => $location->location_city,
				'state'                   => $location->location_state,
				'country'                 => $location->location_country,
				'onclick_action'          => $onclick,
				'redirect_custom_link'    => isset($location->location_settings['redirect_link']) ? $location->location_settings['redirect_link'] : '',
				'marker_image'            => $marker_image,
				'open_new_tab'            => isset($location->location_settings['redirect_link_window']) ? $location->location_settings['redirect_link_window'] : '',
				'postal_code'             => $location->location_postal_code,
				'draggable'               => ( 'true' == $location->location_draggable ),
				'infowindow_default_open' => ( 'true' == $location->location_infowindow_default_open ),
				'animation'               => $location->location_animation,
				'infowindow_disable'      => ( $location->location_settings['hide_infowindow'] !== 'false' ),
				'zoom'                    => 5,
				'extra_fields'            => $extra_fields,
			),
			'categories'     => $location_categories,
			'custom_filters' => $extra_fields_filters,
		);

		$loc_count++;
	}
}

// KML Layer.
if ( ! empty( $map->map_layer_setting['choose_layer']['kml_layer'] ) && $map->map_layer_setting['choose_layer']['kml_layer'] == 'KmlLayer' ) {
	if ( strpos( $map->map_layer_setting['map_links'], ',' ) !== false ) {
		$kml_layers_links = explode( ',', $map->map_layer_setting['map_links'] );
	} else {
		$kml_layers_links = array( $map->map_layer_setting['map_links'] );
		$new_kml_links    = array();
		foreach ( $kml_layers_links as $kml ) {
			$new_kml_links[] = add_query_arg( 'x', time(), $kml );
		}
		$kml_layers_links = $new_kml_links;
	}

	$map_data['kml_layer'] = array(
		'kml_layers_links' => $kml_layers_links,
	);

	$map_data['kml_layer'] = apply_filters( 'wpgmp_kml_layer', $map_data['kml_layer'], $map );

}
// Fusion Layer.
if ( ! empty( $map->map_layer_setting['choose_layer']['fusion_layer'] ) && $map->map_layer_setting['choose_layer']['fusion_layer'] == 'FusionTablesLayer' ) {
	$map_data['fusion_layer'] = array(
		'fusion_table_select' => $map->map_layer_setting['fusion_select'],
		'fusion_table_from'   => $map->map_layer_setting['fusion_from'],
		'fusion_icon_name'    => $map->map_layer_setting['fusion_icon_name'],
		'fusion_heat_map'     => ( $map->map_layer_setting['heat_map'] === 'true' ? true : false ),
	);

	$map_data['fusion_layer'] = apply_filters( 'wpgmp_fusion_layer', $map_data['fusion_layer'], $map );

}

if ( ! empty( $map->map_layer_setting['choose_layer']['bicycling_layer'] ) && $map->map_layer_setting['choose_layer']['bicycling_layer'] == 'BicyclingLayer' ) {
	$map_data['bicyle_layer'] = array(
		'display_layer' => true,
	);

	$map_data['bicycling_layer'] = apply_filters( 'wpgmp_bicycling_layer', $map_data['bicyle_layer'], $map );

}

if ( ! empty( $map->map_layer_setting['choose_layer']['traffic_layer'] ) && $map->map_layer_setting['choose_layer']['traffic_layer'] == 'TrafficLayer' ) {
	$map_data['traffic_layer'] = array(
		'display_layer' => true,
	);

	$map_data['traffic_layer'] = apply_filters( 'wpgmp_traffic_layer', $map_data['traffic_layer'], $map );

}

if ( ! empty( $map->map_layer_setting['choose_layer']['transit_layer'] ) && $map->map_layer_setting['choose_layer']['transit_layer'] == 'TransitLayer' ) {
	$map_data['transit_layer'] = array(
		'display_layer' => true,
	);

	$map_data['transit_layer'] = apply_filters( 'wpgmp_transit_layer', $map_data['transit_layer'], $map );

}
// Geo tags for google maps pro.
if ( ! empty( $map->map_all_control['geo_tags'] ) && $map->map_all_control['geo_tags'] == 'true' ) {
	$geo_filters = array_filter( $map->map_geotags );
	if ( is_array( $geo_filters ) ) {
		foreach ( $geo_filters as $filter_post_type => $filter ) {
			$filter_array[] = array( $filter_post_type => $filter );
		}
	}
}
$screens = array( 'post', 'page' );

$args = array(
	'public'   => true,
	'_builtin' => false,
);

$output            = 'names';
$operator          = 'and';
$post_types        = get_post_types( $args, $output, $operator );
$custom_post_types = array( 'post', 'page' );
$all_post_types    = array_merge( $post_types, $custom_post_types );
$all_post_types    = apply_filters( 'wpgmp_post_types', $all_post_types, $map );

if ( is_array( $all_post_types ) ) {
	$selected_values = unserialize( get_option( 'wpgmp_allow_meta' ) );

	foreach ( $all_post_types as $post_type ) {

		if ( is_array( $selected_values ) ) {

			if ( in_array( $post_type, $selected_values ) ) {
				continue;
			}
		}

		$filter_array[] = array(
			$post_type => array(
				'address'   => '_wpgmp_location_address',
				'latitude'  => '_wpgmp_metabox_latitude',
				'longitude' => '_wpgmp_metabox_longitude',
				'category'  => '_wpgmp_metabox_marker_id',
				'acf_key'   => ( $map->map_all_control['wpgmp_acf_field_name'] != '' ) ? $map->map_all_control['wpgmp_acf_field_name'] : '',
			),
		);
	}
}

if ( ! empty( $filter_array ) ) {
	foreach ( $filter_array as $filter ) {
		foreach ( $filter as $key => $value ) {
			if ( 'geo_tags' != $key ) {

				$custom_meta_keys = array();

				if ( ! empty( $value['acf_key'] ) ) {
					$custom_meta_keys['relation'] = 'OR';
					$custom_meta_keys[0]          = array(
						'key'     => $value['acf_key'],
						'value'   => '',
						'compare' => '!=',
					);
					if ( ! empty( $value['latitude'] ) ) {
						$custom_meta_keys[1]['relation'] = 'AND';
						$custom_meta_keys[1][0]          = array(
							'key'     => $value['latitude'],
							'value'   => '',
							'compare' => '!=',
						);
					}
					if ( ! empty( $value['longitude'] ) ) {
						$custom_meta_keys[1][1] = array(
							'key'     => $value['longitude'],
							'value'   => '',
							'compare' => '!=',
						);
					}
				} else {
					if ( ! empty( $value['latitude'] ) ) {
						$custom_meta_keys[] = array(
							'key'     => $value['latitude'],
							'value'   => '',
							'compare' => '!=',
						);
					}

					if ( ! empty( $value['longitude'] ) ) {
						$custom_meta_keys[] = array(
							'key'     => $value['longitude'],
							'value'   => '',
							'compare' => '!=',
						);
					}
					$custom_meta_keys = array( $custom_meta_keys );
				}

				if ( ( is_single() or is_page() ) and isset( $options['current_post_only'] ) and $options['current_post_only'] == 'true' ) {
					global $post;
					$args = array(
						'p'              => $post->ID,
						'post_type'      => $key,
						'posts_per_page' => -1,
						'meta_query'     => array( $custom_meta_keys ),
						'post_status'    => array( 'publish' ),
					);
				} else {
					$args = array(
						'post_type'      => $key,
						'posts_per_page' => -1,
						'meta_query'     => array( $custom_meta_keys ),
						'post_status'    => array( 'publish' ),
					);
				}

				$args            = apply_filters( 'wpgmp_post_args', $args, $map );
				$wpgmp_the_query = new WP_Query( $args );

				if ( $wpgmp_the_query->have_posts() ) {
					while ( $wpgmp_the_query->have_posts() ) {
						$wpgmp_the_query->the_post();
						global $post;
						$places         = array();
						$content        = $infowindow_post_view_source;
						$category_names = '';
						if ( isset( $value['acf_key'] ) ) {
							$acf_key        = get_post_meta( $post->ID, $value['acf_key'], true );
						} else {
							$acf_key = array();
						}

						if ( empty( $acf_key['lat'] ) && empty( $acf_key['lng'] ) ) {

							if ( empty( $value['latitude'] ) or empty( $value['longitude'] ) ) {
								continue; }
								// Check if meta post is assigned to $map->map_id.
							if ( '_wpgmp_location_address' == $value['address'] ) {

								$wpgmp_map_ids = get_post_meta( $post->ID, '_wpgmp_map_id', true );
								$wpgmp_map_id  = unserialize( $wpgmp_map_ids );

								if ( ! is_array( $wpgmp_map_id ) ) {
											$wpgmp_map_id = array( $wpgmp_map_ids );
								}
								if ( ! in_array( $map->map_id, $wpgmp_map_id ) ) {
									continue;
								}
							}
						}

						$replace_data['post_title']   = get_the_title();
						$replace_data['post_excerpt'] = get_the_excerpt();
						$replace_data['post_content'] = get_the_content();
						$replace_data['post_link']    = get_permalink( $post->ID );
						$categories                   = get_the_category();
						$category_names               = array();
						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$category_names[] = $category->name;
							}
						}

						$delimiter      = apply_filters( 'wpgmp_taxonomy_separator', ', ', $map );
						$category_names = implode( $delimiter, $category_names );

						$replace_data['post_categories'] = $category_names;

						$posttags  = get_the_tags();
						$tag_names = array();
						if ( $posttags ) {
							foreach ( $posttags as $tag ) {
								$tag_names[] = $tag->name;
							}
						}
						$tag_names = implode( $delimiter, $tag_names );

						$post_featured_image       = '';
						$replace_data['post_tags'] = $tag_names;
						$feature_image_size        = apply_filters( 'wpgmp_featured_image_size', 'medium', $post, $map );
						$featured_image            = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $feature_image_size );
						$image_alt                 = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
						if ( $image_alt == '' ) {
							$image_alt = $replace_data['post_title'];
						}
						if ( isset( $featured_image[0] ) && $featured_image[0] != '' ) {
							$post_featured_image = '<div class="fc-feature-img"><img alt="' . esc_attr( $image_alt ) . '" width="' . $featured_image[1] . '" height="' . $featured_image[2] . '" src="' . $featured_image[0] . '" class="wp-post-image   wpgmp_featured_image" ></div>';
						} else {
							$post_featured_image = '';
						}
						$replace_data['post_featured_image'] = apply_filters( 'wpgmp_featured_image', $post_featured_image, $post->ID, $map->map_id );
						$replace_data['marker_image']        = $replace_data['post_featured_image'];

						// Display custom fields here.
						$matches        = array();
						$custom_fields  = array();
						$custom_filters = array();
						preg_match_all( '/{%(.*?)%}/', $content, $matches );
						if ( isset( $matches[0] ) ) {
							foreach ( $matches[0] as $k => $m ) {
								$post_meta_key                               = $matches[1][ $k ];
								$meta_value                                  = get_post_meta( $post->ID, $post_meta_key, true ) ? get_post_meta( $post->ID, $post_meta_key, true ) : '';
								$replace_data[ '%' . $post_meta_key . '%' ]  = $meta_value;
								$custom_fields[ '%' . $post_meta_key . '%' ] = $meta_value;
							}
						}
						if ( empty( $custom_fields ) ) {
							$listing_content = stripslashes( trim( $listing_placeholder_content ) );

							preg_match_all( '/{%(.*?)%}/', $listing_content, $matches );
							if ( isset( $matches[0] ) ) {
								foreach ( $matches[0] as $k => $m ) {
											$post_meta_key                               = $matches[1][ $k ];
											$meta_value                                  = get_post_meta( $post->ID, $post_meta_key, true ) ? get_post_meta( $post->ID, $post_meta_key, true ) : '';
											$replace_data[ '%' . $post_meta_key . '%' ]  = $meta_value;
											$custom_fields[ '%' . $post_meta_key . '%' ] = $meta_value;
								}
							}
						}

						preg_match_all( '/{\s*taxonomy\s*=\s*(.*?)}/', $content, $matches );

						if ( isset( $matches[0] ) ) {

							foreach ( $matches[0] as $k => $m ) {
								$post_meta_key = $matches[1][ $k ];
								$terms         = wp_get_post_terms( $post->ID, $post_meta_key, array( 'fields' => 'all' ) );
								$meta_value    = '';
								if ( $terms ) {
											$tags_links = array();
									foreach ( $terms as $tag ) {
										$tags_links[] = $tag->name;
									}
									if ( ! empty( $tags_links ) ) {
										$meta_value = implode( ', ', $tags_links );
									}
								}
								$replace_data[ 'taxonomy=' . $post_meta_key ]  = $meta_value;
								$custom_fields[ 'taxonomy=' . $post_meta_key ] = $meta_value;

							}
						}

						if ( empty( $custom_fields ) ) {
							$listing_content = stripslashes( trim( $listing_placeholder_content ) );
							preg_match_all( '/{\s*taxonomy\s*=\s*(.*?)}/', $listing_content, $matches );
							if ( isset( $matches[0] ) ) {

								foreach ( $matches[0] as $k => $m ) {
											$post_meta_key = $matches[1][ $k ];
											$terms         = wp_get_post_terms( $post->ID, $post_meta_key, array( 'fields' => 'all' ) );
											$meta_value    = '';
									if ( $terms ) {
										$tags_links = array();
										foreach ( $terms as $tag ) {
											$tags_links[] = $tag->name;
										}
										if ( ! empty( $tags_links ) ) {
											$meta_value = implode( ', ', $tags_links );
										}
									}
										$replace_data[ 'taxonomy=' . $post_meta_key ]  = $meta_value;
										$custom_fields[ 'taxonomy=' . $post_meta_key ] = $meta_value;

								}
							}
						}

						$replace_data = apply_filters( 'wpgmp_post_placeholder', $replace_data, $post, $map );

						// Here parse infowindow setting and create infowindow message.
						$places['source'] = 'post';
						$places['title']  = $replace_data['post_title'];
						foreach ( $replace_data as $placeholder => $holder_value ) {
							$content = str_replace( '{' . $placeholder . '}', $holder_value, $content );
						}

						$places['infowindow_content'] = $content;
						$places['content']            = $replace_data['post_excerpt'];

						if ( ! empty( $acf_key['address'] ) ) {
							$places['address'] = $acf_key['address'];
						} elseif ( ! empty( $value['address'] ) ) {
							$places['address'] = get_post_meta( $post->ID, $value['address'], true );
						} else {
							$places['address'] = '';
						}

						if ( !empty($acf_key['lat']) ) {
							$places['location']['lat'] = $acf_key['lat'];
						} elseif ( ! empty( $value['latitude'] ) ) {
							$places['location']['lat'] = get_post_meta( $post->ID, $value['latitude'], true );
						} else {
							$places['location']['lat'] = '';
						}

						$post_city    = get_post_meta( $post->ID, '_wpgmp_location_city', true );
						$post_state   = get_post_meta( $post->ID, '_wpgmp_location_state', true );
						$post_country = get_post_meta( $post->ID, '_wpgmp_location_country', true );
						if ( ! empty( $post_city ) ) {
							$places['location']['city'] = $post_city;
						}
						if ( ! empty( $post_state ) ) {
							$places['location']['state'] = $post_state;
						}
						if ( ! empty( $post_country ) ) {
							$places['location']['country'] = $post_country;
						}

						if ( ! empty( $acf_key['lng'] ) ) {
							$places['location']['lng'] = $acf_key['lng'];
						} elseif ( ! empty( $value['longitude'] ) ) {
							$places['location']['lng'] = get_post_meta( $post->ID, $value['longitude'], true );
						} else {
							$places['location']['lng'] = '';
						}

						if ( ! empty( $value['category'] ) ) {
							$category_name = get_post_meta( $post->ID, $value['category'], true ); }

						$assigned_category = unserialize( $category_name );

						if ( ! is_array( $assigned_category ) and '' != $category_name ) {
							$assigned_category[] = $category_name;
						}
						$places['id']                               = $post->ID;
						$onclick                                    = get_post_meta( $post->ID, '_wpgmp_metabox_location_redirect', true );
						$onclick                                    = ( $onclick ) ? $onclick : 'marker';
						$wpgmp_metabox_custom_link                  = get_post_meta( $post->ID, '_wpgmp_metabox_custom_link', true );
						$places['location']['redirect_custom_link'] = $wpgmp_metabox_custom_link;
						$places['location']['onclick_action']       = $onclick;
						$places['location']['redirect_permalink']   = get_permalink( $post->ID );
						$places['location']['zoom']                 = intval( $map->map_zoom_level );
						$custom_fields['post_excerpt']              = $replace_data['post_excerpt'];
						$custom_fields['post_content']              = $replace_data['post_content'];
						$custom_fields['post_title']                = $replace_data['post_title'];
						$custom_fields['post_link']                 = $replace_data['post_link'];
						$custom_fields['post_featured_image']       = $replace_data['post_featured_image'];
						$custom_fields['post_categories']           = $replace_data['post_categories'];
						$custom_fields['post_tags']                 = $replace_data['post_tags'];
						$places['location']['extra_fields']         = $custom_fields;
						$post_custom_fields                         = get_post_custom( $post->ID );
						$post_custom_fields = apply_filters('wpgmp_skip_cf_list',$post_custom_fields, $post->ID, $map->map_id);
						if ( $post_custom_fields ) {
							foreach ( $post_custom_fields as $k => $cvalue ) {
								$k = trim( $k );

								$custom_fields[ '%' . $k . '%' ] = maybe_unserialize( $cvalue[0] );

								if ( is_array( $custom_fields[ '%' . $k . '%' ] ) ) {
										
										$is_nested_level = false;
										foreach($custom_fields[ '%' . $k . '%' ] as $key1 => $value1) {
											if(is_array($value1)){
												$is_nested_level = true;
												break;
											}
										}
										if(!$is_nested_level){
											$custom_fields[ '%' . $k . '%' ] = implode( $delimiter, $custom_fields[ '%' . $k . '%' ] );
										}
								}


								if ( in_array( '{%' . $k . '%}', $map_custom_filters ) ) {
									$filter_value = maybe_unserialize( $cvalue[0] );
									if ( is_array( $filter_value ) ) {
										$custom_filters[ $k ] = $filter_value;
									} else {
										$custom_filters[ $k ] = $cvalue[0];
									}
								}
							}
						}
						$post_taxonomies = get_post_taxonomies( $post->ID );
						if ( $post_taxonomies ) {
							foreach ( $post_taxonomies as $k => $tax ) {
								$term_list  = wp_get_post_terms( $post->ID, $tax, array( 'fields' => 'all' ) );
								$meta_value = '';
								$tags_links = array();
								if ( $term_list ) {
									foreach ( $term_list as $tag ) {
										$tags_links[] = $tag->name;
									}
									if ( ! empty( $tags_links ) ) {
										$meta_value = implode( ', ', $tags_links );
									}
								}
								$custom_fields[ 'taxonomy=' . $tax ] = $meta_value;
								if ( in_array( '{%' . $tax . '%}', $map_custom_filters ) ) {
									 $custom_filters[ '%' . $tax . '%' ] = $tags_links;
								}
							}
						}
						$places['location']['extra_fields'] = $custom_fields;
						$places['custom_filters']           = $custom_filters;
						$places['infowindow_disable']       = false;
						if ( is_array( $assigned_category ) ) {
							$category_count = 0;
							$cats_with_order_id_post = array();
							foreach ( $assigned_category as $category_name ) {
								if ( ! empty( $category_name ) ) {

											$loc_category = isset($all_categories_name[ sanitize_title( $category_name ) ]) ?  $all_categories_name[ sanitize_title( $category_name ) ] : "";

									if ( empty( $loc_category ) ) {
										$loc_category = isset($all_categories[ sanitize_title( $category_name ) ]) ? $all_categories[ sanitize_title( $category_name ) ] : "";
									}

									if( is_object($loc_category) and !empty($loc_category) ) {

									$places['categories'][ $category_count ]['icon']             = $loc_category->group_marker;
									$places['categories'][ $category_count ]['name']             = $loc_category->group_map_title;
									$places['categories'][ $category_count ]['id']               = $loc_category->group_map_id;
									$places['categories'][ $category_count ]['type']             = 'category';
									$places['categories'][ $category_count ]['extension_fields'] = $loc_category->extensions_fields;

									}
									if(!empty($loc_category->extensions_fields['cat_order'])){
										$cats_with_order_id_post[$loc_category->group_map_title] = $loc_category->extensions_fields['cat_order'];	
									}
									if (  isset($loc_category->group_marker) && $loc_category->group_marker != '' ) {
										$places['location']['icon'] = $loc_category->group_marker;
									} else {
										$places['location']['icon'] = $map_data['map_options']['marker_default_icon'];
									}
									if(!empty($cats_with_order_id_post) && count($cats_with_order_id_post)>0){
										$top_priority_key_post = min(array_keys($cats_with_order_id_post, min($cats_with_order_id_post)));
										$places['location']['icon'] = isset( $all_categories_name[ sanitize_title( $top_priority_key_post)]->group_marker ) ? $all_categories_name[ sanitize_title( $top_priority_key_post)]->group_marker : $map_data['map_options']['marker_default_icon'];

									}

								}
								$category_count++;
							}
						}

						$map_data['places'][] = $places;
					}
				}
				wp_reset_postdata();
			}
		}
	}
}


// Add  new places from external data source.
$custom_markers     = array();
$map_id             = $map->map_id;
$all_custom_markers = apply_filters( 'wpgmp_marker_source', $custom_markers, $map_id );
if ( is_array( $all_custom_markers ) ) {
	foreach ( $all_custom_markers as $marker ) {
		$places                               = array();

		if ( isset( $all_categories_name[ sanitize_title( $marker['category'] ) ] ) ) {
				$new_catagory = $all_categories_name[ sanitize_title( $marker['category'] ) ];
		} else {
				$new_catagory = '';
		}
		if( empty($new_catagory) && isset( $marker['category'] ) && !empty($marker['category']) ){
			$multiple_categories = explode(',',$marker['category']);
			$new_catagory = '';
		}


		$places['id']                         = isset( $marker['id'] ) ? $marker['id'] : rand( 4000, 9999 );
		$places['title']                      = $marker['title'];
		$places['source']                     = 'external';
		$places['address']                    = $marker['address'];
		$places['']                           = $marker['address'];
		$places['content']                    = $marker['message'];
		$places['location']['onclick_action'] = 'marker';
		$places['location']['lat']            = $marker['latitude'];
		$places['location']['lng']            = $marker['longitude'];
		$places['location']['postal_code']    = $marker['postal_code'];
        $places['location']['country']        = $marker['country'];
        $places['location']['city']           = $marker['city'];
        $places['location']['state']          = $marker['state'];
		$places['infowindow_disable']         = false;
		$places['location']['zoom']           = intval( $map->map_zoom_level );
		if ( $new_catagory != '' ) {
			
			$places['categories'][0]['icon']             = $new_catagory->group_marker;
			$places['categories'][0]['name']             = $new_catagory->group_map_title;
			$places['categories'][0]['id']               = $new_catagory->group_map_id;
			$places['categories'][0]['type']             = 'category';
			$places['categories'][0]['extension_fields'] = $new_catagory->extensions_fields;
			$places['location']['icon']                  = ( isset($marker['icon']) && !empty($marker['icon']) ) ? $marker['icon'] : $new_catagory->group_marker;
			
		}else if(isset($multiple_categories) && count($multiple_categories)>0){
					
			foreach($multiple_categories as $key => $assigned_cat){
				 
				    $assigned = $all_categories_name[ sanitize_title( $assigned_cat ) ];
					$places['categories'][$key]['icon']             = $assigned->group_marker;
					$places['categories'][$key]['name']             = $assigned->group_map_title;
					$places['categories'][$key]['id']               = $assigned->group_map_id;
					$places['categories'][$key]['type']             = 'category';
					$places['categories'][$key]['extension_fields'] = $assigned->extensions_fields;
					$places['location']['icon']                  = ( $marker['icon'] ) ? $marker['icon'] : $assigned->group_marker;
			}
			
		}
		$places['location']['marker_image'] = isset( $marker['marker_image'] ) ? $marker['marker_image'] : '';
		$places['location']['extra_fields'] = isset( $marker['extra_fields'] ) ? $marker['extra_fields'] : '';
		$map_data['places'][] = $places;
	}
}

// Here loop through all places and apply filter. Shortcode Awesome.
$filterd_places   = array();
$render_shortcode = apply_filters( 'wpgmp_render_shortcode', true, $map );
if ( is_array( $map_data['places'] ) ) {

	foreach ( $map_data['places'] as $place ) {
		$use_me = true;

		// Category filter here.
		if ( $map->map_all_control['url_filter'] == 'true' ) {

			if ( isset( $_GET['category'] ) and $_GET['category'] != '' ) {
				$shortcode_filters['category'] = sanitize_text_field( $_GET['category'] );
			}
		}

		if ( isset( $shortcode_filters['category'] ) ) {

			$found_category       = false;
			$show_categories_only = explode( ',', strtolower( $shortcode_filters['category'] ) );

			if( isset($place['categories']) ) {

				foreach ( $place['categories'] as $cat ) {
				if ( in_array( strtolower( $cat['name'] ), $show_categories_only ) or in_array( strtolower( $cat['id'] ), $show_categories_only ) ) {
					$found_category = true;
				}
			}
				
			}
			

			if ( false == $found_category ) {
				$use_me = false;
			}
		}


		if ( true == $render_shortcode ) {
			$place['content'] = do_shortcode( $place['content'] );
		}

		$use_me = apply_filters( 'wpgmp_show_place', $use_me, $place, $map );

		if ( true == $use_me ) {
			$filterd_places[] = $place;
		}
	}
	unset( $map_data['places'] );
}

if ( isset( $location_criteria['limit'] ) and $location_criteria['limit'] > 0 ) {

	$how_many       = intval( $location_criteria['limit'] );
	$filterd_places = array_slice( $filterd_places, 0, $how_many );

}

$map_data['places'] = apply_filters( 'wpgmp_markers', $filterd_places, $map->map_id );

if ( '' == $map_data['map_options']['center_lat'] && !empty($map_data['places'])) {
	$map_data['map_options']['center_lat'] = $map_data['places'][0]['location']['lat'];
}

if ( '' == $map_data['map_options']['center_lng'] && !empty($map_data['places'])) {
	$map_data['map_options']['center_lng'] = $map_data['places'][0]['location']['lng'];
}


// Styles.
$map_stylers = array();
if ( isset( $map->style_google_map['mapfeaturetype'] ) ) {
	unset( $map_stylers );
	$total_rows = count( $map->style_google_map['mapfeaturetype'] );
	for ( $i = 0;$i < $total_rows;$i++ ) {
		if ( empty( $map->style_google_map['mapfeaturetype'][ $i ] ) or empty( $map->style_google_map['mapelementtype'][ $i ] ) ) {
			continue;
		}
		if ( esc_html__( 'Select Featured Type', 'wpgmp-google-map' ) == $map->style_google_map['mapfeaturetype'][ $i ] ) {
			continue;
		}
		if ( $map->style_google_map['visibility'][ $i ] == 'off' ) {
			$map_stylers[] = array(
				'featureType' => $map->style_google_map['mapfeaturetype'][ $i ],
				'elementType' => $map->style_google_map['mapelementtype'][ $i ],
				'stylers'     => array(
					array(
						'visibility' => $map->style_google_map['visibility'][ $i ],
					),
				),
			);
		} else {
			$map_stylers[] = array(
				'featureType' => $map->style_google_map['mapfeaturetype'][ $i ],
				'elementType' => $map->style_google_map['mapelementtype'][ $i ],
				'stylers'     => array(
					array(
						'color'      => '#' . str_replace( '#', '', $map->style_google_map['color'][ $i ] ),
						'visibility' => $map->style_google_map['visibility'][ $i ],
					),
				),
			);
		}
	}
}

if ( isset( $map_stylers ) ) {
	if ( is_array( $map_stylers ) ) {
		$map_data['styles'] = $map_stylers;
	}
} elseif ( $map->map_all_control['custom_style'] != '' ) {
	$map_data['styles'] = stripslashes( $map->map_all_control['custom_style'] );
} else {
	$map_data['styles'] = '';
}
$map_data['styles'] = apply_filters( 'wpgmp_map_styles', $map_data['styles'], $map );

// Street view.
if ( isset( $map->map_street_view_setting['street_control'] ) && $map->map_street_view_setting['street_control'] == 'true' ) {
	$map_data['street_view'] = array(
		'street_control'           => ( isset( $map->map_street_view_setting['street_control'] ) ? $map->map_street_view_setting['street_control'] : '' ),
		'street_view_close_button' => ( ( isset( $map->map_street_view_setting['street_view_close_button'] ) && $map->map_street_view_setting['street_view_close_button'] === 'true' ) ? true : false ),
		'links_control'            => ( ( isset( $map->map_street_view_setting['links_control'] ) && $map->map_street_view_setting['links_control'] === 'true' ) ? true : false ),
		'street_view_pan_control'  => ( ( isset( $map->map_street_view_setting['street_view_pan_control'] ) && $map->map_street_view_setting['street_view_pan_control'] === 'true' ) ? true : false ),
		'pov_heading'              => $map->map_street_view_setting['pov_heading'],
		'pov_pitch'                => $map->map_street_view_setting['pov_pitch'],
	);
} else {
	$map_data['street_view'] = '';
}
$map_data['street_view'] = apply_filters( 'wpgmp_map_streetview', $map_data['street_view'], $map );

// Routes.
if ( ! empty( $map->map_route_direction_setting['route_direction'] ) && $map->map_route_direction_setting['route_direction'] == 'true' && isset( $map->map_route_direction_setting['specific_routes'] ) ) {
	$wpgmp_routes = $map->map_route_direction_setting['specific_routes'];
	$location_data = array();
	if ( ! empty( $wpgmp_routes ) ) {

		$all_routes = array();
		foreach ( $wpgmp_routes as $route_key => $wpgmp_route ) {

			if( isset($routes_data[ $wpgmp_route ]) ) {

				$wpgmp_route_data[ $route_key ] = $routes_data[ $wpgmp_route ];
				$wpgmp_route_way_points         = $wpgmp_route_data[ $route_key ]->route_way_points;

				$location_data[ $route_key ]['route_id']                 = $wpgmp_route_data[ $route_key ]->route_id;
				$location_data[ $route_key ]['route_title']              = $wpgmp_route_data[ $route_key ]->route_title;
				$location_data[ $route_key ]['route_stroke_color']       = '#' . str_replace( '#', '', $wpgmp_route_data[ $route_key ]->route_stroke_color );
				$location_data[ $route_key ]['route_stroke_opacity']     = $wpgmp_route_data[ $route_key ]->route_stroke_opacity;
				$location_data[ $route_key ]['route_stroke_weight']      = $wpgmp_route_data[ $route_key ]->route_stroke_weight;
				$location_data[ $route_key ]['route_travel_mode']        = $wpgmp_route_data[ $route_key ]->route_travel_mode;
				$location_data[ $route_key ]['route_unit_system']        = $wpgmp_route_data[ $route_key ]->route_unit_system;
				$location_data[ $route_key ]['route_marker_draggable']   = ( $wpgmp_route_data[ $route_key ]->route_marker_draggable === 'true' );
				$location_data[ $route_key ]['route_optimize_waypoints'] = ( $wpgmp_route_data[ $route_key ]->route_optimize_waypoints === 'true' );

				if ( is_array( $wpgmp_route_way_points ) and ! empty( $wpgmp_route_way_points ) ) {

				$wpgmp_route_way_point_data = $location_obj->fetch( array( array( 'location_id', 'IN', implode( ',', $wpgmp_route_way_points ) ) ) );

				if ( $wpgmp_route_way_point_data ) {
					foreach ( $wpgmp_route_way_point_data as $wpgmp_route_way_point_key => $row ) {
						$location_data[ $route_key ]['way_points'][] = $row->location_latitude . ',' . $row->location_longitude;
					}
				}
			}

				if ( isset($wpgmp_route_data[ $route_key ]->route_start_location) && ! empty( $wpgmp_route_data[ $route_key ]->route_start_location ) ) {
					$route_start_obj                                    = $location_obj->fetch( array( array( 'location_id', 'IN', $wpgmp_route_data[ $route_key ]->route_start_location ) ) );

					if( !empty($route_start_obj) ) {
						$location_data[ $route_key ]['start_location_data'] = $route_start_obj[0]->location_latitude . ',' . $route_start_obj[0]->location_longitude;
					}
					

				}

				if ( $wpgmp_route_data[ $route_key ]->route_end_location && ! empty( $wpgmp_route_data[ $route_key ]->route_end_location ) ) {
					$route_end_obj = $location_obj->fetch( array( array( 'location_id', 'IN', $wpgmp_route_data[ $route_key ]->route_end_location ) ) );

					if( !empty($route_end_obj) ) {
						$location_data[ $route_key ]['end_location_data'] = $route_end_obj[0]->location_latitude . ',' . $route_end_obj[0]->location_longitude;
					}
					
				}
			
			}
			
		}
	}
	$map_data['routes'] = $location_data;
} else {
	$map_data['routes'] = '';
}

$map_data['routes'] = apply_filters( 'wpgmp_map_routes', $map_data['routes'], $map );

// Marker cluster.
if ( ! empty( $map->map_cluster_setting['marker_cluster'] ) && $map->map_cluster_setting['marker_cluster'] == 'true' ) {

	if ( ! isset( $map->map_cluster_setting['marker_cluster_style'] ) ) {
		$map->map_cluster_setting['marker_cluster_style'] = false;
	}

	$map_data['marker_cluster'] = array(
		'grid'              => $map->map_cluster_setting['grid'],
		'max_zoom'          => $map->map_cluster_setting['max_zoom'],
		'image_path'        => WPGMP_IMAGES . 'm',
		'icon'              => WPGMP_IMAGES . 'cluster/' . $map->map_cluster_setting['icon'],
		'hover_icon'        => WPGMP_IMAGES . 'cluster/' . $map->map_cluster_setting['hover_icon'],
		'apply_style'       => ( $map->map_cluster_setting['marker_cluster_style'] == 'true' ),
		'marker_zoom_level' => ( isset( $map->map_cluster_setting['location_zoom'] ) ? $map->map_cluster_setting['location_zoom'] : 10 ),
	);
} else {
	$map_data['marker_cluster'] = '';
}

$map_data['marker_cluster'] = apply_filters( 'wpgmp_map_markercluster', $map_data['marker_cluster'], $map );

// Overlays.
if ( ! empty( $map->map_overlay_setting['overlay'] ) && $map->map_overlay_setting['overlay'] == 'true' ) {
	$map_data['overlay_setting'] = array(
		'border_color' => '#' . str_replace( '#', '', $map->map_overlay_setting['overlay_border_color'] ),
		'width'        => $map->map_overlay_setting['overlay_width'],
		'height'       => $map->map_overlay_setting['overlay_height'],
		'font_size'    => $map->map_overlay_setting['overlay_fontsize'],
		'border_width' => $map->map_overlay_setting['overlay_border_width'],
		'border_style' => $map->map_overlay_setting['overlay_border_style'],
	);
} else {
	$map_data['overlay_setting'] = '';
}

$map_data['overlay_setting'] = apply_filters( 'wpgmp_map_overlays', $map_data['overlay_setting'], $map );

// Limit panning and zoom control.
if ( ! empty( $map->map_all_control['panning_control'] ) && $map->map_all_control['panning_control'] == 'true' ) {
	$map_data['panning_control'] = array(
		'from_latitude'  => $map->map_all_control['from_latitude'],
		'from_longitude' => $map->map_all_control['from_longitude'],
		'to_latitude'    => $map->map_all_control['to_latitude'],
		'to_longitude'   => $map->map_all_control['to_longitude'],
		'zoom_level'     => $map->map_all_control['zoom_level'],
	);
} else {
	$map_data['panning_control'] = '';
}
$map_data['panning_control'] = apply_filters( 'wpgmp_map_panning', $map_data['panning_control'], $map );


if ( ! empty( $map->map_all_control['gm_amenities'] ) && $map->map_all_control['gm_amenities'] == true ) {

	$default_amenities = array();

	if ( isset( $map->map_all_control['wpgmp_show_amenities'] ) ) {

		foreach ( $map->map_all_control['wpgmp_show_amenities'] as $key => $value ) {
			$default_amenities[ $value ] = str_replace( '_', ' ', $value );
		}

		$map_data['default_amenities'] = array(
			'dimension' => $map->map_all_control['gm_radius_dimension'],
			'radius'    => $map->map_all_control['gm_radius'],
			'amenities' => $default_amenities,
		);

	}
}




if ( isset( $options['maps_only'] ) and $options['maps_only'] == 'true' ) {
	$map->map_all_control['display_marker_category'] = false;
	$map->map_all_control['display_listing']         = false;
} elseif ( isset( $_GET['maps_only'] ) and $_GET['maps_only'] == 'true' and $map->map_all_control['url_filter'] == 'true' ) {
	$map->map_all_control['display_marker_category'] = false;
	$map->map_all_control['display_listing']         = false;
}

$display_route_tab_data = array();
// Display tabs on maps.
if ( ! empty( $map->map_all_control['display_marker_category'] ) && $map->map_all_control['display_marker_category'] == true ) {

	if ( ! empty( $map->map_route_direction_setting['route_direction'] ) && $map->map_route_direction_setting['route_direction'] == 'true' ) {
		$display_route_tab_data = $map->map_route_direction_setting['route_direction'];
	}

	$selected_amenities = array();

	if ( isset( $map->map_all_control['wpgmp_nearby_amenities'] ) ) {

		foreach ( $map->map_all_control['wpgmp_nearby_amenities'] as $key => $value ) {
			$selected_amenities[ $value ] = str_replace( '_', ' ', $value );
		}
	}

	if ( ! isset( $map->map_all_control['hide_tabs_default'] ) ) {
		$map->map_all_control['hide_tabs_default'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_category_tab'] ) ) {
		$map->map_all_control['wpgmp_category_tab'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_category_tab_show_count'] ) ) {
		$map->map_all_control['wpgmp_category_tab_show_count'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_category_tab_hide_location'] ) ) {
		$map->map_all_control['wpgmp_category_tab_hide_location'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_category_tab_show_all'] ) ) {
		$map->map_all_control['wpgmp_category_tab_show_all'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_direction_tab_suppress_markers'] ) ) {
		$map->map_all_control['wpgmp_direction_tab_suppress_markers'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_nearby_tab'] ) ) {
		$map->map_all_control['wpgmp_nearby_tab'] = false;
	}

	if ( ! isset( $map->map_all_control['show_nearby_circle'] ) ) {
		$map->map_all_control['show_nearby_circle'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_direction_tab'] ) ) {
		$map->map_all_control['wpgmp_direction_tab'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_route_tab'] ) ) {
		$map->map_all_control['wpgmp_route_tab'] = false;
	}




	$map_data['map_tabs'] = array(
		'hide_tabs_default'    => ( 'true' == $map->map_all_control['hide_tabs_default'] ),
		'category_tab'         => array(
			'cat_tab'        => ( 'true' == $map->map_all_control['wpgmp_category_tab'] ),
			'cat_tab_title'  => ( $map->map_all_control['wpgmp_category_tab_title'] ) ? $map->map_all_control['wpgmp_category_tab_title'] : esc_html__( 'Categories', 'wpgmp-google-map' ),
			'cat_order_by'   => $map->map_all_control['wpgmp_category_order'],
			'cat_post_order' => isset( $map->map_all_control['wpgmp_category_location_sort_order'] ) ? $map->map_all_control['wpgmp_category_location_sort_order'] : 'asc',
			'show_count'     => ( 'true' == $map->map_all_control['wpgmp_category_tab_show_count'] ),
			'hide_location'  => ( $map->map_all_control['wpgmp_category_tab_hide_location'] == 'true' ),
			'select_all'     => ( $map->map_all_control['wpgmp_category_tab_show_all'] == 'true' ),
			'child_cats'     => (array) $all_child_categories,
			'parent_cats'    => (array) $all_parent_categories,
			'all_cats'       => (array) $all_categories,
		),
		'direction_tab'        => array(
			'dir_tab'                => ( 'true' == $map->map_all_control['wpgmp_direction_tab'] ),
			'direction_tab_title'    => ( $map->map_all_control['wpgmp_direction_tab_title'] ) ? $map->map_all_control['wpgmp_direction_tab_title'] : esc_html__( 'Directions', 'wpgmp-google-map' ),
			'default_start_location' => $map->map_all_control['wpgmp_direction_tab_start_default'],
			'default_end_location'   => $map->map_all_control['wpgmp_direction_tab_end_default'],
			'suppress_markers'       => ( 'true' == $map->map_all_control['wpgmp_direction_tab_suppress_markers'] ),
		),
		'nearby_tab'           => array(
			'near_tab'                    => ( $map->map_all_control['wpgmp_nearby_tab'] == 'true' ),
			'nearby_tab_title'            => ( $map->map_all_control['wpgmp_nearby_tab_title'] ) ? $map->map_all_control['wpgmp_nearby_tab_title'] : esc_html__( 'Nearby', 'wpgmp-google-map' ),
			'nearby_amenities'            => $selected_amenities,
			'nearby_circle_fillcolor'     => sanitize_text_field( $map->map_all_control['nearby_circle_fillcolor'] ),
			'nearby_circle_fillopacity'   => sanitize_text_field( $map->map_all_control['nearby_circle_fillopacity'] ),
			'nearby_circle_strokecolor'   => sanitize_text_field( $map->map_all_control['nearby_circle_strokecolor'] ),
			'nearby_circle_strokeopacity' => sanitize_text_field( $map->map_all_control['nearby_circle_strokeopacity'] ),
			'show_nearby_circle'          => ( sanitize_text_field( $map->map_all_control['show_nearby_circle'] ) == 'true' ),
			'nearby_circle_strokeweight'  => sanitize_text_field( $map->map_all_control['nearby_circle_strokeweight'] ),
			'nearby_circle_zoom'          => ( $map->map_all_control['nearby_circle_zoom'] ) ? sanitize_text_field( $map->map_all_control['nearby_circle_zoom'] ) : 9,
		),
		'route_tab'            => array(
			'display_route_tab'      => ( $map->map_all_control['wpgmp_route_tab'] == 'true' ),
			'route_tab_title'        => ( $map->map_all_control['wpgmp_route_tab_title'] ) ? $map->map_all_control['wpgmp_route_tab_title'] : esc_html__( 'Routes', 'wpgmp-google-map' ),
			'display_route_tab_data' => ( 'true' == $display_route_tab_data ),
			'route_tab_data'         => $map_data['routes'],
			'route_tab_title'        => $map->map_all_control['wpgmp_route_tab_title'],

		),
		'route_start_location' => ( $map->map_all_control['wpgmp_direction_tab_start'] ) ? $map->map_all_control['wpgmp_direction_tab_start'] : 'textbox',
		'route_end_location'   => ( $map->map_all_control['wpgmp_direction_tab_end'] ) ? $map->map_all_control['wpgmp_direction_tab_end'] : 'textbox',
	);

}

if ( isset( $map_data['map_tabs'] ) ) {

	$map_data['map_tabs']['category_tab'] = apply_filters( 'wpgmp_category_tab', $map_data['map_tabs']['category_tab'], $map );

	$map_data['map_tabs']['direction_tab'] = apply_filters( 'wpgmp_direction_tab', $map_data['map_tabs']['direction_tab'], $map );

	$map_data['map_tabs']['nearby_tab'] = apply_filters( 'wpgmp_nearby_tab', $map_data['map_tabs']['nearby_tab'], $map );

	$map_data['map_tabs']['route_tab'] = apply_filters( 'wpgmp_route_tab', $map_data['map_tabs']['route_tab'], $map );


}

// Display nearby tabs.
if ( ! is_admin() && ! empty( $map->map_all_control['wpgmp_nearby_tab'] ) && $map->map_all_control['wpgmp_nearby_tab'] == true ) {
	$map_data['nearby_tab'] = array();
}

if ( ! empty( $map->map_all_control['display_listing'] ) && $map->map_all_control['display_listing'] == true ) {
	$filcate       = array( 'place_category' );
	$sorting_array = array(
		'category__asc'  => esc_html__( 'A-Z Category', 'wpgmp-google-map' ),
		'category__desc' => esc_html__( 'Z-A Category', 'wpgmp-google-map' ),
		'title__asc'     => esc_html__( 'A-Z Title', 'wpgmp-google-map' ),
		'title__desc'    => esc_html__( 'Z-A Title', 'wpgmp-google-map' ),
		'address__asc'   => esc_html__( 'A-Z Address', 'wpgmp-google-map' ),
		'address__desc'  => esc_html__( 'Z-A Address', 'wpgmp-google-map' ),
	);

	$sorting_array = apply_filters( 'wpgmp_sorting', $sorting_array, $map );

	if ( empty( $map->map_all_control['wpgmp_listing_number'] ) ) {
		$map->map_all_control['wpgmp_listing_number'] = 10; }

	if ( ! isset( $map->map_all_control['wpgmp_categorydisplaysortby'] ) or $map->map_all_control['wpgmp_categorydisplaysortby'] == '' ) {
		$map->map_all_control['wpgmp_categorydisplaysortby'] = 'asc';
	}
	$render_shortcode = apply_filters( 'wpgmp_listing_render_shortcode', true, $map );

	if ( $render_shortcode == true ) {
		$listing_placeholder_text = do_shortcode( stripslashes( trim( $listing_placeholder_content ) ) );
	} else {
		$listing_placeholder_text = stripslashes( trim( $listing_placeholder_content ) );
	}

	if ( isset( $options['hide_map'] ) and $options['hide_map'] == 'true' ) {
		$map->map_all_control['hide_map'] = 'true';
	} elseif ( isset( $_GET['hide_map'] ) and $_GET['hide_map'] == 'true' and $map->map_all_control['url_filter'] == 'true' ) {
		$map->map_all_control['hide_map'] = 'true';
	}

	if ( isset( $options['perpage'] ) and $options['perpage'] > 0 ) {
		$map->map_all_control['wpgmp_listing_number'] = sanitize_text_field( $options['perpage'] );
	} elseif ( isset( $_GET['perpage'] ) and $map->map_all_control['url_filter'] == 'true' ) {
		$map->map_all_control['wpgmp_listing_number'] = sanitize_text_field( $_GET['perpage'] );
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_sorting_filter'] ) ) {
		$map->map_all_control['wpgmp_display_sorting_filter'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_radius_filter'] ) ) {
		$map->map_all_control['wpgmp_display_radius_filter'] = false;
	}

	if ( ! isset( $map->map_all_control['hide_locations'] ) ) {
		$map->map_all_control['hide_locations'] = false;
	}

	if ( ! isset( $map->map_all_control['hide_map'] ) ) {
		$map->map_all_control['hide_map'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_apply_radius_only'] ) ) {
		$map->map_all_control['wpgmp_apply_radius_only'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_grid_option'] ) ) {
		$map->map_all_control['wpgmp_display_grid_option'] = false;
	}

	
	if ( ! isset( $map->map_all_control['wpgmp_search_display'] ) ) {
		$map->map_all_control['wpgmp_search_display'] = false;
	}

	if ( ! isset( $map->map_all_control['search_field_autosuggest'] ) ) {
		$map->map_all_control['search_field_autosuggest'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_sorting_filter'] ) ) {
		$map->map_all_control['wpgmp_display_sorting_filter'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_radius_filter'] ) ) {
		$map->map_all_control['wpgmp_display_radius_filter'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_apply_radius_only'] ) ) {
		$map->map_all_control['wpgmp_apply_radius_only'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_category_filter'] ) ) {
		$map->map_all_control['wpgmp_display_category_filter'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_location_per_page_filter'] ) ) {
		$map->map_all_control['wpgmp_display_location_per_page_filter'] = false;
	}

	if ( ! isset( $map->map_all_control['wpgmp_display_print_option'] ) ) {
		$map->map_all_control['wpgmp_display_print_option'] = false;
	}

	$map_data['listing'] = array(
		'listing_header'                   => $map->map_all_control['wpgmp_before_listing'],
		'display_search_form'              => ( 'true' == $map->map_all_control['wpgmp_search_display'] ),
		'search_field_autosuggest'         => ( $map->map_all_control['search_field_autosuggest'] == 'true' ),
		'display_category_filter'          => ( $map->map_all_control['wpgmp_display_category_filter'] == 'true' ),
		'display_sorting_filter'           => ( 'true' == $map->map_all_control['wpgmp_display_sorting_filter'] ),
		'display_radius_filter'            => ( 'true' == $map->map_all_control['wpgmp_display_radius_filter'] ),
		'radius_dimension'                 => $map->map_all_control['wpgmp_radius_dimension'],
		'radius_options'                   => $map->map_all_control['wpgmp_radius_options'],
		'apply_default_radius'             => ( 'true' == $map->map_all_control['wpgmp_apply_radius_only'] ),
		'default_radius'                   => $map->map_all_control['wpgmp_default_radius'],
		'default_radius_dimension'         => $map->map_all_control['wpgmp_default_radius_dimension'],
		'display_location_per_page_filter' => ( 'true' == $map->map_all_control['wpgmp_display_location_per_page_filter'] ),
		'display_print_option'             => ( $map->map_all_control['wpgmp_display_print_option'] == 'true' ),
		'display_grid_option'              => ( $map->map_all_control['wpgmp_display_grid_option'] == 'true' ),
		'filters'                          => array( 'place_category' ),
		'sorting_options'                  => $sorting_array,
		'default_sorting'                  => array(
			'orderby' => $map->map_all_control['wpgmp_categorydisplaysort'],
			'inorder' => $map->map_all_control['wpgmp_categorydisplaysortby'],
		),
		'listing_container'                => '.location_listing' . $map->map_id,
		'tabs_container'                   => '.location_listing' . $map->map_id,
		'hide_locations'                   => ( $map->map_all_control['hide_locations'] == 'true' ),
		'filters_position'                 => ( $map->map_all_control['filters_position'] ) ? $map->map_all_control['filters_position'] : '',
		'hide_map'                         => ( $map->map_all_control['hide_map'] == 'true' ),
		'pagination'                       => array( 'listing_per_page' => $map->map_all_control['wpgmp_listing_number'] ),
		'list_grid'                        => ( $map->map_all_control['wpgmp_list_grid'] ) ? $map->map_all_control['wpgmp_list_grid'] : 'wpgmp_listing_list',
		'listing_placeholder'              => $listing_placeholder_text,
		'list_item_skin'                   => ( isset( $map->map_all_control['item_skin'] ) ) ? $map->map_all_control['item_skin'] : array(
			'name'       => 'default',
			'type'       => 'item',
			'sourcecode' => $listing_placeholder_text,
		),
	);
} else {
	$map_data['listing'] = '';
}
$map_data['listing']      = apply_filters( 'wpgmp_listing', $map_data['listing'], $map );
$map_data['map_property'] = array(
	'map_id'     => $map->map_id,
	'debug_mode' => ( $wpgmp_settings['wpgmp_debug_mode'] == 'true' ),
);


if ( '' != sanitize_text_field( $map->map_all_control['geojson_url'] ) ) {
	$map_data['geojson'] = sanitize_text_field( $map->map_all_control['geojson_url'] );
}

// Drawing.
$drawing_editable_true = false;
if ( is_admin() && current_user_can( 'manage_options' ) ) {
	$drawing_editable_true = true;
	$objects               = array( 'circle', 'polygon', 'polyline', 'rectangle' );
	for ( $i = 0; $i < count( $objects ); $i++ ) {
		$object_name    = $objects[ $i ];
		$drawingModes[] = 'google.maps.drawing.OverlayType.' . strtoupper( $object_name );

		$drawing_options[ $object_name ][] = "fillColor: '#ff0000'";
		$drawing_options[ $object_name ][] = "strokeColor: '#ff0000'";
		$drawing_options[ $object_name ][] = 'strokeWeight: 1';
		$drawing_options[ $object_name ][] = 'strokeOpacity: 1';
		$drawing_options[ $object_name ][] = 'zindex: 1';
		$drawing_options[ $object_name ][] = 'fillOpacity: 1';
		$drawing_options[ $object_name ][] = 'editable: true';
		$drawing_options[ $object_name ][] = 'draggable: true';
		$drawing_options[ $object_name ][] = 'clickable: false';
	}

	if ( is_array( $drawingModes ) ) {
		$display_modes = implode( ',', $drawingModes ); }

	if ( is_array( $drawing_options['circle'] ) ) {
		$display_circle_options = implode( ',', $drawing_options['circle'] ); }

	if ( is_array( $drawing_options['polygon'] ) ) {
		$display_polygon_options = implode( ',', $drawing_options['polygon'] ); }

	if ( is_array( $drawing_options['polyline'] ) ) {
		$display_polyline_options = implode( ',', $drawing_options['polyline'] ); }

	if ( is_array( $drawing_options['rectangle'] ) ) {
		$display_rectangle_options = implode( ',', $drawing_options['rectangle'] ); }
}
if ( $map->map_polyline_setting['polylines'] != '' ) {
	$map_shapes      = array();
	$all_saved_shape = $map->map_polyline_setting['polylines'];
	$all_shapes      = explode( '|', $all_saved_shape[0] );
	if ( is_array( $all_shapes ) ) {
		foreach ( $all_shapes as $key => $shapes ) {
			$find_shape = explode( '=', $shapes, 2 );

			if ( 'polylines' == $find_shape[0] ) {
				$polylines_shape[0] = $find_shape[1]; } elseif ( 'polygons' == $find_shape[0] ) {
				$polygons_shape[0] = $find_shape[1]; } elseif ( 'circles' == $find_shape[0] ) {
					$circles_shape[0] = $find_shape[1]; } elseif ( 'rectangles' == $find_shape[0] ) {
						$rectangles_shape[0] = $find_shape[1]; }
		}
	}

	if ( $polygons_shape[0] && ! empty( $polygons_shape[0] ) ) {
		$all_polylines = explode( '::', $polygons_shape[0] );

		for ( $p = 0;$p < count( $all_polylines );$p++ ) {
			unset( $settings );
			$all_settings     = explode( '...', $all_polylines[ $p ] );
			$cordinates       = explode( '----', $all_settings[0] );
			$all_events       = $all_settings[2];
			$all_events       = explode( '***', $all_events );
			$all_settings_val = explode( ',', $all_settings[1] );

			if ( empty( $all_settings_val[3] ) ) {
				$all_settings_val[3] = '#ff0000'; }

			if ( empty( $all_settings_val[4] ) ) {
				$all_settings_val[4] = 1; }

			if ( empty( $all_settings_val[2] ) ) {
				$all_settings_val[2] = '#ff0000'; }

			if ( empty( $all_settings_val[1] ) ) {
				$all_settings_val[1] = 1; }

			if ( empty( $all_settings_val[0] ) ) {
				$all_settings_val[0] = 5; }

			$settings['stroke_color']   = '#' . str_replace( '#', '', $all_settings_val[2] );
			$settings['stroke_opacity'] = $all_settings_val[1];
			$settings['stroke_weight']  = $all_settings_val[0];
			$settings['fill_color']     = '#' . str_replace( '#', '', $all_settings_val[3] );
			$settings['fill_opacity']   = $all_settings_val[4];
			$events                     = array();
			$events['url']              = $all_events[0];
			$events['message']          = nl2br( stripcslashes( $all_events[1] ) );
			$map_shapes['polygons'][]   = array(
				'cordinates' => $cordinates,
				'settings'   => $settings,
				'events'     => $events,
			);
		}
	}

	if ( $polylines_shape[0] && ! empty( $polylines_shape[0] ) ) {
		$all_polylines = explode( '::', $polylines_shape[0] );
		for ( $p = 0;$p < count( $all_polylines );$p++ ) {
			$all_settings     = explode( '...', $all_polylines[ $p ] );
			$cordinates       = explode( '----', $all_settings[0] );
			$all_events       = $all_settings[2];
			$all_events       = explode( '***', $all_events );
			$all_settings_val = explode( ',', $all_settings[1] );

			if ( empty( $all_settings_val[2] ) ) {
				$all_settings_val[2] = '#ff0000'; }

			if ( empty( $all_settings_val[1] ) ) {
				$all_settings_val[1] = 1; }

			if ( empty( $all_settings_val[0] ) ) {
				$all_settings_val[0] = 5; }

			$settings['stroke_color']   = '#' . str_replace( '#', '', $all_settings_val[2] );
			$settings['stroke_opacity'] = $all_settings_val[1];
			$settings['stroke_weight']  = $all_settings_val[0];
			$events                     = array();
			$events['url']              = $all_events[0];
			$events['message']          = nl2br( stripcslashes( $all_events[1] ) );
			$map_shapes['polylines'][]  = array(
				'cordinates' => $cordinates,
				'settings'   => $settings,
				'events'     => $events,
			);
		}
	}
	if ( $circles_shape && ! empty( $circles_shape[0] ) ) {
		$all_circles = explode( '::', $circles_shape[0] );
		for ( $p = 0;$p < count( $all_circles );$p++ ) {
			$all_settings     = explode( '...', $all_circles[ $p ] );
			$cordinates       = explode( '----', $all_settings[0] );
			$all_events       = $all_settings[2];
			$all_events       = explode( '***', $all_events );
			$all_settings_val = explode( ',', $all_settings[1] );

			if ( empty( $all_settings_val[5] ) ) {
				$all_settings_val[5] = 1; }

			if ( empty( $all_settings_val[3] ) ) {
				$all_settings_val[3] = '#ff0000'; }

			if ( empty( $all_settings_val[4] ) ) {
				$all_settings_val[4] = 1; }

			if ( empty( $all_settings_val[2] ) ) {
				$all_settings_val[2] = '#ff0000'; }

			if ( empty( $all_settings_val[1] ) ) {
				$all_settings_val[1] = 1; }

			if ( empty( $all_settings_val[0] ) ) {
				$all_settings_val[0] = 5; }

			$settings['stroke_color']   = '#' . str_replace( '#', '', $all_settings_val[2] );
			$settings['stroke_opacity'] = $all_settings_val[1];
			$settings['stroke_weight']  = $all_settings_val[0];
			$settings['fill_color']     = '#' . str_replace( '#', '', $all_settings_val[3] );
			$settings['fill_opacity']   = $all_settings_val[4];
			$settings['radius']         = $all_settings_val[5];
			$events                     = array();
			$events['url']              = $all_events[0];
			$events['message']          = nl2br( stripcslashes( $all_events[1] ) );
			$map_shapes['circles'][]    = array(
				'cordinates' => $cordinates,
				'settings'   => $settings,
				'events'     => $events,
			);
		}
	}

	if ( $rectangles_shape[0] && ! empty( $rectangles_shape[0] ) ) {
		$all_polylines = explode( '::', $rectangles_shape[0] );
		for ( $p = 0;$p < count( $all_polylines );$p++ ) {
			$all_settings     = explode( '...', $all_polylines[ $p ] );
			$cordinates       = explode( '----', $all_settings[0] );
			$all_settings_val = explode( ',', $all_settings[1] );
			$all_events       = $all_settings[2];
			$all_events       = explode( '***', $all_events );
			if ( empty( $all_settings_val[3] ) ) {
				$all_settings_val[3] = 'ff0000'; }

			if ( empty( $all_settings_val[4] ) ) {
				$all_settings_val[4] = 1; }

			if ( empty( $all_settings_val[2] ) ) {
				$all_settings_val[2] = 'ff0000'; }

			if ( empty( $all_settings_val[1] ) ) {
				$all_settings_val[1] = 1; }

			if ( empty( $all_settings_val[0] ) ) {
				$all_settings_val[0] = 5; }

			$settings['stroke_color']   = '#' . str_replace( '#', '', $all_settings_val[2] );
			$settings['stroke_opacity'] = $all_settings_val[1];
			$settings['stroke_weight']  = $all_settings_val[0];
			$settings['fill_color']     = '#' . str_replace( '#', '', $all_settings_val[3] );
			$settings['fill_opacity']   = $all_settings_val[4];
			$events                     = array();
			$events['url']              = $all_events[0];
			$events['message']          = nl2br( stripcslashes( $all_events[1] ) );
			$map_shapes['rectangles'][] = array(
				'cordinates' => $cordinates,
				'settings'   => $settings,
				'events'     => $events,
			);
		}
	}
}


$map_data['shapes'] = array(
	'drawing_editable' => $drawing_editable_true,
);

if ( ! isset( $map_shapes ) ) {
	$map_shapes = array();
}

$map_shapes = apply_filters( 'wpgmp_shapes', $map_shapes, $map_data, $map->map_id );

if ( ! empty( $map_shapes ) && is_array( $map_shapes ) ) {
	$map_data['shapes']['shape'] = $map_shapes; }

$all_filters = array();
if ( isset( $map->map_all_control['wpgmp_display_custom_filters'] ) && $map->map_all_control['wpgmp_display_custom_filters'] == 'true' ) {
	if ( isset( $map->map_all_control['custom_filters'] ) and ! empty( $map->map_all_control['custom_filters'] ) ) {
		foreach ( $map->map_all_control['custom_filters'] as $key => $val ) {
			$val['slug'] = preg_replace( '/[{}]/', '', $val['slug'] );
			if ( $val['slug'] == 'category' ) {
				$val['slug'] = 'post_categories';}
			$listing_custom_filters['dropdown'][ $val['slug'] ] = $val['text'];
		}
		$all_filters['filters'] = $listing_custom_filters;
	}
}
$all_filters             = apply_filters( 'wpgmp_filters', $all_filters, $map );
$custom_filter_container = apply_filters( 'wpgmp_filter_container', '[data-container="wpgmp-filters-container"]', $map );

$map_data['filters'] = array(
	'custom_filters'    => $all_filters,
	'filters_container' => $custom_filter_container,
);


$map_output = apply_filters( 'wpgmp_before_container', '', $map );

$map_output .= '<div class="wpgmp_map_container ' . apply_filters( 'wpgmp_container_class', 'wpgmp-map-' . $map->map_id, $map ) . '" rel="map' . $map->map_id . '">';

/* Search Control over map */
if ( $map->map_all_control['search_control'] == 'true' ) {
	$map_output .= '<input  data-input="map-search-control" placeholder="' . esc_html__( 'Type here...', 'wpgmp-google-map' ) . '" type="text">';
}

$map_div = apply_filters( 'wpgmp_before_map', '', $map );

if ( isset( $map->map_all_control['hide_map'] ) && $map->map_all_control['hide_map'] == 'true' ) {
	$width  = '0px';
	$height = '0px';
}

$filters_div = '<div class="wpgmp_filter_wrappers"></div>';

if(!class_exists('Listing_Designs_For_Google_Maps') || wp_is_mobile() ){

	if( isset( $map->map_all_control['display_listing'] ) && $map->map_all_control['display_listing'] == 'true'){
		
		if($map->map_all_control['filters_position'] == 'top_map'){
		
		$map_div .= $filters_div.'<div class="wpgmp_map_parent"><div class="wpgmp_map ' . apply_filters( 'wpgmp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';" id="map' . $map->map_id . '" ></div></div>';
		}else{

			$map_div .= '<div class="wpgmp_map_parent"><div class="wpgmp_map ' . apply_filters( 'wpgmp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';" id="map' . $map->map_id . '" ></div></div>'.$filters_div;
			
		}
			
	}else{ 
		
		$map_div .= '<div class="wpgmp_map_parent"><div class="wpgmp_map ' . apply_filters( 'wpgmp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';" id="map' . $map->map_id . '" ></div></div>';
		
	}

}else{

	$map_div .= '<div class="wpgmp_map_parent"><div class="wpgmp_map ' . apply_filters( 'wpgmp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';" id="map' . $map->map_id . '" ></div></div>';
}

$map_div .= apply_filters( 'wpgmp_after_map', '', $map );

$listing_div = apply_filters( 'wpgmp_before_listing', '', $map );

if ( ! empty( $map->map_all_control['display_listing'] ) && $map->map_all_control['display_listing'] == true ) {

	$listing_div .= '<div class="location_listing' . $map->map_id . ' ' . apply_filters( 'wpgmp_listing_class', '', $map ) . '" style="float:left; width:100%;"></div>';

	if ( $map->map_all_control['hide_locations'] != true ) {

		$listing_div .= '<div class="location_pagination' . $map->map_id . ' ' . apply_filters( 'wpgmp_pagination_class', '', $map ) . ' wpgmp_pagination" style="float:left; width:100%;"></div>';

	}
}

$listing_div .= apply_filters( 'wpgmp_after_listing', '', $map );

$output = $map_div . $listing_div;

if(class_exists('Listing_Designs_For_Google_Maps')){ 
	$map_output .= apply_filters( 'wpgmp_map_output', $output, $map_div, $filters_div, $listing_div, $map->map_id );
}
else { 
$map_output .= apply_filters( 'wpgmp_map_output', $output, $map_div, $listing_div, $map->map_id );
}

$map_output .= '</div>';

$map_output .= apply_filters( 'wpgmp_after_container', '', $map );

if ( isset( $map->map_all_control['fc_custom_styles'] ) ) {
	$fc_custom_styles = json_decode( $map->map_all_control['fc_custom_styles'], true );
	if ( ! empty( $fc_custom_styles ) && is_array( $fc_custom_styles ) ) {
		$fc_skin_styles = '';
		$font_families  = array();
		foreach ( $fc_custom_styles as $fc_style ) {
			if ( is_array( $fc_style ) ) {
				foreach ( $fc_style as $skin => $class_style ) {
					if ( is_array( $class_style ) ) {
						foreach ( $class_style as $class => $style ) {
							$ind_style         = explode( ';', $style );

							if ( strpos( $class, '.' ) !== 0 ) {
								$class = '.' . $class;
							}

							foreach ($ind_style as $css_value) {
								if ( strpos( $css_value, 'font-family' ) !== false ) {
										$font_family_properties   = explode( ':', $css_value );
										if(!empty($font_family_properties['1'])){
											$multiple_family = explode( ',', $font_family_properties['1']);
											if(count($multiple_family)==1){
												$font_families[] = $font_family_properties['1'];
											}
										}
								}
							}

							if ( strpos( $skin, 'infowindow' ) !== false ) {
								$class = ' .wpgmp_infowindow ' . $class;
							} elseif ( strpos( $skin, 'post' ) !== false ) {
								$class = ' .wpgmp_infowindow.wpgmp_infowindow_post ' . $class;
							} elseif ( strpos( $class, 'fc-item-title' ) !== false ) {
								$fc_skin_styles .= ' ' . $class . ' a, ' . $class . ' a:hover, ' . $class . ' a:focus, ' . $class . ' a:visited{' . $style . '}';
							}
							$fc_skin_styles .= ' ' . '.wpgmp-map-' . $map->map_id . ' ' . $class . '{' . $style . '}';
						}
					}
				}
			}
		}

		if ( ! empty( $fc_skin_styles ) ) {
			$map_output .= '<style>' . $fc_skin_styles . '</style>';
		}
		if ( ! empty( $font_families ) ) {
			$font_families = array_unique($font_families);
			$map_data['map_options']['google_fonts'] = $font_families;
		}
	}
}

$map_data['marker_category_icons'] = $marker_category_icons;

$map_data = apply_filters( 'wpgmp_map_data', $map_data, $map );

$map_data = $map_obj->clear_empty_array_values( $map_data );

$auto_fix = $wpgmp_settings['wpgmp_auto_fix'];

if ( $auto_fix == 'true' ) { 
    $map_data_obj = json_encode( $map_data , JSON_UNESCAPED_SLASHES );
}else{
	$map_data_obj = json_encode( $map_data );
}


$map_output    .= '<script>jQuery(document).ready(function($) {var map' . $map_id . ' = $("#map' . $map_id . '").maps(' . $map_data_obj . ').data("wpgmp_maps");});</script>';
$base_font_size = trim( str_replace( 'px', '', $map->map_all_control['wpgmp_base_font_size'] ) );
$css_rules      = array();
$base_class     = '.wpgmp-map-' . $map->map_id . ' ';

if ( $base_font_size != '' ) {
	$base_font_size = $base_font_size . 'px';
	$css_rules[]    = $base_class . ',' . $base_class . ' .wpgmp_tabs_container,' . $base_class . ' .wpgmp_listing_container { font-size : ' . $base_font_size . ' !important;}';
}

if ( trim( $map->map_all_control['wpgmp_custom_css'] ) != '' ) {
	$css_rules[] = $map->map_all_control['wpgmp_custom_css'];
}

if ( ! isset( $map->map_all_control['apply_own_schema'] ) ) {
		$map->map_all_control['apply_own_schema'] = false;
	}


if ( isset( $map->map_all_control['color_schema'] ) && trim( $map->map_all_control['color_schema'] ) != '' and $map->map_all_control['apply_own_schema'] != true ) {
	$color_schema                                  = $map->map_all_control['color_schema'];
	$color_schema_colors                           = explode( '_', $color_schema );
	$map->map_all_control['wpgmp_primary_color']   = $color_schema_colors[0];
	$map->map_all_control['wpgmp_secondary_color'] = $color_schema_colors[1];
}


if ( isset( $map->map_all_control['apply_custom_design'] ) && $map->map_all_control['apply_custom_design'] == 'true' ) {

	if ( trim( $map->map_all_control['wpgmp_primary_color'] ) != '' && $map->map_all_control['wpgmp_primary_color'] != '#' ) {

		$secondary_color = $map->map_all_control['wpgmp_primary_color'];

		$css_rules[] = $base_class . '.wpgmp_tabs_container .wpgmp_tabs li a.active, ' . $base_class . '.fc-primary-bg, ' . $base_class . '.wpgmp_infowindow .fc-badge.info, ' . $base_class . '.wpgmp_toggle_main_container .amenity_type:hover, ' . $base_class . '
.wpgmp_direction_container p input.wpgmp_find_direction,
' . $base_class . '.wpgmp_nearby_container .wpgmp_find_nearby_button, ' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info, ' . $base_class . '.wpgmp_pagination span,
' . $base_class . '.wpgmp_pagination a, ' . $base_class . 'div.categories_filter select,  ' . $base_class . '.wpgmp_toggle_container, ' . $base_class . ' .categories_filter_reset_btn,' . $base_class . '.categories_filter input[type="button"], ' . $base_class . '.categories_filter_reset_btn:hover {
        background-color: ' . $secondary_color . ';
}

' . $base_class . '.wpgmp-select-all,' . $base_class . '.fc-primary-fg{
        color: ' . $secondary_color . ';
} 

' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info {
    border: 1px solid ' . $secondary_color . ';
}

' . $base_class . 'div.wpgmp_search_form input.wpgmp_search_input {
	border-bottom: 1px solid ' . $secondary_color . ';
} ' . $base_class . '.wpgmp_iw_content .fc-item-title span{color:#fff;}' . $base_class . '.wpgmp_location_category.fc-badge.info{color:#fff;}';

	}
}

if ( isset( $map->map_all_control['apply_own_schema'] ) && $map->map_all_control['apply_own_schema'] == 'true' ) {

	if ( trim( $map->map_all_control['wpgmp_secondary_color'] ) != '' && $map->map_all_control['wpgmp_secondary_color'] != '#' ) {

		$primary_color = $map->map_all_control['wpgmp_secondary_color'];
		$css_rules[]   = $base_class . '.wpgmp_tabs_container .wpgmp_tabs, ' . $base_class . '.fc-secondary-bg, ' . $base_class . '.wpgmp_toggle_main_container .amenity_type, ' . $base_class . '.wpgmp_pagination span.current, ' . $base_class . '.wpgmp_pagination a:hover, .wpgmp_toggle_main_container input[type="submit"] {
background: ' . $primary_color . '; 
}

' . $base_class . '.fc-secondary-fg,' . $base_class . '.wpgmp_infowindow .fc-item-title,' . $base_class . '.wpgmp_tabs_container .wpgmp_tab_item .wpgmp_cat_title, ' . $base_class . '.wpgmp_location_title a.place_title {
    color: ' . $primary_color . '; 
}

' . $base_class . 'div.wpgmp_search_form input.wpgmp_search_input:focus {
    border: 1px solid ' . $primary_color . '; 
}' . $base_class . '.wpgmp_location_category.fc-badge.info{color:#fff;}' . $base_class . '.wpgmp_iw_content .fc-item-title span{color:#fff;}';

	}


	/* End Primary Color */

	if ( trim( $map->map_all_control['wpgmp_primary_color'] ) != '' && $map->map_all_control['wpgmp_primary_color'] != '#' ) {

		$secondary_color = $map->map_all_control['wpgmp_primary_color'];

		$css_rules[] = $base_class . '.wpgmp_tabs_container .wpgmp_tabs li a.active, ' . $base_class . '.fc-primary-bg, ' . $base_class . '.wpgmp_infowindow .fc-badge.info, ' . $base_class . '.wpgmp_toggle_main_container .amenity_type:hover, ' . $base_class . '
.wpgmp_direction_container p input.wpgmp_find_direction,
' . $base_class . '.wpgmp_nearby_container .wpgmp_find_nearby_button, ' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info, ' . $base_class . '.wpgmp_pagination span,
' . $base_class . '.wpgmp_pagination a, ' . $base_class . 'div.categories_filter select,  ' . $base_class . '.wpgmp_toggle_container, ' . $base_class . '.categories_filter_reset_btn,' . $base_class . '.categories_filter input[type="button"], ' . $base_class . '.categories_filter_reset_btn:hover {
        background-color: ' . $secondary_color . ';
}

' . $base_class . '.wpgmp-select-all,' . $base_class . '.fc-primary-fg {
        color: ' . $secondary_color . ';
} 

' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info {
    border: 1px solid ' . $secondary_color . ';
}

' . $base_class . 'div.wpgmp_search_form input.wpgmp_search_input {
	border-bottom: 1px solid ' . $secondary_color . ';
}
';

	}
}


if ( ! empty( $css_rules ) ) {
	$map_output .= '<style>' . implode( ' ', $css_rules ) . '</style>';
}

return $map_output;
