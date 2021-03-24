<?php

$screen = get_current_screen();
if(!empty($screen->id) && !($screen->id == 'wp-google-map-pro_page_wpgmp_form_map'))
return $markup;

if(isset($_GET['map_id']) && !empty($_GET['map_id'])) {
	$extention_data = $this->fmlv_get_extention_data(sanitize_key($_GET['map_id']));
}

$wpld_settings_markup	 = '';
$wpld_settings_markup = '</div></div>';

$layout_addon_group = FlipperCode_HTML_Markup::field_group('viewport_addon_group', array(
		'value'  => esc_html__( 'Filters Maps Listing By Viewport', 'filter-map-listing-by-viewport' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);
$wpld_settings_markup .=
	'<div class="fc-form-group ">
		<div class="fc-12">'
			.$layout_addon_group.
		'</div>
	</div>';


$enable_layouts = FlipperCode_HTML_Markup::field_checkbox('extensions_fields[viewport_filter][enable]',array(
	'value'   => 'true',
	'id'      => 'wpgmp_viewport_enable',
	'current' => isset( $extention_data['extensions_fields']['viewport_filter']['enable'] ) ? $extention_data['extensions_fields']['viewport_filter']['enable'] : '',
	'desc'    => esc_html__( 'Please check this checkbox to apply viewport filter on this google map listing.', 'filter-map-listing-by-viewport' ),
	'class'   => 'chkbox_class',
));

$wpld_settings_markup .=
'<div class="fc-form-group ">
	<div class="fc-3">
		<label for="enable_layouts">'.esc_html__('Enable Viewport Filter ','filter-map-listing-by-viewport').'</label>
	</div>
	<div class="fc-8">'
		.$enable_layouts.
	'</div>
</div>';

$wpld_settings_markup .='<div class="fc-form-group"><div class="fc-8">';

return $wpld_settings_markup.$markup;