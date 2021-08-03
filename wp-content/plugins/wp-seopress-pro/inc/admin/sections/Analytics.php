<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_google_analytics_dashboard()
{
    $docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : ''; ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Stats in dashboard', 'wp-seopress'); ?>
    </h2>
</div>

<p><?php _e('Connect your WordPress site with Google Analytics API and get statistics right in your Dashboard.', 'wp-seopress-pro'); ?>
</p>
<p><?php _e('This feature is completely independent of user tracking. For example, statistical data will be collected even if you have not entered your API keys below.', 'wp-seopress-pro'); ?>
</p>

<span class="seopress-help dashicons dashicons-external"></span>
<a class="seopress-help"
    href="<?php echo $docs['analytics']['connect']; ?>"
    target="_blank">
    <?php _e('Watch our video guide to connect your WordPress site with Google Analytics API + common errors', 'wp-seopress-pro'); ?>
</a>

<div class="seopress-notice">
    <p><?php _e('No stats in the <strong>dashboard widget?</strong> Make sure to have activated these 2 Google APIs from Google Console: <strong>Google Analytics API</strong> and <strong>Google Analytics Reporting API</strong>.', 'wp-seopress-pro'); ?>
    </p>
</div>

<?php
}
