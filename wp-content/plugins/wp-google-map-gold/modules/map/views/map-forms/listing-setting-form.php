<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_elements_setting', array(
		'value'  => esc_html__( 'Custom Filters', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox_toggle', 'map_all_control[wpgmp_display_custom_filters]', array(
		'label'   => esc_html__( 'Display Custom Filters', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_display_custom_filters',
		'current' => isset( $data['map_all_control']['wpgmp_display_custom_filters'] ) ? $data['map_all_control']['wpgmp_display_custom_filters'] : '',
		'desc'    => esc_html__( 'Check to enable custom filters for extra fields, custom fields & taxonomies.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff ',
		'data'    => array( 'target' => '.wpgmp_custom_filters' ),
	)
);
$form->set_col( 3 );
$next_index = 0;
if ( isset( $data['map_all_control']['custom_filters'] ) && isset( $data['map_all_control']['wpgmp_display_custom_filters'] ) && $data['map_all_control']['wpgmp_display_custom_filters'] == true ) {
	$ex = 0;
	foreach ( $data['map_all_control']['custom_filters'] as $i => $label ) {
		$form->add_element(
			'text', 'map_all_control[custom_filters][' . $ex . '][slug]', array(
				'value'       => ( isset( $data['map_all_control']['custom_filters'][ $i ]['slug'] ) and ! empty( $data['map_all_control']['custom_filters'][ $i ]['slug'] ) ) ? $data['map_all_control']['custom_filters'][ $i ]['slug'] : '',
				'desc'        => esc_html__( 'Enter placeholder for marker taxonomies, extra fields or custom fields as {%custom_field_slug_here%}, {extra_field_slug_here}, {%taxonomy%}, e.g: {color}.', 'wpgmp-google-map' ),
				'class'       => 'wpgmp_custom_filters form-control sortable_child',
				'placeholder' => esc_html__( 'Enter placeholder', 'wpgmp-google-map' ),
				'before'      => '<div class="fc-4">',
				'after'       => '</div>',
				'show'        => 'false',
				'lable'       => '&nbsp;',
			)
		);
		$form->add_element(
			'text', 'map_all_control[custom_filters][' . $ex . '][text]', array(
				'value'       => ( isset( $data['map_all_control']['custom_filters'][ $i ]['text'] ) and ! empty( $data['map_all_control']['custom_filters'][ $i ]['text'] ) ) ? $data['map_all_control']['custom_filters'][ $i ]['text'] : '',
				'desc'        => esc_html__( 'Enter text here for the filter to be shown, e.g: Select Colors.', 'wpgmp-google-map' ),
				'class'       => 'wpgmp_custom_filters form-control',
				'placeholder' => esc_html__( 'Enter filter text', 'wpgmp-google-map' ),
				'before'      => '<div class="fc-3">',
				'after'       => '</div>',
				'show'        => 'false',
			)
		);
		$form->add_element(
			'button', 'custom_filters_add_btn[' . $ex . ']', array(
				'value'  => esc_html__( 'Remove', 'wpgmp-google-map' ),
				'desc'   => '',
				'class'  => 'repeat_remove_button fc-btn fc-btn-blue btn-sm wpgmp_custom_filters',
				'before' => '<div class="fc-2">',
				'after'  => '</div>',
				'show'   => 'false',
			)
		);
		$ex++;
	}
	$next_index = $ex;
}

$form->add_element(
	'text', 'map_all_control[custom_filters][' . $next_index . '][slug]', array(
		'value'       => ( isset( $data['map_all_control']['custom_filters'][ $next_index ]['slug'] ) and ! empty( $data['map_all_control']['custom_filters'][ $next_index ]['slug'] ) ) ? $data['map_all_control']['custom_filters'][ $next_index ]['slug'] : '',
		'desc'        => esc_html__( 'Enter placeholder here for marker taxonomies, extra fields or custom fields as {%custom_field_slug_here%}, {extra_field_slug_here}, {%taxonomy%}, e.g: {color}.', 'wpgmp-google-map' ),
		'class'       => 'wpgmp_custom_filters form-control sortable_child',
		'placeholder' => esc_html__( 'Enter placeholder', 'wpgmp-google-map' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
		'show'        => 'false',
		'lable'       => '&nbsp;',
	)
);

$form->add_element(
	'text', 'map_all_control[custom_filters][' . $next_index . '][text]', array(
		'value'       => ( isset( $data['map_all_control']['custom_filters'][ $next_index ]['text'] ) and ! empty( $data['map_all_control']['custom_filters'][ $next_index ]['text'] ) ) ? $data['map_all_control']['custom_filters'][ $next_index ]['text'] : '',
		'desc'        => esc_html__( 'Enter text here for the filter to be shown, e,g, : Select Colors.', 'wpgmp-google-map' ),
		'class'       => 'wpgmp_custom_filters form-control',
		'placeholder' => esc_html__( 'Enter filter text', 'wpgmp-google-map' ),
		'before'      => '<div class="fc-3">',
		'after'       => '</div>',
		'show'        => 'false',
	)
);

$form->add_element(
	'button', 'custom_filters_add_btn[' . $next_index . ']', array(
		'value'  => esc_html__( 'Add More...', 'wpgmp-google-map' ),
		'desc'   => '',
		'class'  => 'repeat_button fc-btn fc-btn-blue btn-sm wpgmp_custom_filters',
		'before' => '<div class="fc-2">',
		'after'  => '</div>',
		'show'   => 'false',
	)
);

$form->set_col( 1 );


$form->add_element(
	'group', 'custom_filters_bound', array(
		'value'  => esc_html__( 'Advance Custom Filters Functionality', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[bound_map_after_filter]', array(
		'label'   => esc_html__( 'Fitbound Map After Filteration', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['bound_map_after_filter'] ) ? $data['map_all_control']['bound_map_after_filter'] : '',
		'desc'    => esc_html__( 'Fit bound the map with resultant markers after filteration process', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',

	)
);

$form->add_element(
	'checkbox', 'map_all_control[display_reset_button]', array(
		'label'   => esc_html__( 'Display Reset Map Button', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['display_reset_button'] ) ? $data['map_all_control']['display_reset_button'] : '',
		'desc'    => esc_html__( 'Check to enable display reset map button on frontend.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.map_reset_button_text' ),
	)
);

$form->add_element(
	'text', 'map_all_control[map_reset_button_text]', array(
		'label'       => esc_html__( 'Reset Map Button Text', 'wpgmp-google-map' ),
		'value'       => ( isset( $data['map_all_control']['map_reset_button_text'] ) and ! empty( $data['map_all_control']['map_reset_button_text'] ) ) ? $data['map_all_control']['map_reset_button_text'] : esc_html__( 'Reset', 'wpgmp-google-map' ),
		'desc'        => esc_html__( 'Enter text to be displayed on Reset Map Button', 'wpgmp-google-map' ),
		'class'       => 'form-control map_reset_button_text',
		'placeholder' => esc_html__( 'Enter Reset Map Text', 'wpgmp-google-map' ),
		'show'        => 'false',
	)
);

$form->add_element(
	'group', 'map_listing_setting', array(
		'value'  => esc_html__( 'Listing Settings', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[display_listing]', array(
		'label'   => esc_html__( 'Display Listing', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_display_listing',
		'current' => isset( $data['map_all_control']['display_listing'] ) ? $data['map_all_control']['display_listing'] : '',
		'desc'    => esc_html__( 'Display locations listing below the map.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.wpgmp_display_listing' ),
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_search_display]', array(
		'label'   => esc_html__( 'Display Search Form', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_search_display',
		'current' => isset( $data['map_all_control']['wpgmp_search_display'] ) ? $data['map_all_control']['wpgmp_search_display'] : '',
		'desc'    => esc_html__( 'Check to display search form below the map.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpgmp_search_display' ),

	)
);

$form->add_element(
	'checkbox', 'map_all_control[search_field_autosuggest]', array(
		'label'   => esc_html__( 'Enable Google Autosuggest', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['search_field_autosuggest'] ) ? $data['map_all_control']['search_field_autosuggest'] : '',
		'desc'    => esc_html__( 'Apply google autosuggest on search field.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing wpgmp_search_display',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_display_category_filter]', array(
		'label'   => esc_html__( 'Display Category Filter', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_display_category_filter',
		'current' => isset( $data['map_all_control']['wpgmp_display_category_filter'] ) ? $data['map_all_control']['wpgmp_display_category_filter'] : '',
		'desc'    => esc_html__( 'Check to display category filter.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
	)
);


$form->add_element(
	'checkbox', 'map_all_control[wpgmp_display_sorting_filter]', array(
		'label'   => esc_html__( 'Display Sorting Filter', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_display_sorting_filter',
		'current' => isset( $data['map_all_control']['wpgmp_display_sorting_filter'] ) ? $data['map_all_control']['wpgmp_display_sorting_filter'] : '',
		'desc'    => esc_html__( 'Check to display sorting filter.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_display_radius_filter]', array(
		'label'   => esc_html__( 'Display Radius Filter', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_display_radius_filter',
		'current' => isset( $data['map_all_control']['wpgmp_display_radius_filter'] ) ? $data['map_all_control']['wpgmp_display_radius_filter'] : '',
		'desc'    => esc_html__( 'Check to display radius filter. Recommended to display search results within certian radius.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpgmp_radius_filter' ),
	)
);

$dimension_options = array(
	'miles' => esc_html__( 'Miles', 'wpgmp-google-map' ),
	'km'    => esc_html__( 'KM', 'wpgmp-google-map' ),
);
$form->add_element(
	'select', 'map_all_control[wpgmp_radius_dimension]', array(
		'label'   => esc_html__( 'Dimension', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['wpgmp_radius_dimension'] ) ? $data['map_all_control']['wpgmp_radius_dimension'] : '',
		'desc'    => esc_html__( 'Choose radius dimension in miles or km.', 'wpgmp-google-map' ),
		'options' => $dimension_options,
		'class'   => 'form-control  wpgmp_radius_filter',
		'show'    => 'false',
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_radius_options]', array(
		'label'         => esc_html__( 'Radius Options', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_all_control']['wpgmp_radius_options'] ) ? $data['map_all_control']['wpgmp_radius_options'] : '',
		'desc'          => esc_html__( 'Set radius options. Enter comma seperated numbers.', 'wpgmp-google-map' ),
		'class'         => 'form-control  wpgmp_radius_filter',
		'show'          => 'false',
		'default_value' => '5,10,15,20,25,50,100,200,500',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_display_location_per_page_filter]', array(
		'label'   => esc_html__( 'Display Per Page Filter', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_wpgmp_display_location_per_page_filter',
		'current' => isset( $data['map_all_control']['wpgmp_display_location_per_page_filter'] ) ? $data['map_all_control']['wpgmp_display_location_per_page_filter'] : '',
		'desc'    => esc_html__( 'Check to enable locations per page filter.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_display_print_option]', array(
		'label'   => esc_html__( 'Display Print Option', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_display_print_option',
		'current' => isset( $data['map_all_control']['wpgmp_display_print_option'] ) ? $data['map_all_control']['wpgmp_display_print_option'] : '',
		'desc'    => esc_html__( 'Check to display print option.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_display_grid_option]', array(
		'label'   => esc_html__( 'Display Grid/List Option', 'wpgmp-google-map' ),
		'value'   => 'true',
		'id'      => 'wpgmp_display_grid_option',
		'current' => isset( $data['map_all_control']['wpgmp_display_grid_option'] ) ? $data['map_all_control']['wpgmp_display_grid_option'] : '',
		'desc'    => esc_html__( 'Switch between list/grid view.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_listing_number]', array(
		'label'         => esc_html__( 'Locations Per Page', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_all_control']['wpgmp_listing_number'] ) ? $data['map_all_control']['wpgmp_listing_number'] : '',
		'desc'          => esc_html__( 'Set locations to display per page. Default is 10.', 'wpgmp-google-map' ),
		'class'         => 'form-control wpgmp_display_listing',
		'show'          => 'false',
		'default_value' => 10,
	)
);


$form->add_element(
	'textarea', 'map_all_control[wpgmp_before_listing]', array(
		'label'         => esc_html__( 'Before Listing Placeholder', 'wpgmp-google-map' ),
		'value'         => ( isset( $data['map_all_control']['wpgmp_before_listing']) && !empty($data['map_all_control']['wpgmp_before_listing']) ) ? $data['map_all_control']['wpgmp_before_listing'] : esc_html__( 'Locations Listing', 'wpgmp-google-map' ),
		'desc'          => esc_html__( 'Display a text/html content before display listing.', 'wpgmp-google-map' ),
		'textarea_rows' => 10,
		'textarea_name' => 'map_all_control[wpgmp_before_listing]',
		'class'         => 'form-control wpgmp_display_listing',
		'show'          => 'false',
		'default_value' => esc_html__( 'Map Locations', 'wpgmp-google-map' ),
	)
);

$list_grid = array(
	'wpgmp_listing_list' => 'List',
	'wpgmp_listing_grid' => 'Grid',
);
$form->add_element(
	'select', 'map_all_control[wpgmp_list_grid]', array(
		'label'   => esc_html__( 'List/Grid', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['wpgmp_list_grid'] ) ? $data['map_all_control']['wpgmp_list_grid'] : '',
		'desc'    => esc_html__( 'Choose listing style for frontend display.', 'wpgmp-google-map' ),
		'options' => $list_grid,
		'class'   => 'form-control wpgmp_display_listing',
		'show'    => 'false',
	)
);

$default_place_holder = '
<div class="wpgmp_locations">
<div class="wpgmp_locations_head">
<div class="wpgmp_location_title">
<a href="" class="place_title" data-zoom="{marker_zoom}" data-marker="{marker_id}">{marker_title}</a>
</div>
<div class="wpgmp_location_meta">
<span class="wpgmp_location_category fc-badge info">{marker_category}</span>
</div>
</div>
<div class="wpgmp_locations_content">
{marker_message}
</div>
<div class="wpgmp_locations_foot"></div>
</div>';
$listing_place_holder = stripslashes( trim( $default_place_holder ) );
$listing_place_holder = ( isset( $data['map_all_control']['wpgmp_categorydisplayformat'] ) ? $data['map_all_control']['wpgmp_categorydisplayformat'] : $listing_place_holder );

$form->add_element(
	'select', 'map_all_control[wpgmp_categorydisplaysort]', array(
		'label'   => esc_html__( 'Sort By', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['wpgmp_categorydisplaysort'] ) ? $data['map_all_control']['wpgmp_categorydisplaysort'] : '',
		'desc'    => esc_html__( 'Select Sort By.', 'wpgmp-google-map' ),
		'options' => array(
			'title'     => esc_html__( 'Title', 'wpgmp-google-map' ),
			'address'   => esc_html__( 'Address', 'wpgmp-google-map' ),
			'category'  => esc_html__( 'Category', 'wpgmp-google-map' ),
			'listorder' => esc_html__( 'Category Priority', 'wpgmp-google-map' ),
		),
		'class'   => 'form-control wpgmp_display_listing',
		'show'    => 'false',
	)
);


$form->add_element(
	'select', 'map_all_control[wpgmp_categorydisplaysortby]', array(
		'label'         => esc_html__( 'Sort Order', 'wpgmp-google-map' ),
		'current'       => isset( $data['map_all_control']['wpgmp_categorydisplaysortby'] ) ? $data['map_all_control']['wpgmp_categorydisplaysortby'] : '',
		'desc'          => esc_html__( 'Select sorting order.', 'wpgmp-google-map' ),
		'options'       => array(
			'asc'  => esc_html__( 'Ascending', 'wpgmp-google-map' ),
			'desc' => esc_html__( 'Descending', 'wpgmp-google-map' ),
		),
		'class'         => 'form-control wpgmp_display_listing',
		'show'          => 'false',
		'default_value' => 'asc',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpgmp_apply_radius_only]', array(
		'label'   => esc_html__( 'Apply Default Radius Filter', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['wpgmp_apply_radius_only'] ) ? $data['map_all_control']['wpgmp_apply_radius_only'] : '',
		'desc'    => esc_html__( 'Show markers available in certain radius based on user search.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpgmp_radius_filter_apply' ),
	)
);

$form->add_element(
	'text', 'map_all_control[wpgmp_default_radius]', array(
		'label'         => esc_html__( 'Default Radius', 'wpgmp-google-map' ),
		'value'         => isset( $data['map_all_control']['wpgmp_default_radius'] ) ? $data['map_all_control']['wpgmp_default_radius'] : '',
		'desc'          => esc_html__( 'Set default radius options.', 'wpgmp-google-map' ),
		'class'         => 'form-control wpgmp_radius_filter_apply',
		'show'          => 'false',
		'default_value' => '100',
	)
);

$dimension_options = array(
	'miles' => esc_html__( 'Miles', 'wpgmp-google-map' ),
	'km'    => esc_html__( 'KM', 'wpgmp-google-map' ),
);
$form->add_element(
	'select', 'map_all_control[wpgmp_default_radius_dimension]', array(
		'label'   => esc_html__( 'Dimension', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['wpgmp_default_radius_dimension'] ) ? $data['map_all_control']['wpgmp_default_radius_dimension'] : '',
		'desc'    => esc_html__( 'Choose default radius dimension in miles or km.', 'wpgmp-google-map' ),
		'options' => $dimension_options,
		'class'   => 'form-control  wpgmp_radius_filter_apply',
		'show'    => 'false',
	)
);

$form->add_element(
	'group', 'map_list_skin_setting', array(
		'value'  => esc_html__( 'Listing Item Skin', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12 wpgmp_display_listing">',
		'after'  => '</div>',
		'show'   => 'false',
	)
);

$location_placeholders = array(
	'{marker_id}',
	'{marker_title}',
	'{marker_address}',
	'{marker_message}',
	'{marker_category}',
	'{marker_icon}',
	'{marker_latitude}',
	'{marker_longitude}',
	'{marker_city}',
	'{marker_state}',
	'{marker_country}',
	'{marker_zoom}',
	'{marker_image}',
	'{marker_postal_code}',
	'{extra_field_slug}',
	'{post_title}',
	'{post_link}',
	'{post_excerpt}',
	'{post_content}',
	'{post_categories}',
	'{post_tags}',
	'{%custom_field_slug_here%}',
);

$form->add_element(
	'templates', 'map_all_control[item_skin]', array(
		'before'              => '<div class="fc-12 wpgmp_display_listing">',
		'after'               => '</div>',
		'template_types'      => 'item',
		'data_placeholders'   => $location_placeholders,
		'templatePath'        => WPGMP_TEMPLATES,
		'templateURL'         => WPGMP_TEMPLATES_URL,
		'customiser'          => 'true',
		'show'                => 'false',
		'current'             => ( isset( $data['map_all_control']['item_skin'] ) ) ? $data['map_all_control']['item_skin'] : array(
			'name'       => 'default',
			'type'       => 'item',
			'sourcecode' => $listing_place_holder,
		),
		'customiser_controls' => array( 'edit_mode', 'placeholder', 'sourcecode', 'mobile', 'desktop', 'grid' ),
	)
);

$filters_position = array(
	'default' => esc_html__( 'Bottom of the Map', 'wpgmp-google-map' ),
	'top_map' => esc_html__( 'Top of the Map', 'wpgmp-google-map' ),
);
$form->add_element(
	'select', 'map_all_control[filters_position]', array(
		'label'   => esc_html__( 'Filters Position', 'wpgmp-google-map' ),
		'current' => isset( $data['map_all_control']['filters_position'] ) ? $data['map_all_control']['filters_position'] : '',
		'desc'    => esc_html__( 'Choose filters position. Default is below the map.', 'wpgmp-google-map' ),
		'options' => $filters_position,
		'class'   => 'form-control  wpgmp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[hide_locations]', array(
		'label'   => esc_html__( 'Show Filters Only', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['hide_locations'] ) ? $data['map_all_control']['hide_locations'] : '',
		'desc'    => esc_html__( 'Check to display filters only.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[hide_map]', array(
		'label'   => esc_html__( "Don't Show Maps", 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['hide_map'] ) ? $data['map_all_control']['hide_map'] : '',
		'desc'    => esc_html__( 'Check to display filters & locations only. Maps will be invisible.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
	)
);


$form->add_element(
	'group', 'map_geojson_setting', array(
		'value'  => esc_html__( 'GEOJSON', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'map_all_control[geojson_url]', array(
		'label' => esc_html__( 'Paste GEO JSON URL', 'wpgmp-google-map' ),
		'value' => isset( $data['map_all_control']['geojson_url'] ) ? $data['map_all_control']['geojson_url'] : '',
		'desc'  => esc_html__( 'Enter GEO JSON Url', 'wpgmp-google-map' ),
		'class' => 'form-control',
	)
);
