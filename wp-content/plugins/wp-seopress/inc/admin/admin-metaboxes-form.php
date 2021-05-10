<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

global $typenow;
global $pagenow;

function seopress_redirections_value($seopress_redirections_value) {
    if ('' != $seopress_redirections_value) {
        return $seopress_redirections_value;
    }
}

$data_attr             = [];
$data_attr['data_tax'] = '';
$data_attr['termId']   = '';

if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
    $data_attr['current_id'] = get_the_id();
    $data_attr['origin']     = 'post';
    $data_attr['title']      = get_the_title($data_attr['current_id']);
} elseif ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
    global $tag;
    $data_attr['current_id'] = $tag->term_id;
    $data_attr['termId']     = $tag->term_id;
    $data_attr['origin']     = 'term';
    $data_attr['data_tax']   = $tag->taxonomy;
    $data_attr['title']      = $tag->name;
}

$data_attr['isHomeId'] = get_option('page_on_front');
if ('0' === $data_attr['isHomeId']) {
    $data_attr['isHomeId'] = '';
}

if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
    echo '
		<tr id="term-seopress" class="form-field">
			<th scope="row"><h2>' . __('SEO', 'wp-seopress') . '</h2></th>
			<td>
				<div id="seopress_cpt">
					<div class="inside">';
}

