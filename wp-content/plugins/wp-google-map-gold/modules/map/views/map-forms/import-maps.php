<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */


$form->add_element(
	'group', 'map_import_setting', array(
		'value'  => esc_html__( 'Import Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'textarea', 'wpgmp_import_code', array(
		'label'         => esc_html__( 'Import Code', 'wpgmp-google-map' ),
		'value'         => '',
		'desc'          => esc_html__( 'Paste here import json code to overwrite map settings. Your map settings will be overwrite permanately.', 'wpgmp-google-map' ),
		'textarea_rows' => 10,
		'textarea_name' => 'wpgmp_import_code',
		'class'         => 'form-control',
	)
);

if ( ! empty( $map ) ) {

	$json_hash = base64_encode( serialize( $map ) );

	$form->add_element(
		'textarea', 'wpgmp_export_code', array(
			'label'         => esc_html__( 'Export Code', 'wpgmp-google-map' ),
			'value'         => $json_hash,
			'desc'          => esc_html__( 'Copy above export code and paste on your map import setting to migrate maps settings from one site to another site.', 'wpgmp-google-map' ),
			'textarea_rows' => 10,
			'textarea_name' => 'wpgmp_export_code',
			'class'         => 'form-control',
		)
	);

}
