<?php

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPressPro\Core\Kernel;

/**
 * Get a service.
 *
 * @since 4.3.0
 *
 * @param string $service
 *
 * @return object
 */
function seopress_pro_get_service($service) {
    return Kernel::getContainer()->getServiceByName($service);
}

/**
 * Enable Google Suggestions
 *
 * @since 5.0
 *
 * @param boolean true
 *
 * @return boolean
 */
add_filter('seopress_ui_metabox_google_suggest', '__return_true');
