<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Breadcrumbs
///////////////////////////////////////////////////////////////////////////////////////////////////
//Breadcrumbs separator
function seopress_breadcrumbs_separator_option() {
    $seopress_breadcrumbs_separator_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_separator_option)) {
        foreach ($seopress_breadcrumbs_separator_option as $key => $seopress_breadcrumbs_separator_value) {
            $options[$key] = $seopress_breadcrumbs_separator_value;
        }
        if (isset($seopress_breadcrumbs_separator_option['seopress_breadcrumbs_separator'])) {
            return $seopress_breadcrumbs_separator_option['seopress_breadcrumbs_separator'];
        }
    }
}

//i18n You are here
function seopress_breadcrumbs_i18n_here_option() {
    $seopress_breadcrumbs_i18n_here_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_i18n_here_option)) {
        foreach ($seopress_breadcrumbs_i18n_here_option as $key => $seopress_breadcrumbs_i18n_here_value) {
            $options[$key] = $seopress_breadcrumbs_i18n_here_value;
        }
        if (isset($seopress_breadcrumbs_i18n_here_option['seopress_breadcrumbs_i18n_here'])) {
            return $seopress_breadcrumbs_i18n_here_option['seopress_breadcrumbs_i18n_here'];
        }
    }
}

//i18n Homepage
function seopress_breadcrumbs_i18n_home_option() {
    $seopress_breadcrumbs_i18n_home_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_i18n_home_option)) {
        foreach ($seopress_breadcrumbs_i18n_home_option as $key => $seopress_breadcrumbs_i18n_home_value) {
            $options[$key] = $seopress_breadcrumbs_i18n_home_value;
        }
        if (isset($seopress_breadcrumbs_i18n_home_option['seopress_breadcrumbs_i18n_home'])) {
            return $seopress_breadcrumbs_i18n_home_option['seopress_breadcrumbs_i18n_home'];
        }
    }
}

//i18n 404 error
function seopress_breadcrumbs_i18n_404_option() {
    $seopress_breadcrumbs_i18n_404_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_i18n_404_option)) {
        foreach ($seopress_breadcrumbs_i18n_404_option as $key => $seopress_breadcrumbs_i18n_404_value) {
            $options[$key] = $seopress_breadcrumbs_i18n_404_value;
        }
        if (isset($seopress_breadcrumbs_i18n_404_option['seopress_breadcrumbs_i18n_404'])) {
            return $seopress_breadcrumbs_i18n_404_option['seopress_breadcrumbs_i18n_404'];
        }
    }
}

//i18n Search results for
function seopress_breadcrumbs_i18n_search_option() {
    $seopress_breadcrumbs_i18n_search_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_i18n_search_option)) {
        foreach ($seopress_breadcrumbs_i18n_search_option as $key => $seopress_breadcrumbs_i18n_search_value) {
            $options[$key] = $seopress_breadcrumbs_i18n_search_value;
        }
        if (isset($seopress_breadcrumbs_i18n_search_option['seopress_breadcrumbs_i18n_search'])) {
            return $seopress_breadcrumbs_i18n_search_option['seopress_breadcrumbs_i18n_search'];
        }
    }
}

//i18n No results
function seopress_breadcrumbs_i18n_no_results_option() {
    $seopress_breadcrumbs_i18n_no_results_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_i18n_no_results_option)) {
        foreach ($seopress_breadcrumbs_i18n_no_results_option as $key => $seopress_breadcrumbs_i18n_no_results_value) {
            $options[$key] = $seopress_breadcrumbs_i18n_no_results_value;
        }
        if (isset($seopress_breadcrumbs_i18n_no_results_option['seopress_breadcrumbs_i18n_no_results'])) {
            return $seopress_breadcrumbs_i18n_no_results_option['seopress_breadcrumbs_i18n_no_results'];
        }
    }
}

//Breadcrumbs remove blog page
function seopress_breadcrumbs_remove_blog_page_option() {
    $seopress_breadcrumbs_remove_blog_page_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_remove_blog_page_option)) {
        foreach ($seopress_breadcrumbs_remove_blog_page_option as $key => $seopress_breadcrumbs_remove_blog_page_value) {
            $options[$key] = $seopress_breadcrumbs_remove_blog_page_value;
        }
        if (isset($seopress_breadcrumbs_remove_blog_page_option['seopress_breadcrumbs_remove_blog_page'])) {
            return $seopress_breadcrumbs_remove_blog_page_option['seopress_breadcrumbs_remove_blog_page'];
        }
    }
}

