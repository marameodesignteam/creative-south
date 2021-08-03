<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//PageSpeed Insights SECTION=======================================================================
add_settings_section(
    'seopress_setting_section_page_speed', // ID
    '',
    //__("PageSpeed Insights","wp-seopress-pro"), // Title
    'print_section_info_page_speed', // Callback
    'seopress-settings-admin-page-speed' // Page
);
