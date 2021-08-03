<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_monitor_404() {
    print_pro_section('404'); ?>

<div class="seopress-notice">
    <p>
        <?php _e('404 URLS are bad for:', 'wp-seopress-pro'); ?>
    </p>
    <ul class="list-none">
        <li><span class="dashicons dashicons-minus"></span><?php _e('User experience', 'wp-seopress-pro'); ?>
        </li>
        <li><span class="dashicons dashicons-minus"></span><?php _e('Performances', 'wp-seopress-pro'); ?>
        </li>
        <li><span class="dashicons dashicons-minus"></span><?php _e('Crawl budget allocated by Google', 'wp-seopress-pro'); ?>
        </li>
    </ul>

    <p>
        <?php _e('All these reasons degrade your SEO AND your conversion.', 'wp-seopress-pro'); ?>
    </p>

    <p>
        <?php _e('We recommend to enable this feature after a site migration for example, or after major content changes on your site for <strong>about 1 month max to avoid false positives (robots, SPAMS...)</strong>.', 'wp-seopress-pro'); ?>
    </p>
</div>

<?php
}