echo '<div id="seopress-tabs" data-home-id="' . $data_attr['isHomeId'] . '" data-term-id="' . $data_attr['termId'] . '" data_id="' . $data_attr['current_id'] . '" data_origin="' . $data_attr['origin'] . '" data_tax="' . $data_attr['data_tax'] . '">';

        if ('seopress_404' != $typenow) {
            $seo_tabs['title-tab']    = '<li><a href="#tabs-1"><span class="dashicons dashicons-editor-table"></span>' . __('Titles settings', 'wp-seopress') . '</a></li>';
            $seo_tabs['advanced-tab'] = '<li><a href="#tabs-2"><span class="dashicons dashicons-admin-generic"></span>' . __('Advanced', 'wp-seopress') . '<span id="sp-advanced-alert"></span></a></li>';
            $seo_tabs['social-tab']   = '<li><a href="#tabs-3"><span class="dashicons dashicons-share"></span>' . __('Social', 'wp-seopress') . '</a></li>';
        }

        $seo_tabs['redirect-tab'] = '<li><a href="#tabs-4"><span class="dashicons dashicons-admin-links"></span>' . __('Redirection', 'wp-seopress') . '</a></li>';

        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('news')) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        $seo_tabs['news-tab'] = '<li><a href="#tabs-5"><span class="dashicons dashicons-admin-post"></span>' . __('Google News', 'wp-seopress') . '</a></li>';
                    }
                }
            }
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('xml-sitemap') && function_exists('seopress_xml_sitemap_video_enable_option') && '1' == seopress_xml_sitemap_video_enable_option()) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        $seo_tabs['video-tab'] = '<li><a href="#tabs-6"><span class="dashicons dashicons-format-video"></span>' . __('Video Sitemap', 'wp-seopress') . '</a></li>';
                    }
                }
            }
        }

        $seo_tabs = apply_filters('seopress_metabox_seo_tabs', $seo_tabs);

        if ( ! empty($seo_tabs)) {
            echo '<ul>';
            foreach ($seo_tabs as $tab) {
                echo $tab;
            }
            echo '</ul>';
        }

        if ('seopress_404' != $typenow) {
            if (array_key_exists('title-tab', $seo_tabs)) {
                echo '<div id="tabs-1">';
                if (is_plugin_active('woocommerce/woocommerce.php') && function_exists('wc_get_page_id')) {
                    $shop_page_id = wc_get_page_id('shop');
                    if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                        if ($post && absint($shop_page_id) === absint($post->ID)) {
                            echo '<p class="notice notice-info">' . __('This is your <strong>Shop page</strong>. Go to <strong>SEO > Titles & Metas > Archives > Products</strong> ', 'wp-seopress') . ' <a href="' . admin_url('admin.php?page=seopress-titles') . '">' . __('to edit your title and meta description', 'wp-seopress') . '</a></p>';
                        }
                    }
                }
                echo '<div class="box-left">
						<p style="margin-bottom:0">
							<label for="seopress_titles_title_meta">'
                                . __('Title', 'wp-seopress')
                                . seopress_tooltip(__('Meta title', 'wp-seopress'), __('Titles are critical to give users a quick insight into the content of a result and why it’s relevant to their query. It\'s often the primary piece of information used to decide which result to click on, so it\'s important to use high-quality titles on your web pages.', 'wp-seopress'), esc_html('<title>My super title</title>')) .
                            '</label>
							<input id="seopress_titles_title_meta" type="text" name="seopress_titles_title" placeholder="' . esc_html__('Enter your title', 'wp-seopress') . '" aria-label="' . __('Title', 'wp-seopress') . '" value="' . $seopress_titles_title . '" />
						</p>
						<div class="sp-progress">
							<div id="seopress_titles_title_counters_progress" class="sp-progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">1%</div>
						</div>
						<div class="wrap-seopress-counters">
							<div id="seopress_titles_title_pixel"></div>
							<strong>' . __(' / 568 pixels - ', 'wp-seopress') . '</strong>
							<div id="seopress_titles_title_counters"></div>
							' . __(' (maximum recommended limit)', 'wp-seopress') . '
						</div>

						<div class="wrap-tags">';
                if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
                    echo '<span id="seopress-tag-single-title" data-tag="%%term_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Term Title', 'wp-seopress') . '</span>';
                } else {
                    echo '<span id="seopress-tag-single-title" data-tag="%%post_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Post Title', 'wp-seopress') . '</span>';
                }
                echo '<span id="seopress-tag-single-site-title" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>
							<span id="seopress-tag-single-sep" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';

                echo seopress_render_dyn_variables('tag-title');

                echo '</div>

						<p style="margin-bottom:0">
							<label for="seopress_titles_desc_meta">'
                            . __('Meta description', 'wp-seopress')
                            . seopress_tooltip(__('Meta description', 'wp-seopress'), __('A meta description tag should generally inform and interest users with a short, relevant summary of what a particular page is about. <br>They are like a pitch that convince the user that the page is exactly what they\'re looking for. <br>There\'s no limit on how long a meta description can be, but the search result snippets are truncated as needed, typically to fit the device width.', 'wp-seopress'), esc_html('<meta name="description" content="my super meta description" />')) . '
							</label>
							<textarea id="seopress_titles_desc_meta" style="width:100%" rows="4" name="seopress_titles_desc" placeholder="' . esc_html__('Enter your meta description', 'wp-seopress') . '" aria-label="' . __('Meta description', 'wp-seopress') . '" value="' . $seopress_titles_desc . '">' . $seopress_titles_desc . '</textarea>
						</p>
						<div class="sp-progress">
							<div id="seopress_titles_desc_counters_progress" class="sp-progress-bar" role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">1%</div>
						</div>
						<div class="wrap-seopress-counters">
							<div id="seopress_titles_desc_pixel"></div>
							<strong>' . __(' / 940 pixels - ', 'wp-seopress') . '</strong>
							<div id="seopress_titles_desc_counters"></div>
							' . __(' (maximum recommended limit)', 'wp-seopress') . '
						</div>
						<div class="wrap-tags">';
                if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
                    echo '<span id="seopress-tag-single-excerpt" data-tag="%%_category_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Category / term description', 'wp-seopress') . '</span>';
                } else {
                    echo '<span id="seopress-tag-single-excerpt" data-tag="%%post_excerpt%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Post Excerpt', 'wp-seopress') . '</span>';
                }
                echo seopress_render_dyn_variables('tag-description');
                echo '</div></div>';

                $toggle_preview = 1;
                $toggle_preview = apply_filters('seopress_toggle_mobile_preview', $toggle_preview);

                echo '<div class="box-right">
						<div class="google-snippet-preview mobile-preview">
							<h3>'
                                . __('Google Snippet Preview', 'wp-seopress')
                                . seopress_tooltip(__('Snippet Preview', 'wp-seopress'), __('The Google preview is a simulation. <br>There is no reliable preview because it depends on the screen resolution, the device used, the expression sought, and Google. <br>There is not one snippet for one URL but several. <br>All the data in this overview comes directly from your source code. <br>This is what the crawlers will see.', 'wp-seopress'), null) . '
							</h3>
							<p>' . __('This is what your page will look like in Google search results. You have to publish your post to get the Google Snippet Preview.', 'wp-seopress') . '</p>

							<div class="wrap-toggle-preview">
								<p>
									<span class="dashicons dashicons-smartphone"></span>
									' . __('Mobile Preview', 'wp-seopress') . '
									<input type="checkbox" name="toggle-preview" id="toggle-preview" class="toggle" data-toggle="' . $toggle_preview . '">
									<label for="toggle-preview"></label>
								</p>
							</div>';

                global $tag;

                $gp_title       = '';
                $gp_permalink   = '';
                if (get_the_title()) {
                    $gp_title       = '<div class="snippet-title-default" style="display:none">' . get_the_title() . ' - ' . get_bloginfo('name') . '</div>';
                    $gp_permalink   = '<div class="snippet-permalink">' . htmlspecialchars(urldecode(get_permalink())) . '</div>';
                } elseif ($tag) {
                    if (false === is_wp_error(get_term_link($tag))) {
                        $gp_title       = '<div class="snippet-title-default" style="display:none">' . $tag->name . ' - ' . get_bloginfo('name') . '</div>';
                        $gp_permalink   = '<div class="snippet-permalink">' . htmlspecialchars(urldecode(get_term_link($tag))) . '</div>';
                    }
                }

                $siteicon = '<div class="snippet-favicon"><img aria-hidden="true" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABs0lEQVR4AWL4//8/RRjO8Iucx+noO0MWUDo16FYABMGP6ZfUcRnWtm27jVPbtm3bttuH2t3eFPcY9pLz7NxiLjCyVd87pKnHyqXyxtCs8APd0rnyxiu4qSeA3QEDrAwBDrT1s1Rc/OrjLZwqVmOSu6+Lamcpp2KKMA9PH1BYXMe1mUP5qotvXTywsOEEYHXxrY+3cqk6TMkYpNr2FeoY3KIr0RPtn9wQ2unlA+GMkRw6+9TFw4YTwDUzx/JVvARj9KaedXRO8P5B1Du2S32smzqUrcKGEyA+uAgQjKX7zf0boWHGfn71jIKj2689gxp7OAGShNcBUmLMPVjZuiKcA2vuWHHDCQxMCz629kXAIU4ApY15QwggAFbfOP9DhgBJ+nWVJ1AZAfICAj1pAlY6hCADZnveQf7bQIwzVONGJonhLIlS9gr5mFg44Xd+4S3XHoGNPdJl1INIwKyEgHckEhgTe1bGiFY9GSFBYUwLh1IkiJUbY407E7syBSFxKTszEoiE/YdrgCEayDmtaJwCI9uu8TKMuZSVfSa4BpGgzvomBR/INhLGzrqDotp01ZR8pn/1L0JN9d9XNyx0AAAAAElFTkSuQmCC" width="16" alt="favicon"></div>';
                if (get_site_icon_url(32)) {
                    $siteicon = '<div class="snippet-favicon"><img aria-hidden="true" height="16" src="' . get_site_icon_url(32) . '" width="16" alt="favicon"/></div>';
                }

                echo '<div class="wrap-snippet">
								<div class="wrap-m-icon-permalink">' . $siteicon . $gp_permalink . '</div>
								<div class="snippet-title"></div>
								<div class="snippet-title-custom" style="display:none"></div>';

                echo $gp_title;
                echo $gp_permalink;

                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    echo seopress_display_date_snippet();
                }
                echo '<div class="snippet-description">...</div>
								<div class="snippet-description-custom" style="display:none"></div>
								<div class="snippet-description-default" style="display:none"></div>';
                echo '</div>
						</div>
					</div>
				</div>';
            }
            if (array_key_exists('advanced-tab', $seo_tabs)) {
                echo '<div id="tabs-2">
                <span class="sp-section">' . __('Meta robots settings', 'wp-seopress') . '</span>
					<p>
						<label for="seopress_robots_index_meta">
							<input type="checkbox" name="seopress_robots_index" id="seopress_robots_index_meta" value="yes" ' . checked($seopress_robots_index, 'yes', false) . ' ' . $disabled['robots_index'] . '/>
								' . __('Do not display this page in search engine results / XML - HTML sitemaps <strong>(noindex)</strong>', 'wp-seopress') . '
								' . seopress_tooltip(__('"noindex" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noindex". <br>Search engines will not index this URL in the search results.', 'wp-seopress'), esc_html('<meta name="robots" content="noindex" />')) . '
						</label>
					</p>
					<p>
						<label for="seopress_robots_follow_meta">
							<input type="checkbox" name="seopress_robots_follow" id="seopress_robots_follow_meta" value="yes" ' . checked($seopress_robots_follow, 'yes', false) . ' ' . $disabled['robots_follow'] . '/>
								' . __('Do not follow links for this page <strong>(nofollow)</strong>', 'wp-seopress') . '
								' . seopress_tooltip(__('"nofollow" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "nofollow". <br>Search engines will not follow links from this URL.', 'wp-seopress'), esc_html('<meta name="robots" content="nofollow" />')) . '
						</label>
					</p>
					<p>
						<label for="seopress_robots_odp_meta">
							<input type="checkbox" name="seopress_robots_odp" id="seopress_robots_odp_meta" value="yes" ' . checked($seopress_robots_odp, 'yes', false) . ' ' . $disabled['robots_odp'] . '/>
								' . __('Do not use Open Directory project metadata for titles or excerpts for this page <strong>(noodp)</strong>', 'wp-seopress') . '
								' . seopress_tooltip(__('"noodp" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noodp". <br>Note that Google and Yahoo have stopped considering this tag since the closing of DMOZ directory.', 'wp-seopress'), esc_html('<meta name="robots" content="noodp" />')) . '
						</label>
					</p>
					<p>
						<label for="seopress_robots_imageindex_meta">
							<input type="checkbox" name="seopress_robots_imageindex" id="seopress_robots_imageindex_meta" value="yes" ' . checked($seopress_robots_imageindex, 'yes', false) . ' ' . $disabled['imageindex'] . '/>
								' . __('Do not index images for this page <strong>(noimageindex)</strong>', 'wp-seopress') . '
								' . seopress_tooltip(__('"noimageindex" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noimageindex". <br> Note that your images can always be indexed if they are linked from other pages.', 'wp-seopress'), esc_html('<meta name="google" content="noimageindex" />')) . '
						</label>
					</p>
					<p>
						<label for="seopress_robots_archive_meta">
							<input type="checkbox" name="seopress_robots_archive" id="seopress_robots_archive_meta" value="yes" ' . checked($seopress_robots_archive, 'yes', false) . ' ' . $disabled['archive'] . '/>
								' . __('Do not display a "Cached" link in the Google search results <strong>(noarchive)</strong>', 'wp-seopress') . '
								' . seopress_tooltip(__('"noarchive" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "noarchive".', 'wp-seopress'), esc_html('<meta name="robots" content="noarchive" />')) . '
						</label>
					</p>
					<p>
						<label for="seopress_robots_snippet_meta">
							<input type="checkbox" name="seopress_robots_snippet" id="seopress_robots_snippet_meta" value="yes" ' . checked($seopress_robots_snippet, 'yes', false) . ' ' . $disabled['snippet'] . '/>
								' . __('Do not display a description in search results for this page <strong>(nosnippet)</strong>', 'wp-seopress') . '
								' . seopress_tooltip(__('"nosnippet" robots meta tag', 'wp-seopress'), __('By checking this option, you will add a meta robots tag with the value "nosnippet".', 'wp-seopress'), esc_html('<meta name="robots" content="nosnippet" />')) . '
						</label>
					</p>
					<p class="description">';
                $url = admin_url('admin.php?page=seopress-titles#tab=tab_seopress_titles_single');
                /* translators: %s: link to plugin settings page */
                echo sprintf(__('You cannot uncheck a parameter? This is normal, and it‘s most likely defined in the <a href="%s">global settings of the plugin.</a>', 'wp-seopress'), $url);
                echo '</p>
					<p>
						<label for="seopress_robots_canonical_meta">' . __('Canonical URL', 'wp-seopress') . '
							' . seopress_tooltip(__('Canonical URL', 'wp-seopress'), __('A canonical URL is the URL of the page that Google thinks is most representative from a set of duplicate pages on your site. <br>For example, if you have URLs for the same page (for example: example.com?dress=1234 and example.com/dresses/1234), Google chooses one as canonical. <br>Note that the pages do not need to be absolutely identical; minor changes in sorting or filtering of list pages do not make the page unique (for example, sorting by price or filtering by item color).
							The canonical can be in a different domain than a duplicate.', 'wp-seopress'), esc_html('<link rel="canonical" href="https://www.example.com/my-post-url/" />')) . '
						</label>
						<input id="seopress_robots_canonical_meta" type="text" name="seopress_robots_canonical" placeholder="' . esc_html__('Default value: ', 'wp-seopress') . htmlspecialchars(urldecode(get_permalink())) . '" aria-label="' . __('Canonical URL', 'wp-seopress') . '" value="' . $seopress_robots_canonical . '" />

						</span>
					</p>';

                if (('post' == $typenow || 'product' == $typenow) && ('post.php' == $pagenow || 'post-new.php' == $pagenow)) {
                    echo '<p>
							<label for="seopress_robots_primary_cat_meta">' . __('Select a primary category', 'wp-seopress') . '</label>
							<span class="description">' . __('Set the category that gets used in the %category% permalink and in our breadcrumbs if you have multiple categories.', 'wp-seopress') . '</p>
							<select name="seopress_robots_primary_cat">';

                    $cats = get_categories();

                    if ('product' == $typenow) {
                        $cats = get_the_terms($post, 'product_cat');
                    }

                    if ( ! empty($cats)) {
                        echo '<option ' . selected('none', $seopress_robots_primary_cat, false) . ' value="none">' . __('None (will disable this feature)', 'wp-seopress') . '</option>';
                        foreach ($cats as $category) {
                            echo '<option ' . selected($category->term_id, $seopress_robots_primary_cat, false) . ' value="' . $category->term_id . '">' . $category->name . '</option>';
                        }
                    }
                    echo '</select>
						</p>';
                }

                if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    echo '<p>
							<label for="seopress_robots_breadcrumbs_meta">' . __('Custom breadcrumbs', 'wp-seopress') . '</label>
							<span class="description">' . __('Enter a custom value, useful if your title is too long', 'wp-seopress') . '</span>
						</p>
						<p>
							<input id="seopress_robots_breadcrumbs_meta" type="text" name="seopress_robots_breadcrumbs" placeholder="' . esc_html(sprintf(__('Current breadcrumbs: %s', 'wp-seopress'), $data_attr['title'])) . '" aria-label="' . __('Custom breadcrumbs', 'wp-seopress') . '" value="' . $seopress_robots_breadcrumbs . '" />
						</p>';
                }
                echo '</div>';
            }
            if (array_key_exists('social-tab', $seo_tabs)) {
                echo '<div id="tabs-3">
					<div class="box-left">
						<span class="dashicons dashicons-facebook-alt"></span>
						<br><br>
						<span class="dashicons dashicons-external"></span><a href="https://developers.facebook.com/tools/debug/sharing/?q=' . get_permalink(get_the_id()) . '" target="_blank">' . __('Ask Facebook to update its cache', 'wp-seopress') . '</a>
						<p>' . __('<span class="label">Did you know?</span> LinkedIn, Instagram and Pinterest use the same social metadata as Facebook. Twitter does the same if no Twitter cards tags are defined below.', 'wp-seopress') . '</p>
						<p>
							<label for="seopress_social_fb_title_meta">' . __('Facebook Title', 'wp-seopress') . '</label>
							<input id="seopress_social_fb_title_meta" type="text" name="seopress_social_fb_title" placeholder="' . esc_html__('Enter your Facebook title', 'wp-seopress') . '" aria-label="' . __('Facebook Title', 'wp-seopress') . '" value="' . $seopress_social_fb_title . '" />
						</p>
						<p>
							<label for="seopress_social_fb_desc_meta">' . __('Facebook description', 'wp-seopress') . '</label>
							<textarea id="seopress_social_fb_desc_meta" name="seopress_social_fb_desc" placeholder="' . esc_html__('Enter your Facebook description', 'wp-seopress') . '" aria-label="' . __('Facebook description', 'wp-seopress') . '" value="' . $seopress_social_fb_desc . '">' . $seopress_social_fb_desc . '</textarea>
						</p>
						<p>
							<label for="seopress_social_fb_img_meta">' . __('Facebook Thumbnail', 'wp-seopress') . '</label>
							<input id="seopress_social_fb_img_meta" type="text" name="seopress_social_fb_img" placeholder="' . esc_html__('Select your default thumbnail', 'wp-seopress') . '" aria-label="' . __('Facebook Thumbnail', 'wp-seopress') . '" value="' . $seopress_social_fb_img . '" />
							<span class="advise">' . __('Minimum size: 200x200px, ideal ratio 1.91:1, 8Mb max. (eg: 1640x856px or 3280x1712px for retina screens)', 'wp-seopress') . '</span>
							<input id="seopress_social_fb_img_upload" class="button" type="button" value="' . __('Upload an Image', 'wp-seopress') . '" />
						</p>
					</div>
					<div class="box-right">
						<div class="facebook-snippet-preview">
							<h3>' . __('Facebook Preview', 'wp-seopress') . '</h3>';
                if ('1' == seopress_get_toggle_option('social')) {
                    echo '<p>' . __('This is what your post will look like in Facebook. You have to publish your post to get the Facebook Preview.', 'wp-seopress') . '</p>';
                } else {
                    echo '<p class="notice notice-error" style="margin: 0 0 1rem 0">' . __('The Social Networks feature is disabled. Still seing informations from the FB Preview? You probably have social tags added by your theme or a plugin.', 'wp-seopress') . '</p>';
                }
                echo '<div class="facebook-snippet-box">
								<div class="snippet-fb-img-alert alert1" style="display:none"><p class="notice notice-error">' . __('File type not supported by Facebook. Please choose another image.', 'wp-seopress') . '</p></div>
								<div class="snippet-fb-img-alert alert2" style="display:none"><p class="notice notice-error">' . __('Minimun size for Facebook is <strong>200x200px</strong>. Please choose another image.', 'wp-seopress') . '</p></div>
								<div class="snippet-fb-img-alert alert3" style="display:none"><p class="notice notice-error">' . __('File error. Please choose another image.', 'wp-seopress') . '</p></div>
								<div class="snippet-fb-img-alert alert4" style="display:none"><p class="notice notice-info">' . __('Your image ratio is: ', 'wp-seopress') . '<span></span>. ' . __('The closer to 1.91 the better.', 'wp-seopress') . '</p></div>
								<div class="snippet-fb-img-alert alert5" style="display:none"><p class="notice notice-error">' . __('File URL is not valid.', 'wp-seopress') . '</p></div>
								<div class="snippet-fb-img"><img src="" width="524" height="274" alt="" aria-label=""/></div>
								<div class="snippet-fb-img-custom" style="display:none"><img src="" width="524" height="274" alt="" aria-label=""/></div>
								<div class="snippet-fb-img-default" style="display:none"><img src="" width="524" height="274" alt="" aria-label=""/></div>

								<div class="facebook-snippet-text">
									<div class="snippet-meta">
										<div class="snippet-fb-url"></div>
										<div class="fb-sep">|</div>
										<div class="fb-by">' . __('By&nbsp;', 'wp-seopress') . '</div>
										<div class="snippet-fb-site-name"></div>
									</div>
									<div class="title-desc">
										<div class="snippet-fb-title"></div>
										<div class="snippet-fb-title-custom" style="display:none"></div>';
                global $tag;
                if (get_the_title()) {
                    echo '<div class="snippet-fb-title-default" style="display:none">' . get_the_title() . ' - ' . get_bloginfo('name') . '</div>';
                } elseif ($tag) {
                    echo '<div class="snippet-fb-title-default" style="display:none">' . $tag->name . ' - ' . get_bloginfo('name') . '</div>';
                }
                echo '<div class="snippet-fb-description">...</div>
										<div class="snippet-fb-description-custom" style="display:none"></div>
										<div class="snippet-fb-description-default" style="display:none"></div>';
                echo '</div>
								</div>
							</div>
						</div>
					</div>
					<div class="clear"></div>
					<div class="box-left">
						<br/>
						<span class="dashicons dashicons-twitter"></span>
						<br><br>
						<span class="dashicons dashicons-external"></span><a href="https://cards-dev.twitter.com/validator" target="_blank">' . __('Preview your Twitter card using the official validator', 'wp-seopress') . '</a>
						<p>
							<label for="seopress_social_twitter_title_meta">' . __('Twitter Title', 'wp-seopress') . '</label>
							<input id="seopress_social_twitter_title_meta" type="text" name="seopress_social_twitter_title" placeholder="' . esc_html__('Enter your Twitter title', 'wp-seopress') . '" aria-label="' . __('Twitter Title', 'wp-seopress') . '" value="' . $seopress_social_twitter_title . '" />
						</p>
						<p>
							<label for="seopress_social_twitter_desc_meta">' . __('Twitter description', 'wp-seopress') . '</label>
							<textarea id="seopress_social_twitter_desc_meta" name="seopress_social_twitter_desc" placeholder="' . esc_html__('Enter your Twitter description', 'wp-seopress') . '" aria-label="' . __('Twitter description', 'wp-seopress') . '" value="' . $seopress_social_twitter_desc . '">' . $seopress_social_twitter_desc . '</textarea>
						</p>
						<p>
							<label for="seopress_social_twitter_img_meta">' . __('Twitter Thumbnail', 'wp-seopress') . '</label>
							<input id="seopress_social_twitter_img_meta" type="text" name="seopress_social_twitter_img" placeholder="' . esc_html__('Select your default thumbnail', 'wp-seopress') . '" value="' . $seopress_social_twitter_img . '" />
							<span class="advise">' . __('Minimum size: 144x144px (300x157px with large card enabled), ideal ratio 1:1 (2:1 with large card), 5Mb max.', 'wp-seopress') . '</span>
							<input id="seopress_social_twitter_img_upload" class="button" type="button" aria-label="' . __('Twitter Thumbnail', 'wp-seopress') . '" value="' . __('Upload an Image', 'wp-seopress') . '" />
						</p>
					</div>
					<div class="box-right">
						<div class="twitter-snippet-preview">
							<h3>' . __('Twitter Preview', 'wp-seopress') . '</h3>';
                if ('1' == seopress_get_toggle_option('social')) {
                    echo '<p>' . __('This is what your post will look like in Twitter. You have to publish your post to get the Twitter Preview.', 'wp-seopress') . '</p>';
                } else {
                    echo '<p class="notice notice-error" style="margin: 0 0 1rem 0">' . __('The Social Networks feature is disabled. Still seing informations from the Twitter Preview? You probably have social tags added by your theme or a plugin.', 'wp-seopress') . '</p>';
                }
                echo '<div class="twitter-snippet-box">
								<div class="snippet-twitter-img-alert alert1" style="display:none"><p class="notice notice-error">' . __('File type not supported by Twitter. Please choose another image.', 'wp-seopress') . '</p></div>
								<div class="snippet-twitter-img-alert alert2" style="display:none"><p class="notice notice-error">' . __('Minimun size for Twitter is <strong>144x144px</strong>. Please choose another image.', 'wp-seopress') . '</p></div>
								<div class="snippet-twitter-img-alert alert3" style="display:none"><p class="notice notice-error">' . __('File error. Please choose another image.', 'wp-seopress') . '</p></div>
								<div class="snippet-twitter-img-alert alert4" style="display:none"><p class="notice notice-info">' . __('Your image ratio is: ', 'wp-seopress') . '<span></span>. ' . __('The closer to 1 the better (with large card, 2 is better).', 'wp-seopress') . '</p></div>
								<div class="snippet-twitter-img-alert alert5" style="display:none"><p class="notice notice-error">' . __('File URL is not valid.', 'wp-seopress') . '</p></div>
								<div class="snippet-twitter-img"><img src="" width="524" height="274" alt="" aria-label=""/></div>
								<div class="snippet-twitter-img-custom" style="display:none"><img src="" width="600" height="314" alt="" aria-label=""/></div>
								<div class="snippet-twitter-img-default" style="display:none"><img src="" width="600" height="314" alt="" aria-label=""/></div>

								<div class="twitter-snippet-text">
									<div class="title-desc">
										<div class="snippet-twitter-title"></div>
										<div class="snippet-twitter-title-custom" style="display:none"></div>';
                global $tag;
                if (get_the_title()) {
                    echo '<div class="snippet-twitter-title-default" style="display:none">' . get_the_title() . ' - ' . get_bloginfo('name') . '</div>';
                } elseif ($tag) {
                    echo '<div class="snippet-twitter-title-default" style="display:none">' . $tag->name . ' - ' . get_bloginfo('name') . '</div>';
                }
                echo '<div class="snippet-twitter-description">...</div>
										<div class="snippet-twitter-description-custom" style="display:none"></div>
										<div class="snippet-twitter-description-default" style="display:none"></div>';
                echo '</div>
									 <div class="snippet-meta">
										<div class="snippet-twitter-url"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>';
            }
        }

        if (array_key_exists('redirect-tab', $seo_tabs)) {
            echo '<div id="tabs-4">
				<p>
					<label for="seopress_redirections_enabled_meta" id="seopress_redirections_enabled">
						<input type="checkbox" name="seopress_redirections_enabled" id="seopress_redirections_enabled_meta" value="yes" ' . checked($seopress_redirections_enabled, 'yes', false) . ' />
							' . __('Enable redirection?', 'wp-seopress') . '
					</label>
				</p>
				<p>
					<label for="seopress_redirections_value_meta">' . __('URL redirection', 'wp-seopress') . '</label>
					<select name="seopress_redirections_type">
						<option ' . selected('301', $seopress_redirections_type, false) . ' value="301">' . __('301 Moved Permanently', 'wp-seopress') . '</option>
						<option ' . selected('302', $seopress_redirections_type, false) . ' value="302">' . __('302 Found / Moved Temporarily', 'wp-seopress') . '</option>
						<option ' . selected('307', $seopress_redirections_type, false) . ' value="307">' . __('307 Moved Temporarily', 'wp-seopress') . '</option>
						<option ' . selected('410', $seopress_redirections_type, false) . ' value="410">' . __('410 Gone', 'wp-seopress') . '</option>
						<option ' . selected('451', $seopress_redirections_type, false) . ' value="451">' . __('451 Unavailable For Legal Reasons', 'wp-seopress') . '</option>
					</select>
					<input id="seopress_redirections_value_meta" type="text" name="seopress_redirections_value" placeholder="' . esc_html__('Enter your new URL in absolute (eg: https://www.example.com/)', 'wp-seopress') . '" aria-label="' . __('URL redirection', 'wp-seopress') . '" value="' . $seopress_redirections_value . '" />
					<br><br>
				</p>';
            if ('seopress_404' == $typenow) {
                echo '<p>
					<label for="seopress_redirections_param_meta">' . __('Query parameters', 'wp-seopress') . '</label>
					<select name="seopress_redirections_param">
						<option ' . selected('exact_match', $seopress_redirections_param, false) . ' value="exact_match">' . __('Exactly match all parameters', 'wp-seopress') . '</option>
						<option ' . selected('without_param', $seopress_redirections_param, false) . ' value="without_param">' . __('Exclude all parameters', 'wp-seopress') . '</option>
						<option ' . selected('with_ignored_param', $seopress_redirections_param, false) . ' value="with_ignored_param">' . __('Exclude all parameters and pass them to the redirection', 'wp-seopress') . '</option>
					</select></p>';
            }
            echo '<p>';
            if ('yes' == $seopress_redirections_enabled) {
                $status_code = ['410', '451'];
                if ('' != $seopress_redirections_value || in_array($seopress_redirections_type, $status_code)) {
                    if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                        if ('seopress_404' == $typenow) {
                            echo '<a href="' . get_home_url() . '/' . get_the_title() . '" id="seopress_redirections_value_default" class="button" target="_blank">' . __('Test your URL', 'wp-seopress') . '</a>';
                        } else {
                            echo '<a href="' . get_permalink() . '" id="seopress_redirections_value_default" class="button" target="_blank">' . __('Test your URL', 'wp-seopress') . '</a>';
                        }
                    } elseif ('term.php' == $pagenow) {
                        echo '<a href="' . get_term_link($term) . '" id="seopress_redirections_value_default" class="button" target="_blank">' . __('Test your URL', 'wp-seopress') . '</a>';
                    } else {
                        echo '<a href="' . get_permalink() . '" id="seopress_redirections_value_default" class="button" target="_blank">' . __('Test your URL', 'wp-seopress') . '</a>';
                    }
                }
            }

            if ((function_exists('seopress_mu_white_label_help_links_option') && '1' !== seopress_mu_white_label_help_links_option()) || (function_exists('seopress_white_label_help_links_option') && '1' !== seopress_white_label_help_links_option())) {
                if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
                    $seopress_docs_link['support']['redirection'] = 'https://www.seopress.org/fr/support/guides/activer-redirections-301-surveillance-404/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                } else {
                    $seopress_docs_link['support']['redirection'] = 'https://www.seopress.org/support/guides/redirections/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                } ?>
                        <span class="seopress-help dashicons dashicons-external"></span>
                        <a href="<?php echo $seopress_docs_link['support']['redirection']; ?>" target="_blank" class="seopress-help seopress-doc"><?php _e('Need help with your redirections? Read our guide.', 'wp-seopress'); ?></a>
                        <?php echo '</p>';
            }
            echo '</div>';
        }
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('news')) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        if (array_key_exists('news-tab', $seo_tabs)) {
                            echo '<div id="tabs-5">
								<p>
									<label for="seopress_news_disabled_meta" id="seopress_news_disabled">
										<input type="checkbox" name="seopress_news_disabled" id="seopress_news_disabled_meta" value="yes" ' . checked($seopress_news_disabled, 'yes', false) . ' />
											' . __('Exclude this post from Google News Sitemap?', 'wp-seopress') . '
									</label>
								</p>
							</div>';
                        }
                    }
                }
            }
            if (function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('xml-sitemap') && function_exists('seopress_xml_sitemap_video_enable_option') && '1' == seopress_xml_sitemap_video_enable_option()) {
                if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                    if ('seopress_404' != $typenow) {
                        //Init $seopress_video array if empty
                        if (empty($seopress_video)) {
                            $seopress_video = ['0' => ['']];
                        }

                        $count = $seopress_video[0];
                        end($count);
                        $total = key($count);

                        if (array_key_exists('video-tab', $seo_tabs)) {
                            echo '<div id="tabs-6">
								<p>
									<label for="seopress_video_disabled_meta" id="seopress_video_disabled">
										<input type="checkbox" name="seopress_video_disabled" id="seopress_video_disabled_meta" value="yes" ' . checked($seopress_video_disabled, 'yes', false) . ' />
											' . __('Exclude this post from Video Sitemap?', 'wp-seopress') . '
									</label>
									<span class="advise">' . __('If your post is set to noindex, it will be automatically excluded from the sitemap.', 'wp-seopress') . '</span>
								</p>
								<div id="wrap-videos" data-count="' . $total . '">';
                            foreach ($seopress_video[0] as $key => $value) {
                                $check_url             = isset($seopress_video[0][$key]['url']) ? esc_attr($seopress_video[0][$key]['url']) : null;
                                $check_internal_video  = isset($seopress_video[0][$key]['internal_video']) ? esc_attr($seopress_video[0][$key]['internal_video']) : null;
                                $check_title           = isset($seopress_video[0][$key]['title']) ? esc_attr($seopress_video[0][$key]['title']) : null;
                                $check_desc            = isset($seopress_video[0][$key]['desc']) ? esc_attr($seopress_video[0][$key]['desc']) : null;
                                $check_thumbnail       = isset($seopress_video[0][$key]['thumbnail']) ? esc_attr($seopress_video[0][$key]['thumbnail']) : null;
                                $check_duration        = isset($seopress_video[0][$key]['duration']) ? esc_attr($seopress_video[0][$key]['duration']) : null;
                                $check_rating          = isset($seopress_video[0][$key]['rating']) ? esc_attr($seopress_video[0][$key]['rating']) : null;
                                $check_view_count      = isset($seopress_video[0][$key]['view_count']) ? esc_attr($seopress_video[0][$key]['view_count']) : null;
                                $check_view_count      = isset($seopress_video[0][$key]['view_count']) ? esc_attr($seopress_video[0][$key]['view_count']) : null;
                                $check_tag             = isset($seopress_video[0][$key]['tag']) ? esc_attr($seopress_video[0][$key]['tag']) : null;
                                $check_cat             = isset($seopress_video[0][$key]['cat']) ? esc_attr($seopress_video[0][$key]['cat']) : null;
                                $check_family_friendly = isset($seopress_video[0][$key]['family_friendly']) ? esc_attr($seopress_video[0][$key]['family_friendly']) : null;

                                echo '<div class="video">
											<h3 class="accordion-section-title" tabindex="0">' . __('Video ', 'wp-seopress') . $check_title . '</h3>
											<div class="accordion-section-content">
												<div class="inside">
													<p>
														<label for="seopress_video[' . $key . '][url_meta]">' . __('Video URL (required)', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][url_meta]" type="text" name="seopress_video[' . $key . '][url]" placeholder="' . esc_html__('Enter your video URL', 'wp-seopress') . '" aria-label="' . __('Video URL', 'wp-seopress') . '" value="' . $check_url . '" />
													</p>
													<p class="internal_video">
														<label for="seopress_video[' . $key . '][internal_video_meta]" id="seopress_video[' . $key . '][internal_video]">
															<input type="checkbox" name="seopress_video[' . $key . '][internal_video]" id="seopress_video[' . $key . '][internal_video_meta]" value="yes" ' . checked($check_internal_video, 'yes', false) . ' />
																' . __('NOT an external video (eg: video hosting on YouTube, Vimeo, Wistia...)? Check this if your video is hosting on this server.', 'wp-seopress') . '
														</label>
													</p>
													<p>
														<label for="seopress_video[' . $key . '][title_meta]">' . __('Video Title (required)', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][title_meta]" type="text" name="seopress_video[' . $key . '][title]" placeholder="' . esc_html__('Enter your video title', 'wp-seopress') . '" aria-label="' . __('Video title', 'wp-seopress') . '" value="' . $check_title . '" />
														<span class="advise">' . __('Default: title tag, if not available, post title.', 'wp-seopress') . '</span>
													</p>
													<p>
														<label for="seopress_video[' . $key . '][desc_meta]">' . __('Video Description (required)', 'wp-seopress') . '</label>
														<textarea id="seopress_video[' . $key . '][desc_meta]" name="seopress_video[' . $key . '][desc]" placeholder="' . esc_html__('Enter your video description', 'wp-seopress') . '" aria-label="' . __('Video description', 'wp-seopress') . '" value="' . $check_desc . '">' . $check_desc . '</textarea>
														<span class="advise">' . __('2048 characters max.; default: meta description. If not available, use the beginning of the post content.', 'wp-seopress') . '</span>
													</p>
													<p>
														<label for="seopress_video[' . $key . '][thumbnail_meta]">' . __('Video Thumbnail (required)', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][thumbnail_meta]" class="seopress_video_thumbnail_meta" type="text" name="seopress_video[' . $key . '][thumbnail]" placeholder="' . esc_html__('Select your video thumbnail', 'wp-seopress') . '" value="' . $check_thumbnail . '" />
														<input class="button seopress_video_thumbnail_upload seopress_media_upload" type="button" aria-label="' . __('Video Thumbnail', 'wp-seopress') . '" value="' . __('Upload an Image', 'wp-seopress') . '" />
														<span class="advise">' . __('Minimum size: 160x90px (1920x1080 max), JPG, PNG or GIF formats. Default: your post featured image.', 'wp-seopress') . '</span>
													</p>
													<p>
														<label for="seopress_video[' . $key . '][duration_meta]">' . __('Video Duration (recommended)', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][duration_meta]" type="number" step="1" min="0" max="28800" name="seopress_video[' . $key . '][duration]" placeholder="' . esc_html__('Duration in seconds', 'wp-seopress') . '" aria-label="' . __('Video duration', 'wp-seopress') . '" value="' . $check_duration . '" />
														<span class="advise">' . __('The duration of the video in seconds. Value must be between 0 and 28800 (8 hours).', 'wp-seopress') . '</span>
													</p>
													<p>
														<label for="seopress_video[' . $key . '][rating_meta]">' . __('Video Rating', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][rating_meta]" type="number" step="0.1" min="0" max="5" name="seopress_video[' . $key . '][rating]" placeholder="' . esc_html__('Video rating', 'wp-seopress') . '" aria-label="' . __('Video rating', 'wp-seopress') . '" value="' . $check_rating . '" />
														<span class="advise">' . __('Allowed values are float numbers in the range 0.0 to 5.0.', 'wp-seopress') . '</span>
													</p>
													<p>
														<label for="seopress_video[' . $key . '][view_count_meta]">' . __('View count', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][view_count_meta]" type="number" name="seopress_video[' . $key . '][view_count]" placeholder="' . esc_html__('Number of views', 'wp-seopress') . '" aria-label="' . __('View count', 'wp-seopress') . '" value="' . $check_view_count . '" />
													</p>
													<p>
														<label for="seopress_video[' . $key . '][tag_meta]">' . __('Video tags', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][tag_meta]" type="text" name="seopress_video[' . $key . '][tag]" placeholder="' . esc_html__('Enter your video tags', 'wp-seopress') . '" aria-label="' . __('Video tags', 'wp-seopress') . '" value="' . $check_tag . '" />
														<span class="advise">' . __('32 tags max., separate tags with commas. Default: target keywords + post tags if available.', 'wp-seopress') . '</span>
													</p>
													<p>
														<label for="seopress_video[' . $key . '][cat_meta]">' . __('Video categories', 'wp-seopress') . '</label>
														<input id="seopress_video[' . $key . '][cat_meta]" type="text" name="seopress_video[' . $key . '][cat]" placeholder="' . esc_html__('Enter your video categories', 'wp-seopress') . '" aria-label="' . __('Video categories', 'wp-seopress') . '" value="' . $check_cat . '" />
														<span class="advise">' . __('256 characters max., usually a video will belong to a single category, separate categories with commas. Default: first post category if available.', 'wp-seopress') . '</span>
													</p>
													<p class="family-friendly">
														<label for="seopress_video[' . $key . '][family_friendly_meta]" id="seopress_video[' . $key . '][family_friendly]">
															<input type="checkbox" name="seopress_video[' . $key . '][family_friendly]" id="seopress_video[' . $key . '][family_friendly_meta]" value="yes" ' . checked($check_family_friendly, 'yes', false) . ' />
																' . __('NOT family friendly?', 'wp-seopress') . '
														</label>
														<span class="advise">' . __('The video will be available only to users with SafeSearch turned off.', 'wp-seopress') . '</span>
													</p>
													<p><a href="#" class="remove-video button">' . __('Remove video', 'wp-seopress') . '</a></p>
												</div>
											</div>
										</div>
									';
                            }
                            echo '</div>
						<p><a href="#" id="add-video" class="add-video button button-primary">' . __('Add video', 'wp-seopress') . '</a></p>
						</div>';
                        }
                    }
                }
            }
        }
        echo '</div>';

if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
    echo '</div>';
    echo '</div>';
    echo '</td>';
    echo '</tr>';
}
echo '<input type="hidden" id="seo_tabs" name="seo_tabs" value="' . htmlspecialchars(json_encode(array_keys($seo_tabs))) . '">';
