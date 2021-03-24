<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_geotags_settings', array(
		'value'  => esc_html__( 'Show Posts Using Custom Fields', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[geo_tags]', array(
		'label'   => esc_html__( 'GEO Tags', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_geo_tags',
		'current' => isset( $data['map_all_control']['geo_tags'] ) ? $data['map_all_control']['geo_tags'] : '',
		'desc'    => esc_html__( 'Enable to display location from your own custom fields of posts or custom post types.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.geo_tags_setting' ),
	)
);

$screens = array( 'post' );

$args = array(
	'public'   => true,
	'_builtin' => false,
);

$output            = 'names';
$operator          = 'and';
$post_types        = get_post_types( $args, $output, $operator );
$custom_post_types = array( 'post' );
$all_post_types    = array_merge( $post_types, $custom_post_types );

if ( ! empty( $all_post_types ) ) {
	$count = 0;
	foreach ( $all_post_types  as $post_type ) {

		$input_data[ $count ][0] = $post_type;

		$cf_address = '';
		$cf_latitude = '';
		$cf_longitude = '';
		$cf_category = '';

		if ( isset( $data['map_geotags'][ $post_type ]['address'] ) ) {
			$cf_address = $data['map_geotags'][ $post_type ]['address'];
		}

		if ( isset( $data['map_geotags'][ $post_type ]['latitude'] ) ) {
			$cf_latitude = $data['map_geotags'][ $post_type ]['latitude'];
		}

		if ( isset( $data['map_geotags'][ $post_type ]['longitude'] ) ) {
			$cf_longitude = $data['map_geotags'][ $post_type ]['longitude'];
		}

		if ( isset( $data['map_geotags'][ $post_type ]['category'] ) ) {
			$cf_category = $data['map_geotags'][ $post_type ]['category'];
		}


		$input_data[ $count ][1] = '<input placeholder="' . esc_html__( 'Custom Field Name', 'wpgmp-google-map' ) . '" type="text" class="form-control" name="map_geotags[' . $post_type . '][address]" value="' . $cf_address . '">';

		$input_data[ $count ][2] = '<input placeholder="' . esc_html__( 'Custom Field Name', 'wpgmp-google-map' ) . '" type="text" class="form-control" name="map_geotags[' . $post_type . '][latitude]" value="' . $cf_latitude . '">';

		$input_data[ $count ][3] = '<input placeholder="' . esc_html__( 'Custom Field Name', 'wpgmp-google-map' ) . '" type="text" class="form-control" name="map_geotags[' . $post_type . '][longitude]" value="' . $cf_longitude . '">';

		$input_data[ $count ][4] = '<input placeholder="' . esc_html__( 'Custom Field Name', 'wpgmp-google-map' ) . '" type="text" class="form-control" name="map_geotags[' . $post_type . '][category]" value="' . $cf_category . '">';

		$count++;
	}
}

$form->add_element(
	'table', 'geotags_table', array(
		'heading' => array( 'Post Type', 'Address', 'Latitude', 'Longitude', 'Category' ),
		'data'    => $input_data,
		'id'      => 'geo_tags_table',
		'before'  => '<div class="fc-12">',
		'after'   => '</div>',
		'show'    => 'false',
		'class'   => 'dataTable geo_tags_setting',
	)
);


$form->add_element(
	'group', 'map_acf_settings', array(
		'value'  => esc_html__( 'Show Posts using ACF Plugin', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);
$form->add_element(
	'text', 'map_all_control[wpgmp_acf_field_name]', array(
		'label' => esc_html__( 'ACF Field Name', 'wpgmp-google-map' ),
		'value' => isset( $data['map_all_control']['wpgmp_acf_field_name'] ) ? $data['map_all_control']['wpgmp_acf_field_name'] : '',
		'id'    => 'wpgmp_acf_field_name',
		'desc'  => esc_html__( 'Enter acf field name. It should be exactly same which you entered in field group.', 'wpgmp-google-map' ),
		'class' => 'form-control  geo_acf_setting',
	)
);
