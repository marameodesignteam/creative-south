<?php
/**
 * Map's general setting(s).
 *
 * @package Maps
 */

$form->add_element(
	'text', 'map_title', array(
		'label'       => esc_html__( 'Map Title', 'wpgmp-google-map' ),
		'value'       => isset( $data['map_title'] ) ? $data['map_title'] : '',
		'desc'        => esc_html__( 'Enter here the map title.', 'wpgmp-google-map' ),
		'required'    => true,
		'placeholder' => '',
	)
);
$form->add_element(
	'text', 'map_width', array(
		'label'       => esc_html__( 'Map Width', 'wpgmp-google-map' ),
		'value'       => isset( $data['map_width'] ) ? $data['map_width'] : '',
		'desc'        => esc_html__( 'Enter here the map width in pixel. Leave it blank for 100% width.', 'wpgmp-google-map' ),
		'placeholder' => '',
	)
);
$form->add_element(
	'text', 'map_height', array(
		'label'       => esc_html__( 'Map Height', 'wpgmp-google-map' ),
		'value'       => isset( $data['map_height'] ) ? $data['map_height'] : '',
		'desc'        => esc_html__( 'Enter here the map height in pixel.', 'wpgmp-google-map' ),
		'required'    => true,
		'placeholder' => '',
	)
);

$zoom_level = array();
for ( $i = 0; $i < 20; $i++ ) {
	$zoom_level[ $i ] = $i;
}

$form->add_element(
	'select', 'map_all_control[map_minzoom_level]', array(
		'label'         => esc_html__( 'Minimum Zoom Level', 'wpgmp-google-map' ),
		'current'       => isset( $data['map_all_control']['map_minzoom_level'] ) ? $data['map_all_control']['map_minzoom_level'] : '',
		'desc'          => esc_html__( 'The minimum zoom level which will be displayed on the map.', 'wpgmp-google-map' ),
		'options'       => $zoom_level,
		'default_value' => 0,
	)
);

$form->add_element(
	'select', 'map_all_control[map_maxzoom_level]', array(
		'label'         => esc_html__( 'Maximum Zoom Level', 'wpgmp-google-map' ),
		'current'       => isset( $data['map_all_control']['map_maxzoom_level'] ) ? $data['map_all_control']['map_maxzoom_level'] : '',
		'desc'          => esc_html__( 'The maximum zoom level which will be displayed on the map.', 'wpgmp-google-map' ),
		'options'       => $zoom_level,
		'default_value' => 19,
	)
);

$form->add_element(
	'select', 'map_zoom_level', array(
		'label'         => esc_html__( 'Default Zoom Level', 'wpgmp-google-map' ),
		'current'       => isset( $data['map_zoom_level'] ) ? $data['map_zoom_level'] : '',
		'desc'          => esc_html__( 'Default zoom level when page is loaded.', 'wpgmp-google-map' ),
		'options'       => $zoom_level,
		'default_value' => 5,
	)
);

$form->add_element(
	'select', 'map_all_control[zoom_level_after_search]', array(
		'label'         => esc_html__( 'Zoom Level After Search', 'wpgmp-google-map' ),
		'current'       => isset( $data['map_all_control']['zoom_level_after_search'] ) ? $data['map_all_control']['zoom_level_after_search'] : '',
		'desc'          => esc_html__( 'Please select zoom level after search a location on map.', 'wpgmp-google-map' ),
		'options'       => $zoom_level,
		'default_value' => 10,
	)
);

$map_type = array(
	'ROADMAP'   => 'ROADMAP',
	'SATELLITE' => 'SATELLITE',
	'HYBRID'    => 'HYBRID',
	'TERRAIN'   => 'TERRAIN',
);
$form->add_element(
	'select', 'map_type', array(
		'label'   => esc_html__( 'Map Type', 'wpgmp-google-map' ),
		'current' => isset( $data['map_type'] ) ? $data['map_type'] : '',
		'options' => $map_type,
	)
);

$form->add_element(
	'checkbox', 'map_scrolling_wheel', array(
		'label'   => esc_html__( 'Turn Off Scrolling Wheel', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_map_scrolling_wheel',
		'current' => isset( $data['map_scrolling_wheel'] ) ? $data['map_scrolling_wheel'] : '',
		'desc'    => esc_html__( 'Please check to disable scroll wheel zoom.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class ',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[doubleclickzoom]', array(
		'label'   => esc_html__( 'Double Click Zoom', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'doubleclickzoom',
		'current' => isset( $data['map_all_control']['doubleclickzoom'] ) ? $data['map_all_control']['doubleclickzoom'] : '',
		'desc'    => esc_html__( 'Please check to enable zoom on double click on the map.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class ',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[map_draggable]', array(
		'label'   => esc_html__( 'Map Draggable', 'wpgmp-google-map' ),
		'value'   => 'false',
		'id'      => 'wpgmp_map_draggable',
		'current' => isset( $data['map_all_control']['map_draggable'] ) ? $data['map_all_control']['map_draggable'] : '',
		'desc'    => esc_html__( 'Please check to disable map draggable.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'checkbox', 'map_45imagery', array(
		'label'   => esc_html__( '45&deg; Imagery', 'wpgmp-google-map' ),
		'value'   => '45',
		'id'      => 'wpgmp_map_45imagery',
		'current' => isset( $data['map_45imagery'] ) ? $data['map_45imagery'] : '',
		'desc'    => esc_html__( 'Apply 45&deg; Imagery ? (only available for map type SATELLITE and HYBRID).', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$gesture = array(
	'auto'        => 'Auto',
	'greedy'      => 'Greedy',
	'cooperative' => 'Cooperative',
	'none'        => 'None',
);
$form->add_element(
	'select', 'map_all_control[gesture]', array(
		'label'   => esc_html__( 'Gesture Handling', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['gesture'] ) ? $data['map_all_control']['gesture'] : '',
		'options' => $gesture,
		'desc'    => esc_html__( 'Controlling Zoom and Pan for desktop, touchscreen and mobile devices.', 'wpgmp-google-map' ),
	)
);
