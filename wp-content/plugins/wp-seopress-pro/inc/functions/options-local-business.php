<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Business page
function seopress_local_business_page_option() {
	$seopress_local_business_page_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_page_option ) ) {
		foreach ($seopress_local_business_page_option as $key => $seopress_local_business_page_value)
			$options[$key] = $seopress_local_business_page_value;
			if (isset($seopress_local_business_page_option['seopress_local_business_page'])) { 
			return $seopress_local_business_page_option['seopress_local_business_page'];
			}
	}
}

//Street address
function seopress_local_business_street_address_option() {
	$seopress_local_business_street_address_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_street_address_option ) ) {
		foreach ($seopress_local_business_street_address_option as $key => $seopress_local_business_street_address_value)
			$options[$key] = $seopress_local_business_street_address_value;
		if (isset($seopress_local_business_street_address_option['seopress_local_business_street_address'])) { 
			return $seopress_local_business_street_address_option['seopress_local_business_street_address'];
		}
	}
}
//Locality
function seopress_local_business_address_locality_option() {
	$seopress_local_business_address_locality_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_address_locality_option ) ) {
		foreach ($seopress_local_business_address_locality_option as $key => $seopress_local_business_address_locality_value)
			$options[$key] = $seopress_local_business_address_locality_value;
		if (isset($seopress_local_business_address_locality_option['seopress_local_business_address_locality'])) { 
			return $seopress_local_business_address_locality_option['seopress_local_business_address_locality'];
		}
	}
}
//Region
function seopress_local_business_address_region_option() {
	$seopress_local_business_address_region_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_address_region_option ) ) {
		foreach ($seopress_local_business_address_region_option as $key => $seopress_local_business_address_region_value)
			$options[$key] = $seopress_local_business_address_region_value;
		if (isset($seopress_local_business_address_region_option['seopress_local_business_address_region'])) { 
			return $seopress_local_business_address_region_option['seopress_local_business_address_region'];
		}
	}
}
//Code
function seopress_local_business_postal_code_option() {
	$seopress_local_business_postal_code_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_postal_code_option ) ) {
		foreach ($seopress_local_business_postal_code_option as $key => $seopress_local_business_postal_code_value)
			$options[$key] = $seopress_local_business_postal_code_value;
		if (isset($seopress_local_business_postal_code_option['seopress_local_business_postal_code'])) { 
			return $seopress_local_business_postal_code_option['seopress_local_business_postal_code'];
		}
	}
}
//Country
function seopress_local_business_address_country_option() {
	$seopress_local_business_address_country_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_address_country_option ) ) {
		foreach ($seopress_local_business_address_country_option as $key => $seopress_local_business_address_country_value)
			$options[$key] = $seopress_local_business_address_country_value;
		if (isset($seopress_local_business_address_country_option['seopress_local_business_address_country'])) { 
			return $seopress_local_business_address_country_option['seopress_local_business_address_country'];
		}
	}
}
//Lat
function seopress_local_business_lat_option() {
	$seopress_local_business_lat_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_lat_option ) ) {
		foreach ($seopress_local_business_lat_option as $key => $seopress_local_business_lat_value)
			$options[$key] = $seopress_local_business_lat_value;
		if (isset($seopress_local_business_lat_option['seopress_local_business_lat'])) { 
			return $seopress_local_business_lat_option['seopress_local_business_lat'];
		}
	}
}
//Lon
function seopress_local_business_lon_option() {
	$seopress_local_business_lon_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_lon_option ) ) {
		foreach ($seopress_local_business_lon_option as $key => $seopress_local_business_lon_value)
			$options[$key] = $seopress_local_business_lon_value;
		if (isset($seopress_local_business_lon_option['seopress_local_business_lon'])) { 
			return $seopress_local_business_lon_option['seopress_local_business_lon'];
		}
	}
}
//Google Place ID
function seopress_local_business_place_id_option() {
	$seopress_local_business_place_id_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_place_id_option ) ) {
		foreach ($seopress_local_business_place_id_option as $key => $seopress_local_business_place_id_value)
			$options[$key] = $seopress_local_business_place_id_value;
		if (isset($seopress_local_business_place_id_option['seopress_local_business_place_id'])) { 
			return $seopress_local_business_place_id_option['seopress_local_business_place_id'];
		}
	}
}
//Phone
function seopress_local_business_phone_option() {
	$seopress_local_business_phone_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_phone_option ) ) {
		foreach ($seopress_local_business_phone_option as $key => $seopress_local_business_phone_value)
			$options[$key] = $seopress_local_business_phone_value;
		if (isset($seopress_local_business_phone_option['seopress_local_business_phone'])) { 
			return $seopress_local_business_phone_option['seopress_local_business_phone'];
		}
	}
}
//Opening Hours
function seopress_local_business_opening_hours_option() {
	$seopress_local_business_opening_hours_option = get_option("seopress_pro_option_name");
	if ( ! empty ( $seopress_local_business_opening_hours_option ) ) {
		foreach ($seopress_local_business_opening_hours_option as $key => $seopress_local_business_opening_hours_value)
			$options[$key] = $seopress_local_business_opening_hours_value;
		if (isset($seopress_local_business_opening_hours_option['seopress_local_business_opening_hours'])) { 
			return $seopress_local_business_opening_hours_option['seopress_local_business_opening_hours'];
		}
	}
}