<?php

namespace SEOPressPro\Helpers\Settings;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

abstract class LocalBusinessHelper {
    /**
     * @since 4.5.0
     *
     * @param string|stdObject $classCallback
     *
     * @return void
     */
    public static function getSettingsSection($classCallback) {
        $idSection = 'seopress_setting_section_local_business';
        $page      = 'seopress-settings-admin-local-business';

        $settings  = [
            'section' => [
                'id'       => $idSection,
                'title'    => '',
                'callback' => [$classCallback, 'renderSection'],
                'page'     => $page,
            ],
            'fields' => [
                [
                    'id'        => 'seopress_local_business_page',
                    'title'     => __('Where to display the schema?', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldPage'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_type',
                    'title'     => __('Business type', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldType'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_street_address',
                    'title'     => __('Street Address', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldStreetAddress'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_address_locality',
                    'title'     => __('City', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldAddressLocality'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_address_region',
                    'title'     => __('State', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldAddressRegion'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_postal_code',
                    'title'     => __('Postal code', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldPostalCode'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_address_country',
                    'title'     => __('Country', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldAddressCountry'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_lat',
                    'title'     => __('Latitude', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldLatitude'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_lon',
                    'title'     => __('Longitude', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldLongitude'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_place_id',
                    'title'     => __('Place ID', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldPlaceId'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_url',
                    'title'     => __('URL', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldUrl'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_phone',
                    'title'     => __('Telephone', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldPhone'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_price_range',
                    'title'     => __('Price range', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldPriceRange'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_cuisine',
                    'title'     => __('Cuisine served', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldCuisine'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
                [
                    'id'        => 'seopress_local_business_opening_hours',
                    'title'     => __('Opening hours', 'wp-seopress-pro'),
                    'callback'  => [$classCallback, 'renderFieldOpeningHours'],
                    'page'      => $page,
                    'section'   => $idSection,
                ],
            ],
        ];

        return $settings;
    }
}
