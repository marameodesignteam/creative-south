<?php
/**
 * Custom Control Setting(s).
 *
 * @package Maps
 */

$form->add_element(
	'group', 'map_custom_control_setting', array(
		'value'  => esc_html__( 'Custom Control(s) Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[map_custom_control]', array(
		'label'   => esc_html__( 'Turn On Custom Control', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'map_custom_control',
		'current' => isset( $data['map_all_control']['map_custom_control'] ) ? $data['map_all_control']['map_custom_control'] : '',
		'desc'    => esc_html__( 'Please check to enable map custom control.', 'wpgmp-google-map' ),
		'class'   => 'switch_onoff chkbox_class',
		'data'    => array( 'target' => '.map_control_setting' ),
	)
);

$form->set_col( 3 );
if ( isset( $_POST['map_all_control']['map_control_settings'] ) ) {
	$arr = array();
	$i   = 0;
	foreach ( $_POST['map_all_control']['map_control_settings'] as $key => $val ) {
		if ( $val['html'] != '' ) {
			$arr[ $i ]['html']     = $val['html'];
			$arr[ $i ]['position'] = $val['position'];
			$i++;
		}
	}
	$data['map_all_control']['map_control_settings'] = $_POST['map_all_control']['map_control_settings'];
	$next_index                                      = count( $data['map_all_control']['map_control_settings'] );
} elseif ( isset( $data['map_all_control']['map_control_settings'] ) ) {
	$data['map_all_control']['map_control_settings'] = $data['map_all_control']['map_control_settings'];
	$next_index                                      = count( $data['map_all_control']['map_control_settings'] );
} else {
	$next_index = 0;
}


if ( isset( $data['map_all_control']['map_control_settings'] ) && isset( $data['map_all_control']['map_custom_control'] ) && $data['map_all_control']['map_custom_control'] == 'true' ) {
	for ( $c = 0;$c < count( $data['map_all_control']['map_control_settings'] );$c++ ) {

		$form->add_element(
			'textarea', 'map_all_control[map_control_settings][' . $c . '][html]', array(
				'label'                => esc_html__( 'Custom Control HTML', 'wpgmp-google-map' ),
				'value'                => ( isset( $data['map_all_control']['map_control_settings'][ $c ]['html'] ) and ! empty( $data['map_all_control']['map_control_settings'][ $c ]['html'] ) ) ? $data['map_all_control']['map_control_settings'][ $c ]['html'] : '',
				'desc'                 => esc_html__( 'Paste HTML or text here that you want to show on map.', 'wpgmp-google-map' ),
				'textarea_fc-dividers' => 10,
				'textarea_name'        => 'map_control_setting',
				'class'                => 'form-control map_control_setting',
				'show'                 => 'false',
			)
		);

		$form->add_element(
			'select', 'map_all_control[map_control_settings][' . $c . '][position]', array(
				'label'   => esc_html__( 'Custom Control Position', 'wpgmp-google-map' ),
				'current' => $data['map_all_control']['map_control_settings'][ $c ]['position'],
				'desc'    => esc_html__( 'Please select position of custom control on map.', 'wpgmp-google-map' ),
				'options' => $positions,
				'class'   => 'form-control map_control_setting',
				'show'    => 'false',
				'before'  => '<div class="fc-6">',
				'after'   => '</div>',
			)
		);

		$form->add_element(
			'button', 'wpgmp_custom_controls_repeat_' . $c, array(
				'value'  => esc_html__( 'Remove', 'wpgmp-google-map' ),
				'desc'   => '',
				'class'  => 'repeat_remove_button fc-btn fc-btn-default btn-sm',
				'before' => '<div class="fc-3">',
				'after'  => '</div>',
			)
		);
	}
}

$form->add_element(
	'textarea', 'map_all_control[map_control_settings][' . $next_index . '][html]', array(
		'label'                => esc_html__( 'Custom Control HTML', 'wpgmp-google-map' ),
		'value'                => ( isset( $data['map_all_control']['map_control_settings'][ $next_index ]['html'] ) and ! empty( $data['map_all_control']['map_control_settings'][ $next_index ]['html'] ) ) ? $data['map_all_control']['map_control_settings'][ $next_index ]['html'] : '',
		'desc'                 => esc_html__( 'Paste HTML or text here that you want to show on map.', 'wpgmp-google-map' ),
		'textarea_fc-dividers' => 10,
		'textarea_name'        => 'map_control_setting',
		'class'                => 'form-control map_control_setting',
		'show'                 => 'false',
	)
);
$form->add_element(
	'select', 'map_all_control[map_control_settings][' . $next_index . '][position]', array(
		'label'   => esc_html__( 'Custom Control Position', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['map_control_settings'][ $next_index ]['position'] ) ? $data['map_all_control']['map_control_settings'][ $next_index ]['position'] : '',
		'desc'    => esc_html__( 'Please select position of custom control on map.', 'wpgmp-google-map' ),
		'options' => $positions,
		'class'   => 'form-control map_control_setting',
		'show'    => 'false',
		'before'  => '<div class="fc-6">',
		'after'   => '</div>',
	)
);


$form->add_element(
	'button', 'wpgmp_custom_controls_repeat_', array(
		'value'  => esc_html__( 'Add More...', 'wpgmp-google-map' ),
		'class'  => 'repeat_button fc-btn fc-btn-default btn-sm map_control_setting',
		'before' => '<div class="fc-3">',
		'after'  => '</div>',
		'show'   => 'false',
	)
);

$form->set_col( 1 );
