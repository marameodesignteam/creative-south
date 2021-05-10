<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

// Can be removed later when there are no more migrations of the required schemas
add_action('seopress_admin_notices', 'seopress_notices_migrate_schema');

/**
 * Information notice when updating manual schema data since 3.9.
 *
 * @since 3.9
 *
 * @return void
 *
 * @author Thomas Deneulin
 */
function seopress_notices_migrate_schema() {
    if ( ! current_user_can(seopress_capability('manage_options', 'notice')) || ! is_seopress_page()) {
        return;
    }

    $total = get_option('_seopress_migrate_schema_total');
    if (false === $total) {
        return;
    }

    if (0 == $total) {
        delete_option('_seopress_migrate_schema');
        delete_option('_seopress_migrate_schema_total');
        delete_option('_seopress_migrate_schema_current');

        return;
    }

    $current = get_option('_seopress_migrate_schema_current');

    if ($total === $current) {
        return;
    }

    $class   = 'notice notice-info';
    $message = '<p><strong>' . __('Optimization of your database in progress!', 'wp-seopress-pro') . '</strong></p>';

    $message .= sprintf('<p>' . __('We migrate a total of %s items.', 'wp-seopress-pro') . '</p>', get_option('_seopress_migrate_schema_total'));

    if (false !== $current) {
        // Prevent double process
        if ($current > $total) {
            $current = $total;
            delete_option('_seopress_migrate_schema');
            delete_option('_seopress_migrate_schema_total');
            delete_option('_seopress_migrate_schema_current');
            update_option('_seopress_can_clean_migrate_schema', 1);
        }
        $message .= sprintf('<p>' . __('We are currently dealing with the <strong>%s element</strong>', 'wp-seopress-pro') . '</p>', $current);
    }

    printf('<div class="%1$s">%2$s</div>', esc_attr($class), $message);
}

// Can be removed later when there are no more migrations of the required schemas
add_action('seopress_admin_notices', 'seopress_notice_finish_clean_database');

/**
 * Information notice for deleting old data from diagrams before 3.9.
 *
 * @since 3.9
 *
 * @return void
 *
 * @author Thomas Deneulin
 */
function seopress_notice_finish_clean_database() {
    if ( ! current_user_can(seopress_capability('manage_options', 'notice')) || ! is_seopress_page()) {
        return;
    }

    $current = get_option('_seopress_migrate_schema_current');
    $total   = get_option('_seopress_migrate_schema_total');

    if (false !== get_option('_seopress_can_clean_migrate_schema') || (false !== $current && $current >= $total)) {
        $class   = 'notice notice-info';
        $message = '<p><strong>' . __('We have finished optimizing your database.', 'wp-seopress-pro') . '</strong></p>';
        $message .= sprintf('<p>' . __('As a security measure, we still have your old data so that we can go back if you find an error.', 'wp-seopress-pro') . '</p>', get_option('_seopress_migrate_schema_total'));
        $message .= sprintf('<p>' . __('In which case, you can also delete this old information by clicking on the following button to complete the optimization.', 'wp-seopress-pro') . '</p>');

        $message .= sprintf('<p><a href="%s" class="button button-primary">' . __('Clean database', 'wp-seopress-pro') . '</a></p>', wp_nonce_url(
            add_query_arg(
            [
                'action' => 'clean_old_schema_manual',
            ],
            admin_url('admin-post.php')
        ),
            'clean_old_schema_manual'
        ));
        $message .= sprintf('<a href="%s" class="notice-dismiss" style="text-decoration:none;"><span class="screen-reader-text">' . __('Dismiss this notice', 'wp-seopress-pro') . '</span></a>', wp_nonce_url(
            add_query_arg(
            [
                'action' => 'seopress_dismiss_clean_migrate_notice',
            ],
            admin_url('admin-post.php')
        ),
            'seopress_dismiss_clean_migrate_notice'
        ));

        printf('<div class="%1$s" style="position:relative;">%2$s</div>', esc_attr($class), $message);
    }
}

add_action('admin_post_clean_old_schema_manual', 'seopress_clean_old_schema_manual');

/**
 * Action to start the process in the background that deletes the old data.
 *
 * @since 3.9
 *
 * @return void
 *
 * @author Thomas Deneulin
 */
function seopress_clean_old_schema_manual() {
    if ( ! wp_verify_nonce($_GET['_wpnonce'], 'clean_old_schema_manual')) {
        wp_redirect(admin_url('admin.php?page=seopress-option'));
        exit;
    }

    if ( ! current_user_can(seopress_capability('manage_options', 'notice'))) {
        wp_redirect(admin_url('admin.php?page=seopress-option'));
        exit;
    }

    global $background_process_clean_old_schema;

    if ( ! $background_process_clean_old_schema) {
        return;
    }

    $background_process_clean_old_schema->push_to_queue(['site_id' => get_current_blog_id()]);
    $background_process_clean_old_schema->save()->dispatch();
    delete_option('_seopress_can_clean_migrate_schema');

    wp_redirect(admin_url('admin.php?page=seopress-option'));
}

add_action('admin_post_seopress_relaunch_upgrader', 'seopress_seopress_relaunch_upgrader');

