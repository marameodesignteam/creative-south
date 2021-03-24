<?php
/**
 * Overlay Settings.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_overlay_setting', array(
		'value'  => esc_html__( 'Overlays Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_overlay_setting[overlay]', array(
		'label'   => esc_html__( 'Apply Overlays', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_overlay',
		'current' => isset( $data['map_overlay_setting']['overlay'] ) ? $data['map_overlay_setting']['overlay'] : '',
		'desc'    => esc_html__( 'Please check to apply overlays. if enabled, below information can not be empty.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.map_overlay_setting' ),
	)
);


$form->add_element(
	'text', 'map_overlay_setting[overlay_border_color]', array(
		'label' => esc_html__( 'Overlay Border Color', 'wpgmp-google-map' ),
		'value' => isset( $data['map_overlay_setting']['overlay_border_color'] ) ? $data['map_overlay_setting']['overlay_border_color'] : '',
		'desc'  => esc_html__( 'Default is red.', 'wpgmp-google-map' ),
		'class' => 'color {pickerClosable:true} form-control map_overlay_setting',
		'show'  => 'false',
	)
);

$form->add_element(
	'text', 'map_overlay_setting[overlay_width]', array(
		'label'         => esc_html__( 'Overlay Width', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_overlay_setting']['overlay_width'] ) ? $data['map_overlay_setting']['overlay_width'] : '',
		'desc'          => esc_html__( 'Enter here overlay width. Default is 200px.', 'wpgmp-google-map' ),
		'class'         => 'form-control map_overlay_setting',
		'show'          => 'false',
		'default_value' => '200',
	)
);

$form->add_element(
	'text', 'map_overlay_setting[overlay_height]', array(
		'label'         => esc_html__( 'Overlay Height', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_overlay_setting']['overlay_height'] ) ? $data['map_overlay_setting']['overlay_height'] : '',
		'desc'          => esc_html__( 'Enter here overlay height. Default is 200px.', 'wpgmp-google-map' ),
		'class'         => 'form-control map_overlay_setting',
		'show'          => 'false',
		'default_value' => '200',
	)
);

$form->add_element(
	'text', 'map_overlay_setting[overlay_fontsize]', array(
		'label'         => esc_html__( 'Overlay Font size', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_overlay_setting']['overlay_fontsize'] ) ? $data['map_overlay_setting']['overlay_fontsize'] : '',
		'desc'          => esc_html__( 'Enter here Overlay Font Size. Default is 16px.', 'wpgmp-google-map' ),
		'class'         => 'form-control map_overlay_setting',
		'show'          => 'false',
		'default_value' => '16',
	)
);

$form->add_element(
	'text', 'map_overlay_setting[overlay_border_width]', array(
		'label'         => esc_html__( 'Overlay Border Width', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_overlay_setting']['overlay_border_width'] ) ? $data['map_overlay_setting']['overlay_border_width'] : '',
		'desc'          => esc_html__( 'Enter here Overlay Border Width. Default is 2px.', 'wpgmp-google-map' ),
		'class'         => 'form-control map_overlay_setting',
		'show'          => 'false',
		'default_value' => '2',
	)
);
$overlay_values = array(
	'dotted' => 'Dotted',
	'solid'  => 'Solid',
	'dashed' => 'Dashed',
);
$form->add_element(
	'select', 'map_overlay_setting[overlay_border_style]', array(
		'label'   => esc_html__( 'Overlay Border Style', 'wpgmp-google-map' ),
		'current' => isset( $data['map_overlay_setting']['overlay_border_style'] ) ? $data['map_overlay_setting']['overlay_border_style'] : '',
		'desc'    => esc_html__( 'Select overlay border style.', 'wpgmp-google-map' ),
		'options' => $overlay_values,
		'class'   => 'map_overlay_setting form-control',
		'show'    => 'false',
	)
);
