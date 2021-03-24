<?php
/**
 * Template for Drawing Operation
 *
 * @author  Flipper Code <hello@flippercode.com>
 * @package Maps
 */

global $wpdb;
$modelFactory = new WPGMP_Model();
$mapobj       = $modelFactory->create_object( 'map' );
$map_records  = $mapobj->fetch();
if ( isset( $_REQUEST['_wpnonce'] ) ) {

	$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

		die( 'Cheating...' );

	} else {
		$data = $_POST;
	}
}
if ( ! empty( $_POST['save_shapes'] ) && $_POST['save_shapes'] == 'save_shapes' ) {
	$map_id                                       = intval( wp_unslash( $_POST['map_id'] ) );
	$data['polylines']                            = $_POST['shapes_values'];
	$infowindow['map_polyline_setting']['shapes'] = serialize( $data );
	$in_loc_data                                  = array(
		'map_polyline_setting' => $infowindow['map_polyline_setting']['shapes'],
	);
	$where['map_id']                              = $map_id;
	$insertId                                     = FlipperCode_Database::insert_or_update( TBL_MAP, $in_loc_data, $where );
}

if ( ! empty( $_GET['map_id'] ) ) {
	$map_id       = intval( wp_unslash( $_GET['map_id'] ) );
	$selected_map = $mapobj->fetch( array( array( 'map_id', '=', $map_id ) ) );
} else {
	$map_id = '';
}
$all_map[] = esc_html__( 'Select Map', 'wpgmp-google-map' );
foreach ( $map_records as $key => $map_record ) {
	$all_map[ $map_record->map_id ] = $map_record->map_title;
}

if ( isset( $_GET['page'] ) ) {
	$page_name = sanitize_text_field( wp_unslash( $_GET['page'] ) );
} else {
	$page_name = '';
}

