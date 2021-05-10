<?php

namespace SEOPressPro\Services\Admin\Settings;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class RenderSectionPro {
    /**
     * @since 4.5.0
     *
     * @param string $key
     */
    public function render($key) {
        $breadcrumbs_desc = __('Configure your breadcrumbs, using schema.org markup, allowing it to appear in Google\'s search results.', 'wp-seopress-pro') . ' <a class="seopress-help" href="https://developers.google.com/search/docs/data-types/breadcrumb" target="_blank" rel="nofollow" title="' . __('Google developers website (new window)', 'wp-seopress-pro') . '">' . __('Lean more on Google developers website', 'wp-seopress-pro') . '</a><span class="seopress-help dashicons dashicons-external"></span>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $docsLink['lb']['eat'] = 'https://www.seopress.org/fr/blog/optimiser-site-wordpress-google-eat/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $docsLink['lb']['eat'] = 'https://www.seopress.org/blog/optimizing-wordpress-sites-for-google-eat/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        $sections = [
            'local-business'=> [
                'toggle' => 1,
                'icon'   => 'store',
                'title'  => __('Local Business', 'wp-seopress-pro'),
                'desc'   => sprintf(__('Local Business data type for Google. This schema will be displayed on the homepage. <br>You can also display these informations using our <a href="%1$s">Local Business widget</a> to optimize your site for <a class="seopress-help" href="%2$s" target="_blank" rel="nofollow" title="' . __('Optimizing WordPress sites for Google EAT (new window)', 'wp-seopress-pro') . '">Google EAT</a><span class="seopress-help dashicons dashicons-external"></span>.', 'wp-seopress-pro'), admin_url('widgets.php'), $docsLink['lb']['eat'] ),
            ],
            'edd'=> [
                'toggle' => 1,
                'icon'   => 'cart',
                'title'  => __('Easy Digital Downloads', 'wp-seopress-pro'),
                'desc'   => __('Improve Easy Digital Downloads SEO.', 'wp-seopress-pro'),
            ],
            'woocommerce'=> [
                'toggle' => 1,
                'icon'   => 'cart',
                'title'  => __('WooCommerce', 'wp-seopress-pro'),
                'desc'   => __('Improve WooCommerce SEO. By enabling this feature, we‘ll automatically add <strong>product identifiers type</strong> and <strong>product identifiers value</strong> fields to the WooCommerce product metabox (barcode) for the Product schema.', 'wp-seopress-pro'),
            ],
            'dublin-core'=> [
                'toggle' => 1,
                'icon'   => 'welcome-learn-more',
                'title'  => __('Dublin Core', 'wp-seopress-pro'),
                'desc'   => __('Dublin Core is a set of meta tags to describe your content.<br> These tags are automatically generated. Recognized by states / governements, they are used by directories, Bing, Baidu and Yandex.', 'wp-seopress-pro'),
            ],
            'rich-snippets'=> [
                'toggle' => 1,
                'icon'   => 'media-spreadsheet',
                'title'  => __('Structured Data Types (schema.org)', 'wp-seopress-pro'),
                'desc'   => __('Add Structured Data Types support, mark your content, and get better Google Search Results.', 'wp-seopress-pro'),
            ],
            'page-speed'=> [
                'icon'  => 'performance',
                'title' => __('PageSpeed Insights', 'wp-seopress-pro'),
                'desc'  => __('Check your site performance with Google PageSpeed Insights.', 'wp-seopress-pro'),
            ],
            'robots'=> [
                'toggle' => 1,
                'icon'   => 'media-text',
                'title'  => __('robots.txt', 'wp-seopress-pro'),
                'desc'   => __('Configure your virtual robots.txt file.', 'wp-seopress-pro'),
            ],
            'news'=> [
                'toggle' => 1,
                'icon'   => 'admin-post',
                'title'  => __('Google News', 'wp-seopress-pro'),
                'desc'   => __('Enable your Google News Sitemap.', 'wp-seopress-pro'),
            ],
            '404'=> [
                'toggle' => 1,
                'icon'   => 'admin-links',
                'title'  => __('404 monitoring / Redirections', 'wp-seopress-pro'),
                'desc'   => __('Monitor 404 urls in your Dashboard. Crawlers (robots/spiders) will be automatically exclude (eg: Google Bot, Yahoo, Bing...).', 'wp-seopress-pro'),
            ],
            'htaccess'=> [
                'icon'  => 'media-text',
                'title' => __('.htaccess', 'wp-seopress-pro'),
                'desc'  => __('Edit your htaccess file.', 'wp-seopress-pro'),
            ],
            'rss'=> [
                'icon'  => 'rss',
                'title' => __('RSS feeds', 'wp-seopress-pro'),
                'desc'  => sprintf(__('Configure WordPress default feeds. <br><br><a href="%s" class="button" target="_blank"><span class="dashicons dashicons-visibility"></span>View my RSS feed</a>', 'wp-seopress-pro'), get_home_url() . '/feed'),
            ],
            'rewrite'=> [
                'toggle' => 1,
                'icon'   => 'admin-links',
                'title'  => __('Rewrite', 'wp-seopress-pro'),
                'desc'   => __('Change the URL rewriting.', 'wp-seopress-pro'),
            ],
            'white-label'=> [
                'toggle' => 1,
                'icon'   => 'tag',
                'title'  => __('White Label', 'wp-seopress-pro'),
                'desc'   => __('Enable White Label. By enabling this feature, the <strong>"How-to get started notice"</strong> will be removed from the SEOPress dashboard.', 'wp-seopress-pro'),
            ],
            'breadcrumbs'=> [
                'toggle' => 1,
                'icon'   => 'feedback',
                'title'  => __('Breadcrumbs', 'wp-seopress-pro'),
                'desc'   => $breadcrumbs_desc,
            ],
        ];

        if ( ! empty($sections)) {
            if ('1' == seopress_get_toggle_option($key)) {
                $seopress_get_toggle_option = '1';
            } else {
                $seopress_get_toggle_option = '0';
            } ?>
			<div class="sp-section-header">
				<span class="dashicons dashicons-<?php echo $sections[$key]['icon']; ?>"></span>

				<h2><?php echo $sections[$key]['title']; ?></h2>

				<?php if ( ! empty($sections[$key]['toggle']) && 1 == $sections[$key]['toggle']) { ?>
					<div class="wrap-toggle-checkboxes">
						<input type="checkbox" name="toggle-<?php echo $key; ?>" id="toggle-<?php echo $key; ?>" class="toggle" data-toggle="<?php echo $seopress_get_toggle_option; ?>">
						<label for="toggle-<?php echo $key; ?>"></label>

						<?php
                            if ('1' == $seopress_get_toggle_option) {
                                echo '<span id="' . $key . '-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress-pro') . '</span>';
                                echo '<span id="' . $key . '-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress-pro') . '</span>';
                            } else {
                                echo '<span id="' . $key . '-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress-pro') . '</span>';
                                echo '<span id="' . $key . '-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress-pro') . '</span>';
                            }
                        ?>
					</div>
				<?php } ?>
			</div>
			<p><?php echo $sections[$key]['desc']; ?></p>
			<?php
        }
    }
}
