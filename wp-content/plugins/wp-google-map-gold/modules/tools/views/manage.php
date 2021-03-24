<?php
/**
 * This class used to backup all tables for this plugins.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */


if ( isset( $_POST['operation'] ) and 'clean_database' == $_POST['operation'] ) {
	$clean_database = $response;
} else {
	$clean_database = array();
}

if ( isset( $_POST['operation'] ) and 'upload_sampledata' == $_POST['operation'] ) {
	$upload_sampledata = $response;
} else {
	$upload_sampledata = array();
}

	$form = new WPGMP_Template();
	$form->set_header( esc_html__( 'Clean Database', 'wpgmp-google-map' ), $clean_database );

	$form->add_element(
		'hidden', 'operation', array(
			'value' => 'clean_database',
		)
	);

	$form->add_element(
		'message', 'backup_message', array(
			'value' => esc_html__( 'Click below to remove all locations, maps, categories and routes. This is to be used to remove all dummy entries. This method is not recommended on a live site. Plugin settings will not remove.', 'wpgmp-google-map' ),
			'class' => 'fc-msg fc-danger',
		)
	);

	$form->add_element(
		'text', 'wpgmp_clean_consent', array(
			'label'  => esc_html__( 'Verify Action', 'wpgmp-google-map' ),
			'id'     => 'wpgmp_consent',
			'class'  => 'form-control',
			'desc'   => esc_html__( 'Type "DELETE" to give consent that you actually want to remove all maps data.', 'wpgmp-google-map' ),
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);

	$form->add_element(
		'submit', 'wpgmp_cleandatabase_tools', array(
			'value' => esc_html__( 'Clear Database', 'wpgmp-google-map' ),
		)
	);

	$form->render();

	$import_form = new WPGMP_Template( array( 'no_header' => true ) );
	$import_form->set_header( esc_html__( 'Install Sample Data', 'wpgmp-google-map' ), $upload_sampledata );
	$import_form->add_element(
		'hidden', 'operation', array(
			'value' => 'upload_sampledata',
		)
	);

	$import_form->add_element(
		'message', 'sampledata_message', array(
			'value' => esc_html__( 'Click below to install sample data. This is very useful to get started. 2 categories, 5 locations, 2 routes and 1 map will be created for demonstration purpose.', 'wpgmp-google-map' ),
			'class' => 'fc-msg',
		)
	);

	$import_form->add_element(
		'text', 'wpgmp_sampledata_consent', array(
			'label'  => esc_html__( 'Verify Action', 'wpgmp-google-map' ),
			'class'  => 'form-control',
			'desc'   => esc_html__( 'Type "YES" to create sample data.', 'wpgmp-google-map' ),
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);


	$import_form->add_element(
		'submit', 'wpgmp_sampledata_submit', array(
			'value' => esc_html__( 'Create Sample Data', 'wpgmp-google-map' ),
		)
	);

	$import_form->render();