/**
 * Reload upgrader.
 *
 * @since 3.9
 *
 * @return void
 *
 * @author Thomas Deneulin
 */
function seopress_seopress_relaunch_upgrader() {
    if ( ! wp_verify_nonce($_GET['_wpnonce'], 'seopress_relaunch_upgrader')) {
        wp_redirect(admin_url('admin.php?page=seopress-option'));
        exit;
    }

    if ( ! current_user_can(seopress_capability('manage_options', 'notice'))) {
        wp_redirect(admin_url('admin.php?page=seopress-option'));
        exit;
    }

    if (is_multisite()) {
        $totalSites = \get_sites(['count'=>true, 'public' => true]);
        $sites      = get_sites(['public'=>true, 'number'=>$totalSites]);
        foreach ($sites as $site) {
            delete_blog_option($site->blog_id, '_seopress_migrate_schema_current');
            delete_blog_option($site->blog_id, '_seopress_migrate_schema_total');

            $seopress_versions        = get_blog_option($site->blog_id, 'seopress_versions');
            $seopress_versions['pro'] = '3.8.9';

            update_blog_option($site->blog_id, 'seopress_versions', $options);
        }
    } else {
        $seopress_versions        = get_option('seopress_versions');
        $seopress_versions['pro'] = '3.8.9';

        delete_option('_seopress_migrate_schema_current');
        delete_option('_seopress_migrate_schema_total');

        update_option('seopress_versions', $seopress_versions);
    }

    wp_redirect(admin_url('admin.php?page=seopress-option'));
}

add_action('admin_post_seopress_dismiss_clean_migrate_notice', 'seopress_dismiss_clean_migrate_notice');

/**
 * Deleting the record to clean the data.
 *
 * @since 3.9
 *
 * @return void
 *
 * @author Thomas Deneulin
 */
function seopress_dismiss_clean_migrate_notice() {
    if ( ! wp_verify_nonce($_GET['_wpnonce'], 'seopress_dismiss_clean_migrate_notice')) {
        wp_redirect(admin_url('admin.php?page=seopress-option'));
        exit;
    }

    if ( ! current_user_can(seopress_capability('manage_options', 'notice'))) {
        wp_redirect(admin_url('admin.php?page=seopress-option'));
        exit;
    }

    delete_option('_seopress_can_clean_migrate_schema');
    delete_option('_seopress_migrate_schema_total');
    delete_option('_seopress_migrate_schema_current');

    wp_redirect(admin_url('admin.php?page=seopress-option'));
}

/**
 * Retrieves all the IDs of posts that need to migrate their schema manually.
 *
 * @since 3.9
 *
 * @return array
 *
 * @author Thomas Deneulin
 *
 * @param mixed      $offset
 * @param mixed|null $siteId
 */
function seopress_get_post_ids_need_to_migrate($offset, $siteId = null) {
    $limit_input = (int) ini_get('max_input_vars');

    if (null !== $siteId) {
        switch_to_blog($siteId);
    }

    global $wpdb;

    $query = 'SELECT DISTINCT pm.post_id ';
    $query .= "FROM {$wpdb->postmeta} pm ";
    $query .= "INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id ";
    $query .= 'WHERE 1=1 ';
    $query .= "AND p.post_type != 'seopress_schemas' ";
    $query .= "AND pm.meta_key LIKE '_seopress_pro_rich_snippets%' ";
    $query .= "LIMIT {$offset}, {$limit_input} ";

    $rows = $wpdb->get_results($query, ARRAY_A);
    if (empty($rows)) {
        return [];
    }

    return $rows;
}

/**
 * Manual schema migration function for a given post ID.
 *
 * @since 3.9
 *
 * @return bool
 *
 * @author Thomas Deneulin
 *
 * @param mixed $post_id
 */
function seopress_migrate_schema_by_post_id($post_id) {
    $new_meta_key = '_seopress_pro_schemas_manual';
    global $wpdb;

    $query = 'SELECT * ';
    $query .= "FROM {$wpdb->postmeta} p ";
    $query .= 'WHERE 1=1 ';
    $query .= "AND p.post_id = '" . $post_id . "' AND p.meta_key = '_seopress_pro_schemas_manual'";

    $new_meta_key_already_exist = $wpdb->get_results($query, ARRAY_A);

    if ( ! empty($new_meta_key_already_exist)) {
        return true;
    }

    $query = 'SELECT pm.meta_key, pm.meta_value ';
    $query .= "FROM {$wpdb->postmeta} pm ";
    $query .= 'WHERE 1=1 ';
    $query .= "AND pm.meta_key LIKE '_seopress_pro_rich_snippets%' ";
    $query .= "AND pm.post_id = '" . $post_id . "' ";

    $rows = $wpdb->get_results($query, ARRAY_A);

    if (empty($rows)) {
        return true;
    }

    $meta_value_migrate = [];
    foreach ($rows as $key => $row) {
        $meta_value_migrate[$row['meta_key']] = maybe_unserialize($row['meta_value']);
    }

    update_post_meta($post_id, $new_meta_key, $meta_value_migrate);

    return true;
}
