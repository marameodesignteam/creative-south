<?php
/**
 * Map's Advanced setting(s).
 *
 * @package Maps
 */

$form->add_element(
	'group', 'map_advanced_setting', array(
		'value'  => esc_html__( 'Advanced Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox_toggle', 'map_all_control[url_filter]', array(
		'label'   => esc_html__( 'URL Filters', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_url_filter',
		'current' => isset( $data['map_all_control']['url_filter'] ) ? $data['map_all_control']['url_filter'] : '',
		'desc'    => esc_html__( 'Check to enable filters by url parameters.', 'wpgmp-google-map' ),
		'class'   => 'checkbox_toggle switch_onoff',
		'data'    => array( 'target' => '.url_filer_options' ),
	)
);

$form->add_element(
	'message', 'url_instruction', array(
		'value' => esc_html__( 'You can filter markers/locations/posts on maps using url parameters. Following default parameters are supported :', 'wpgmp-google-map' ),
		'class' => 'fc-msg fc-success url_filer_options',
		'show'  => 'false',
	)
);

$url_parameters = array(
	array( 'search', esc_html__( 'Search Term', 'wpgmp-google-map' ) ),
	array( 'category', esc_html__( 'Category ID or Name.', 'wpgmp-google-map' ) ),
	array( 'limit', esc_html__( '# of Locations.', 'wpgmp-google-map' ) ),
	array( 'perpage', esc_html__( '# of Locations per page.', 'wpgmp-google-map' ) ),
	array( 'zoom', esc_html__( 'Zoom Level.', 'wpgmp-google-map' ) ),
	array( 'hide_map', esc_html__( 'To hide the map. Filters & listing will be visible if enabled.', 'wpgmp-google-map' ) ),
	array( 'maps_only', esc_html__( 'To show only maps. Tabs, filters, listing will be hide.', 'wpgmp-google-map' ) ),
);

$form->add_element(
	'table', 'wpgmp_urlparameters_table', array(
		'heading' => array( 'Query Parameter', 'Value' ),
		'data'    => $url_parameters,
		'before'  => '<div class="fc-8">',
		'after'   => '</div>',
		'class'   => 'fc-table fc-table-layout5 url_filer_options',
		'show'    => 'false',
	)
);
