<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_control_settings', array(
		'value'  => esc_html__( 'Infowindow Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);
$url  = admin_url( 'admin.php?page=wpgmp_how_overview' );
$link = sprintf(
	wp_kses(
		esc_html__( 'Enter placeholders {marker_title},{marker_address},{marker_message},{marker_image},{marker_latitude},{marker_longitude}, {extra_field_slug_here}. View complete list <a target="_blank" href="%s">here</a>.', 'wpgmp-google-map' ), array(
			'a' => array(
				'href'   => array(),
				'target' => '_blank',
			),
		)
	), esc_url( $url )
);

$form->add_element(
	'checkbox', 'map_all_control[infowindow_filter_only]', array(
		'label'   => esc_html__( 'Hide Markers on Page Load', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'infowindow_default_open',
		'current' => isset( $data['map_all_control']['infowindow_filter_only'] ) ? $data['map_all_control']['infowindow_filter_only'] : '',
		'desc'    => esc_html__( "Don't display markers on page load. Display markers after filtration only.", 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$info_default_value = '<div class="fc-main"><div class="fc-item-title">{marker_title} <span class="fc-badge info">{marker_category}</span></div> <div class="fc-item-featured_image">{marker_image} </div>{marker_message}<address><b>Address : </b>{marker_address}</address></div>';

$info_default_value = ( isset( $data['map_all_control']['infowindow_setting'] ) and '' != $data['map_all_control']['infowindow_setting'] ) ? $data['map_all_control']['infowindow_setting'] : $info_default_value;

$default_value = '<div class="fc-main"><div class="fc-item-title">{post_title} <span class="fc-badge info">{post_categories}</span></div> <div class="fc-item-featured_image">{post_featured_image} </div>{post_excerpt}<address><b>Address : </b>{marker_address}</address><a target="_blank"  class="fc-btn fc-btn-small fc-btn-red" href="{post_link}">Read More...</a></div>';
$default_value = ( isset( $data['map_all_control']['infowindow_geotags_setting'] ) and '' != $data['map_all_control']['infowindow_geotags_setting'] ) ? $data['map_all_control']['infowindow_geotags_setting'] : $default_value;

if ( isset( $data['map_all_control']['infowindow_openoption'] ) && 'mouseclick' == $data['map_all_control']['infowindow_openoption'] ) {
	$data['map_all_control']['infowindow_openoption'] = 'click'; } elseif ( isset( $data['map_all_control']['infowindow_openoption'] ) && 'mousehover' == $data['map_all_control']['infowindow_openoption'] ) {
	$data['map_all_control']['infowindow_openoption'] = 'mouseover'; }
	$event = array(
		'click'     => 'Mouse Click',
		'mouseover' => 'Mouse Hover',
	);
	$form->add_element(
		'select', 'map_all_control[infowindow_openoption]', array(
			'label'   => esc_html__( 'Show Infowindow on', 'wpgmp-google-map' ),
			'current' => isset( $data['map_all_control']['infowindow_openoption'] ) ? $data['map_all_control']['infowindow_openoption'] : '',
			'desc'    => esc_html__( 'Open infowindow on Mouse Click or Mouse Hover.', 'wpgmp-google-map' ),
			'options' => $event,
		)
	);

	$form->add_element(
		'image_picker', 'map_all_control[marker_default_icon]', array(
			'label'         => esc_html__( 'Choose Marker Image', 'wpgmp-google-map' ),
			'src'           => ( isset( $data['map_all_control']['marker_default_icon'] ) ? wp_unslash( $data['map_all_control']['marker_default_icon'] ) : WPGMP_IMAGES . '/default_marker.png' ),
			'required'      => false,
			'choose_button' => esc_html__( 'Choose', 'wpgmp-google-map' ),
			'remove_button' => esc_html__( 'Remove', 'wpgmp-google-map' ),
			'id'            => 'marker_category_icon',
		)
	);

	$form->add_element(
		'checkbox', 'map_all_control[infowindow_open]', array(
			'label'   => esc_html__( 'InfoWindow Open', 'wpgmp-google-map' ),
			'value'   => 'true',
			'id'      => 'wpgmp_infowindow_open',
			'current' => isset( $data['map_all_control']['infowindow_open'] ) ? $data['map_all_control']['infowindow_open'] : '',
			'desc'    => esc_html__( 'Please check to enable infowindow default open.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class',
		)
	);

	$form->add_element(
		'checkbox', 'map_all_control[infowindow_close]', array(
			'label'   => esc_html__( 'Close InfoWindow', 'wpgmp-google-map' ),
			'value'   => 'true',
			'id'      => 'wpgmp_infowindow_close',
			'current' => isset( $data['map_all_control']['infowindow_close'] ) ? $data['map_all_control']['infowindow_close'] : '',
			'desc'    => esc_html__( 'Please check to close infowindow on map click.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class',
		)
	);

	$event = array(
		''          => esc_html__( 'Select Animation', 'wpgmp-google-map' ),
		'click'     => esc_html__( 'Mouse Click', 'wpgmp-google-map' ),
		'mouseover' => esc_html__( 'Mouse Hover', 'wpgmp-google-map' ),
	);
	$form->add_element(
		'select', 'map_all_control[infowindow_bounce_animation]', array(
			'label'   => esc_html__( 'Bounce Animation', 'wpgmp-google-map' ),
			'current' => isset( $data['map_all_control']['infowindow_bounce_animation'] ) ? $data['map_all_control']['infowindow_bounce_animation'] : '',
			'desc'    => esc_html__( 'Apply bounce animation on mousehover or mouse click. BOUNCE indicates that the marker should bounce in place.', 'wpgmp-google-map' ),
			'options' => $event,
		)
	);

	$form->add_element(
		'checkbox', 'map_all_control[infowindow_drop_animation]', array(
			'label'   => esc_html__( 'Apply Drop Animation', 'wpgmp-google-map' ),
			'value'   => 'true',
			'id'      => 'infowindow_drop_animation',
			'current' => isset( $data['map_all_control']['infowindow_drop_animation'] ) ? $data['map_all_control']['infowindow_drop_animation'] : '',
			'desc'    => esc_html__( 'DROP indicates that the marker should drop from the top of the map. ', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class',
		)
	);

	$zoom_level     = array();
	$zoom_level[''] = esc_html__( 'Select Zoom', 'wpgmp-google-map' );
	for ( $i = 1; $i < 20; $i++ ) {
		$zoom_level[ $i ] = $i;
	}

	$form->add_element(
		'select', 'map_all_control[infowindow_zoomlevel]', array(
			'label'   => esc_html__( 'Change Zoom on Click', 'wpgmp-google-map' ),
			'current' => isset( $data['map_all_control']['infowindow_zoomlevel'] ) ? $data['map_all_control']['infowindow_zoomlevel'] : '',
			'desc'    => esc_html__( 'Change zoom level of the map on marker click.', 'wpgmp-google-map' ),
			'options' => $zoom_level,
			'before'  => '<div class="fc-3">',
			'after'   => '</div>',
		)
	);

	$form->add_element(
		'checkbox', 'map_all_control[infowindow_iscenter]', array(
			'label'   => esc_html__( 'Center the Map', 'wpgmp-google-map' ),
			'value'   => 'true',
			'current' => isset( $data['map_all_control']['infowindow_iscenter'] ) ? $data['map_all_control']['infowindow_iscenter'] : '',
			'desc'    => esc_html__( 'Set as center point on marker click', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class',
		)
	);

	$form->add_element(
		'group', 'map_infowindow_settings', array(
			'value'  => esc_html__( 'Infowindow Customization Settings', 'wpgmp-google-map' ),
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);

	$form->add_element(
		'checkbox', 'map_all_control[map_infowindow_customisations]', array(
			'label'   => esc_html__( 'Turn On Infowindow Customization', 'wpgmp-google-map' ),
			'value'   => 'true',
			'id'      => 'map_infowindow_customisations',
			'current' => isset( $data['map_all_control']['map_infowindow_customisations'] ) ? $data['map_all_control']['map_infowindow_customisations'] : '',
			'desc'    => esc_html__( 'Please check to enable infowindow customization.', 'wpgmp-google-map' ),
			'class'   => 'switch_onoff chkbox_class',
			'data'    => array( 'target' => '.map_iw_customisations' ),
		)
	);

	$form->add_element(
		'text', 'map_all_control[infowindow_width]', array(
			'label'         => esc_html__( 'Width', 'wpgmp-google-map' ),
			'value'         => isset( $data['map_all_control']['infowindow_width'] ) ? $data['map_all_control']['infowindow_width'] : '',
			'class'         => 'form-control map_iw_customisations',
			'desc'          => esc_html__( 'Enter infowindow width in px. Leave blank for default settings.', 'wpgmp-google-map' ),
			'show'          => 'false',
			'default_value' => '',
		)
	);

	$form->add_element(
		'text', 'map_all_control[infowindow_border_color]', array(
			'label'         => esc_html__( 'Border Color', 'wpgmp-google-map' ),
			'value'         => isset( $data['map_all_control']['infowindow_border_color'] ) ? $data['map_all_control']['infowindow_border_color'] : '',
			'class'         => 'color {pickerClosable:true} form-control map_iw_customisations',
			'desc'          => esc_html__( 'Choose color for the border of infowindow. Leave blank for default settings.', 'wpgmp-google-map' ),
			'show'          => 'false',
			'default_value' => '',
		)
	);

	$form->add_element(
		'text', 'map_all_control[infowindow_border_radius]', array(
			'label'         => esc_html__( 'Border Radius', 'wpgmp-google-map' ),
			'value'         => isset( $data['map_all_control']['infowindow_border_radius'] ) ? $data['map_all_control']['infowindow_border_radius'] : '',
			'class'         => 'form-control map_iw_customisations',
			'desc'          => esc_html__( 'Enter border radius in px for the infowindow. Leave blank for default settings.', 'wpgmp-google-map' ),
			'show'          => 'false',
			'default_value' => '',
		)
	);

	$form->add_element(
		'text', 'map_all_control[infowindow_bg_color]', array(
			'label'         => esc_html__( 'Background Color', 'wpgmp-google-map' ),
			'value'         => isset( $data['map_all_control']['infowindow_bg_color'] ) ? $data['map_all_control']['infowindow_bg_color'] : '',
			'class'         => 'color {pickerClosable:true} form-control map_iw_customisations',
			'desc'          => esc_html__( 'Choose color for the background of infowindow text. Leave blank for default settings.', 'wpgmp-google-map' ),
			'show'          => 'false',
			'default_value' => '',
		)
	);

	$form->add_element(
		'group', 'map_infowindow_location_skin_setting', array(
			'value'  => esc_html__( 'Infowindow Message for Locations', 'wpgmp-google-map' ),
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);
	$location_placeholders = array(
		'{marker_id}',
		'{marker_title}',
		'{marker_image}',
		'{marker_address}',
		'{marker_message}',
		'{marker_category}',
		'{marker_icon}',
		'{marker_latitude}',
		'{marker_longitude}',
		'{marker_city}',
		'{marker_state}',
		'{marker_country}',
		'{marker_zoom}',
		'{marker_postal_code}',
		'{extra_field_slug}',
		'{get_directions_link}'
	);
	$form->add_element(
		'templates', 'map_all_control[location_infowindow_skin]', array(
			'before'              => '<div class="fc-12 fc-type-infowindow">',
			'after'               => '</div>',
			'template_types'      => 'infowindow',
			'templatePath'        => WPGMP_TEMPLATES,
			'templateURL'         => WPGMP_TEMPLATES_URL,
			'data_placeholders'   => $location_placeholders,
			'customiser'          => 'true',
			'current'             => ( isset( $data['map_all_control']['location_infowindow_skin'] ) ) ? $data['map_all_control']['location_infowindow_skin'] : array(
				'name'       => 'default',
				'type'       => 'infowindow',
				'sourcecode' => $info_default_value,
			),
			'customiser_controls' => array( 'edit_mode', 'placeholder', 'sourcecode' ),
		)
	);


	$form->add_element(
		'group', 'map_infowindow_post_skin_setting', array(
			'value'  => esc_html__( 'Infowindow Message for Posts', 'wpgmp-google-map' ),
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);

	$post_placeholders = array(
		'{post_title}',
		'{post_link}',
		'{post_excerpt}',
		'{post_content}',
		'{post_featured_image}',
		'{post_categories}',
		'{post_tags}',
		'{%custom_field_slug_here%}',
		'{get_directions_link}'
	);
	$form->add_element(
		'templates', 'map_all_control[post_infowindow_skin]', array(
			'before'              => '<div class="fc-12 fc-type-post">',
			'after'               => '</div>',
			'template_types'      => 'post',
			'data_placeholders'   => $post_placeholders,
			'templatePath'        => WPGMP_TEMPLATES,
			'templateURL'         => WPGMP_TEMPLATES_URL,
			'customiser'          => 'true',
			'current'             => ( isset( $data['map_all_control']['post_infowindow_skin'] ) ) ? $data['map_all_control']['post_infowindow_skin'] : array(
				'name'       => 'default',
				'type'       => 'post',
				'sourcecode' => $default_value,
			),
			'customiser_controls' => array( 'edit_mode', 'placeholder', 'sourcecode' ),
		)
	);

	$form->add_element(
		'group', 'map_control_layers', array(
			'value'  => esc_html__( 'Layers Settings', 'wpgmp-google-map' ),
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);
	$form->add_element(
		'checkbox', 'map_layer_setting[choose_layer][kml_layer]', array(
			'label'   => esc_html__( 'Kml/Kmz Layer', 'wpgmp-google-map' ),
			'value'   => 'KmlLayer',
			'id'      => 'wpgmp_kml_layer',
			'current' => isset( $data['map_layer_setting']['choose_layer']['kml_layer'] ) ? $data['map_layer_setting']['choose_layer']['kml_layer'] : '',
			'desc'    => esc_html__( 'Please check to enable Kml/Kmz Layer.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class switch_onoff',
			'data'    => array( 'target' => '#map_links' ),
		)
	);
	$form->add_element(
		'textarea', 'map_layer_setting[map_links]', array(
			'label'         => esc_html__( 'KML Link(s)', 'wpgmp-google-map' ),
			'value'         => isset( $data['map_layer_setting']['map_links'] ) ? $data['map_layer_setting']['map_links'] : '',
			'desc'          => esc_html__( 'Paste here kml or kmz link. you can insert multiple kml or kmz links by comma (,) separated.', 'wpgmp-google-map' ),
			'textarea_rows' => 10,
			'textarea_name' => 'map_layer_setting[map_links]',
			'class'         => 'form-control',
			'id'            => 'map_links',
			'show'          => 'false',
		)
	);
	$form->add_element(
		'checkbox', 'map_layer_setting[choose_layer][fusion_layer]', array(
			'label'   => esc_html__( 'Fusion Table Layer', 'wpgmp-google-map' ),
			'value'   => 'FusionTablesLayer',
			'id'      => 'wpgmp_fusion_layer',
			'current' => isset( $data['map_layer_setting']['choose_layer']['fusion_layer'] ) ? $data['map_layer_setting']['choose_layer']['fusion_layer'] : '',
			'desc'    => esc_html__( 'Please check to enable Fusion Table Layer.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class switch_onoff',
			'data'    => array( 'target' => '.fusion_setting' ),
		)
	);

	$form->add_element(
		'text', 'map_layer_setting[fusion_select]', array(
			'label'  => esc_html__( 'Fusion Select', 'wpgmp-google-map' ),
			'value'  => isset( $data['map_layer_setting']['fusion_select'] ) ? $data['map_layer_setting']['fusion_select'] : '',
			'id'     => 'fusion_select',
			'class'  => 'form-control fusion_setting',
			'desc'   => esc_html__( 'A select property whose value is the column name containing the location information in your fusion table. ', 'wpgmp-google-map' ),
			'before' => '<div class="fc-8">',
			'after'  => '</div>',
			'show'   => 'false',
		)
	);

	$form->add_element(
		'text', 'map_layer_setting[fusion_from]', array(
			'label'  => esc_html__( 'Fusion From', 'wpgmp-google-map' ),
			'value'  => isset( $data['map_layer_setting']['fusion_from'] ) ? $data['map_layer_setting']['fusion_from'] : '',
			'id'     => 'fusion_from',
			'class'  => 'form-control fusion_setting',
			'desc'   => esc_html__( 'A from property whose value is either of the Encrypted ID.', 'wpgmp-google-map' ),
			'before' => '<div class="fc-8">',
			'after'  => '</div>',
			'show'   => 'false',
		)
	);

	$url  = 'https://www.google.com/fusiontables/DataSource?docid=1BDnT5U1Spyaes0Nj3DXciJKa_tuu7CzNRXWdVA#map:id=3';
	$link = sprintf(
		wp_kses(
			esc_html__( 'Specify Marker icon name from the <a target="_blank" href="%s">supported Map marker or Icon names</a>.', 'wpgmp-google-map' ), array(
				'a' => array(
					'href'   => array(),
					'target' => '_blank',
				),
			)
		), esc_url( $url )
	);

	$form->add_element(
		'text', 'map_layer_setting[fusion_icon_name]', array(
			'label'  => esc_html__( 'Icon Name', 'wpgmp-google-map' ),
			'value'  => isset( $data['map_layer_setting']['fusion_icon_name'] ) ? $data['map_layer_setting']['fusion_icon_name'] : '',
			'id'     => 'fusion_from',
			'class'  => 'form-control fusion_setting',
			'desc'   => $link,
			'before' => '<div class="fc-8">',
			'after'  => '</div>',
			'show'   => 'false',
		)
	);

	$form->add_element(
		'checkbox', 'map_layer_setting[heat_map]', array(
			'label'   => esc_html__( 'Heat Map', 'wpgmp-google-map' ),
			'value'   => 'true',
			'id'      => 'wpgmp_heat_map',
			'class'   => 'form-control fusion_setting',
			'current' => isset( $data['map_layer_setting']['heat_map'] ) ? $data['map_layer_setting']['heat_map'] : '',
			'desc'    => esc_html__( 'Enable heatmaps, where the density of matched locations is depicted using a palette of colors.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class fusion_setting',
			'show'    => 'false',
		)
	);

	$form->add_element(
		'checkbox', 'map_layer_setting[choose_layer][traffic_layer]', array(
			'label'   => esc_html__( 'Traffic Layer', 'wpgmp-google-map' ),
			'value'   => 'TrafficLayer',
			'id'      => 'wpgmp_traffic_layer',
			'current' => isset( $data['map_layer_setting']['choose_layer']['traffic_layer'] ) ? $data['map_layer_setting']['choose_layer']['traffic_layer'] : '',
			'desc'    => esc_html__( 'Please check to enable traffic Layer.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class',
		)
	);

	$form->add_element(
		'checkbox', 'map_layer_setting[choose_layer][transit_layer]', array(
			'label'   => esc_html__( 'Transit Layer', 'wpgmp-google-map' ),
			'value'   => 'TransitLayer',
			'id'      => 'wpgmp_transit_layer',
			'current' => isset( $data['map_layer_setting']['choose_layer']['transit_layer'] ) ? $data['map_layer_setting']['choose_layer']['transit_layer'] : '',
			'desc'    => esc_html__( 'Please check to enable Transit Layer.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class',
		)
	);


	$form->add_element(
		'checkbox', 'map_layer_setting[choose_layer][bicycling_layer]', array(
			'label'   => esc_html__( 'Bicycling Layer', 'wpgmp-google-map' ),
			'value'   => 'BicyclingLayer',
			'id'      => 'wpgmp_bicycling_layer',
			'current' => isset( $data['map_layer_setting']['choose_layer']['bicycling_layer'] ) ? $data['map_layer_setting']['choose_layer']['bicycling_layer'] : '',
			'desc'    => esc_html__( 'Please check to enable Bicycling Layer.', 'wpgmp-google-map' ),
			'class'   => 'chkbox_class',
		)
	);
