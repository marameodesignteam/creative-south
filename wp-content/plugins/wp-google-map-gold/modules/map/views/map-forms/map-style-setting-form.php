<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_styles_settings', array(
		'value'  => esc_html__( 'Map Style Setting', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

if ( ! isset( $data['style_google_map'] ) ) {
	$data['style_google_map'] = array();
}

$featuredtype = array(
	'Select Featured Type',
	'administrative',
	'administrative.country',
	'administrative.land_parcel',
	'administrative.locality',
	'administrative.neighborhood',
	'administrative.province',
	'all',
	'landscape',
	'landscape.man_made',
	'landscape.natural',
	'landscape.natural.landcover',
	'landscape.natural.terrain',
	'poi',
	'poi.attraction',
	'poi.business',
	'poi.government',
	'poi.medical',
	'poi.park',
	'poi.place_of_worship',
	'poi.school',
	'poi.sports_complex',
	'road',
	'road.arterial',
	'road.highway',
	'road.highway.controlled_access',
	'road.local',
	'transit',
	'transit.line',
	'transit.station',
	'transit.station.airport',
	'transit.station.bus',
	'transit.station.rail',
	'water',
);
foreach ( $featuredtype as $key => $value ) {
	$featuredtype_pair[ $value ] = $value;
}

$elementstype = array( 'Select Element Type', 'all', 'geometry', 'geometry.fill', 'geometry.stroke', 'labels', 'labels.icon', 'labels.text', 'labels.text.fill', 'labels.text.stroke' );

foreach ( $elementstype as $key => $value ) {
	$elementstype_pair[ $value ] = $value;
}

for ( $i = 0; $i < 10; $i++ ) {
	$input[ $i ][0] = esc_html__( 'Style', 'wpgmp-google-map' ) . ' ' . ( $i + 1 );
	$input[ $i ][1] = $form->field_select(
		'style_google_map[mapfeaturetype][' . $i . ']', array(
			'options' => $featuredtype_pair,
			'current' => isset( $data['style_google_map']['mapfeaturetype'][ $i ] ) ? $data['style_google_map']['mapfeaturetype'][ $i ] : '',
		)
	);
	$input[ $i ][2] = $form->field_select(
		'style_google_map[mapelementtype][' . $i . ']', array(
			'options' => $elementstype_pair,
			'current' => isset( $data['style_google_map']['mapelementtype'][ $i ] ) ? $data['style_google_map']['mapelementtype'][ $i ] : '',
		)
	);
	$input[ $i ][3] = $form->field_text(
		'style_google_map[color][' . $i . ']', array(
			'value' => isset( $data['style_google_map']['color'][ $i ] ) ? $data['style_google_map']['color'][ $i ] : '',
			'class' => 'color {pickerClosable:true} form-control',
		)
	);
	$input[ $i ][4] = $form->field_select(
		'style_google_map[visibility][' . $i . ']', array(
			'options' => array(
				'on'        => 'YES',
				'off'       => 'NO',
				'simplifed' => 'Simplifed',
			),
			'current' => isset( $data['style_google_map']['visibility'][ $i ] ) ? $data['style_google_map']['visibility'][ $i ] : '',
		)
	);

}

$form->add_element(
	'table', 'map_styles_table', array(
		'heading' => array( '#', esc_html__('Feature Type','wpgmp-google-map'), esc_html__('Element Type','wpgmp-google-map'),esc_html__('Color','wpgmp-google-map'), esc_html__('Visibility' ,'wpgmp-google-map')),
		'data'    => $input,
		'id'      => 'map_styles_table',
		'before'  => '<div class="fc-12">',
		'after'   => '</div>',
		'current' => isset( $data['style_google_map'] ) ? $data['style_google_map'] : '',
	)
);

$form->add_element(
	'message', 'styles_message', array(
		'value'  => esc_html__( 'or you can paste Javascript style array here. You can get awesome readymade styles from http://snazzymaps.com/', 'wpgmp-google-map' ),
		'class'  => 'alert',
		'id'     => 'styles_message',
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);


$form->add_element(
	'textarea', 'map_all_control[custom_style]', array(
		'label'         => esc_html__( 'Paste Style here', 'wpgmp-google-map' ),
		'value'         => ( isset( $data['map_all_control']['custom_style'] ) and ! empty( $data['map_all_control']['custom_style'] ) ) ? $data['map_all_control']['custom_style'] : '',
		'desc'          => esc_html__( 'Copy google map style from snazzymaps.com and paste here.', 'wpgmp-google-map' ),
		'textarea_rows' => 20,
		'textarea_name' => 'location_messages',
		'class'         => 'form-control',
		'id'            => 'map_custom_style',
		'before'        => '<div class="fc-11">',
		'after'         => '</div>',
	)
);
