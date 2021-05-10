<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$data     = seopress_pro_get_service('OptionPro')->getLocalBusinessOpeningHours();
$fallback = true;
if (isset($data[0]) && isset($data[0]['am'], $data[0]['pm'])) {
    $fallback = false;
}

if (apply_filters('seopress_fallback_local_business_schema_renderer', $fallback)) {
    //Local Business
    //=================================================================================================
    //Local Business JSON-LD
    $display = false;
    if ('' != seopress_local_business_page_option() && (is_single(seopress_local_business_page_option()) || is_page(seopress_local_business_page_option()))) {
        $display = true;
    } elseif ('' == seopress_local_business_page_option() && (is_home() || is_front_page())) {
        $display = true;
    }

    if (true === $display) {
        if ('1' == seopress_get_toggle_option('local-business') && 'localbusiness' != get_post_meta(get_the_ID(), '_seopress_pro_rich_snippets_type', true)) { //Is Local Business enable
            //Business Type
            function seopress_local_business_type_option() {
                $seopress_local_business_type_option = get_option('seopress_pro_option_name');
                if ( ! empty($seopress_local_business_type_option)) {
                    foreach ($seopress_local_business_type_option as $key => $seopress_local_business_type_value) {
                        $options[$key] = $seopress_local_business_type_value;
                    }
                    if (isset($seopress_local_business_type_option['seopress_local_business_type'])) {
                        return $seopress_local_business_type_option['seopress_local_business_type'];
                    }
                }
            }

            //URL
            function seopress_local_business_url_option() {
                $seopress_local_business_url_option = get_option('seopress_pro_option_name');
                if ( ! empty($seopress_local_business_url_option)) {
                    foreach ($seopress_local_business_url_option as $key => $seopress_local_business_url_value) {
                        $options[$key] = $seopress_local_business_url_value;
                    }
                    if (isset($seopress_local_business_url_option['seopress_local_business_url'])) {
                        return $seopress_local_business_url_option['seopress_local_business_url'];
                    }
                }
            }
            //Price range
            function seopress_local_business_price_range_option() {
                $seopress_local_business_price_range_option = get_option('seopress_pro_option_name');
                if ( ! empty($seopress_local_business_price_range_option)) {
                    foreach ($seopress_local_business_price_range_option as $key => $seopress_local_business_price_range_value) {
                        $options[$key] = $seopress_local_business_price_range_value;
                    }
                    if (isset($seopress_local_business_price_range_option['seopress_local_business_price_range'])) {
                        return $seopress_local_business_price_range_option['seopress_local_business_price_range'];
                    }
                }
            }
            //Cuisine served
            function seopress_local_business_cuisine_option() {
                $seopress_local_business_cuisine_option = get_option('seopress_pro_option_name');
                if ( ! empty($seopress_local_business_cuisine_option)) {
                    foreach ($seopress_local_business_cuisine_option as $key => $seopress_local_business_cuisine_value) {
                        $options[$key] = $seopress_local_business_cuisine_value;
                    }
                    if (isset($seopress_local_business_cuisine_option['seopress_local_business_cuisine'])) {
                        return $seopress_local_business_cuisine_option['seopress_local_business_cuisine'];
                    }
                }
            }
            //Name
            function seopress_local_business_name_option() {
                $seopress_local_business_name_option = get_option('seopress_social_option_name');
                if ( ! empty($seopress_local_business_name_option)) {
                    foreach ($seopress_local_business_name_option as $key => $seopress_local_business_name_value) {
                        $options[$key] = $seopress_local_business_name_value;
                    }
                    if (isset($seopress_local_business_name_option['seopress_social_knowledge_name'])) {
                        return $seopress_local_business_name_option['seopress_social_knowledge_name'];
                    }
                }
            }
            //Logo
            function seopress_local_business_img_option() {
                $seopress_local_business_img_option = get_option('seopress_social_option_name');
                if ( ! empty($seopress_local_business_img_option)) {
                    foreach ($seopress_local_business_img_option as $key => $seopress_local_business_img_value) {
                        $options[$key] = $seopress_local_business_img_value;
                    }
                    if (isset($seopress_local_business_img_option['seopress_social_knowledge_img'])) {
                        return $seopress_local_business_img_option['seopress_social_knowledge_img'];
                    }
                }
            }
            function seopress_local_business_jsonld_hook() {
                if ('' != seopress_local_business_img_option()) {
                    $seopress_local_business_img_option = json_encode(seopress_local_business_img_option());
                }

                if ('' != seopress_local_business_name_option()) {
                    $seopress_local_business_name_option = json_encode(seopress_local_business_name_option());
                }

                if ('' != seopress_local_business_type_option()) {
                    $seopress_local_business_type_option = json_encode(seopress_local_business_type_option());
                }

                if ('' != seopress_local_business_street_address_option()) {
                    $seopress_local_business_street_address_option = json_encode(seopress_local_business_street_address_option());
                }

                if ('' != seopress_local_business_address_locality_option()) {
                    $seopress_local_business_address_locality_option = json_encode(seopress_local_business_address_locality_option());
                }

                if ('' != seopress_local_business_address_region_option()) {
                    $seopress_local_business_address_region_option = json_encode(seopress_local_business_address_region_option());
                }

                if ('' != seopress_local_business_postal_code_option()) {
                    $seopress_local_business_postal_code_option = json_encode(seopress_local_business_postal_code_option());
                }

                if ('' != seopress_local_business_address_country_option()) {
                    $seopress_local_business_address_country_option = json_encode(seopress_local_business_address_country_option());
                }

                if ('' != seopress_local_business_lat_option()) {
                    $seopress_local_business_lat_option = json_encode(seopress_local_business_lat_option());
                }

                if ('' != seopress_local_business_lon_option()) {
                    $seopress_local_business_lon_option = json_encode(seopress_local_business_lon_option());
                }

                if ('' != seopress_local_business_url_option()) {
                    $seopress_local_business_url_option = json_encode(seopress_local_business_url_option());
                }

                if ('' != seopress_local_business_phone_option()) {
                    $seopress_local_business_phone_option = json_encode(seopress_local_business_phone_option());
                }

                if ('' != seopress_local_business_price_range_option()) {
                    $seopress_local_business_price_range_option = json_encode(seopress_local_business_price_range_option());
                }
                if ('' != seopress_local_business_cuisine_option()) {
                    $seopress_local_business_cuisine_option = json_encode(seopress_local_business_cuisine_option());
                }
                if ('' != seopress_local_business_opening_hours_option()) {
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

                    $seopress_local_business_opening_hours_option ='';

                    foreach (seopress_local_business_opening_hours_option() as $key => $day) {
                        if ( ! array_key_exists('open', $day)) {
                            $seopress_local_business_opening_hours_option .= '{ ';
                            $seopress_local_business_opening_hours_option .= '"@type": "OpeningHoursSpecification",';
                            $seopress_local_business_opening_hours_option .= '"dayOfWeek": "' . $days[$key] . '", ';
                            foreach ($day as $_key => $value) {
                                if ('start' == $_key) {
                                    $seopress_local_business_opening_hours_option .= '"opens": "';
                                    foreach ($value as $__key => $time) {
                                        $seopress_local_business_opening_hours_option .= $time;
                                        if ('hours' == $__key) {
                                            $seopress_local_business_opening_hours_option .= ':';
                                        }
                                    }
                                    $seopress_local_business_opening_hours_option .= '",';
                                }
                                if ('end' == $_key) {
                                    $seopress_local_business_opening_hours_option .= '"closes": "';
                                    foreach ($value as $__key => $time) {
                                        $seopress_local_business_opening_hours_option .= $time;
                                        if ('hours' == $__key) {
                                            $seopress_local_business_opening_hours_option .= ':';
                                        }
                                    }
                                    $seopress_local_business_opening_hours_option .= '"';
                                }
                            }
                            $seopress_local_business_opening_hours_option .= '|';
                        }
                    }
                }

                if (isset($seopress_local_business_type_option)) {
                    echo '<script type="application/ld+json">';
                    echo '{"@context" : "' . seopress_check_ssl() . 'schema.org","@type" : ' . $seopress_local_business_type_option . ',';
                    if (isset($seopress_local_business_img_option)) {
                        echo '"image": ' . $seopress_local_business_img_option . ', ';
                    }
                    echo '"@id": ' . json_encode(get_home_url()) . ',';

                    if (isset($seopress_local_business_street_address_option) || isset($seopress_local_business_address_locality_option) || isset($seopress_local_business_address_region_option) || isset($seopress_local_business_postal_code_option) || isset($seopress_local_business_address_country_option)) {
                        echo '"address": {
						"@type": "PostalAddress",';
                        if (isset($seopress_local_business_street_address_option)) {
                            echo '"streetAddress": ' . $seopress_local_business_street_address_option . ',';
                        }
                        if (isset($seopress_local_business_address_locality_option)) {
                            echo '"addressLocality": ' . $seopress_local_business_address_locality_option . ',';
                        }
                        if (isset($seopress_local_business_address_region_option)) {
                            echo '"addressRegion": ' . $seopress_local_business_address_region_option . ',';
                        }
                        if (isset($seopress_local_business_postal_code_option)) {
                            echo '"postalCode": ' . $seopress_local_business_postal_code_option . ',';
                        }
                        if (isset($seopress_local_business_address_country_option)) {
                            echo '"addressCountry": ' . $seopress_local_business_address_country_option;
                        }
                        echo '},';
                    }

                    if (isset($seopress_local_business_lat_option) || isset($seopress_local_business_lon_option)) {
                        echo '"geo": {
						"@type": "GeoCoordinates",';
                        if (isset($seopress_local_business_lat_option)) {
                            echo '"latitude": ' . $seopress_local_business_lat_option . ',';
                        }
                        if (isset($seopress_local_business_lon_option)) {
                            echo '"longitude": ' . $seopress_local_business_lon_option;
                        }
                        echo '},';
                    }

                    if (isset($seopress_local_business_url_option)) {
                        echo '"url": ' . $seopress_local_business_url_option . ',';
                    }

                    if (isset($seopress_local_business_phone_option)) {
                        echo '"telephone": ' . $seopress_local_business_phone_option . ',';
                    }

                    if (isset($seopress_local_business_price_range_option)) {
                        echo '"priceRange": ' . $seopress_local_business_price_range_option . ',';
                    }

                    if (isset($seopress_local_business_cuisine_option)) {
                        echo '"servesCuisine": ' . $seopress_local_business_cuisine_option . ',';
                    }

                    if (isset($seopress_local_business_opening_hours_option)) {
                        echo '"openingHoursSpecification": [';

                        $explode              = array_filter(explode('|', $seopress_local_business_opening_hours_option));
                        $seopress_comma_count = count($explode);
                        for ($i = 0; $i < $seopress_comma_count; ++$i) {
                            echo $explode[$i];
                            if ($i < ($seopress_comma_count - 1)) {
                                echo '}, ';
                            } else {
                                echo '} ';
                            }
                        }

                        echo '],';
                    }
                    if (isset($seopress_local_business_name_option)) {
                        echo '"name": ' . $seopress_local_business_name_option;
                    } else {
                        echo '"name": "' . get_bloginfo('name') . '"';
                    }
                    echo '}';
                    echo '</script>';
                    echo "\n";
                }
            }
            add_action('wp_head', 'seopress_local_business_jsonld_hook', 2);
        }
    }
}