//Breadcrumbs remove shop page
function seopress_breadcrumbs_remove_shop_page_option() {
    $seopress_breadcrumbs_remove_shop_page_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_remove_shop_page_option)) {
        foreach ($seopress_breadcrumbs_remove_shop_page_option as $key => $seopress_breadcrumbs_remove_shop_page_value) {
            $options[$key] = $seopress_breadcrumbs_remove_shop_page_value;
        }
        if (isset($seopress_breadcrumbs_remove_shop_page_option['seopress_breadcrumbs_remove_shop_page'])) {
            return $seopress_breadcrumbs_remove_shop_page_option['seopress_breadcrumbs_remove_shop_page'];
        }
    }
}

//Breadcrumbs disable default separator
function seopress_breadcrumbs_separator_disable_option() {
    $seopress_breadcrumbs_separator_disable_option = get_option('seopress_pro_option_name');
    if ( ! empty($seopress_breadcrumbs_separator_disable_option)) {
        foreach ($seopress_breadcrumbs_separator_disable_option as $key => $seopress_breadcrumbs_separator_disable_value) {
            $options[$key] = $seopress_breadcrumbs_separator_disable_value;
        }
        if (isset($seopress_breadcrumbs_separator_disable_option['seopress_breadcrumbs_separator_disable'])) {
            return $seopress_breadcrumbs_separator_disable_option['seopress_breadcrumbs_separator_disable'];
        }
    }
}

//Display Term archive link
function seopress_breadcrumbs_term_link($post, $crumbs, $options) {
    $cpt      = get_post_type($post);
    $taxonomy = isset($options['seopress_breadcrumbs_tax'][$cpt]['tax']) ? $options['seopress_breadcrumbs_tax'][$cpt]['tax'] : null;

    if ('none' != $taxonomy && null != $taxonomy) {//IF TAXONOMY SET FROM BREADCRUMBS OPTION
        if (get_post_meta($post->ID, '_seopress_robots_primary_cat', true)) {
            $_seopress_robots_primary_cat = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);

            if (isset($_seopress_robots_primary_cat) && '' != $_seopress_robots_primary_cat && 'none' != $_seopress_robots_primary_cat) {
                $tax = get_term($_seopress_robots_primary_cat, $taxonomy);

                $parent = current(wp_get_post_terms($post->ID, $taxonomy, ['orderby' => 'parent', 'order' => 'DESC', 'child_of' => $tax->term_id]));

                if ($parent !== false) {
                    $tax = $parent;
                }

            } else {
                $tax = current(wp_get_post_terms($post->ID, $taxonomy, ['orderby' => 'parent', 'order' => 'DESC']));
            }
        } else {
            $tax = current(wp_get_post_terms($post->ID, $taxonomy, ['orderby' => 'parent', 'order' => 'DESC']));
        }

        if (isset($tax->term_id)) {
            $ancestors_cat = get_ancestors($tax->term_id, $taxonomy);

            $ancestors_crumb = array_reverse($ancestors_cat);

            if ( ! empty($ancestors_crumb)) {
                foreach ($ancestors_crumb as $key => $value) {
                    $term = get_term($value, $taxonomy);
                    $term = $term->name;

                    if ('' != get_term_meta($value, '_seopress_robots_breadcrumbs', true)) {
                        $term = get_term_meta($value, '_seopress_robots_breadcrumbs', true);
                    }

                    $crumbs[] = [
                        0 => wp_strip_all_tags($term),
                        1 => get_term_link($value),
                    ];
                }
            }

            if ($tax) {
                $tax_name = $tax->name;

                if ('' != get_term_meta($tax->term_id, '_seopress_robots_breadcrumbs', true)) {
                    $tax_name = get_term_meta($tax->term_id, '_seopress_robots_breadcrumbs', true);
                }

                $crumbs[] = [
                    0 => wp_strip_all_tags($tax_name),
                    1 => get_term_link($tax),
                ];
            }
        }
    }

    return $crumbs;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//The Breadcrumbs