$form = new WPGMP_Template();
$form->set_form_method( 'get' );
$form->add_element(
	'hidden', 'page', array(
		'value' => $page_name,
	)
);
$form->set_header( esc_html__( 'Choose Map for Drawing', 'wpgmp-google-map' ), $response, esc_html__( 'Choose Map for Drawing', 'wpgmp-google-map' ), 'wpgmp_manage_location' );
$form->add_element(
	'select', 'map_id', array(
		'label'    => esc_html__( 'Select map', 'wpgmp-google-map' ),
		'current'  => $map_id,
		'value'    => isset( $data['map_id'] ) ? $data['map_id'] : '',
		'desc'     => esc_html__( 'Enter here the location title.', 'wpgmp-google-map' ),
		'required' => true,
		'options'  => $all_map,
	)
);
$form->render();
if ( ! empty( $map_id ) ) {
	$form = new FlipperCode_HTML_Markup( array( 'no_header' => true ) );
	$form->set_header( esc_html__( 'Draw Shapes', 'wpgmp-google-map' ), $response, esc_html__( 'Draw Shapes', 'wpgmp-google-map' ), 'wpgmp_manage_location' );

	$form->output( $form->get_header() );
	$form->output( $form->get_form_header() );

	echo "<div class='form-group'><div class='fc-8'>";
	// do_shortcode("[put_wpgm id=".$map_id."]",false);
	wpgmp_generate_map( $selected_map[0] );
	echo "</div><div class='fc-4'>";
	echo '<h4 class="fc-title-blue">' . esc_html__( 'Shape Properties', 'wpgmp-google-map' ) . '<i class="wpgmp-shape-delete hiderow dashicons-before dashicons-trash"></i></h4>';
	echo "<div class='row hiderow'><div class='fc-6'>";
	$form->output(
		FlipperCode_HTML_Markup::field_text(
			'shape_stroke_color', array(
				'value'       => '#ff0000',
				'class'       => 'color {pickerClosable:true} form-control',
				'id'          => 'shape_stroke_color',
				'desc'        => esc_html__( 'Stroke Color', 'wpgmp-google-map' ),
				'placeholder' => esc_html__( 'Stroke Color', 'wpgmp-google-map' ),
			)
		)
	);
	echo "</div><div class='fc-6'>";
	$stroke_opacity = array(
		'1'   => '1',
		'0.9' => '0.9',
		'0.8' => '0.8',
		'0.7' => '0.7',
		'0.6' => '0.6',
		'0.5' => '0.5',
		'0.4' => '0.4',
		'0.3' => '0.3',
		'0.2' => '0.2',
		'0.1' => '0.1',
	);

	$form->output(
		FlipperCode_HTML_Markup::field_text(
			'shape_fill_color', array(
				'value'       => '#ff0000',
				'class'       => 'color {pickerClosable:true} form-control',
				'id'          => 'shape_fill_color',
				'desc'        => esc_html__( 'Fill Color', 'wpgmp-google-map' ),
				'placeholder' => esc_html__( 'Fill Color', 'wpgmp-google-map' ),
			)
		)
	);

	echo "</div></div><div class='row hiderow'><div class='fc-6'>";
	$stroke_weight = array(
		'1'  => '1',
		'2'  => '2',
		'3'  => '3',
		'4'  => '4',
		'5'  => '5',
		'6'  => '6',
		'7'  => '7',
		'8'  => '8',
		'9'  => '9',
		'10' => '10',
		'11' => '11',
		'12' => '12',
		'13' => '13',
		'14' => '14',
		'15' => '15',
		'16' => '16',
		'17' => '17',
		'18' => '18',
		'19' => '19',
		'20' => '20',
	);

	$form->output(
		FlipperCode_HTML_Markup::field_select(
			'shape_stroke_weight', array(
				'current' => ( isset( $data['shape_stroke_weight'] ) and ! empty( $data['shape_stroke_weight'] ) ) ? sanitize_text_field( wp_unslash( $data['shape_stroke_weight'] ) ) : '',
				'desc'    => esc_html__( 'Stroke Weight', 'wpgmp-google-map' ),
				'options' => $stroke_weight,
				'class'   => 'form-control-select',
			)
		)
	);
	echo "</div><div class='fc-6'>";
	$form->output(
		FlipperCode_HTML_Markup::field_select(
			'shape_stroke_opacity', array(
				'current' => ( isset( $data['shape_stroke_opacity'] ) and ! empty( $data['route_stroke_opacity'] ) ) ? sanitize_text_field( wp_unslash( $data['route_stroke_opacity'] ) ) : '',
				'desc'    => esc_html__( 'Stroke Opacity', 'wpgmp-google-map' ),
				'options' => $stroke_opacity,
				'class'   => 'form-control-select',
			)
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-6'>";
	$form->output(
		FlipperCode_HTML_Markup::field_select(
			'shape_fill_opacity', array(
				'current' => ( isset( $data['shape_fill_opacity'] ) and ! empty( $data['shape_fill_opacity'] ) ) ? sanitize_text_field( wp_unslash( $data['shape_fill_opacity'] ) ) : '',
				'desc'    => esc_html__( 'Fill Opacity', 'wpgmp-google-map' ),
				'options' => $stroke_opacity,
				'class'   => 'form-control-select',
			)
		)
	);
	echo "</div><div class='fc-6'>";
	$form->output(
		FlipperCode_HTML_Markup::field_text(
			'shape_radius', array(
				'value' => '',
				'desc'  => esc_html__( 'Radius (Meters)', 'wpgmp-google-map' ),
				'class' => 'form-control',
			)
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-12'>";
	$form->output(
		FlipperCode_HTML_Markup::field_text(
			'shape_center', array(
				'value' => '',
				'desc'  => esc_html__( 'Center Cordinates', 'wpgmp-google-map' ),
				'class' => 'form-control',
			)
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-12'>";
	$form->output(
		FlipperCode_HTML_Markup::field_text(
			'shape_northeast', array(
				'value' => '',
				'desc'  => esc_html__( 'NorthEast Corner', 'wpgmp-google-map' ),
				'class' => 'form-control',
			)
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-12'>";
	$form->output(
		FlipperCode_HTML_Markup::field_text(
			'shape_southwest', array(
				'value' => '',
				'desc'  => esc_html__( 'SouthWest Corner', 'wpgmp-google-map' ),
				'class' => 'form-control',
			)
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-12'>";
	$form->output(
		FlipperCode_HTML_Markup::field_textarea(
			'shape_path', array(
				'value'       => '',
				'class'       => 'form-control',
				'id'          => 'shape_path',
				'desc'        => esc_html__( 'Cordinates', 'wpgmp-google-map' ),
				'placeholder' => esc_html__( 'Cordinates', 'wpgmp-google-map' ),
			)
		)
	);
	echo '</div></div>';

	$form->output(
		FlipperCode_HTML_Markup::field_message(
			'shape_message', array(
				'value' => esc_html__( 'Draw or click on a shape to apply properties.', 'wpgmp-google-map' ),
				'class' => 'fc-msg fc-msg-default',
			)
		)
	);
	echo '';

	echo '<h4 class="fc-title-blue">' . esc_html__( 'Shape onclick Event', 'wpgmp-google-map' ) . '</h4>';

	$shape_events = array(
		'click'     => 'click',
		'dblclick'  => 'dblclick',
		'mouseover' => 'mouseover',
		'mouseout'  => 'mouseout',
	);
	echo "<div class='row hiderow'><div class='fc-12'>";
	$form->output(
		FlipperCode_HTML_Markup::field_text(
			'shape_click_url', array(
				'value'       => '',
				'class'       => 'form-control',
				'id'          => 'shape_click_url',
				'desc'        => esc_html__( 'Redirect URL', 'wpgmp-google-map' ),
				'placeholder' => esc_html__( 'Redirect url on click.', 'wpgmp-google-map' ),
			)
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-12'>";
	$form->output(
		FlipperCode_HTML_Markup::field_textarea(
			'shape_click_message', array(
				'value'       => '',
				'class'       => 'form-control',
				'id'          => 'shape_click_message',
				'desc'        => esc_html__( 'Message on click.', 'wpgmp-google-map' ),
				'placeholder' => esc_html__( 'Message to display on click.', 'wpgmp-google-map' ),
			)
		)
	);
	echo '</div></div>';
	$form->output(
		FlipperCode_HTML_Markup::field_submit(
			'wpgmp_save_drawing', array(
				'value' => esc_html__( 'Save Drawing', 'wpgmp-google-map' ),
			)
		)
	);
	echo '</div></div>';
	$form->output(
		FlipperCode_HTML_Markup::field_hidden(
			'shapes_values', array(
				'value' => '',
			)
		)
	);
	$form->output(
		FlipperCode_HTML_Markup::field_hidden(
			'map_id', array(
				'value' => $map_id,
			)
		)
	);
	$form->output(
		FlipperCode_HTML_Markup::field_hidden(
			'operation', array(
				'value' => 'save',
			)
		)
	);
	$form->output( $form->get_form_footer() );
	$form->output( $form->get_footer() );
}
function wpgmp_generate_map( $map ) {

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

	// Fetch map information.
	$modelFactory        = new WPGMP_Model();
	$category_obj        = $modelFactory->create_object( 'group_map' );
	$categories          = $category_obj->fetch();
	$all_categories      = array();
	$all_categories_name = array();
	$route_obj           = $modelFactory->create_object( 'route' );
	$all_routes          = $route_obj->fetch();

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $category ) {
			$all_categories[ $category->group_map_id ]         = $category;
			$all_categories_name[ $category->group_map_title ] = $category;
		}
	}
	if ( ! empty( $map->map_locations ) ) {
		$location_obj  = $modelFactory->create_object( 'location' );
		$map_locations = $location_obj->fetch( array( array( 'location_id', 'IN', implode( ',', $map->map_locations ) ) ) );
	}
	// Routes data
	if ( ! empty( $all_routes ) ) {
		$routes_data = array();
		foreach ( $all_routes as $route ) {
			$routes_data[ $route->route_id ] = $route;
		}
	}
	$map_id   = $map->map_id;
	$map_data = array();
	// Set map options.
	$map_data['places']      = array();
	$map_data['map_options'] = array(
		'center_lat'                   => sanitize_text_field( $map->map_all_control['map_center_latitude'] ),
		'center_lng'                   => sanitize_text_field( $map->map_all_control['map_center_longitude'] ),
		'zoom'                         => intval( $map->map_zoom_level ),
		'search_control'               => ( isset( $page_name ) && $page_name == 'wpgmp_manage_drawing' ) ? true : false,
		'search_control_position'      => 'BOTTOM_CENTER',
		'map_type_id'                  => sanitize_text_field( $map->map_type ),
		'center_circle_fillcolor'      => sanitize_text_field( $map->map_all_control['center_circle_fillcolor'] ),
		'center_circle_fillopacity'    => sanitize_text_field( $map->map_all_control['center_circle_fillopacity'] ),
		'center_circle_strokecolor'    => sanitize_text_field( $map->map_all_control['center_circle_strokecolor'] ),
		'center_circle_strokeopacity'  => sanitize_text_field( $map->map_all_control['center_circle_strokeopacity'] ),
		'center_circle_strokeweight'   => sanitize_text_field( $map->map_all_control['center_circle_strokeweight'] ),
		'scroll_wheel'                 => sanitize_text_field( $map->map_scrolling_wheel ),
		'display_45_imagery'           => sanitize_text_field( $map->map_45imagery ),
		'marker_default_icon'          => esc_url( $map->map_all_control['marker_default_icon'] ),
		'infowindow_setting'           => wp_unslash( $map->map_all_control['infowindow_setting'] ),
		'infowindow_skin'              => array(
			'name'       => 'default',
			'type'       => 'infowindow',
			'sourcecode' => wp_unslash( $map->map_all_control['infowindow_setting'] ),
		),
	);

	$map_data['map_options']['width'] = sanitize_text_field( $map->map_width );

	$map_data['map_options']['height'] = sanitize_text_field( $map->map_height );

	$map_data['map_options'] = apply_filters( 'wpgmp_map_options', $map_data['map_options'] );

	if ( isset( $map_data['map_options']['width'] ) ) {
		$width = $map_data['map_options']['width'];
	} else {
		$width = '100%'; }

	if ( isset( $map_data['map_options']['height'] ) ) {
		$height = $map_data['map_options']['height'];
	} else {
		$height = '300px'; }

	if ( strstr( $width, '%' ) === false ) {
		$width = str_replace( 'px', '', $width ) . 'px';
	}

	if ( strstr( $height, '%' ) === false ) {
		$height = str_replace( 'px', '', $height ) . 'px';
	}
	$width  = '100%';
	$height = '500px';

	$wpgmp_local = array();
	if ( isset( $map->map_all_control['wpgmp_language'] ) && $map->map_all_control['wpgmp_language'] ) {
		$wpgmp_local['language'] = $map->map_all_control['wpgmp_language'];
	} else {
		$wpgmp_local['language'] = 'en'; }

	$wpgmp_local['wpgmp_not_working']         = esc_html__( 'not working...', 'wpgmp-google-map' );
	$wpgmp_local['place_icon_url']            = WPGMP_ICONS;
	$wpgmp_local['wpgmp_location_no_results'] = esc_html__( 'No results found.', 'wpgmp-google-map' );
	$wpgmp_local['wpgmp_route_not_avilable']  = esc_html__( 'Route is not available for your requested route.', 'wpgmp-google-map' );
	$wpgmp_local['img_grid']                  = "<span class='span_grid'><a class='wpgmp_grid'><img src='" . WPGMP_IMAGES . "grid.png'></a></span>";
	$wpgmp_local['img_list']                  = "<span class='span_list'><a class='wpgmp_list'><img src='" . WPGMP_IMAGES . "list.png'></a></span>";
	$wpgmp_local['img_print']                 = "<span class='span_print'><a class='wpgmp_print' onclick=jQuery('.wpgmp_print_listing').print()><img src='" . WPGMP_IMAGES . "print.png'></a></span>";
	wp_localize_script( 'wpgmp-google-map-main', 'wpgmp_local', $wpgmp_local );

	if ( !empty( $map_locations ) && is_array( $map_locations )) {
		$loc_count = 0;
		foreach ( $map_locations as $location ) {
			$location_categories = array();
			if ( empty( $location->location_group_map ) ) {
				$map_data['places'][ $loc_count ]['categories'][] = array(
					'id'   => '',
					'name' => '',
					'type' => 'category',
					'icon' => '',
				);
			} else {

				foreach ( $location->location_group_map as $key => $loc_category_id ) {
					$loc_category          = isset($all_categories[ $loc_category_id ] ) && !empty($all_categories[ $loc_category_id ]) ? $all_categories[ $loc_category_id ] : array();
					$location_categories[] = array(
						'id'   => isset($loc_category->group_map_id) ? $loc_category->group_map_id : '',
						'name' => isset($loc_category->group_map_title) ? $loc_category->group_map_title : '',
						'type' => 'category',
						'icon' => isset($loc_category->group_marker) ? $loc_category->group_marker : '',
					);
				}
			}
			// Extra Fields in location
			$extra_fields = array();

			if ( isset( $location->location_extrafields['key'] ) ) {
				foreach ( $location->location_extrafields['key'] as $i => $label ) {
					$extra_fields[ sanitize_title( $label ) ] = $location->location_extrafields['value'][ $i ];
				}
			}

			$map_data['places'][ $loc_count ] = array(
				'id'         => $location->location_id,
				'title'      => $location->location_title,
				'address'    => $location->location_address,
				'content'    => $location->location_messages,
				'location'   => array(
					'icon'                    => $location_categories[0]['icon'],
					'lat'                     => $location->location_latitude,
					'lng'                     => $location->location_longitude,
					'city'                    => $location->location_city,
					'state'                   => $location->location_state,
					'country'                 => $location->location_country,
					'onclick_action'          => 'marker',
					'postal_code'             => $location->location_postal_code,
					'draggable'               => ( 'true' == $location->location_draggable ),
					'infowindow_default_open' => $location->location_infowindow_default_open,
					'animation'               => $location->location_animation,
					'infowindow_disable'      => (isset($location->location_settings['hide_infowindow']) ? $location->location_settings['hide_infowindow'] : true ),
					'zoom'                    => 5,
					'marker_image'            => '',
					'extra_fields'            => $extra_fields,
				),
				'categories' => $location_categories,
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
		}

		$map_data['kml_layer'] = array(
			'kml_layers_links' => $kml_layers_links,
		);
	}
	// Fusion Layer.
	if ( ! empty( $map->map_layer_setting['choose_layer']['fusion_layer'] ) && $map->map_layer_setting['choose_layer']['fusion_layer'] == 'FusionTablesLayer' ) {
		$map_data['fusion_layer'] = array(
			'fusion_table_select' => $map->map_layer_setting['fusion_select'],
			'fusion_table_from'   => $map->map_layer_setting['fusion_from'],
			'fusion_icon_name'    => $map->map_layer_setting['fusion_icon_name'],
			'fusion_heat_map'     => ( $map->map_layer_setting['heat_map'] === 'true' ? true : false ),
		);
	}

	if ( ! empty( $map->map_layer_setting['choose_layer']['bicycling_layer'] ) && $map->map_layer_setting['choose_layer']['bicycling_layer'] == 'BicyclingLayer' ) {
		$map_data['bicyle_layer'] = array(
			'display_layer' => true,
		);
	}

	if ( ! empty( $map->map_layer_setting['choose_layer']['traffic_layer'] ) && $map->map_layer_setting['choose_layer']['traffic_layer'] == 'TrafficLayer' ) {
		$map_data['traffic_layer'] = array(
			'display_layer' => true,
		);
	}

	if ( ! empty( $map->map_layer_setting['choose_layer']['transit_layer'] ) && $map->map_layer_setting['choose_layer']['transit_layer'] == 'TransitLayer' ) {
		$map_data['transit_layer'] = array(
			'display_layer' => true,
		);
	}

	// Geo tags for google maps pro
	if ( ! empty( $map->map_all_control['geo_tags'] ) && $map->map_all_control['geo_tags'] == 'true' ) {
		$filter_array = array_filter( $map->map_geotags );
		foreach ( $filter_array as $key => $value ) {
			if ( $key != 'geo_tags' ) {
				$custom_meta_keys = array();

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

				$args = array(
					'post_type'  => $key,
					'meta_query' => array( $custom_meta_keys ),
				);

				$wpgmp_the_query = new WP_Query( $args );

				if ( $wpgmp_the_query->have_posts() ) {
					while ( $wpgmp_the_query->have_posts() ) {
						$wpgmp_the_query->the_post();
						global $post;
						$places         = array();
						$content        = $map->map_all_control['infowindow_geotags_setting'];
						$category_names = '';
						if ( empty( $value['latitude'] ) or empty( $value['longitude'] ) ) {
							continue; }

						$replace_data['post_title']   = get_the_title( $post->ID );
						$replace_data['post_excerpt'] = get_the_excerpt( $post->ID );
						$replace_data['post_content'] = get_the_content( $post->ID );
						$replace_data['post_link']    = get_permalink( $post->ID );
						$categories                   = get_the_category( $post->ID );
						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$category_names .= $category->name . ',';
							}
						}
						$replace_data['post_categories'] = trim( $category_names, ',' );
						$posttags                        = get_the_tags( $post->ID );
						if ( $posttags ) {
							foreach ( $posttags as $tag ) {
								$tag_names .= $tag->name . ',';
							}
						}
						$replace_data['post_tags'] = trim( $tag_names, ',' );
						$featured_image            = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
						if ( isset( $featured_image[0] ) ) {
							$post_featured_image = '<img width="' . $featured_image[1] . '" height="' . $featured_image[2] . '" src="' . $featured_image[0] . '" class="wp-post-image alignleft wpgmp_featured_image" >';
						}

						$replace_data['post_featured_image'] = $post_featured_image;
						$replace_data                        = apply_filters( 'wpgmp_geotags_placeholder', $replace_data, $post->ID, $map->map_id );
						// Here parse infowindow setting and create infowindow message.
						$places['title'] = $replace_data['post_title'];
						foreach ( $replace_data as $placeholder => $holder_value ) {
							$content = str_replace( '{' . $placeholder . '}', $holder_value, $content );
						}

						$places['content'] = apply_filters( 'wpgmp_geotags_content', $content, $post->ID, $map->map_id );
						if ( ! empty( $value['address'] ) ) {
							$places['address'] = get_post_meta( $post->ID, $value['address'], true );
						} else {
							$places['address'] = '';
						}

						if ( empty( $value['latitude'] ) ) {
							$places['location']['lat'] = '';
						} else {
							$places['location']['lat'] = get_post_meta( $post->ID, $value['latitude'], true );
						}

						if ( empty( $value['longitude'] ) ) {
							$places['location']['lng'] = '';
						} else {
							$places['location']['lng'] = get_post_meta( $post->ID, $value['longitude'], true ); }

						if ( ! empty( $value['category'] ) ) {
							$category_name = get_post_meta( $post->ID, $value['category'], true ); }

						if ( ! empty( $category_name ) ) {
							$loc_category                    = $all_categories_name[ sanitize_text_field( $category_name ) ];
							$places['location']['icon']      = $loc_category->group_marker;
							$places['categories'][0]['icon'] = $loc_category->group_marker;
							$places['categories'][0]['name'] = $loc_category->group_map_title;
							$places['categories'][0]['id']   = $loc_category->group_map_id;
						}

						$places['location']['marker_image'] = '';

						$map_data['places'][] = $places;
					}
				}
				wp_reset_postdata();
			}
		}
	}
	
	if ( $map_data['map_options']['center_lat'] == '' && !empty($map_data['places'])) {
		$map_data['map_options']['center_lat'] = $map_data['places'][0]['location']['lat'];
	}

	if ( $map_data['map_options']['center_lng'] == '' && !empty($map_data['places'])) {
		$map_data['map_options']['center_lng'] = $map_data['places'][0]['location']['lng'];
	}

	// Styles
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

			$map_stylers[] = array(
				'featureType' => $map->style_google_map['mapfeaturetype'][ $i ],
				'elementType' => $map->style_google_map['mapelementtype'][ $i ],
				'stylers'     => array(
					array(
						'color'      => $map->style_google_map['color'][ $i ],
						'visibility' => $map->style_google_map['visibility'][ $i ],
					),
				),
			);
		}
	}

	if ( isset( $map_stylers ) ) {
		if ( is_array( $map_stylers ) ) {
			$map_data['styles'] = $map_stylers;
		}
	} elseif ( $map->map_all_control['custom_style'] != '' ) {
		$map_data['styles'] = stripslashes( $map->map_all_control['custom_style'] );
	}

	// routes
	if ( ! empty( $map->map_route_direction_setting['route_direction'] ) && $map->map_route_direction_setting['route_direction'] == 'true' ) {
		$wpgmp_routes = $map->map_route_direction_setting['specific_routes'];
		if ( ! empty( $wpgmp_routes ) ) {

			$all_routes = array();
			foreach ( $wpgmp_routes as $route_key => $wpgmp_route ) {

				if( isset($routes_data[ $wpgmp_route ]) ) {

					$wpgmp_route_data[ $route_key ] = $routes_data[ $wpgmp_route ];
				$wpgmp_route_way_points         = $wpgmp_route_data[ $route_key ]->route_way_points;

				$location_data[ $route_key ]['route_id']                 = $wpgmp_route_data[ $route_key ]->route_id;
				$location_data[ $route_key ]['route_title']              = $wpgmp_route_data[ $route_key ]->route_title;
				$location_data[ $route_key ]['route_stroke_color']       = $wpgmp_route_data[ $route_key ]->route_stroke_color;
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

				if ( $wpgmp_route_data[ $route_key ]->route_start_location && ! empty( $wpgmp_route_data[ $route_key ]->route_start_location ) ) {
					 $route_start_obj                                   = $location_obj->fetch( array( array( 'location_id', 'IN', $wpgmp_route_data[ $route_key ]->route_start_location ) ) );
					$location_data[ $route_key ]['start_location_data'] = $route_start_obj[0]->location_latitude . ',' . $route_start_obj[0]->location_longitude;
				}

				if ( $wpgmp_route_data[ $route_key ]->route_end_location && ! empty( $wpgmp_route_data[ $route_key ]->route_end_location ) ) {
					 $route_end_obj = $location_obj->fetch( array( array( 'location_id', 'IN', $wpgmp_route_data[ $route_key ]->route_end_location ) ) );

					$location_data[ $route_key ]['end_location_data'] = $route_end_obj[0]->location_latitude . ',' . $route_end_obj[0]->location_longitude;
				}

				}
				
			}
		}
		$map_data['routes'] = $location_data;
	}

	$map_data['map_property'] = array(
		'map_id'     => $map->map_id,
		'debug_mode' => false,
	);

	// drawing
	$drawing_editable_true = true;
	$objects               = array( 'circle', 'polygon', 'polyline', 'rectangle' );
	for ( $i = 0; $i < count( $objects ); $i++ ) {
		$object_name    = $objects[ $i ];
		$drawingModes[] = 'google.maps.drawing.OverlayType.' . strtoupper( $object_name );

		$drawing_options[ $object_name ][] = "fillColor: '#003dce'";
		$drawing_options[ $object_name ][] = "strokeColor: '#003dce'";
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

	if ( $map->map_polyline_setting['polylines'] != '' ) {
		$map_shapes      = array();
		$all_saved_shape = $map->map_polyline_setting['polylines'];
		$all_shapes      = explode( '|', $all_saved_shape[0] );
		if ( is_array( $all_shapes ) ) {
			foreach ( $all_shapes as $key => $shapes ) {
				$find_shape = explode( '=', $shapes, 2 );

				if ( $find_shape[0] == 'polylines' ) {
					$polylines_shape[0] = $find_shape[1]; } elseif ( $find_shape[0] == 'polygons' ) {
					$polygons_shape[0] = $find_shape[1]; } elseif ( $find_shape[0] == 'circles' ) {
						$circles_shape[0] = $find_shape[1]; } elseif ( $find_shape[0] == 'rectangles' ) {
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
					$all_settings_val[3] = '#003dce'; }

				if ( empty( $all_settings_val[4] ) ) {
					$all_settings_val[4] = 1; }

				if ( empty( $all_settings_val[2] ) ) {
					$all_settings_val[2] = '#003dce'; }

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
				$events['message']          = stripcslashes( $all_events[1] );
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
					$all_settings_val[2] = '#003dce'; }

				if ( empty( $all_settings_val[1] ) ) {
					$all_settings_val[1] = 1; }

				if ( empty( $all_settings_val[0] ) ) {
					$all_settings_val[0] = 5; }

				$settings['stroke_color']   = '#' . str_replace( '#', '', $all_settings_val[2] );
				$settings['stroke_opacity'] = $all_settings_val[1];
				$settings['stroke_weight']  = $all_settings_val[0];
				$events                     = array();
				$events['url']              = $all_events[0];
				$events['message']          = stripcslashes( $all_events[1] );
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
					$all_settings_val[3] = '#003dce'; }

				if ( empty( $all_settings_val[4] ) ) {
					$all_settings_val[4] = 1; }

				if ( empty( $all_settings_val[2] ) ) {
					$all_settings_val[2] = '#003dce'; }

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
				$events['message']          = stripcslashes( $all_events[1] );
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
					$all_settings_val[3] = '#003dce'; }

				if ( empty( $all_settings_val[4] ) ) {
					$all_settings_val[4] = 1; }

				if ( empty( $all_settings_val[2] ) ) {
					$all_settings_val[2] = '#003dce'; }

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
				$events['message']          = stripcslashes( $all_events[1] );
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

	echo '<div class="wpgmp_map_container" rel="map' . $map->map_id . '">';

	echo '<input class="wpgmp_auto_suggest" placeholder="' . esc_html__( 'Search location...', 'wpgmp-google-map' ) . '" type="text" style="margin-bottom: 20px;">';

	echo '<div class="wpgmp_map" style="width:' . $width . '; height:' . $height . ';" id="map' . $map->map_id . '" ></div>';

	echo '</div>';
	$map_data_obj = json_encode( $map_data );

	echo '<script type="text/javascript">';
	echo 'jQuery(document).ready(function($) {var map = $("#map' . $map_id . '").maps(' . $map_data_obj . ').data("wpgmp_maps");});';
	echo '</script>';
}
