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
