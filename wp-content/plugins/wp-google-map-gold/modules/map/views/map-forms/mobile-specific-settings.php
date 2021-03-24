<?php
/**
 * Map's mobile specific setting(s).
 *
 * @package Maps
 */

$form->add_element(
	'group', 'mobile_specific_settings', array(
		'value'  => esc_html__( 'Screens Specific Setting', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[mobile_specific]', array(
		'label'   => esc_html__( 'Apply Screens Settings', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_overlay',
		'current' => isset( $data['map_all_control']['mobile_specific'] ) ? $data['map_all_control']['mobile_specific'] : '',
		'desc'    => esc_html__( 'Apply screen specific settings for desktop, mobile and tablets.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.map_mobile_specific' ),
	)
);

$screens_options = array();


$zoom_level = array();
for ( $i = 0; $i < 20; $i++ ) {
	$zoom_level[ $i ] = $i;
}


$supported_screens = array( 'Smartphones', 'iPads', 'Large screens' );


foreach ( $supported_screens as $key => $screen ) {
	$screen_slug = sanitize_title( $screen );
	$width       = $form->field_text(
		'map_all_control[screens][' . $screen_slug . '][map_width_mobile]', array(
			'label'       => esc_html__( 'Map Width', 'wpgmp-google-map' ),
			'value'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_width_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_width_mobile'] : '',
			'placeholder' => esc_html__( 'Map width in pixel.', 'wpgmp-google-map' ),
		)
	);

	$height = $form->field_text(
		'map_all_control[screens][' . $screen_slug . '][map_height_mobile]', array(
			'label'       => esc_html__( 'Map Height', 'wpgmp-google-map' ),
			'value'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_height_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_height_mobile'] : '',
			'placeholder' => esc_html__( 'Map height in pixel.', 'wpgmp-google-map' ),
		)
	);


	$zoom = $form->field_select(
		'map_all_control[screens][' . $screen_slug . '][map_zoom_level_mobile]', array(
			'label'         => esc_html__( 'Map Zoom Level', 'wpgmp-google-map' ),
			'current'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_zoom_level_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_zoom_level_mobile'] : '',
			'options'       => $zoom_level,
			'class'         => 'form-controls',
			'default_value' => '5',
		)
	);

	$draggable = $form->field_checkbox(
		'map_all_control[screens][' . $screen_slug . '][map_draggable_mobile]', array(
			'label'         => esc_html__( 'Map Draggable', 'wpgmp-google-map' ),
			'value'         => 'false',
			'id'            => 'wpgmp_map_draggable_mobile',
			'current'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_draggable_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_draggable_mobile'] : '',
			'desc'          => esc_html__( 'Tick to off map draggable.', 'wpgmp-google-map' ),
			'class'         => 'chkbox_class',
			'default_value' => 'true',
		)
	);

	$scrolling = $form->field_checkbox(
		'map_all_control[screens][' . $screen_slug . '][map_scrolling_wheel_mobile]', array(
			'label'         => esc_html__( 'Turn Off Scrolling Wheel', 'wpgmp-google-map' ),
			'value'         => 'false',
			'id'            => 'map_scrolling_wheel_mobile',
			'current'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_scrolling_wheel_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_scrolling_wheel_mobile'] : '',
			'desc'          => esc_html__( 'Tick to off scrolling wheel.', 'wpgmp-google-map' ),
			'class'         => 'chkbox_class ',
			'default_value' => 'true',

		)
	);

	$screens_options[] = array( $screen, $width, $height, $zoom, $draggable, $scrolling );
}

$form->add_element(
	'table', 'screen_specific_settings', array(
		'heading' => array( 'Screen', 'Width', 'Height', 'Zoom', 'Draggable', 'Scrolling Wheel' ),
		'data'    => $screens_options,
		'before'  => '<div class="fc-12 map_mobile_specific">',
		'after'   => '</div>',
		'show'    => 'false',
	)
);
