<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');
///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPress Bot
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_request_bot() {
    check_ajax_referer('seopress_request_bot_nonce');

    if (current_user_can(seopress_capability('manage_options', 'bot')) && is_admin()) {
        //Init
        $data = [];

        //Links cleaning
        function seopress_bot_scan_settings_cleaning_option() {
            $seopress_bot_scan_settings_cleaning_option = get_option('seopress_bot_option_name');
            if ( ! empty($seopress_bot_scan_settings_cleaning_option)) {
                foreach ($seopress_bot_scan_settings_cleaning_option as $key => $seopress_bot_scan_settings_cleaning_value) {
                    $options[$key] = $seopress_bot_scan_settings_cleaning_value;
                }
                if (isset($seopress_bot_scan_settings_cleaning_option['seopress_bot_scan_settings_cleaning'])) {
                    return $seopress_bot_scan_settings_cleaning_option['seopress_bot_scan_settings_cleaning'];
                }
            }
        }

        //Cleaning seopress_bot post type
        if (1 == seopress_bot_scan_settings_cleaning_option() && isset($_POST['offset']) && 0 == $_POST['offset']) {
            global $wpdb;

            // delete all posts by post type.
            $sql = 'DELETE `posts`, `pm`
				FROM `' . $wpdb->prefix . 'posts` AS `posts`
				LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
				WHERE `posts`.`post_type` = \'seopress_bot\'';
            $wpdb->query($sql);
        }

        if (isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        //Type of links
        function seopress_bot_scan_settings_type_option() {
            $seopress_bot_scan_settings_type_option = get_option('seopress_bot_option_name');
            if ( ! empty($seopress_bot_scan_settings_type_option)) {
                foreach ($seopress_bot_scan_settings_type_option as $key => $seopress_bot_scan_settings_type_value) {
                    $options[$key] = $seopress_bot_scan_settings_type_value;
                }
                if (isset($seopress_bot_scan_settings_type_option['seopress_bot_scan_settings_type'])) {
                    return $seopress_bot_scan_settings_type_option['seopress_bot_scan_settings_type'];
                }
            }
        }

        //Find links in
        function seopress_bot_scan_settings_where_option() {
            $seopress_bot_scan_settings_where_option = get_option('seopress_bot_option_name');
            if ( ! empty($seopress_bot_scan_settings_where_option)) {
                foreach ($seopress_bot_scan_settings_where_option as $key => $seopress_bot_scan_settings_where_value) {
                    $options[$key] = $seopress_bot_scan_settings_where_value;
                }
                if (isset($seopress_bot_scan_settings_where_option['seopress_bot_scan_settings_where'])) {
                    return $seopress_bot_scan_settings_where_option['seopress_bot_scan_settings_where'];
                }
            }
        }

        //404 only
        function seopress_bot_scan_settings_404_option() {
            $seopress_bot_scan_settings_404_option = get_option('seopress_bot_option_name');
            if ( ! empty($seopress_bot_scan_settings_404_option)) {
                foreach ($seopress_bot_scan_settings_404_option as $key => $seopress_bot_scan_settings_404_value) {
                    $options[$key] = $seopress_bot_scan_settings_404_value;
                }
                if (isset($seopress_bot_scan_settings_404_option['seopress_bot_scan_settings_404'])) {
                    return $seopress_bot_scan_settings_404_option['seopress_bot_scan_settings_404'];
                }
            }
        }

        //Timeout
        function seopress_bot_scan_settings_timeout_option() {
            $seopress_bot_scan_settings_timeout_option = get_option('seopress_bot_option_name');
            if ( ! empty($seopress_bot_scan_settings_timeout_option)) {
                foreach ($seopress_bot_scan_settings_timeout_option as $key => $seopress_bot_scan_settings_timeout_value) {
                    $options[$key] = $seopress_bot_scan_settings_timeout_value;
                }
                if (isset($seopress_bot_scan_settings_timeout_option['seopress_bot_scan_settings_timeout'])) {
                    return $seopress_bot_scan_settings_timeout_option['seopress_bot_scan_settings_timeout'];
                }
            }
        }

        //Number of content to scan
        function seopress_bot_scan_settings_number_option() {
            $seopress_bot_scan_settings_number_option = get_option('seopress_bot_option_name');
            if ( ! empty($seopress_bot_scan_settings_number_option)) {
                foreach ($seopress_bot_scan_settings_number_option as $key => $seopress_bot_scan_settings_number_value) {
                    $options[$key] = $seopress_bot_scan_settings_number_value;
                }
                if (isset($seopress_bot_scan_settings_number_option['seopress_bot_scan_settings_number'])) {
                    return $seopress_bot_scan_settings_number_option['seopress_bot_scan_settings_number'];
                }
            }
        }

        //Include Custom Post Types
        function seopress_bot_scan_settings_post_types_option() {
            $seopress_bot_scan_settings_post_types_option = get_option('seopress_bot_option_name');
            if ( ! empty($seopress_bot_scan_settings_post_types_option)) {
                foreach ($seopress_bot_scan_settings_post_types_option as $key => $seopress_bot_scan_settings_post_types_value) {
                    $options[$key] = $seopress_bot_scan_settings_post_types_value;
                }
                if (isset($seopress_bot_scan_settings_post_types_option['seopress_bot_scan_settings_post_types'])) {
                    return $seopress_bot_scan_settings_post_types_option['seopress_bot_scan_settings_post_types'];
                }
            }
        }
        if ('' != seopress_bot_scan_settings_post_types_option()) {
            $seopress_bot_post_types_cpt_array = [];
            foreach (seopress_bot_scan_settings_post_types_option() as $cpt_key => $cpt_value) {
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if ('1' == $_cpt_value) {
                        array_push($seopress_bot_post_types_cpt_array, $cpt_key);
                    }
                }
            }

            if ('' != seopress_bot_scan_settings_number_option() && seopress_bot_scan_settings_number_option() >= 10) {
                $limit = seopress_bot_scan_settings_number_option();
            } else {
                $limit = 100;
            }

            global $post;

            if ($offset > $limit) {
                wp_reset_query();
                //Log date
                update_option('seopress-bot-log', current_time('Y-m-d H:i'), 'yes');

                $offset = 'done';
            } else {
                $args = [
                    'posts_per_page' => 1,
                    'offset'         => $offset,
                    'cache_results'  => false,
                    'order'          => 'DESC',
                    'orderby'        => 'date',
                    'post_type'      => $seopress_bot_post_types_cpt_array,
                    'post_status'    => 'publish',
                    'fields'         => 'ids',
                ];
                $args      = apply_filters('seopress_bot_query', $args);
                $bot_query = get_posts($args);

                if ($bot_query) {
                    //DOM
                    $dom                     = new DOMDocument();
                    $internalErrors          = libxml_use_internal_errors(true);
                    $dom->preserveWhiteSpace = false;

                    //Get source code
                    if ('' != seopress_bot_scan_settings_timeout_option()) {
                        $timeout = seopress_bot_scan_settings_timeout_option();
                    } else {
                        $timeout = 5;
                    }
                    $args = [
                        'blocking'    => true,
                        'timeout'     => $timeout,
                        'sslverify'   => false,
                        'compress'    => true,
                    ];
                    foreach ($bot_query as $post) {
                        if ('' == seopress_bot_scan_settings_where_option() || 'post_content' == seopress_bot_scan_settings_where_option()) {//post content
                            $response = apply_filters('the_content', get_post_field('post_content', $post));

                            //Themify compatibility
                            if (defined('THEMIFY_DIR')) {
                                $response = get_post_field('post_content', $post);
                            }
                        } else { //body page
                            $response = wp_remote_get(get_permalink($post), $args);

                            //Check for error
                            if (is_wp_error($response) || '404' == wp_remote_retrieve_response_code($response)) {
                                $data['post_title'] = __('Unable to request page: ', 'wp-seopress-pro') . get_the_title($post);
                            } else {
                                $response = wp_remote_retrieve_body($response);
                            }
                        }

                        if ( ! is_wp_error($response) || '404' !== wp_remote_retrieve_response_code($response)) {
                            $data['post_title'][] = get_the_title($post);

                            if ($dom->loadHTML('<?xml encoding="utf-8" ?>' . $response)) {
                                $xpath = new DOMXPath($dom);

                                //Links
                                $links = $xpath->query('//a');

                                if ( ! empty($links)) {
                                    foreach ($links as $key => $link) {
                                        $links2 = [];
                                        $links3 = [];

                                        $href = $link->getAttribute('href');
                                        $text = esc_attr($link->textContent);

                                        //remove anchors
                                        if ('#' != $href) {
                                            $links2[$text] = $href;
                                        }

                                        //remove duplicates
                                        $links2 = array_unique($links2);

                                        foreach ($links2 as $_key => $_value) {
                                            $args = [
                                                'timeout'       => $timeout,
                                                'blocking'      => true,
                                                'sslverify'     => false,
                                                'compress'      => true,
                                            ];

                                            $response = wp_remote_get($_value, $args);

                                            $bot_status_code = wp_remote_retrieve_response_code($response);

                                            if ( ! $bot_status_code) {
                                                $bot_status_code = __('domain not found', 'wp-seopress-pro');
                                            }

                                            if ('1' == seopress_bot_scan_settings_type_option()) {
                                                $bot_status_type = wp_remote_retrieve_header($response, 'content-type');
                                            }

                                            if ('1' == seopress_bot_scan_settings_404_option()) {
                                                if ('404' == $bot_status_code || strpos(json_encode($response), 'cURL error 6')) {
                                                    $links3[] = $_value;
                                                }
                                            } else {
                                                $links3[] = $_value;
                                            }
                                        }

                                        foreach ($links3 as $_key => $_value) {
                                            $check_page_id = get_page_by_title($_value, OBJECT, 'seopress_bot');

                                            if ($check_page_id->post_title != $_value && get_post_meta($check_page_id->ID, 'seopress_bot_source_url', true) != $_value) {
                                                wp_insert_post([
                                                    'post_title'        => $_value,
                                                    'post_type'         => 'seopress_bot',
                                                    'post_status'       => 'publish',
                                                    'meta_input'        => [
                                                        'seopress_bot_response'         => json_encode($response),
                                                        'seopress_bot_type'             => $bot_status_type,
                                                        'seopress_bot_status'           => $bot_status_code,
                                                        'seopress_bot_source_url'       => get_permalink($post),
                                                        'seopress_bot_source_id'        => $post,
                                                        'seopress_bot_cpt'        		    => get_post_type($post),
                                                        'seopress_bot_source_title'     => get_the_title($post),
                                                        'seopress_bot_a_title'          => $text,
                                                        ],
                                                    ]
                                                );
                                            } elseif ($check_page_id->post_title == $_value) {
                                                $seopress_bot_count = get_post_meta($check_page_id->ID, 'seopress_bot_count', true);
                                                update_post_meta($check_page_id->ID, 'seopress_bot_count', ++$seopress_bot_count);
                                            }

                                            $data['link'][] = $_value;
                                        }
                                    }
                                }
                            }
                        }
                    }//End foreach
                    libxml_use_internal_errors($internalErrors);
                    ++$offset;
                } else {
                    wp_reset_query();
                    //Log date
                    update_option('seopress-bot-log', current_time('Y-m-d H:i'), 'yes');

                    $offset = 'done';
                }
            }
        }
        $data['offset'] = $offset;

        //Return
        wp_send_json_success($data);
    }
}
add_action('wp_ajax_seopress_request_bot', 'seopress_request_bot');
///////////////////////////////////////////////////////////////////////////////////////////////////
//Admin Columns PRO
///////////////////////////////////////////////////////////////////////////////////////////////////
if (is_plugin_active('admin-columns-pro/admin-columns-pro.php')) {
    add_action('ac/column_groups', 'ac_register_seopress_column_group');
    function ac_register_seopress_column_group(AC\Groups $groups) {
        $groups->register_group('seopress', 'SEOPress');
    }

    add_action('ac/column_types', 'ac_register_seopress_columns');
    function ac_register_seopress_columns(AC\ListScreen $list_screen) {
        if ($list_screen instanceof ACP\ListScreen\Post) {
            require_once plugin_dir_path(__FILE__) . 'admin-columns/acp-column-sp_title.php';
            require_once plugin_dir_path(__FILE__) . 'admin-columns/acp-column-sp_desc.php';
            require_once plugin_dir_path(__FILE__) . 'admin-columns/acp-column-sp_noindex.php';
            require_once plugin_dir_path(__FILE__) . 'admin-columns/acp-column-sp_nofollow.php';
            require_once plugin_dir_path(__FILE__) . 'admin-columns/acp-column-sp_target_kw.php';

            $list_screen->register_column_type(new ACP_Column_sp_title());
            $list_screen->register_column_type(new ACP_Column_sp_desc());
            $list_screen->register_column_type(new ACP_Column_sp_noindex());
            $list_screen->register_column_type(new ACP_Column_sp_nofollow());
            $list_screen->register_column_type(new ACP_Column_sp_target_kw());
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Request Reverse domains
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_request_reverse() {
    check_ajax_referer('seopress_request_reverse_nonce');

    delete_transient('seopress_results_reverse');
    if (false === ($seopress_results_reverse_cache = get_transient('seopress_results_reverse'))) {
        $clean_url = str_replace(['http://', 'https://'], '', '' . get_home_url());

        $response = wp_remote_post('https://domains.yougetsignal.com/domains.php?remoteAddress=' . $clean_url);

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            $response_body = __('An error occurred, please try again.', 'wp-seopress-pro');
        } else {
            $response_body = wp_remote_retrieve_body($response);
        }

        $seopress_results_reverse_cache = $response_body;
        set_transient('seopress_results_reverse', $seopress_results_reverse_cache, 365 * DAY_IN_SECONDS);
    }
    wp_send_json_success($data);
}
add_action('wp_ajax_seopress_request_reverse', 'seopress_request_reverse');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Request Google PageSpeed Insights
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_request_page_speed() {
    check_ajax_referer('seopress_request_page_speed_nonce');

    $seopress_google_api_key = 'AIzaSyBqvSx2QrqbEqZovzKX8znGpTosw7KClHQ';

    if (isset($_GET['data_permalink'])) {
        $seopress_get_site_url = $_GET['data_permalink'];
    } else {
        $seopress_get_site_url = get_home_url();
    }

    delete_transient('seopress_results_page_speed');

    $args = ['timeout' => 30];

    if (false === ($seopress_results_page_speed_cache = get_transient('seopress_results_page_speed'))) {
        $seopress_results_page_speed       = wp_remote_retrieve_body(wp_remote_get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $seopress_get_site_url . '&key=' . $seopress_google_api_key . '&screenshot=true&strategy=mobile&locale=' . get_locale(), $args));
        $seopress_results_page_speed_cache = $seopress_results_page_speed;
        set_transient('seopress_results_page_speed', $seopress_results_page_speed_cache, 365 * DAY_IN_SECONDS);
    }

    $data = ['url' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_page_speed')];
    wp_send_json_success($data);
}
add_action('wp_ajax_seopress_request_page_speed', 'seopress_request_page_speed');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Reset License
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_request_reset_license() {
    check_ajax_referer('seopress_request_reset_license_nonce');

    if (current_user_can(seopress_capability('manage_options', 'bot')) && is_admin()) {
        delete_option('seopress_pro_license_status');
        delete_option('seopress_pro_license_key');

        $data = ['url' => admin_url('admin.php?page=seopress-license')];
        wp_send_json_success($data);
    }
}
add_action('wp_ajax_seopress_request_reset_license', 'seopress_request_reset_license');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Lock Google Analytics view
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_google_analytics_lock() {
    check_ajax_referer('seopress_google_analytics_lock_nonce');

    update_option('seopress_google_analytics_lock_option_name', '1', 'yes');

    wp_send_json_success();
}
add_action('wp_ajax_seopress_google_analytics_lock', 'seopress_google_analytics_lock');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Request Google Analytics
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Request GA stats by CRON.
 *
 * @since 4.2
 *
 * @author Benjamin
 */
function seopress_request_google_analytics_cron() {
    seopress_request_google_analytics();
}
add_action('seopress_google_analytics_cron', 'seopress_request_google_analytics_cron');

function seopress_request_google_analytics() {
    //check_ajax_referer( 'seopress_request_google_analytics_nonce' );

    if (is_admin()) {
        function seopress_google_analytics_auth_option() {
            $seopress_google_analytics_auth_option = get_option('seopress_google_analytics_option_name');
            if ( ! empty($seopress_google_analytics_auth_option)) {
                foreach ($seopress_google_analytics_auth_option as $key => $seopress_google_analytics_auth_value) {
                    $options[$key] = $seopress_google_analytics_auth_value;
                }
                if (isset($seopress_google_analytics_auth_option['seopress_google_analytics_auth'])) {
                    return $seopress_google_analytics_auth_option['seopress_google_analytics_auth'];
                }
            }
        }

        function seopress_google_analytics_auth_client_id_option() {
            $seopress_google_analytics_auth_client_id_option = get_option('seopress_google_analytics_option_name');
            if ( ! empty($seopress_google_analytics_auth_client_id_option)) {
                foreach ($seopress_google_analytics_auth_client_id_option as $key => $seopress_google_analytics_auth_client_id_value) {
                    $options[$key] = $seopress_google_analytics_auth_client_id_value;
                }
                if (isset($seopress_google_analytics_auth_client_id_option['seopress_google_analytics_auth_client_id'])) {
                    return $seopress_google_analytics_auth_client_id_option['seopress_google_analytics_auth_client_id'];
                }
            }
        }

        function seopress_google_analytics_auth_secret_id_option() {
            $seopress_google_analytics_auth_secret_id_option = get_option('seopress_google_analytics_option_name');
            if ( ! empty($seopress_google_analytics_auth_secret_id_option)) {
                foreach ($seopress_google_analytics_auth_secret_id_option as $key => $seopress_google_analytics_auth_secret_id_value) {
                    $options[$key] = $seopress_google_analytics_auth_secret_id_value;
                }
                if (isset($seopress_google_analytics_auth_secret_id_option['seopress_google_analytics_auth_secret_id'])) {
                    return $seopress_google_analytics_auth_secret_id_option['seopress_google_analytics_auth_secret_id'];
                }
            }
        }

        function seopress_google_analytics_auth_token_option() {
            $seopress_google_analytics_auth_token_option = get_option('seopress_google_analytics_option_name1');
            if ( ! empty($seopress_google_analytics_auth_token_option)) {
                foreach ($seopress_google_analytics_auth_token_option as $key => $seopress_google_analytics_auth_token_value) {
                    $options[$key] = $seopress_google_analytics_auth_token_value;
                }
                if (isset($seopress_google_analytics_auth_token_option['access_token'])) {
                    return $seopress_google_analytics_auth_token_option['access_token'];
                }
            }
        }

        function seopress_google_analytics_refresh_token_option() {
            $seopress_google_analytics_refresh_token_option = get_option('seopress_google_analytics_option_name1');
            if ( ! empty($seopress_google_analytics_refresh_token_option)) {
                foreach ($seopress_google_analytics_refresh_token_option as $key => $seopress_google_analytics_refresh_token_value) {
                    $options[$key] = $seopress_google_analytics_refresh_token_value;
                }
                if (isset($seopress_google_analytics_refresh_token_option['refresh_token'])) {
                    return $seopress_google_analytics_refresh_token_option['refresh_token'];
                }
            }
        }

        function seopress_google_analytics_debug_option() {
            $seopress_google_analytics_debug_option = get_option('seopress_google_analytics_option_name1');
            if ( ! empty($seopress_google_analytics_debug_option)) {
                foreach ($seopress_google_analytics_debug_option as $key => $seopress_google_analytics_debug_value) {
                    $options[$key] = $seopress_google_analytics_debug_value;
                }
                if (isset($seopress_google_analytics_debug_option['debug'])) {
                    return $seopress_google_analytics_debug_option['debug'];
                }
            }
        }

        if ('' != seopress_google_analytics_auth_option() && '' != seopress_google_analytics_auth_token_option()) {
            // get saved data
            if ( ! $widget_options = get_option('seopress_ga_dashboard_widget_options')) {
                $widget_options = [];
            }

            // check if saved data contains content
            $seopress_ga_dashboard_widget_options_period = isset($widget_options['period'])
                ? $widget_options['period'] : false;

            $seopress_ga_dashboard_widget_options_type = isset($widget_options['type'])
                ? $widget_options['type'] : false;

            // custom content saved by control callback, modify output
            if ($seopress_ga_dashboard_widget_options_period) {
                $period = $seopress_ga_dashboard_widget_options_period;
            } else {
                $period = '30daysAgo';
            }

            if ('' != seopress_google_analytics_auth_client_id_option()) {
                $client_id = seopress_google_analytics_auth_client_id_option();
            }

            if ('' != seopress_google_analytics_auth_secret_id_option()) {
                $client_secret = seopress_google_analytics_auth_secret_id_option();
            }

            $ga_account   = 'ga:' . seopress_google_analytics_auth_option();
            $redirect_uri = admin_url('admin.php?page=seopress-google-analytics');

            require_once SEOPRESS_PRO_PLUGIN_DIR_PATH . '/vendor/autoload.php';

            $client = new Google_Client();
            $client->setApplicationName('Client_Library_Examples');
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
            $client->setApprovalPrompt('force');   // mandatory to get this fucking refreshtoken
            $client->setAccessType('offline'); // mandatory to get this fucking refreshtoken

            $client->setAccessToken(seopress_google_analytics_debug_option());

            if ($client->isAccessTokenExpired()) {
                $client->refreshToken(seopress_google_analytics_debug_option());

                $seopress_new_access_token = $client->getAccessToken(seopress_google_analytics_debug_option());

                $seopress_google_analytics_options                  = get_option('seopress_google_analytics_option_name1');
                $seopress_google_analytics_options['access_token']  = $seopress_new_access_token['access_token'];
                $seopress_google_analytics_options['refresh_token'] = $seopress_new_access_token['refresh_token'];
                $seopress_google_analytics_options['debug']         = $seopress_new_access_token;
                update_option('seopress_google_analytics_option_name1', $seopress_google_analytics_options, 'yes');
            }

            $service = new Google_Service_AnalyticsReporting($client);

            if (false === ($seopress_results_google_analytics_cache = get_transient('seopress_results_google_analytics'))) {
                $seopress_results_google_analytics_cache = [];

                ////////////////////////////////////////////////////////////////////////////////////////
                //Request Google Stats
                ////////////////////////////////////////////////////////////////////////////////////////

                //DATE RANGE
                ////////////////////////////////////////////////////////////////////////////////////////

                // Date
                $dateRange = new Google_Service_AnalyticsReporting_DateRange();
                $dateRange->setStartDate($period);
                $dateRange->setEndDate('today');

                //METRICS
                ////////////////////////////////////////////////////////////////////////////////////////

                // Sessions
                $sessions = new Google_Service_AnalyticsReporting_Metric();
                $sessions->setExpression('ga:sessions');
                $sessions->setAlias('sessions');

                // Users
                $users = new Google_Service_AnalyticsReporting_Metric();
                $users->setExpression('ga:users');
                $users->setAlias('users');

                // Page Views
                $pageviews = new Google_Service_AnalyticsReporting_Metric();
                $pageviews->setExpression('ga:pageviews');
                $pageviews->setAlias('pageviews');

                // Page Views per session
                $pageviewsPerSession = new Google_Service_AnalyticsReporting_Metric();
                $pageviewsPerSession->setExpression('ga:pageviewsPerSession');
                $pageviewsPerSession->setAlias('pageviewsPerSession');

                // Average session duration
                $avgSessionDuration = new Google_Service_AnalyticsReporting_Metric();
                $avgSessionDuration->setExpression('ga:avgSessionDuration');
                $avgSessionDuration->setAlias('avgSessionDuration');

                // Bounce rate
                $bounceRate = new Google_Service_AnalyticsReporting_Metric();
                $bounceRate->setExpression('ga:bounceRate');
                $bounceRate->setAlias('bounceRate');

                // % New sessions
                $percentNewSessions = new Google_Service_AnalyticsReporting_Metric();
                $percentNewSessions->setExpression('ga:percentNewSessions');
                $percentNewSessions->setAlias('percentNewSessions');

                // Total events
                $totalEvents = new Google_Service_AnalyticsReporting_Metric();
                $totalEvents->setExpression('ga:totalEvents');
                $totalEvents->setAlias('totalEvents');

                // Unique events
                $uniqueEvents = new Google_Service_AnalyticsReporting_Metric();
                $uniqueEvents->setExpression('ga:uniqueEvents');
                $uniqueEvents->setAlias('uniqueEvents');

                //DIMENSIONS
                ////////////////////////////////////////////////////////////////////////////////////////

                // Date
                $date = new Google_Service_AnalyticsReporting_Dimension();
                $date->setName('ga:date');

                // Language
                $language = new Google_Service_AnalyticsReporting_Dimension();
                $language->setName('ga:language');

                // Country
                $country = new Google_Service_AnalyticsReporting_Dimension();
                $country->setName('ga:country');

                // Device Category
                $deviceCategory = new Google_Service_AnalyticsReporting_Dimension();
                $deviceCategory->setName('ga:deviceCategory');

                // Browser
                $browser = new Google_Service_AnalyticsReporting_Dimension();
                $browser->setName('ga:browser');

                // Social Network
                $socialNetwork = new Google_Service_AnalyticsReporting_Dimension();
                $socialNetwork->setName('ga:socialNetwork');

                // Channel Grouping
                $channelGrouping = new Google_Service_AnalyticsReporting_Dimension();
                $channelGrouping->setName('ga:channelGrouping');

                // Source
                $source = new Google_Service_AnalyticsReporting_Dimension();
                $source->setName('ga:source');

                // Full Referrer
                $fullReferrer = new Google_Service_AnalyticsReporting_Dimension();
                $fullReferrer->setName('ga:fullReferrer');

                // Page Title
                $pageTitle = new Google_Service_AnalyticsReporting_Dimension();
                $pageTitle->setName('ga:pageTitle');

                // Event Category
                $eventCategory = new Google_Service_AnalyticsReporting_Dimension();
                $eventCategory->setName('ga:eventCategory');

                // Event Action
                $eventAction = new Google_Service_AnalyticsReporting_Dimension();
                $eventAction->setName('ga:eventAction');

                // Event Label
                $eventLabel = new Google_Service_AnalyticsReporting_Dimension();
                $eventLabel->setName('ga:eventLabel');

                //ORDERS
                ////////////////////////////////////////////////////////////////////////////////////////

                // Order by user desc
                $order_by_users_desc = new Google_Service_AnalyticsReporting_OrderBy();
                $order_by_users_desc->setFieldName('ga:users');
                $order_by_users_desc->setOrderType('VALUE');
                $order_by_users_desc->setSortOrder('DESCENDING');

                // Order by page views desc
                $order_by_pageviews_desc = new Google_Service_AnalyticsReporting_OrderBy();
                $order_by_pageviews_desc->setFieldName('ga:pageviews');
                $order_by_pageviews_desc->setOrderType('VALUE');
                $order_by_pageviews_desc->setSortOrder('DESCENDING');

                // Order by total events desc
                $order_by_events_desc = new Google_Service_AnalyticsReporting_OrderBy();
                $order_by_events_desc->setFieldName('ga:totalEvents');
                $order_by_events_desc->setOrderType('VALUE');
                $order_by_events_desc->setSortOrder('DESCENDING');

                //REPORTS
                ////////////////////////////////////////////////////////////////////////////////////////

                // Sessions, Users, Page Views, Page Views Per Session, Average Session Duration, Bounce Rate, New Sessions, Total Events and Unique Events by Date
                $request_by_date = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_by_date->setViewId(seopress_google_analytics_auth_option());
                $request_by_date->setDateRanges($dateRange);
                $request_by_date->setDimensions([$date]);
                $request_by_date->setMetrics([$sessions, $users, $pageviews, $pageviewsPerSession, $avgSessionDuration, $bounceRate, $percentNewSessions, $totalEvents, $uniqueEvents]);
                $request_by_date->setSamplingLevel('SMALL');

                // Users by language
                $request_users_by_language = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_language->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_language->setDateRanges($dateRange);
                $request_users_by_language->setDimensions([$language]);
                $request_users_by_language->setMetrics([$users]);
                $request_users_by_language->setSamplingLevel('SMALL');
                $request_users_by_language->setOrderBys($order_by_users_desc);

                // Users by country
                $request_users_by_country = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_country->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_country->setDateRanges($dateRange);
                $request_users_by_country->setDimensions([$country]);
                $request_users_by_country->setMetrics([$users]);
                $request_users_by_country->setSamplingLevel('SMALL');
                $request_users_by_country->setOrderBys($order_by_users_desc);

                // Users by device category
                $request_users_by_device_cat = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_device_cat->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_device_cat->setDateRanges($dateRange);
                $request_users_by_device_cat->setDimensions([$deviceCategory]);
                $request_users_by_device_cat->setMetrics([$users]);
                $request_users_by_device_cat->setSamplingLevel('SMALL');
                $request_users_by_device_cat->setOrderBys($order_by_users_desc);

                // Users by Browser
                $request_users_by_browser = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_browser->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_browser->setDateRanges($dateRange);
                $request_users_by_browser->setDimensions([$browser]);
                $request_users_by_browser->setMetrics([$users]);
                $request_users_by_browser->setSamplingLevel('SMALL');
                $request_users_by_browser->setOrderBys($order_by_users_desc);

                // Users by Social Networks
                $request_users_by_social_network = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_social_network->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_social_network->setDateRanges($dateRange);
                $request_users_by_social_network->setDimensions([$socialNetwork]);
                $request_users_by_social_network->setMetrics([$users]);
                $request_users_by_social_network->setSamplingLevel('SMALL');
                $request_users_by_social_network->setOrderBys($order_by_users_desc);

                // Users by Channel
                $request_users_by_channel = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_channel->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_channel->setDateRanges($dateRange);
                $request_users_by_channel->setDimensions([$channelGrouping]);
                $request_users_by_channel->setMetrics([$users]);
                $request_users_by_channel->setSamplingLevel('SMALL');

                // Users by Source
                $request_users_by_source = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_source->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_source->setDateRanges($dateRange);
                $request_users_by_source->setDimensions([$source]);
                $request_users_by_source->setMetrics([$users]);
                $request_users_by_source->setSamplingLevel('SMALL');
                $request_users_by_source->setOrderBys($order_by_users_desc);

                // Users by Referrer
                $request_users_by_ref = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_users_by_ref->setViewId(seopress_google_analytics_auth_option());
                $request_users_by_ref->setDateRanges($dateRange);
                $request_users_by_ref->setDimensions([$fullReferrer]);
                $request_users_by_ref->setMetrics([$users]);
                $request_users_by_ref->setSamplingLevel('SMALL');
                $request_users_by_ref->setOrderBys($order_by_users_desc);

                // Page Views by Page Title
                $request_page_views_page_title = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_page_views_page_title->setViewId(seopress_google_analytics_auth_option());
                $request_page_views_page_title->setDateRanges($dateRange);
                $request_page_views_page_title->setDimensions([$pageTitle]);
                $request_page_views_page_title->setMetrics([$pageviews]);
                $request_page_views_page_title->setSamplingLevel('SMALL');
                $request_page_views_page_title->setOrderBys($order_by_pageviews_desc);

                // Event Cat
                $request_events_by_cat = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_events_by_cat->setViewId(seopress_google_analytics_auth_option());
                $request_events_by_cat->setDateRanges($dateRange);
                $request_events_by_cat->setDimensions([$eventCategory]);
                $request_events_by_cat->setMetrics([$totalEvents]);
                $request_events_by_cat->setSamplingLevel('SMALL');
                $request_events_by_cat->setOrderBys($order_by_events_desc);

                // Event Action
                $request_events_by_action = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_events_by_action->setViewId(seopress_google_analytics_auth_option());
                $request_events_by_action->setDateRanges($dateRange);
                $request_events_by_action->setDimensions([$eventAction]);
                $request_events_by_action->setMetrics([$totalEvents]);
                $request_events_by_action->setSamplingLevel('SMALL');
                $request_events_by_action->setOrderBys($order_by_events_desc);

                // Event Label
                $request_events_by_label = new Google_Service_AnalyticsReporting_ReportRequest();
                $request_events_by_label->setViewId(seopress_google_analytics_auth_option());
                $request_events_by_label->setDateRanges($dateRange);
                $request_events_by_label->setDimensions([$eventLabel]);
                $request_events_by_label->setMetrics([$totalEvents]);
                $request_events_by_label->setSamplingLevel('SMALL');
                $request_events_by_label->setOrderBys($order_by_events_desc);

                //BATCH REPORT
                ////////////////////////////////////////////////////////////////////////////////////////

                function seopress_analytics_get_reports($reports) {
                    $return = [];

                    for ($reportIndex = 0; $reportIndex < count($reports); ++$reportIndex) {
                        $report           = $reports[$reportIndex];
                        $header           = $report->getColumnHeader();
                        $dimensionHeaders = $header->getDimensions();
                        $metricHeaders    = $header->getMetricHeader()->getMetricHeaderEntries();
                        $rows             = $report->getData()->getRows();

                        for ($rowIndex = 0; $rowIndex < count($rows); ++$rowIndex) {
                            $row        = $rows[$rowIndex];
                            $dimensions = $row->getDimensions();
                            $metrics    = $row->getMetrics();
                            for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); ++$i) {
                                $return[$dimensionHeaders[$i]][] = $dimensions[$i];
                            }

                            for ($j = 0; $j < count($metrics); ++$j) {
                                $values = $metrics[$j]->getValues();
                                for ($k = 0; $k < count($values); ++$k) {
                                    $entry                       = $metricHeaders[$k];
                                    $return[$entry->getName()][] = $values[$k];
                                }
                            }
                        }
                    }

                    return $return;
                }

                $all           = [];

                $requests = [
                    $request_by_date,
                    $request_users_by_country,
                    $request_users_by_device_cat,
                    $request_users_by_browser,
                    $request_users_by_social_network,
                    $request_users_by_channel,
                    $request_users_by_source,
                    $request_users_by_ref,
                    $request_page_views_page_title,
                    $request_events_by_cat,
                    $request_events_by_action,
                    $request_events_by_label,
                    $request_users_by_language,
                ];

                foreach ($requests as $key => $request) {
                    $body          = new Google_Service_AnalyticsReporting_GetReportsRequest();
                    $body->setReportRequests([$request]);
                    $body = $service->reports->batchGet($body);

                    $all[$key] = seopress_analytics_get_reports($body);
                }

                ////////////////////////////////////////////////////////////////////////////////////////
                //Saving datas
                ////////////////////////////////////////////////////////////////////////////////////////
                $seopress_results_google_analytics_cache['sessions']                = array_sum($all[0]['sessions']);
                $seopress_results_google_analytics_cache['users']                   = array_sum($all[0]['users']);
                $seopress_results_google_analytics_cache['pageviews']               = array_sum($all[0]['pageviews']);
                $seopress_results_google_analytics_cache['pageviewsPerSession']     = round(array_sum($all[0]['pageviewsPerSession']) / count($all[0]['pageviewsPerSession']), 2);
                $seopress_results_google_analytics_cache['avgSessionDuration']      = gmdate('i:s', array_sum($all[0]['avgSessionDuration']) / count($all[0]['avgSessionDuration']));
                $seopress_results_google_analytics_cache['bounceRate']              = round(array_sum($all[0]['bounceRate']) / count($all[0]['bounceRate']), 2);
                $seopress_results_google_analytics_cache['percentNewSessions']      = round(array_sum($all[0]['percentNewSessions']) / count($all[0]['percentNewSessions']), 2);
                $seopress_results_google_analytics_cache['language']                = $all[12];
                $seopress_results_google_analytics_cache['country']                 = $all[1];
                $seopress_results_google_analytics_cache['deviceCategory']          = $all[2];
                $seopress_results_google_analytics_cache['browser']                 = $all[3];
                $seopress_results_google_analytics_cache['socialNetwork']           = $all[4];
                $seopress_results_google_analytics_cache['channelGrouping']         = $all[5];
                $seopress_results_google_analytics_cache['source']                  = $all[6];
                $seopress_results_google_analytics_cache['fullReferrer']            = $all[7];
                $seopress_results_google_analytics_cache['contentpageviews']        = $all[8];
                $seopress_results_google_analytics_cache['totalEvents']             = $all[0]['ga:totalEvents'];
                $seopress_results_google_analytics_cache['uniqueEvents']            = $all[0]['ga:uniqueEvents'];
                $seopress_results_google_analytics_cache['eventCategory']           = $all[9];
                $seopress_results_google_analytics_cache['eventAction']             = $all[10];
                $seopress_results_google_analytics_cache['eventLabel']              = $all[11];

                switch ($seopress_ga_dashboard_widget_options_type) {
                    case 'ga_sessions':
                        $ga_sessions_rows                           = $all[0]['sessions'];
                        $seopress_ga_dashboard_widget_options_title = __('Sessions', 'wp-seopress-pro');
                        break;
                    case 'ga_users':
                        $ga_sessions_rows                           = $all[0]['users'];
                        $seopress_ga_dashboard_widget_options_title = __('Users', 'wp-seopress-pro');
                        break;
                    case 'ga_pageviews':
                        $ga_sessions_rows                           = $all[0]['pageviews'];
                        $seopress_ga_dashboard_widget_options_title = __('Page Views', 'wp-seopress-pro');
                        break;
                    case 'ga_pageviewsPerSession':
                        $ga_sessions_rows                           = $all[0]['pageviewsPerSession'];
                        $seopress_ga_dashboard_widget_options_title = __('Page Views Per Session', 'wp-seopress-pro');
                        break;
                    case 'ga_avgSessionDuration':
                        $ga_sessions_rows                           = $all[0]['avgSessionDuration'];
                        $seopress_ga_dashboard_widget_options_title = __('Average Session Duration', 'wp-seopress-pro');
                        break;
                    case 'ga_bounceRate':
                        $ga_sessions_rows                           = $all[0]['bounceRate'];
                        $seopress_ga_dashboard_widget_options_title = __('Bounce Rate', 'wp-seopress-pro');
                        break;
                    case 'ga_percentNewSessions':
                        $ga_sessions_rows                           = $all[0]['percentNewSessions'];
                        $seopress_ga_dashboard_widget_options_title = __('New Sessions', 'wp-seopress-pro');
                        break;
                    default:
                        $ga_sessions_rows                           = $all[0]['sessions'];
                        $seopress_ga_dashboard_widget_options_title = __('Sessions', 'wp-seopress-pro');
                }

                function seopress_ga_dashboard_get_sessions_labels($ga_date) {
                    $labels = [];
                    foreach ($ga_date as $key => $value) {
                        array_push($labels, date_i18n(get_option('date_format'), strtotime($value)));
                    }

                    return $labels;
                }

                function seopress_ga_dashboard_get_sessions_data($ga_sessions_rows) {
                    $data = [];
                    foreach ($ga_sessions_rows as $key => $value) {
                        array_push($data, $value);
                    }

                    return $data;
                }
                $ga_date                                                                = $all[0]['ga:date'];
                $seopress_results_google_analytics_cache['sessions_graph_labels']       = seopress_ga_dashboard_get_sessions_labels($ga_date);
                $seopress_results_google_analytics_cache['sessions_graph_data']         = seopress_ga_dashboard_get_sessions_data($ga_sessions_rows);
                $seopress_results_google_analytics_cache['sessions_graph_title']        = $seopress_ga_dashboard_widget_options_title;

                //Transient
                set_transient('seopress_results_google_analytics', $seopress_results_google_analytics_cache, 2 * HOUR_IN_SECONDS);
            }

            //Return
            $seopress_results_google_analytics_transient = get_transient('seopress_results_google_analytics');

            wp_send_json_success($seopress_results_google_analytics_transient);
        }
    }

    exit();
}
add_action('wp_ajax_seopress_request_google_analytics', 'seopress_request_google_analytics');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Clear Google Page Speed cache
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_clear_page_speed_cache() {
    check_ajax_referer('seopress_clear_page_speed_cache_nonce');

    global $wpdb;

    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_seopress_results_page_speed' ");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_seopress_results_page_speed' ");

    exit();
}
add_action('wp_ajax_seopress_clear_page_speed_cache', 'seopress_clear_page_speed_cache');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Save htaccess file
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_save_htaccess() {
    check_ajax_referer('seopress_save_htaccess_nonce');

    if (current_user_can(seopress_capability('manage_options', 'htaccess')) && is_admin()) {
        $filename = get_home_path() . '/.htaccess';

        if (isset($_POST['htaccess_content'])) {
            $current_htaccess = stripslashes($_POST['htaccess_content']);
        }

        if (is_writable($filename)) {
            if ( ! $handle = fopen($filename, 'w')) {
                _e('Impossible to open file: ', 'wp-seopress-pro') . $filename;
                exit;
            }

            if (false === fwrite($handle, $current_htaccess)) {
                _e('Impossible to write in file: ', 'wp-seopress-pro') . $filename;
                exit;
            }

            _e('htaccess successfully updated!', 'wp-seopress-pro');

            fclose($handle);
        } else {
            _e('Your htaccess is not writable.', 'wp-seopress-pro');
        }
    }
}
add_action('wp_ajax_seopress_save_htaccess', 'seopress_save_htaccess');