///////////////////////////////////////////////////////////////////////////////////////////////////
if ('1' == seopress_breadcrumbs_enable_option() || '1' == seopress_breadcrumbs_json_enable_option()) {
    //Inlince CSS in head
    function seopress_breadcrumbs_inline_css() {
        //Separator
        if ('' != seopress_breadcrumbs_separator_disable_option()) {
            $seopress_display_breadcrumbs_separator = null;
        } elseif (seopress_breadcrumbs_separator_option()) {
            $seopress_display_breadcrumbs_separator = ' ' . seopress_breadcrumbs_separator_option() . ' ';
        } else {
            $seopress_display_breadcrumbs_separator = ' - ';
        }

        $seopress_display_breadcrumbs_separator = apply_filters('seopress_pro_breadcrumbs_sep', $seopress_display_breadcrumbs_separator);

        $inline_css = "<style>.breadcrumb {list-style: none;margin:0}.breadcrumb li {margin:0;display:inline;position:relative}.breadcrumb li::after{content:'" . $seopress_display_breadcrumbs_separator . "'}.breadcrumb li:last-child::after{display:none}</style>";

        $inline_css = apply_filters('seopress_pro_breadcrumbs_css', $inline_css);

        echo $inline_css;
    }
    add_action('wp_head', 'seopress_breadcrumbs_inline_css', 30);

    function seopress_display_breadcrumbs($echo = true) {
        $page_id = get_option('page_for_posts');
        /**i18n**/
        //Home
        if ('' != seopress_breadcrumbs_i18n_home_option()) {
            $i18n_home = seopress_breadcrumbs_i18n_home_option();
        } else {
            $i18n_home = __('Home', 'wp-seopress-pro');
        }
        //404 error
        if ('' != seopress_breadcrumbs_i18n_404_option()) {
            $i18n_404 = seopress_breadcrumbs_i18n_404_option();
        } else {
            $i18n_404 = __('404 error', 'wp-seopress-pro');
        }
        //Search results for
        if ('' != seopress_breadcrumbs_i18n_search_option()) {
            $i18n_search_results = seopress_breadcrumbs_i18n_search_option();
        } else {
            $i18n_search_results = __('Search results for: ', 'wp-seopress-pro');
        }
        //No results
        if ('' != seopress_breadcrumbs_i18n_no_results_option()) {
            $i18n_no_results = seopress_breadcrumbs_i18n_no_results_option();
        } else {
            $i18n_no_results = __('No results', 'wp-seopress-pro');
        }

        //Globals
        global $post, $wp_query;

        //Init
        $crumbs  = [];
        $options = get_option('seopress_pro_option_name');

        //Home prefix
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (is_plugin_active('polylang/polylang.php') || is_plugin_active('polylang-pro/polylang.php')) {
            $real_home = pll_home_url();
        } else {
            $real_home = get_home_url();
        }

        $crumbs[] = [
            0 => $i18n_home,
            1 => $real_home,
        ];

        //404
        if (is_404()) {
            $crumbs[] = [
                0 => $i18n_404,
            ];
        }

        //Attachment
        if (is_attachment()) {
            $crumbs[] = [
                0 => __('Attachments', 'wp-seopress-pro'),
            ];
        }

        //Single
        if (is_single() && ( ! is_home() && ! is_front_page())) {
            if (is_singular('tribe_events')) { //Events calendar
                $queried_object = get_queried_object();
                $post_type      = get_post_type_object('tribe_events');

                $crumbs[] = [
                    0 => $post_type->labels->name,
                    1 => esc_url(tribe_get_events_link()),
                ];

                if ('' != get_post_meta($queried_object->ID, '_seopress_robots_breadcrumbs', true)) {
                    $crumbs[] = [
                        0 => wp_strip_all_tags(get_post_meta($queried_object->ID, '_seopress_robots_breadcrumbs', true)),
                        1 => esc_url(tribe_get_event_link($queried_object->ID)),
                    ];
                } else {
                    $crumbs[] = [
                        0 => wp_strip_all_tags($queried_object->post_title),
                        1 => esc_url(tribe_get_event_link($queried_object->ID)),
                    ];
                }
            } elseif ('post' != get_post_type($post)) {
                $post_type = get_post_type_object(get_post_type($post));
                if ('1' == $post_type->has_archive || true == $post_type->has_archive) {//CPT
                    //Product CPT
                    if (function_exists('is_shop') && 'product' == get_post_type($post) && get_option('woocommerce_shop_page_id')) {
                        //Shop base
                        if ('1' != seopress_breadcrumbs_remove_shop_page_option()) {
                            $crumbs[] = [
                                0 => wp_strip_all_tags(get_the_title(get_option('woocommerce_shop_page_id'))),
                                1 => get_page_link(get_option('woocommerce_shop_page_id')),
                            ];
                        }
                    } else {
                        //Display CPT archive link
                        $crumbs_cpt = [
                            0 => $post_type->labels->name,
                            1 => get_post_type_archive_link(get_post_type($post)),
                        ];

                        $crumbs_cpt = apply_filters('seopress_pro_breadcrumbs_remove_cpt', $crumbs_cpt, $post_type);

                        if (false !== $crumbs_cpt) {
                            $crumbs[] = $crumbs_cpt;
                        }
                    }

                    $crumbs = seopress_breadcrumbs_term_link($post, $crumbs, $options);

                    if ($post->post_parent) { //If post has parent pages
                        $parent_id = $post->post_parent;
                        while ($parent_id) {
                            $page          = get_post($parent_id);
                            $parent_id     = $page->post_parent;
                            if ('' != get_post_meta($page->ID, '_seopress_robots_breadcrumbs', true)) {
                                $parent_crumbs[] = [wp_strip_all_tags(get_post_meta($page->ID, '_seopress_robots_breadcrumbs', true)), get_permalink($page->ID)];
                            } else {
                                $parent_crumbs[] = [get_the_title($page->ID), get_permalink($page->ID)];
                            }
                        }

                        $parent_crumbs = array_reverse($parent_crumbs);

                        foreach ($parent_crumbs as $crumb) {
                            $crumbs[] = [
                                0 => $crumb[0],
                                1 => $crumb[1],
                            ];
                        }
                    }

                    if ('' != get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)) {
                        $crumbs[] = [
                            0 => wp_strip_all_tags(get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)),
                            1 => get_the_permalink(),
                        ];
                    } else {
                        $crumbs[] = [
                            0 => wp_strip_all_tags(get_the_title()),
                            1 => get_the_permalink(),
                        ];
                    }
                } else {
                    if (true === apply_filters('seopress_breadcrumbs_force_archive_name', '__return_false')) {
                        $crumbs[] = [
                            0 => $post_type->labels->name,
                        ];
                    }

                    if ('' != get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)) {
                        $crumbs[] = [
                            0 => wp_strip_all_tags(get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)),
                            1 => get_the_permalink(),
                        ];
                    } else {
                        $crumbs[] = [
                            0 => wp_strip_all_tags(get_the_title()),
                            1 => get_the_permalink(),
                        ];
                    }
                }
            } else {
                //Blog parent page
                if ('1' != seopress_breadcrumbs_remove_blog_page_option()) {
                    if ('page' == get_option('show_on_front') && '0' != $page_id) {
                        if ('' != get_post_meta($page_id, '_seopress_robots_breadcrumbs', true)) {
                            $crumbs[] = [
                                0 => wp_strip_all_tags(get_post_meta($page_id, '_seopress_robots_breadcrumbs', true)),
                                1 => get_the_permalink($page_id),
                            ];
                        } else {
                            $crumbs[] = [
                                0 => wp_strip_all_tags(get_the_title($page_id)),
                                1 => get_the_permalink($page_id),
                            ];
                        }
                    }
                }

                //Display Term archive link
                $crumbs = seopress_breadcrumbs_term_link($post, $crumbs, $options);

                //Default single post (custom + default)
                if ('' != get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)) {
                    $crumbs[] = [
                        0 => wp_strip_all_tags(get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)),
                        1 => get_the_permalink(),
                    ];
                } else {
                    $crumbs[] = [
                        0 => wp_strip_all_tags(get_the_title()),
                        1 => get_the_permalink(),
                    ];
                }
            }
        }

        //Page
        if (is_page() && ( ! is_home() && ! is_front_page())) {
            if ($post->post_parent) { //If post has parent pages
                $parent_id = $post->post_parent;
                while ($parent_id) {
                    $page          = get_post($parent_id);
                    $parent_id     = $page->post_parent;
                    if ('' != get_post_meta($page->ID, '_seopress_robots_breadcrumbs', true)) {
                        $parent_crumbs[] = [wp_strip_all_tags(get_post_meta($page->ID, '_seopress_robots_breadcrumbs', true)), get_permalink($page->ID)];
                    } else {
                        $parent_crumbs[] = [get_the_title($page->ID), get_permalink($page->ID)];
                    }
                }

                $parent_crumbs = array_reverse($parent_crumbs);

                foreach ($parent_crumbs as $crumb) {
                    $crumbs[] = [
                        0 => $crumb[0],
                        1 => $crumb[1],
                    ];
                }
            } elseif (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url()) { //WooCommerce Endpoint
                $crumbs[] = [
                    0 => get_the_title(),
                    1 => get_permalink(),
                ];
            }

            //Display Term archive link
            $crumbs = seopress_breadcrumbs_term_link($post, $crumbs, $options);

            //Current page
            if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url()) {
            } else {
                if ('' != get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)) {
                    $crumbs[] = [
                        0 => wp_strip_all_tags(get_post_meta(get_the_id(), '_seopress_robots_breadcrumbs', true)),
                        1 => get_the_permalink(),
                    ];
                } else {
                    $crumbs[] = [
                        0 => wp_strip_all_tags(get_the_title()),
                        1 => get_the_permalink(),
                    ];
                }
            }
        }

        //Blog
        if (is_home()) {
            if ('page' == get_option('show_on_front') && '0' != $page_id) {
                if ('' != get_post_meta($page_id, '_seopress_robots_breadcrumbs', true)) {
                    $crumbs[] = [
                        0 => wp_strip_all_tags(get_post_meta($page_id, '_seopress_robots_breadcrumbs', true)),
                        1 => get_the_permalink($page_id),
                    ];
                } else {
                    $crumbs[] = [
                        0 => wp_strip_all_tags(get_the_title($page_id)),
                        1 => get_the_permalink($page_id),
                    ];
                }
            }
        }

        //Post Type Archives
        if (is_post_type_archive('tribe_events')) { //Events calendar
            $post_type = get_post_type_object('tribe_events');

            $crumbs[] = [
                0 => wp_strip_all_tags($post_type->labels->name),
                1 => esc_url(tribe_get_events_link()),
            ];
        } elseif (is_post_type_archive()) {
            $post_type = get_post_type_object(get_post_type());

            if (isset($post_type) && 'product' == $post_type->name) {
                //Product CPT
                if (function_exists('is_shop') && get_option('woocommerce_shop_page_id')) {
                    //Shop base
                    if ('1' != seopress_breadcrumbs_remove_shop_page_option()) {
                        $crumbs[] = [
                            0 => wp_strip_all_tags(get_the_title(get_option('woocommerce_shop_page_id'))),
                            1 => get_page_link(get_option('woocommerce_shop_page_id')),
                        ];
                    }
                }
            } elseif (isset($post_type)) {
                $crumbs[] = [
                    0 => wp_strip_all_tags($post_type->labels->name),
                    1 => get_post_type_archive_link($post_type->name),
                ];
            } else {
                $crumbs[] = [
                    0 => $i18n_no_results,
                ];
            }
        }

        //Date Archives
        if (is_date()) {
            if (is_year() || is_month()) {
                $crumbs[] = [
                    0 => get_the_time('Y'),
                    1 => get_year_link(get_the_time('Y')),
                ];
            }
            if (is_month()) {
                $crumbs[] = [
                    0 => get_the_time('F'),
                    1 => get_month_link(get_the_time('Y'), get_the_time('m')),
                ];
            }
        }

        //Author Archives
        if (is_author()) {
            global $author;

            $author_name = get_userdata($author);

            $crumbs[] = [
                0 => __('Author: ', 'wp-seopress-pro') . $author_name->display_name,
                1 => get_author_posts_url($author_name->ID),
            ];
        }

        //Taxonomies (including Post Tag and Post Category)
        if (is_tax() || is_tag() || is_category()) {
            $current_term = $GLOBALS['wp_query']->get_queried_object();
            $taxonomy     = get_taxonomy($current_term->taxonomy);

            $cpt = isset($options['seopress_breadcrumbs_cpt'][$taxonomy->name]['cpt']) ? $options['seopress_breadcrumbs_cpt'][$taxonomy->name]['cpt'] : null;
            $cpt = get_post_type_object($cpt);

            if ('none' != $cpt && null != $cpt) {
                if ('post' == $cpt->name && '1' != seopress_breadcrumbs_remove_blog_page_option()) {//Blog page
                    if ('page' == get_option('show_on_front') && '0' != $page_id) {
                        if ('' != get_post_meta($page_id, '_seopress_robots_breadcrumbs', true)) {
                            $crumbs[] = [
                                0 => wp_strip_all_tags(get_post_meta($page_id, '_seopress_robots_breadcrumbs', true)),
                                1 => get_the_permalink($page_id),
                            ];
                        } else {
                            $crumbs[] = [
                                0 => wp_strip_all_tags(get_the_title($page_id)),
                                1 => get_the_permalink($page_id),
                            ];
                        }
                    }
                } elseif (function_exists('is_shop') && get_option('woocommerce_shop_page_id') && 'product' == $cpt->name) {//Shop page
                    //Shop base
                    if ('1' != seopress_breadcrumbs_remove_shop_page_option()) {
                        $crumbs[] = [
                            0 => wp_strip_all_tags(get_the_title(get_option('woocommerce_shop_page_id'))),
                            1 => get_page_link(get_option('woocommerce_shop_page_id')),
                        ];
                    }
                } else {
                    $crumbs[] = [
                        0 => wp_strip_all_tags($cpt->labels->name),
                        1 => get_post_type_archive_link($cpt->name),
                    ];
                }
            }

            //Ancestors
            if (0 != $current_term->parent) {
                $ancestors_term = get_ancestors($current_term->term_id, $current_term->taxonomy);

                $ancestors_crumb = array_reverse($ancestors_term);

                foreach ($ancestors_crumb as $key => $value) {
                    $current_term_name = get_term($value, $current_term->taxonomy);
                    $current_term_name = $current_term_name->name;

                    if ('' != get_term_meta($value, '_seopress_robots_breadcrumbs', true)) {
                        $current_term_name = get_term_meta($value, '_seopress_robots_breadcrumbs', true);
                    }

                    $crumbs[] = [
                        0 => wp_strip_all_tags($current_term_name),
                        1 => get_term_link($value),
                    ];
                }
            }

            //Current term
            $current_term_name = single_term_title('', false);

            if ('' != get_term_meta($current_term->term_id, '_seopress_robots_breadcrumbs', true)) {
                $current_term_name = get_term_meta($current_term->term_id, '_seopress_robots_breadcrumbs', true);
            }

            $crumbs[] = [
                0 => wp_strip_all_tags($current_term_name),
                1 => get_term_link($current_term),
            ];
        }

        //Search results
        if (is_search()) {
            $s_query = '';
            if ('' != get_search_query()) {
                $s_query = urlencode(get_query_var('s'));
            }

            $crumbs[] = [
                0 => $i18n_search_results . get_search_query(),
                1 => get_search_link($s_query),
            ];
        }

        //Pagination
        if (is_paged()) {
            global $wp;
            $current_url = home_url(add_query_arg([], $wp->request));

            $current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;

            $crumbs[] = [
                0 => __('Page ', 'wp-seopress-pro') . $current_page,
                1 => $current_url,
            ];
        }

        //WooCommerce Endpoint
        if (function_exists('is_wc_endpoint_url') && function_exists('wc_get_account_endpoint_url')) {
            if (is_wc_endpoint_url()) {
                $crumbs[] = [
                    0 => wp_strip_all_tags(WC()->query->get_endpoint_title(WC()->query->get_current_endpoint())),
                    1 => wc_get_account_endpoint_url(WC()->query->get_current_endpoint()),
                ];
            }
        }

        //Render
        if ((is_front_page() && is_paged()) || ! is_front_page()) {
            $sp_breadcrumbs_html = '';

            if (empty($crumbs) || ! is_array($crumbs)) {
                return;
            }

            //Schema.org itemListElement
            $crumbs   = apply_filters('seopress_pro_breadcrumbs_crumbs', $crumbs);
            $last_key = array_keys($crumbs);
            $last_key = array_pop($last_key);

            foreach ($crumbs as $key => $crumb) {
                $sep = $key;
                if ($last_key != $sep) {
                    $sp_breadcrumbs_html .= '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="' . seopress_check_ssl() . 'schema.org/ListItem">';
                } else {
                    $sp_breadcrumbs_html .= '<li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="' . seopress_check_ssl() . 'schema.org/ListItem">';
                }

                if ($last_key != $sep) {
                    if ( ! empty($crumb[1])) {
                        $sp_breadcrumbs_html .= '<a itemscope itemtype="http://schema.org/WebPage" itemprop="item" itemid="' . $crumb[1] . '" href="' . $crumb[1] . '">';
                    }
                }

                $sp_breadcrumbs_html .= '<span itemprop="name">' . $crumb[0] . '</span>';

                if ($last_key != $sep) {
                    if ( ! empty($crumb[1])) {
                        $sp_breadcrumbs_html .= '</a>';
                    }
                }

                $key = $key + 1;
                $sp_breadcrumbs_html .= '<meta itemprop="position" content="' . $key . '" />';
                $sp_breadcrumbs_html .= '</li>';
            }

            $here = '';
            if (seopress_breadcrumbs_i18n_here_option()) {
                $here = seopress_breadcrumbs_i18n_here_option();
            }

            $sp_breadcrumbs = '<nav aria-label="' . esc_html__('breadcrumb', 'wp-seopress-pro') . '">'.$here.'<ol class="breadcrumb" itemscope itemtype="' . seopress_check_ssl() . 'schema.org/BreadcrumbList">' . $sp_breadcrumbs_html . '</ol></nav>';

            $sp_breadcrumbs = apply_filters('seopress_pro_breadcrumbs_html', $sp_breadcrumbs);

            //JSON-LD
            if ('1' == seopress_breadcrumbs_json_enable_option()) {
                if (empty($crumbs) || ! is_array($crumbs)) {
                    return;
                }

                $sp_breadcrumbs_json = [];
                $sp_breadcrumbs_json = ['@context' => seopress_check_ssl() . 'schema.org', '@type' => 'BreadcrumbList'];

                $sp_breadcrumbs_json['itemListElement'] = [];

                foreach ($crumbs as $key => $crumb) {
                    $sp_breadcrumbs_json['itemListElement'][$key] = [
                        '@type'    => 'ListItem',
                        'position' => $key + 1,
                        'name'     => $crumb[0],
                    ];

                    //Check if URL is available
                    if ( ! empty($crumb[1])) {
                        $sp_breadcrumbs_json['itemListElement'][$key]['item'] = $crumb[1];
                    }
                }
            }

            if ('1' == seopress_breadcrumbs_json_enable_option()) {
                $jsonld = '<script type="application/ld+json">';
                $jsonld .= json_encode($sp_breadcrumbs_json);
                $jsonld .= '</script>';
                $jsonld .= "\n";
            }

            if ('1' == seopress_breadcrumbs_enable_option()) {
                if (true === $echo) {
                    do_action('seopress_breadcrumbs_before_html');
                    echo $sp_breadcrumbs;
                    do_action('seopress_breadcrumbs_after_html');
                } elseif (false === $echo) {
                    return do_action('seopress_breadcrumbs_before_html') . $sp_breadcrumbs . do_action('seopress_breadcrumbs_after_html');
                }
            }

            if ('1' == seopress_breadcrumbs_json_enable_option() && 'json' === $echo) {
                return $jsonld;
            }
        }
    }
    //Shortcode
    function seopress_shortcode_breadcrumbs() {
        return seopress_display_breadcrumbs(false);
    }
    if ('1' == seopress_breadcrumbs_enable_option()) {
        add_shortcode('seopress_breadcrumbs', 'seopress_shortcode_breadcrumbs');
    }

    //JSON-LD
    if ('1' == seopress_breadcrumbs_json_enable_option()) {
        add_action('wp_head', 'seopress_jsonld_breadcrumbs', 2);
        function seopress_jsonld_breadcrumbs() {
            echo seopress_display_breadcrumbs('json');
        }
    }
}
