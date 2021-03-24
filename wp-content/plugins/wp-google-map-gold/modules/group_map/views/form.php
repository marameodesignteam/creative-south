<?php
/**
 * Template for Add & Edit Category
 *
 * @author  Flipper Code <hello@flippercode.com>
 * @package Maps
 */

if ( isset( $_REQUEST['_wpnonce'] ) ) {

	$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

		die( 'Cheating...' );

	} else {
		$data = $_POST;
	}
}
global $wpdb;
$modelFactory = new WPGMP_Model();
$category     = $modelFactory->create_object( 'group_map' );
$categories   = (array) $category->fetch();
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['group_map_id'] ) ) {
	$category_obj = $category->fetch( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );
	$_POST        = (array) $category_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $_POST );
}
$form = new WPGMP_Template();
$form->set_header( esc_html__( 'Marker Category', 'wpgmp-google-map' ), $response, esc_html__( 'Manage Marker Categories', 'wpgmp-google-map' ), 'wpgmp_manage_group_map' );
if ( is_array( $categories ) ) {
	$markers = array( ' ' => 'Please Select' );
	foreach ( $categories as $i => $single_category ) {
			$markers[ $single_category->group_map_id ] = $single_category->group_map_title;
	}

	$form->add_element(
		'select', 'group_parent', array(
			'label'   => esc_html__( 'Parent Category', 'wpgmp-google-map' ),
			'current' => ( isset( $_POST['group_parent'] ) and ! empty( $_POST['group_parent'] ) ) ? intval( wp_unslash( $_POST['group_parent'] ) ) : '',
			'desc'    => esc_html__( 'Assign parent category if any.', 'wpgmp-google-map' ),
			'options' => $markers,
		)
	);

}

$form->add_element(
	'text', 'group_map_title', array(
		'label'       => esc_html__( 'Marker Category Title', 'wpgmp-google-map' ),
		'value'       => ( isset( $_POST['group_map_title'] ) and ! empty( $_POST['group_map_title'] ) ) ? sanitize_text_field( wp_unslash( $_POST['group_map_title'] ) ) : '',
		'id'          => 'group_map_title',
		'desc'        => esc_html__( 'Enter here marker category title.', 'wpgmp-google-map' ),
		'class'       => 'create_map form-control',
		'placeholder' => esc_html__( 'Marker Category Title', 'wpgmp-google-map' ),
		'required'    => true,
	)
);


$form->add_element(
	'image_picker', 'group_marker', array(
		'label'         => esc_html__( 'Choose Marker Image', 'wpgmp-google-map' ),
		'src'           => ( isset( $_POST['group_marker'] ) ) ? wp_unslash( $_POST['group_marker'] ) : WPGMP_IMAGES . '/default_marker.png',
		'required'      => false,
		'choose_button' => esc_html__( 'Choose', 'wpgmp-google-map' ),
		'remove_button' => esc_html__( 'Remove', 'wpgmp-google-map' ),
		'id'            => 'marker_category_icon',
	)
);

$form->set_col( 1 );
$form->add_element(
	'text', 'extensions_fields[cat_order]', array(
		'label'         => esc_html__( 'Marker Category Order', 'wpgmp-google-map' ),
		'value'         => ( isset( $_POST['extensions_fields']['cat_order'] ) and ! empty( $_POST['extensions_fields']['cat_order'] ) ) ? sanitize_text_field( wp_unslash( $_POST['extensions_fields']['cat_order'] ) ) : '',
		'id'            => 'group_map_cat_order_value',
		'desc'          => esc_html__( 'Enter here marker category title.', 'wpgmp-google-map' ),
		'class'         => 'create_map form-control',
		'placeholder'   => esc_html__( 'Enter category order in numeric value.', 'wpgmp-google-map' ),
		'default_value' => 0,
	)
);

$form->add_element(
	'extensions', 'wpgmp_category_form', array(
		'value'  => isset( $_POST['extensions_fields'] ) ? $_POST['extensions_fields'] : '',
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);


$form->add_element(
	'submit', 'create_group_map_location', array(
		'value'  => 'Save Marker Category',
		'before' => '<div class="fc-12">',
		'after'  => '</div>',

	)
);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);

if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] ) {
	$form->add_element(
		'hidden', 'entityID', array(
			'value' => intval( wp_unslash( $_GET['group_map_id'] ) ),
		)
	);
}

$form->render();
