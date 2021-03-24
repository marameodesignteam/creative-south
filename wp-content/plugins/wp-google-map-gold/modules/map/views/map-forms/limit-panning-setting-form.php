<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_limit_panning_setting', array(
		'value'  => esc_html__( 'Limit Panning Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[panning_control]', array(
		'label'   => esc_html__( 'Limit Panning', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_panning_control',
		'current' => isset( $data['map_all_control']['panning_control'] ) ? $data['map_all_control']['panning_control'] : '',
		'desc'    => esc_html__( 'Apply limit panning. if you enabled,below information can not be empty.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.panning_control' ),
	)
);

$form->set_col( 2 );
$form->add_element(
	'text', 'map_all_control[from_latitude]', array(
		'label'  => esc_html__( 'South West', 'wpgmp-google-map' ),
		'value'  => isset( $data['map_all_control']['from_latitude'] ) ? $data['map_all_control']['from_latitude'] : '',
		'desc'   => esc_html__( 'Enter here "South West" latitude', 'wpgmp-google-map' ),
		'class'  => 'form-control panning_control',
		'show'   => 'false',
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'map_all_control[from_longitude]', array(
		'value'  => isset( $data['map_all_control']['from_longitude'] ) ? $data['map_all_control']['from_longitude'] : '',
		'desc'   => esc_html__( 'Enter here "South West" longitude', 'wpgmp-google-map' ),
		'class'  => 'form-control panning_control',
		'show'   => 'false',
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'map_all_control[to_latitude]', array(
		'label'  => esc_html__( 'North East', 'wpgmp-google-map' ),
		'value'  => isset( $data['map_all_control']['to_latitude'] ) ? $data['map_all_control']['to_latitude'] : '',
		'desc'   => esc_html__( 'Enter here "North East" latitude', 'wpgmp-google-map' ),
		'class'  => 'form-control panning_control',
		'show'   => 'false',
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'map_all_control[to_longitude]', array(
		'value'  => isset( $data['map_all_control']['to_longitude'] ) ? $data['map_all_control']['to_longitude'] : '',
		'desc'   => esc_html__( 'Enter here "North East" longitude', 'wpgmp-google-map' ),
		'class'  => 'form-control panning_control',
		'show'   => 'false',
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
	)
);
$form->set_col( 1 );
for ( $i = 1; $i < 20;$i++ ) {
	$zoom_level[ $i ] = $i;
}
$form->add_element(
	'select', 'map_all_control[zoom_level]', array(
		'label'   => esc_html__( 'Zoom Level', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['zoom_level'] ) ? $data['map_all_control']['zoom_level'] : '',
		'desc'    => esc_html__( 'Select zoom level.', 'wpgmp-google-map' ),
		'options' => $zoom_level,
		'class'   => 'form-control panning_control',
		'show'    => 'false',
	)
);
