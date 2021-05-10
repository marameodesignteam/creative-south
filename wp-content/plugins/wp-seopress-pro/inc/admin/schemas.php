<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * Return the conditions for schemas.
 *
 * @since 3.8.1
 *
 * @author Julio Potier
 *
 * @return (array)
 **/
function seopress_get_schemas_conditions() {
    return ['equal' => __('is equal to', 'wp-seopress-pro'), 'not_equal' => __('is NOT equal to', 'wp-seopress-pro')];
}

/**
 * Return the filters for schemas.
 *
 * @since 3.8.1
 *
 * @author Julio Potier
 *
 * @return (array)
 **/
function seopress_get_schemas_filters() {
    return ['post_type' => __('Post Type', 'wp-seopress-pro'), 'taxonomy'  => __('Taxonomy', 'wp-seopress-pro')];
}

/**
 * Return default values for retrocompat.
 *
 * @since 3.8.1
 *
 * @author Julio Potier
 *
 * @return (array)
 *
 * @param mixed $rule
 **/
function seopress_get_default_schemas_rules($rule) {
    return [[['filter' => 'post_type', 'cpt' => $rule, 'taxo' => 0, 'cond' => 'equal']]];
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Register SEOPress Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_schemas_fn() {
    $labels = [
        'name'                  => _x('Schemas', 'Post Type General Name', 'wp-seopress-pro'),
        'singular_name'         => _x('Schema', 'Post Type Singular Name', 'wp-seopress-pro'),
        'menu_name'             => __('Schemas', 'wp-seopress-pro'),
        'name_admin_bar'        => __('Schemas', 'wp-seopress-pro'),
        'archives'              => __('Item Archives', 'wp-seopress-pro'),
        'parent_item_colon'     => __('Parent Item:', 'wp-seopress-pro'),
        'all_items'             => __('All schemas', 'wp-seopress-pro'),
        'add_new_item'          => __('Add New schema', 'wp-seopress-pro'),
        'add_new'               => __('Add schema', 'wp-seopress-pro'),
        'new_item'              => __('New schema', 'wp-seopress-pro'),
        'edit_item'             => __('Edit schema', 'wp-seopress-pro'),
        'update_item'           => __('Update schema', 'wp-seopress-pro'),
        'view_item'             => __('View schema', 'wp-seopress-pro'),
        'search_items'          => __('Search schema', 'wp-seopress-pro'),
        'not_found'             => __('Not found', 'wp-seopress-pro'),
        'not_found_in_trash'    => __('Not found in Trash', 'wp-seopress-pro'),
        'featured_image'        => __('Featured Image', 'wp-seopress-pro'),
        'set_featured_image'    => __('Set featured image', 'wp-seopress-pro'),
        'remove_featured_image' => __('Remove featured image', 'wp-seopress-pro'),
        'use_featured_image'    => __('Use as featured image', 'wp-seopress-pro'),
        'insert_into_item'      => __('Insert into item', 'wp-seopress-pro'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'wp-seopress-pro'),
        'items_list'            => __('Schemas list', 'wp-seopress-pro'),
        'items_list_navigation' => __('Schemas list navigation', 'wp-seopress-pro'),
        'filter_items_list'     => __('Filter schema list', 'wp-seopress-pro'),
    ];
    $args = [
        'label'                 => __('Schemas', 'wp-seopress-pro'),
        'description'           => __('List of Schemas', 'wp-seopress-pro'),
        'labels'                => $labels,
        'supports'              => ['title'],
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'menu_icon'             => 'dashicons-excerpt-view',
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'schema',
        'capabilities'          => [
            'edit_post'              => 'edit_schema',
            'edit_posts'             => 'edit_schemas',
            'edit_others_posts'      => 'edit_others_schemas',
            'publish_posts'          => 'publish_schemas',
            'read_post'              => 'read_schema',
            'read_private_posts'     => 'read_private_schemas',
            'delete_post'            => 'delete_schema',
            'delete_others_posts'    => 'delete_others_schemas',
            'delete_published_posts' => 'delete_published_schemas',
        ],
    ];
    register_post_type('seopress_schemas', $args);
}
add_action('admin_init', 'seopress_schemas_fn', 10);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Map SEOPress Schema caps
///////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('map_meta_cap', 'seopress_schemas_map_meta_cap', 10, 4);
function seopress_schemas_map_meta_cap($caps, $cap, $user_id, $args) {
    /* If editing, deleting, or reading a schema, get the post and post type object. */
    if ('edit_schema' === $cap || 'delete_schema' === $cap || 'read_schema' === $cap) {
        $post      = get_post($args[0]);
        $post_type = get_post_type_object($post->post_type);

        /* Set an empty array for the caps. */
        $caps = [];
    }

    /* If editing a schema, assign the required capability. */
    if ('edit_schema' === $cap) {
        if ($user_id == $post->post_author) {
            $caps[] = $post_type->cap->edit_posts;
        } else {
            $caps[] = $post_type->cap->edit_others_posts;
        }
    }

    /* If deleting a schema, assign the required capability. */
    elseif ('delete_schema' === $cap) {
        if ($user_id == $post->post_author) {
            $caps[] = $post_type->cap->delete_published_posts;
        } else {
            $caps[] = $post_type->cap->delete_others_posts;
        }
    }

    /* If reading a private schema, assign the required capability. */
    elseif ('read_schema' === $cap) {
        if ('private' !== $post->post_status) {
            $caps[] = 'read';
        } elseif ($user_id == $post->post_author) {
            $caps[] = 'read';
        } else {
            $caps[] = $post_type->cap->read_private_posts;
        }
    }

    /* Return the capabilities required by the user. */
    return $caps;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Set title placeholder for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_schemas_cpt_title($title) {
    $screen = get_current_screen();
    if ('seopress_schemas' == $screen->post_type) {
        $title = __('Enter the name of your schema', 'wp-seopress-pro');
    }

    return $title;
}

add_filter('enter_title_here', 'seopress_schemas_cpt_title');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Add buttons to post type list if empty
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('manage_posts_extra_tablenav', 'seopress_schemas_maybe_render_blank_state');

function seopress_schemas_render_blank_state() {
    echo '<div class="seopress-BlankState">';

    echo '<h2 class="seopress-BlankState-message">' . esc_html__('Boost your visibility in search results and increase your traffic and conversions.', 'wp-seopress-pro') . '</h2>';

    echo '<div class="seopress-BlankState-buttons">';

    echo '<a class="seopress-BlankState-cta button-primary button" href="' . esc_url(admin_url('post-new.php?post_type=seopress_schemas')) . '">' . esc_html__('Create a schema', 'wp-seopress-pro') . '</a>';

    if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
        $seopress_docs_link['support']['schemas_cpt'] = 'https://www.seopress.org/fr/blog/comment-utiliser-les-schemas-dans-votre-site-wordpress-avec-seopress-pro-1/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
    } else {
        $seopress_docs_link['support']['schemas_cpt'] = 'https://www.seopress.org/blog/how-to-add-schema-to-wordpress-with-seopress-1/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
    }

    echo '<a class="seopress-BlankState-cta button" href="' . esc_url($seopress_docs_link['support']['schemas_cpt']) . '" target="_blank">' . esc_html__('Learn more about schemas', 'wp-seopress-pro') . '</a>';

    echo '</div>';

    echo '</div>';
}
function seopress_schemas_maybe_render_blank_state($which) {
    global $post_type;

    if ('seopress_schemas' === $post_type && 'bottom' === $which) {
        $counts = (array) wp_count_posts($post_type);
        unset($counts['auto-draft']);
        $count = array_sum($counts);

        if (isset($_GET['seopress_support']) && '1' === $_GET['seopress_support']) {
            ?>
			<a href="<?php
                echo wp_nonce_url(
                    add_query_arg(
                    [
                        'action' => 'seopress_relaunch_upgrader',
                    ],
                    admin_url('admin-post.php')
                ),
                    'seopress_relaunch_upgrader'
                ); ?>" class="btn btn-primary">
				Reload upgrader schema
			</a>
			<?php
        }

        if (0 < $count) {
            return;
        }

        seopress_schemas_render_blank_state();

        echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom { height: auto; } </style>';
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Set messages for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////

function seopress_schemas_set_messages($messages) {
    global $post, $post_ID, $typenow;
    $post_type = 'seopress_schemas';

    if ('seopress_schemas' === $typenow) {
        $obj      = get_post_type_object($post_type);
        $singular = $obj->labels->singular_name;

        $messages[$post_type] = [
            0  => '', // Unused. Messages start at index 1.
            1  => __($singular . ' updated.'),
            2  => __('Custom field updated.'),
            3  => __('Custom field deleted.'),
            4  => __($singular . ' updated.'),
            5  => isset($_GET['revision']) ? sprintf(__($singular . ' restored to revision from %s'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6  => __($singular . ' published.'),
            7  => __('Schema saved.'),
            8  => sprintf(__($singular . ' submitted.'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
            9  => sprintf(__($singular . ' scheduled for: <strong>%1$s</strong>. '), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
            10 => sprintf(__($singular . ' draft updated.'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
        ];

        return $messages;
    } else {
        return $messages;
    }
}

add_filter('post_updated_messages', 'seopress_schemas_set_messages');

function seopress_schemas_set_messages_list($bulk_messages, $bulk_counts) {
    $bulk_messages['seopress_schemas'] = [
        'updated'   => _n('%s schema updated.', '%s schemas updated.', $bulk_counts['updated']),
        'locked'    => _n('%s schema not updated, somebody is editing it.', '%s schemas not updated, somebody is editing them.', $bulk_counts['locked']),
        'deleted'   => _n('%s schema permanently deleted.', '%s schemas permanently deleted.', $bulk_counts['deleted']),
        'trashed'   => _n('%s schema moved to the Trash.', '%s schemas moved to the Trash.', $bulk_counts['trashed']),
        'untrashed' => _n('%s schema restored from the Trash.', '%s schemas restored from the Trash.', $bulk_counts['untrashed']),
    ];

    return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'seopress_schemas_set_messages_list', 10, 2);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Columns for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////

add_filter('manage_edit-seopress_schemas_columns', 'seopress_schemas_columns');
add_action('manage_seopress_schemas_posts_custom_column', 'seopress_schemas_display_column', 10, 2);

function seopress_schemas_columns($columns) {
    $columns['seopress_schemas_type'] = __('Data type', 'wp-seopress-pro');
    $columns['seopress_schemas_cpt']  = __('Post type', 'wp-seopress-pro');
    unset($columns['date']);

    return $columns;
}

function seopress_schemas_display_column($column, $post_id) {
    if ('seopress_schemas_type' == $column) {
        if (get_post_meta($post_id, '_seopress_pro_rich_snippets_type', true)) {
            echo get_post_meta($post_id, '_seopress_pro_rich_snippets_type', true);
        }
    }
    if ('seopress_schemas_cpt' == $column) {
        if (get_post_meta($post_id, '_seopress_pro_rich_snippets_rules', true)) {
            $rules = get_post_meta($post_id, '_seopress_pro_rich_snippets_rules', true);
            if ( ! is_array($rules)) {
                $rules = seopress_get_default_schemas_rules($rules);
            }
            $conditions = seopress_get_schemas_conditions();
            $filters    = seopress_get_schemas_filters();
            $n          = 0;
            $html       = '';
            foreach ($rules as $or => $values) {
                foreach ($values as $and => $value) {
                    $filter = esc_html($filters[$value['filter']]);
                    $cond   = $conditions[$value['cond']];
                    if ('post_type' === $value['filter'] && post_type_exists($value['cpt'])) {
                        $label = esc_html(get_post_type_object($value['cpt'])->label);
                        $html .= " <strong>$filter</strong> <em>$cond</em> \"$label\" ";
                    } elseif ('taxonomy' === $value['filter'] && term_exists((int) $value['taxo'])) {
                        $tax   = esc_html(get_taxonomy(get_term($value['taxo'])->taxonomy)->label);
                        $label = esc_html(get_term($value['taxo'])->name);
                        $html .= " <strong>$filter</strong> \"$tax\" <em>$cond</em> \"$label\" ";
                    }
                    $html .= __('and', 'wp-seopress-pro');
                    ++$n;
                    if (3 === $n) {
                        $html = trim($html, __('and', 'wp-seopress-pro') . ' ');
                        $html .= '&hellip;';
                        continue 2;
                    }
                }
                $html = trim($html, __('and', 'wp-seopress-pro'));
                $html .= __('or', 'wp-seopress-pro');
            }
            $html = trim($html, __('or', 'wp-seopress-pro'));
            echo $html;
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display metabox for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('add_meta_boxes', 'seopress_schemas_init_metabox');
function seopress_schemas_init_metabox() {
    add_meta_box('seopress_schemas', __('Your schema', 'wp-seopress-pro'), 'seopress_schemas_cpt', 'seopress_schemas', 'normal', 'default');
}

function seopress_schemas_cpt($post) {
    $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

    wp_nonce_field(plugin_basename(__FILE__), 'seopress_schemas_cpt_nonce');

    global $typenow;

    //Enqueue scripts
    wp_enqueue_script('jquery-ui-accordion');

    wp_enqueue_script('seopress-pro-media-uploader-js', plugins_url('assets/js/seopress-pro-media-uploader.js', dirname(dirname(__FILE__))), ['jquery'], SEOPRESS_PRO_VERSION, false);

    wp_enqueue_script('seopress-pro-rich-snippets-js', plugins_url('assets/js/seopress-pro-rich-snippets' . $prefix . '.js', dirname(dirname(__FILE__))), ['jquery'], SEOPRESS_PRO_VERSION, false);

    wp_enqueue_media();

    wp_enqueue_script('jquery-ui-datepicker');

    //Post types
    if (function_exists('seopress_get_post_types')) {
        $seopress_get_post_types = seopress_get_post_types();
    }

    //Filter taxonomies list to get WC product attributes
    add_filter('seopress_get_taxonomies_args', 'sp_get_taxonomies_args');
    function sp_get_taxonomies_args($args) {
        $args = [];

        return $args;
    }
    add_filter('seopress_get_taxonomies_list', 'sp_get_taxonomies_list');
    function sp_get_taxonomies_list($terms) {
        unset($terms['seopress_404_cat']);
        unset($terms['nav_menu']);
        unset($terms['link_category']);
        unset($terms['post_format']);

        return $terms;
    }

    //Mapping fields
    function seopress_schemas_mapping_array($post_meta_name, $cases) {
        global $post;

        //Custom fields
        if (function_exists('seopress_get_custom_fields')) {
            $seopress_get_custom_fields = seopress_get_custom_fields();
        }

        //init default case array
        $seopress_schemas_mapping_case = [
            'Select an option' => ['none' => __('None', 'wp-seopress-pro')],
            'Site Meta'        => [
                'site_title' => __('Site Title', 'wp-seopress-pro'),
                'tagline'    => __('Tagline', 'wp-seopress-pro'),
                'site_url'   => __('Site URL', 'wp-seopress-pro'),
            ],
            'Post Meta' => [
                'post_id'          => __('Post / Product ID', 'wp-seopress-pro'),
                'post_title'       => __('Post Title / Product title', 'wp-seopress-pro'),
                'post_excerpt'     => __('Excerpt / Product short description', 'wp-seopress-pro'),
                'post_content'     => __('Content', 'wp-seopress-pro'),
                'post_permalink'   => __('Permalink', 'wp-seopress-pro'),
                'post_author_name' => __('Author', 'wp-seopress-pro'),
                'post_date'        => __('Publish date', 'wp-seopress-pro'),
                'post_updated'     => __('Last update', 'wp-seopress-pro'),
            ],
            'Product meta (WooCommerce)' => [
                'product_regular_price'     => __('Regular Price', 'wp-seopress-pro'),
                'product_sale_price'        => __('Sale Price', 'wp-seopress-pro'),
                'product_price_with_tax'    => __('Sales price, including tax', 'wp-seopress-pro'),
                'product_date_from'         => __('Sale price dates "From"', 'wp-seopress-pro'),
                'product_date_to'           => __('Sale price dates "To"', 'wp-seopress-pro'),
                'product_sku'               => __('SKU', 'wp-seopress-pro'),
                'product_barcode_type'      => __('Product Global Identifier type', 'wp-seopress-pro'),
                'product_barcode'           => __('Product Global Identifier', 'wp-seopress-pro'),
                'product_category'          => __('Product category', 'wp-seopress-pro'),
                'product_stock'             => __('Product availability', 'wp-seopress-pro'),
            ],
            'Custom taxonomy / Product attribute (WooCommerce)' => [
                'custom_taxonomy' => __('Select your custom taxonomy / product attribute', 'wp-seopress-pro'),
            ],
            'Custom fields' => [
                'custom_fields' => __('Select your custom field', 'wp-seopress-pro'),
            ],
        ];

        //Custom field
        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_cf', true);

        $seopress_schemas_cf = '<select name="' . $post_meta_name . '_cf" class="cf">';

        foreach ($seopress_get_custom_fields as $value) {
            $seopress_schemas_cf .= '<option ' . selected($value, $post_meta_value, false) . ' value="' . $value . '">' . $value . '</option>';
        }

        $seopress_schemas_cf .= '</select>';

        //Custom taxonomy
        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_tax', true);

        if (function_exists('seopress_get_taxonomies')) {
            $seopress_schemas_tax = '<select name="' . $post_meta_name . '_tax" class="tax">';

            $seopress_get_taxonomies = seopress_get_taxonomies();

            foreach ($seopress_get_taxonomies as $key => $value) {
                $seopress_schemas_tax .= '<option ' . selected($key, $post_meta_value, false) . ' value="' . $key . '">' . $key . '</option>';
            }
            $seopress_schemas_tax .= '</select>';
        }

        if (is_string($cases)) {
            $cases = [$cases];
        }

        foreach ($cases as $case) {
            //LB types list
            if ('lb' === $case) {
                $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_lb', true);

                $seopress_schemas_lb = '<select name="' . $post_meta_name . '_lb" class="lb">';

                foreach (seopress_lb_types_list() as $type_value => $type_i18n) {
                    $seopress_schemas_lb .= '<option ' . selected($type_value, $post_meta_value, false) . ' value="' . $type_value . '">' . __($type_i18n, 'wp-seopress-pro') . '</option>';
                }
                $seopress_schemas_lb .= '</select>';
            }

            switch ($case) {
                case 'default':
                    $seopress_schemas_mapping_case['Manual'] = [
                        'manual_global' => __('Manual text', 'wp-seopress-pro'),
                        'manual_single' => __('Manual text on each post', 'wp-seopress-pro'),
                    ];

                    $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_global', true);

                    $seopress_schemas_manual_global = '<input type="text" id="' . $post_meta_name . '_manual_global" name="' . $post_meta_name . '_manual_global" class="manual_global" placeholder="' . esc_html__('Enter a global value here', 'wp-seopress-pro') . '" aria-label="' . __('Manual value', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';

                    break;
                case 'lb':
                    $seopress_schemas_mapping_case['Manual'] = [
                        'manual_global' => __('Manual text', 'wp-seopress-pro'),
                        'manual_single' => __('Manual text on each post', 'wp-seopress-pro'),
                    ];

                    $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_global', true);

                    $seopress_schemas_manual_global = '<input type="text" id="' . $post_meta_name . '_manual_global" name="' . $post_meta_name . '_manual_global" class="manual_global" placeholder="' . esc_html__('Enter a global value here', 'wp-seopress-pro') . '" aria-label="' . __('Manual value', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';

                    //lb types case
                    $seopress_schemas_mapping_case['Local Business'] = [
                        'manual_lb_global' => __('Local Business type', 'wp-seopress-pro'),
                    ];

                    $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_lb_global', true);

                    break;
                case 'image':
                        $seopress_schemas_mapping_case = [
                            'Select an option' => ['none' => __('None', 'wp-seopress-pro')],
                            'Site Meta'        => [
                                'knowledge_graph_logo' => __('Knowledge Graph logo (SEO > Social)', 'wp-seopress-pro'),
                            ],
                            'Post Meta' => [
                                'post_thumbnail'      => __('Featured image / Product image', 'wp-seopress-pro'),
                                'post_author_picture' => __('Post author picture', 'wp-seopress-pro'),
                            ],
                            'Custom fields' => [
                                'custom_fields' => __('Select your custom field', 'wp-seopress-pro'),
                            ],
                            'Manual' => [
                                'manual_img_global'         => __('Manual Image URL', 'wp-seopress-pro'),
                                'manual_img_library_global' => __('Manual Image from Library', 'wp-seopress-pro'),
                                'manual_img_single'         => __('Manual text on each post', 'wp-seopress-pro'),
                            ],
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_global', true);

                        $seopress_schemas_manual_img_global = '<input type="text" id="' . $post_meta_name . '_manual_img_global" name="' . $post_meta_name . '_manual_img_global" class="manual_img_global" placeholder="' . esc_html__('Enter a global value here', 'wp-seopress-pro') . '" aria-label="' . __('Manual Image URL', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';

                        $post_meta_value  = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_library_global', true);
                        $post_meta_value2 = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_library_global_width', true);
                        $post_meta_value3 = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_library_global_height', true);

                        $seopress_schemas_manual_img_library_global = '<input type="text" id="' . $post_meta_name . '_manual_img_library_global" name="' . $post_meta_name . '_manual_img_library_global" class="manual_img_library_global" placeholder="' . esc_html__('Select your global image from the media library', 'wp-seopress-pro') . '" aria-label="' . __('Manual Image URL', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />

						<input id="' . $post_meta_name . '_manual_img_library_global_width" type="hidden" name="' . $post_meta_name . '_manual_img_library_global_width" class="manual_img_library_global_width" value="' . $post_meta_value2 . '" />

						<input id="' . $post_meta_name . '_manual_img_library_global_height" type="hidden" name="' . $post_meta_name . '_manual_img_library_global_height" class="manual_img_library_global_height" value="' . $post_meta_value3 . '" />

						<input id="' . $post_meta_name . '_manual_img_library_global_btn" class="button manual_img_library_global" type="button" value="' . __('Upload an Image', 'wp-seopress-pro') . '" />';

                    break;
                case 'events':
                        //Events Calendar
                        if (is_plugin_active('the-events-calendar/the-events-calendar.php')) {
                            $seopress_schemas_mapping_case['Events Calendar'] = [
                                'events_start_date'             => __('Start date', 'wp-seopress-pro'),
                                'events_start_time'             => __('Start time', 'wp-seopress-pro'),
                                'events_end_date'               => __('End date', 'wp-seopress-pro'),
                                'events_end_time'               => __('End time', 'wp-seopress-pro'),
                                'events_location_name'          => __('Event location name', 'wp-seopress-pro'),
                                'events_location_address'       => __('Event location address', 'wp-seopress-pro'),
                                'events_website'                => __('Event website', 'wp-seopress-pro'),
                                'events_cost'                   => __('Event cost', 'wp-seopress-pro'),
                                'events_currency'               => __('Event currency', 'wp-seopress-pro'),
                            ];
                        }

                    break;
                case 'date':
                        //date case
                        $seopress_schemas_mapping_case['Manual'] = [
                            'manual_date_global' => __('Manual date', 'wp-seopress-pro'),
                            'manual_date_single' => __('Manual date on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_date_global', true);

                        $seopress_schemas_manual_date_global = '<input type="text" class="seopress-date-picker manual_date_global" autocomplete="false" name="' . $post_meta_name . '_manual_date_global" class="manual_global" placeholder="' . esc_html__('Eg: YYYY-MM-DD', 'wp-seopress-pro') . '" aria-label="' . __('Global date', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';
                    break;
                case 'time':
                        //time case
                        $seopress_schemas_mapping_case['Manual'] = [
                            'manual_time_global' => __('Manual time', 'wp-seopress-pro'),
                            'manual_time_single' => __('Manual time on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_time_global', true);

                        $seopress_schemas_manual_time_global = '<input type="time" step="2" placeholder="' . __('HH:MM', 'wp-seopress-pro') . '" id="' . $post_meta_name . '_manual_time_global" name="' . $post_meta_name . '_manual_time_global" class="manual_time_global" aria-label="' . __('Time', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';
                    break;
                case 'rating':
                        //rating case
                        $seopress_schemas_mapping_case['Manual'] = [
                            'manual_rating_global' => __('Manual rating', 'wp-seopress-pro'),
                            'manual_rating_single' => __('Manual rating on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_rating_global', true);

                        $seopress_schemas_manual_rating_global = '<input type="number" id="' . $post_meta_name . '_manual_rating_global" name="' . $post_meta_name . '_manual_rating_global" min="0" max="5" step="0.1" class="manual_rating_global" aria-label="' . __('Rating', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';
                    break;
                case 'custom':
                        //custom case
                        $seopress_schemas_mapping_case           = [];
                        $seopress_schemas_mapping_case['custom'] = [
                            'manual_custom_global' => __('Manual custom schema', 'wp-seopress-pro'),
                            'manual_custom_single' => __('Manual custom schema on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_custom_global', true);

                        $seopress_schemas_manual_custom_global = '<textarea rows="25" id="' . $post_meta_name . '_manual_custom_global" name="' . $post_meta_name . '_manual_custom_global" class="manual_custom_global" aria-label="' . __('Custom schema', 'wp-seopress-pro') . '" value="' . htmlspecialchars($post_meta_value) . '">' . htmlspecialchars($post_meta_value) . '</textarea>';
                    break;
            }
        }

        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name, true);

        $html = '<select name="' . $post_meta_name . '" class="dyn">';
        foreach ($seopress_schemas_mapping_case as $key => $value) {
            $html .= '<optgroup label="' . $key . '">';
            foreach ($value as $_key => $_value) {
                $html .= '<option ' . selected($_key, $post_meta_value, false) . ' value="' . $_key . '">' . __($_value, 'wp-seopress-pro') . '</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= '</select>';

        if (isset($seopress_schemas_manual_global)) {
            $html .= $seopress_schemas_manual_global;
        }
        if (isset($seopress_schemas_manual_img_global)) {
            $html .= $seopress_schemas_manual_img_global;
        }
        if (isset($seopress_schemas_manual_img_library_global)) {
            $html .= $seopress_schemas_manual_img_library_global;
        }
        if (isset($seopress_schemas_manual_date_global)) {
            $html .= $seopress_schemas_manual_date_global;
        }
        if (isset($seopress_schemas_manual_time_global)) {
            $html .= $seopress_schemas_manual_time_global;
        }
        if (isset($seopress_schemas_manual_rating_global)) {
            $html .= $seopress_schemas_manual_rating_global;
        }
        if (isset($seopress_schemas_cf) && 'custom' != $case) {
            $html .= $seopress_schemas_cf;
        }
        if (isset($seopress_schemas_tax) && 'custom' != $case) {
            $html .= $seopress_schemas_tax;
        }
        if (isset($seopress_schemas_lb) && 'custom' != $case) {
            $html .= $seopress_schemas_lb;
        }
        if (isset($seopress_schemas_manual_custom_global)) {
            $html .= $seopress_schemas_manual_custom_global;
        }

        return $html;
    }

    //Get datas
    $seopress_pro_rich_snippets_type = get_post_meta($post->ID, '_seopress_pro_rich_snippets_type', true);

    //Article
    $seopress_pro_rich_snippets_article_type = get_post_meta($post->ID, '_seopress_pro_rich_snippets_article_type', true);

    //Local Business
    $seopress_pro_rich_snippets_lb_opening_hours = get_post_meta($post->ID, '_seopress_pro_rich_snippets_lb_opening_hours', false);

    echo '<tr id="term-seopress" class="form-field">
			<td>
				<div id="seopress_pro_cpt" class="seopress-your-schema">
					<div class="inside">
						<div id="seopress-your-schema">
							<div class="box-left">
								<div class="wrap-rich-snippets-type schema-steps">
									<label for="seopress_pro_rich_snippets_type_meta">' . __('Select your data type:', 'wp-seopress-pro') . '</label>
									<select id="seopress_pro_rich_snippets_type" name="seopress_pro_rich_snippets_type">
										<option ' . selected('none', $seopress_pro_rich_snippets_type, false) . ' value="none">' . __('None', 'wp-seopress-pro') . '</option>
										<option ' . selected('articles', $seopress_pro_rich_snippets_type, false) . ' value="articles">' . __('Article (WebPage)', 'wp-seopress-pro') . '</option>
										<option ' . selected('localbusiness', $seopress_pro_rich_snippets_type, false) . ' value="localbusiness">' . __('Local Business', 'wp-seopress-pro') . '</option>
										<option ' . selected('faq', $seopress_pro_rich_snippets_type, false) . ' value="faq">' . __('FAQ', 'wp-seopress-pro') . '</option>
										<option ' . selected('courses', $seopress_pro_rich_snippets_type, false) . ' value="courses">' . __('Course', 'wp-seopress-pro') . '</option>
										<option ' . selected('recipes', $seopress_pro_rich_snippets_type, false) . ' value="recipes">' . __('Recipe', 'wp-seopress-pro') . '</option>
										<option ' . selected('jobs', $seopress_pro_rich_snippets_type, false) . ' value="jobs">' . __('Job', 'wp-seopress-pro') . '</option>
										<option ' . selected('videos', $seopress_pro_rich_snippets_type, false) . ' value="videos">' . __('Video', 'wp-seopress-pro') . '</option>
										<option ' . selected('events', $seopress_pro_rich_snippets_type, false) . ' value="events">' . __('Event', 'wp-seopress-pro') . '</option>
										<option ' . selected('products', $seopress_pro_rich_snippets_type, false) . ' value="products">' . __('Product', 'wp-seopress-pro') . '</option>
										<option ' . selected('services', $seopress_pro_rich_snippets_type, false) . ' value="services">' . __('Service', 'wp-seopress-pro') . '</option>
										<option ' . selected('softwareapp', $seopress_pro_rich_snippets_type, false) . ' value="softwareapp">' . __('Software Application ', 'wp-seopress-pro') . '</option>
										<option ' . selected('review', $seopress_pro_rich_snippets_type, false) . ' value="review">' . __('Review', 'wp-seopress-pro') . '</option>
										<option ' . selected('custom', $seopress_pro_rich_snippets_type, false) . ' value="custom">' . __('Custom', 'wp-seopress-pro') . '</option>
									</select>';
    echo '</div>
								<div class="wrap-rich-snippets-rules schema-steps">
									<p>
										<label for="seopress_pro_rich_snippets_rules_meta">' . __('Show this schema if is:', 'wp-seopress-pro') . '</label>';
    $_id_name_for                = 'seopress_pro_rich_snippets_rules';
    $snippets_rules              = get_post_meta($post->ID, '_seopress_pro_rich_snippets_rules', true);
    $_available_rules_filters    = seopress_get_schemas_filters();
    $_available_rules_conditions = seopress_get_schemas_conditions();
    // Retrocompat < 3.8.2
    if ( ! is_array($snippets_rules) || empty($snippets_rules)) {
        $snippets_rules = seopress_get_default_schemas_rules($snippets_rules);
    }
    $_g = 0;
    foreach ($snippets_rules as $_group => $_rules) {
        $_group = $_g++;
        $_n     = 0;
        echo '<div data-group="' . $_group . '">';
        foreach ($_rules as $_index => $_rule) {
            $_index = $_n++;

            echo '<p data-group="' . $_group . '">';

            // Filters
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][filter]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][filter]\" class=\"small-text\">\n";
            foreach ($_available_rules_filters as $_filter => $_filter_label) {
                echo "\t\t" . '<option value="' . $_filter . '" ' . selected($_rule['filter'], $_filter, false) . '>' . $_filter_label . '</option>' . "\n";
            }
            echo '</select>';

            // Condition.
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][cond]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][cond]\" class=\"small-text\">\n";
            foreach ($_available_rules_conditions as $_cond => $_cond_label) {
                echo "\t\t" . '<option value="' . $_cond . '" ' . selected($_rule['cond'], $_cond, false) . '>' . $_cond_label . '</option>' . "\n";
            }
            echo '</select>';

            // CPT
            $class = 'post_type' === $_rule['filter'] ? '' : 'hidden';
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][cpt]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][cpt]\" class=\"{$class}\">\n";
            foreach (seopress_get_post_types() as $_cpt_slug => $_post_type_obj) {
                echo "\t\t" . '<option ' . selected($_rule['cpt'], $_cpt_slug, false) . ' value="' . $_cpt_slug . '">' . $_post_type_obj->labels->name . '</option>' . "\n";
            }
            echo '</select>';

            // TAXO
            $class = 'taxonomy' === $_rule['filter'] ? '' : 'hidden';
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][taxo]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][taxo]\" class=\"{$class}\">\n";
            foreach (seopress_get_taxonomies(true) as $_tax_slug => $_tax) {
                echo "\t\t" . '<optgroup label="' . $_tax->label . '">' . "\n";
                if (isset($_tax->terms)) { // Free version is up to date.
                    foreach ($_tax->terms as $_term) {
                        echo "\t\t" . '<option ' . selected($_rule['taxo'], $_term->term_id, false) . ' value="' . $_term->term_id . '">' . esc_html($_term->name) . '</option>' . "\n";
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>';

            // Buttons
            echo ' <span class="dashicons dashicons-plus-alt ' . $_id_name_for . '_and" data-group="' . $_group . '"></span>';
            echo ' <span class="hidden dashicons dashicons-no-alt ' . $_id_name_for . '_del" data-group="' . $_group . '"></span>';

            echo '</p>';
        }
        echo '</div>';
        echo '<p class="separat_or"><strong>' . __('or', 'wp-seopress-pro') . '</strong></p>';
    }
    echo '<p><button type="button" class="button button-secondary" id="' . $_id_name_for . '_add">' . __('Add a rule', 'wp-seopress-pro') . '</button></p>';
    echo '</p>';

    echo '</p>
								</div>';
    echo '<p><label>' . __('Map all schema properties to a value:', 'wp-seopress-pro') . '</label></p>
								<div class="wrap-rich-snippets-articles schema-steps">';
    echo '<p class="seopress-help seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Article schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/article') . '</p>';

    if (function_exists('seopress_rich_snippets_publisher_logo_option') && '' != seopress_rich_snippets_publisher_logo_option()) {
        echo '<p class="seopress-notice notice-info"><span class="dashicons dashicons-yes"></span>' . __('You have set a publisher logo. Good!', 'wp-seopress-pro') . '</p>';
    } else {
        echo '<p class="seopress-notice notice-error"><span class="dashicons dashicons-no-alt"></span>';
        /* translators: %s: link to settings page */
        echo sprintf(__('You don\'t have set a <a href="%s">publisher logo</a>. It\'s required for Article content types.', 'wp-seopress-pro'), admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets'));
        echo '</p>';
    }

    echo '
									<p>
										<label for="seopress_pro_rich_snippets_article_type_meta">' . __('Select your article type', 'wp-seopress-pro') . '</label>
										<select name="seopress_pro_rich_snippets_article_type">
											<option ' . selected('Article', $seopress_pro_rich_snippets_article_type, false) . ' value="Article">' . __('Article (generic)', 'wp-seopress-pro') . '</option>
											<option ' . selected('AdvertiserContentArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="AdvertiserContentArticle">' . __('Advertiser Content Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('NewsArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="NewsArticle">' . __('News Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('Report', $seopress_pro_rich_snippets_article_type, false) . ' value="Report">' . __('Report', 'wp-seopress-pro') . '</option>
											<option ' . selected('SatiricalArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="SatiricalArticle">' . __('Satirical Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('ScholarlyArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="ScholarlyArticle">' . __('Scholarly Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('SocialMediaPosting', $seopress_pro_rich_snippets_article_type, false) . ' value="SocialMediaPosting">' . __('Social Media Posting', 'wp-seopress-pro') . '</option>
											<option ' . selected('BlogPosting', $seopress_pro_rich_snippets_article_type, false) . ' value="BlogPosting">' . __('Blog Posting', 'wp-seopress-pro') . '</option>
											<option ' . selected('TechArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="TechArticle">' . __('Tech Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('AnalysisNewsArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="AnalysisNewsArticle">' . __('Analysis News Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('AskPublicNewsArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="AskPublicNewsArticle">' . __('Ask Public News Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('BackgroundNewsArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="BackgroundNewsArticle">' . __('Background News Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('OpinionNewsArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="OpinionNewsArticle">' . __('Opinion News Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('ReportageNewsArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="ReportageNewsArticle">' . __('Reportage News Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('ReviewNewsArticle', $seopress_pro_rich_snippets_article_type, false) . ' value="ReviewNewsArticle">' . __('Review News Article', 'wp-seopress-pro') . '</option>
											<option ' . selected('LiveBlogPosting', $seopress_pro_rich_snippets_article_type, false) . ' value="LiveBlogPosting">' . __('Live Blog Posting', 'wp-seopress-pro') . '</option>
										</select>
									</p>
									<p style="margin-bottom:0">
										<label for="seopress_pro_rich_snippets_article_title_meta">
											' . __('Headline <em>(max limit: 110)</em>', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_article_title', 'default') . '
										<span class="description">' . __('The headline of the article', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_article_img_meta">' . __('Image', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_article_img', 'image') . '
										<span class="description">' . __('The representative image of the article. Only a marked-up image that directly belongs to the article should be specified. ', 'wp-seopress-pro') . '<br>
										' . __('Default value if empty: Post thumbnail (featured image)', 'wp-seopress-pro') . '</span>
										<span class="advise">' . __('Minimum size: 696px wide, JPG, PNG or GIF, crawlable and indexable (default: post thumbnail if available)', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_article_coverage_start_date_meta">
											' . __('Coverage Start Date', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_article_coverage_start_date', 'date') . '
											<span class="description">' . __('Eg: YYYY-MM-DD - To use with <strong>Live Blog Posting</strong> article type only', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_article_coverage_start_time_meta">
											' . __('Coverage Start Time', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_article_coverage_start_time', 'time') . '
											<span class="description">' . __('Eg: HH:MM - To use with <strong>Live Blog Posting</strong> article type only', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_article_coverage_end_date_meta">
											' . __('Coverage End Date', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_article_coverage_end_date', 'date') . '
											<span class="description">' . __('Eg: YYYY-MM-DD - To use with <strong>Live Blog Posting</strong> article type only', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_article_coverage_end_time_meta">
											' . __('Coverage End Time', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_article_coverage_end_time', 'time') . '
											<span class="description">' . __('Eg: HH:MM - To use with <strong>Live Blog Posting</strong> article type only', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-local-business">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Local Business schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/local-business') . '</p>';
    echo '<p>
										<label for="seopress_pro_rich_snippets_lb_name_meta">
											' . __('Name of your business', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_name', 'default') . '
											<span class="description">' . __('eg: Miremont', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_type_meta">' . __('Select a business type', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_type', 'lb') . '
									</p>
									<p class="description"><a href="https://schema.org/LocalBusiness" target="_blank" title="' . __('All business types (new window)', 'wp-seopress-pro') . '">' . __('Full list of business types available on schema.org', 'wp-seopress-pro') . '</a><span class="dashicons dashicons-external"></span></p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_img_meta">' . __('Image', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_img', 'image') . '
										<span class="advise">' . __('Every page must contain at least one image (whether or not you include markup). Google will pick the best image to display in Search results based on the aspect ratio and resolution.<br>
Image URLs must be crawlable and indexable.<br>
Images must represent the marked up content.<br>
Images must be in .jpg, .png, or. gif format.<br>
For best results, provide multiple high-resolution images (minimum of 50K pixels when multiplying width and height) with the following aspect ratios: 16x9, 4x3, and 1x1.', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_street_addr_meta">
											' . __('Street Address', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_street_addr', 'default') . '
										<span class="description">' . __('eg: Place Bellevue', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_city_meta">
											' . __('City', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_city', 'default') . '
										<span class="description">' . __('eg: Biarritz', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_state_meta">
											' . __('State', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_state', 'default') . '
											<span class="description">' . __('eg: Pyrenees Atlantiques', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_pc_meta">
											' . __('Postal code', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_pc', 'default') . '
											<span class="description">' . __('eg: 64200', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_country_meta">
											' . __('Country', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_country', 'default') . '
											<span class="description">' . __('eg: France', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_lat_meta">
											' . __('Latitude', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_lat', 'default') . '
										<span class="description">' . __('eg: 43.4831389', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_lon_meta">
											' . __('Longitude', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_lon', 'default') . '
										<span class="description">' . __('eg: -1.5630987', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_website_meta">
											' . __('URL', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_website', 'default') . '
										<span class="description">' . __('eg: ', 'wp-seopress-pro') . get_home_url() . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_tel_meta">
											' . __('Telephone', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_tel', 'default') . '
										<span class="description">' . __('eg: +33559240138', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_price_meta">
											' . __('Price range', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_price', 'default') . '
										<span class="description">' . __('eg: $$, €€€, or ££££...', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_serves_cuisine_meta">
											' . __('Cuisine served', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_lb_serves_cuisine', 'default') . '
										<span class="description">' . __('Only to be filled if the business type is: "FoodEstablishment", "Bakery", "BarOrPub", "Brewery", "CafeOrCoffeeShop", "FastFoodRestaurant", "IceCreamShop", "Restaurant" or "Winery".', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_lb_opening_hours_meta">
											' . __('Opening hours', 'wp-seopress-pro') . '</label>
									</p>';

    $options = $seopress_pro_rich_snippets_lb_opening_hours;

    $days = [__('Monday', 'wp-seopress-pro'), __('Tuesday', 'wp-seopress-pro'), __('Wednesday', 'wp-seopress-pro'), __('Thursday', 'wp-seopress-pro'), __('Friday', 'wp-seopress-pro'), __('Saturday', 'wp-seopress-pro'), __('Sunday', 'wp-seopress-pro')];

    $hours = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];

    $mins = ['00', '15', '30', '45', '59'];

    echo '<ul class="wrap-opening-hours">';

    foreach ($days as $key => $day) {
        $check_day = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['open']);

        $check_day_am = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['open']);

        $check_day_pm = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['open']);

        $selected_start_hours = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['hours']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['hours'] : null;

        $selected_start_mins = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['mins']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['mins'] : null;

        echo '<li>';

        echo '<span class="day"><strong>' . $day . '</strong></span>';

        echo '<ul>';
        //Closed?
        echo '<li>';

        echo '<input id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][open]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][open]" type="checkbox"';
        if ('1' == $check_day) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][open]">' . __('Closed all the day?', 'wp-seopress-pro') . '</label> ';

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['open'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['open']);
        }
        echo '</li>';

        //AM
        echo '<li>';
        echo '<input id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][am][open]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][am][open]" type="checkbox"';
        if ('1' == $check_day_am) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][am][open]">' . __('Open in the morning?', 'wp-seopress-pro') . '</label> ';

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['open'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['open']);
        }

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][am][start][hours]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][am][start][hours]">';

        foreach ($hours as $hour) {
            echo '<option ';
            if ($hour == $selected_start_hours) {
                echo 'selected="selected"';
            }
            echo ' value="' . $hour . '">' . $hour . '</option>';
        }

        echo '</select>';

        echo ' : ';

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][am][start][mins]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][am][start][mins]">';

        foreach ($mins as $min) {
            echo '<option ';
            if ($min == $selected_start_mins) {
                echo 'selected="selected"';
            }
            echo ' value="' . $min . '">' . $min . '</option>';
        }

        echo '</select>';

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['hours'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['hours']);
        }

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['mins'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['start']['mins']);
        }

        echo ' - ';

        $selected_end_hours = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['end']['hours']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['end']['hours'] : null;

        $selected_end_mins = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['end']['mins']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['am']['end']['mins'] : null;

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][am][end][hours]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][am][end][hours]">';

        foreach ($hours as $hour) {
            echo '<option ';
            if ($hour == $selected_end_hours) {
                echo 'selected="selected"';
            }
            echo ' value="' . $hour . '">' . $hour . '</option>';
        }

        echo '</select>';

        echo ' : ';

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][am][end][mins]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][am][end][mins]">';

        foreach ($mins as $min) {
            echo '<option ';
            if ($min == $selected_end_mins) {
                echo 'selected="selected"';
            }
            echo ' value="' . $min . '">' . $min . '</option>';
        }

        echo '</select>';
        echo '</li>';

        //PM
        echo '<li>';
        $selected_start_hours2 = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['hours']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['hours'] : null;

        $selected_start_mins2 = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['mins']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['mins'] : null;

        echo '<input id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][pm][open]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][pm][open]" type="checkbox"';
        if ('1' == $check_day_pm) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][pm][open]">' . __('Open in the afternoon?', 'wp-seopress-pro') . '</label> ';

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['open'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['open']);
        }

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][pm][start][hours]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][pm][start][hours]">';

        foreach ($hours as $hour) {
            echo '<option ';
            if ($hour == $selected_start_hours2) {
                echo 'selected="selected"';
            }
            echo ' value="' . $hour . '">' . $hour . '</option>';
        }

        echo '</select>';

        echo ' : ';

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][pm][start][mins]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][pm][start][mins]">';

        foreach ($mins as $min) {
            echo '<option ';
            if ($min == $selected_start_mins2) {
                echo 'selected="selected"';
            }
            echo ' value="' . $min . '">' . $min . '</option>';
        }

        echo '</select>';

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['hours'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['hours']);
        }

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['mins'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['start']['mins']);
        }

        echo ' - ';

        $selected_end_hours2 = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['hours']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['hours'] : null;

        $selected_end_mins2 = isset($options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['mins']) ? $options[0]['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['mins'] : null;

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][pm][end][hours]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][pm][end][hours]">';

        foreach ($hours as $hour) {
            echo '<option ';
            if ($hour == $selected_end_hours2) {
                echo 'selected="selected"';
            }
            echo ' value="' . $hour . '">' . $hour . '</option>';
        }

        echo '</select>';

        echo ' : ';

        echo '<select id="seopress_pro_rich_snippets_lb_opening_hours[' . $key . '][pm][end][mins]" name="seopress_pro_rich_snippets_lb_opening_hours[seopress_pro_rich_snippets_lb_opening_hours][' . $key . '][pm][end][mins]">';

        foreach ($mins as $min) {
            echo '<option ';
            if ($min == $selected_end_mins2) {
                echo 'selected="selected"';
            }
            echo ' value="' . $min . '">' . $min . '</option>';
        }

        echo '</select>';

        echo '</li>';
        echo '</ul>';

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['hours'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['hours']);
        }

        if (isset($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['mins'])) {
            esc_attr($options['seopress_pro_rich_snippets_lb_opening_hours'][$key]['pm']['end']['mins']);
        }

        $seopress_pro_rich_snippets_lb_opening_hours = $options;
    }

    echo '</ul>
								</div>

								<div class="wrap-rich-snippets-faq">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>FAQ schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/faqpage') . '</p>';

    if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
        $seopress_docs_link['support']['schemas']['faq_acf'] = 'https://www.seopress.org/fr/support/guides/schema-faq-automatique-champs-repeteurs-acf/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
    } else {
        $seopress_docs_link['support']['schemas']['faq_acf'] = 'https://www.seopress.org/support/guides/create-an-automatic-faq-schema-with-acf-repeater-fields/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
    }

    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Using <strong>Advanced Custom Fields</strong> plugin? Learn <a href="%s" target="_blank">how to use repeater fields to build an automatic FAQ schema</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), $seopress_docs_link['support']['schemas']['faq_acf']) . '</p>';
    echo '<p>
										<label for="seopress_pro_rich_snippets_faq_q_meta">
											' . __('Question', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_faq_q', 'default') . '
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_faq_a_meta">
											' . __('Answer', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_faq_a', 'default') . '
									</p>
								</div>

								<div class="wrap-rich-snippets-courses">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Course schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/course') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_courses_title_meta">
											' . __('Title', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_courses_title', 'default') . '
											<span class="description">' . __('The title of your lesson, course...', 'wp-seopress-pro') . '</span>
									</p>
									<p style="margin-bottom:0">
										<label for="seopress_pro_rich_snippets_courses_desc_meta">' . __('Course description', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_courses_desc', 'default') . '
										<span class="description">' . __('Enter your course/lesson description', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_courses_school_meta">
											' . __('School/Organization', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_courses_school', 'default') . '
											<span class="description">' . __('Name of university, organization...', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_courses_website_meta">
											' . __('School/Organization Website', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_courses_website', 'default') . '
											<span class="description">' . __('Enter the URL like https://example.com/', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-recipes">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Recipe schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/recipe') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_recipes_name_meta">
											' . __('Recipe name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_name', 'default') . '
											<span class="description">' . __('The name of your dish', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_desc_meta">' . __('Short recipe description', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_desc', 'default') . '
										<span class="description">' . __('A short summary describing the dish.', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_cat_meta">
											' . __('Recipe category', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_cat', 'default') . '
											<span class="description">' . __('Eg: appetizer, entree, or dessert', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_img_meta">' . __('Image', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_img', 'image') . '
										<span class="advise">' . __('Minimum size: 185px by 185px, aspect ratio 1:1', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_prep_time_meta">
											' . __('Preparation time (in minutes)', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_prep_time', 'default') . '
											<span class="description">' . __('Eg: 30 min', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_cook_time_meta">
											' . __('Cooking time (in minutes)', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_cook_time', 'default') . '
											<span class="description">' . __('Eg: 45 min', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_calories_meta">
											' . __('Calories', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_calories', 'default') . '
											<span class="description">' . __('Number of calories', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_yield_meta">
											' . __('Recipe yield', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_yield', 'default') . '
											<span class="description">' . __('Eg: number of people served, or number of servings', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_keywords_meta">
											' . __('Keywords', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_keywords', 'default') . '
											<span class="description">' . __('Eg: winter apple pie, nutmeg crust (NOT recommended: dessert, American)', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_cuisine_meta">
											' . __('Recipe cuisine', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_cuisine', 'default') . '
											<span class="description">' . __('The region associated with your recipe. For example, "French", Mediterranean", or "American".', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_ingredient_meta">
											' . __('Recipe ingredients', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_ingredient', 'default') . '
											<span class="description">' . __('Ingredients used in the recipe. One ingredient per line. Include only the ingredient text that is necessary for making the recipe. Don\'t include unnecessary information, such as a definition of the ingredient.', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_recipes_instructions_meta">
											' . __('Recipe instructions', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_recipes_instructions', 'default') . '
											<span class="description">' . __('eg: Heat oven to 425°F. Include only text on how to make the recipe and don\'t include other text such as "Directions", "Watch the video", "Step 1".', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-jobs">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Job Posting schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/job-posting') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_jobs_name_meta">
											' . __('Job title', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_name', 'default') . '
											<span class="description">' . __('Job title', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_desc_meta">' . __('Job description', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_desc', 'default') . '
										<span class="description">' . __('Job description', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_date_posted_meta">' . __('Published date', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_date_posted', 'date') . '
										<span class="description">' . __('The original date that employer posted the job in ISO 8601 format. For example, "2017-01-24" or "2017-01-24T19:33:17+00:00".', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_valid_through_meta">' . __('Expiration date', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_valid_through', 'date') . '
										<span class="description">' . __('The date when the job posting will expire in ISO 8601 format. For example, "2017-02-24" or "2017-02-24T19:33:17+00:00".', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_employment_type_meta">' . __('Type of employment', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_employment_type', 'default') . '
										<span class="description">'/* translators: do not translate authorized values, eg: FULL_TIME  */ . __('Type of employment, You can include more than one employmentType property. Authorized values: "FULL_TIME", "PART_TIME", "CONTRACTOR", "TEMPORARY", "INTERN", "VOLUNTEER", "PER_DIEM", "OTHER"', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_identifier_name_meta">' . __('Identifier name', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_identifier_name', 'default') . '
										<span class="description">' . __('The hiring organization\'s unique identifier name for the job', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_identifier_value_meta">' . __('Identifier value', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_identifier_value', 'default') . '
										<span class="description">' . __('The hiring organization\'s value identifier value for the job', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_hiring_organization_meta">' . __('Organization that hires', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_hiring_organization', 'default') . '
										<span class="description">' . __('The organization offering the job position. This should be the name of the company.', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_hiring_same_as_meta">' . __('Organization website', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_hiring_same_as', 'default') . '
										<span class="description">' . __('Enter the URL like https://example.com/', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_hiring_logo_meta">' . __('Image', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_hiring_logo', 'image') . '
										<span class="advise">' . __('Default: Logo from your Knowledge Graph (SEO > Social Networks > Knowledge Graph)', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_address_street_meta">' . __('Street address', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_address_street', 'default') . '
										<span class="description">' . __('Street address', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_address_locality_meta">' . __('Locality address', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_address_locality', 'default') . '
										<span class="description">' . __('Locality address', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_address_region_meta">' . __('Region', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_address_region', 'default') . '
										<span class="description">' . __('Region', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_postal_code_meta">' . __('Postal code', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_postal_code', 'default') . '
										<span class="description">' . __('Postal code', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_country_meta">' . __('Country', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_country', 'default') . '
										<span class="description">' . __('Country', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_remote_meta">' . __('Remote job?', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_remote', 'default') . '
										<span class="description">' . __('If a value exists (eg: "yes"), the job offer will be marked as fully remote. Don\'t mark up jobs that allow occasional work-from-home, jobs for which remote work is a negotiable benefit, or have other arrangements that are not 100% remote. The "gig economy" nature of a job doesn\'t imply that it is or is not remote.', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_salary_meta">' . __('Salary', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_salary', 'default') . '
										<span class="description">' . __('eg: 50.00', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_salary_currency_meta">' . __('Currency', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_salary_currency', 'default') . '
										<span class="description">' . __('eg: USD', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_jobs_salary_unit_meta">' . __('Select your unit text', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_jobs_salary_unit', 'default') . '
										<span class="description">' . __('Authorized values: "HOUR", "DAY", "WEEK", "MONTH", "YEAR"', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-videos">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Video schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/video') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_videos_name_meta">
											' . __('Video name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_videos_name', 'default') . '
											<span class="description">' . __('The title of your video', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_videos_description_meta">' . __('Video description', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_videos_description', 'default') . '
										<span class="description">' . __('The description of the video', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_videos_img_meta">' . __('Video thumbnail', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_videos_img', 'image') . '
											<span class="advise">' . __('Minimum size: 160px by 90px - Max size: 1920x1080px - crawlable and indexable', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_videos_duration_meta">
											' . __('Duration of your video (format: hh:mm:ss)', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_videos_duration', 'time') . '
											<span class="description">' . __('eg: 00:04:30 for 4 minutes and 30 seconds', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_videos_url_meta">
											' . __('Video URL', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_videos_url', 'default') . '
										<span class="description">' . __('Eg: https://example.com/video.mp4', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-events">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Events schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/event') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_events_type_meta">' . __('Select your event type', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_type', ['default', 'events']) . '
										<span class="description">' . __('<strong>Authorized values:</strong> "BusinessEvent", "ChildrensEvent", "ComedyEvent", "CourseInstance", "DanceEvent", "DeliveryEvent", "EducationEvent", "ExhibitionEvent", "Festival", "FoodEvent", "LiteraryEvent", "MusicEvent", "PublicationEvent", "SaleEvent", "ScreeningEvent", "SocialEvent", "SportsEvent", "TheaterEvent", "VisualArtsEvent"', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_name_meta">
											' . __('Event name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_name', ['default', 'events']) . '
											<span class="description">' . __('The name of your event', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_desc_meta">
											' . __('Event description (default excerpt, or beginning of the content)', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_desc', ['default', 'events']) . '
											<span class="description">' . __('Enter your event description', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_img_meta">' . __('Image thumbnail', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_img', ['image', 'events']) . '
											<span class="advise">' . __('Minimum width: 720px - Recommended size: 1920px -  .jpg, .png, or. gif format - crawlable and indexable', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_start_date_meta">
											' . __('Start date', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_start_date', ['date', 'events']) . '
											<span class="description">' . __('Eg: YYYY-MM-DD', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_start_time_meta">
											' . __('Start time', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_start_time', ['time', 'events']) . '
											<span class="description">' . __('Eg: HH:MM', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_end_date_meta">
											' . __('End date', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_end_date', ['date', 'events']) . '
											<span class="description">' . __('Eg: YYYY-MM-DD', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_end_time_meta">
											' . __('End time', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_end_time', ['time', 'events']) . '
											<span class="description">' . __('Eg: HH:MM', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_previous_start_date_meta">
											' . __('Previous Start date', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_previous_start_date', ['date', 'events']) . '
											<span class="description">' . __('Eg: YYYY-MM-DD', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_previous_start_time_meta">
											' . __('Previous Start time', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_previous_start_time', ['time', 'events']) . '
											<span class="description">' . __('Eg: HH:MM', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_location_name_meta">
											' . __('Location name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_location_name', ['default', 'events']) . '
											<span class="description">' . __('Eg: Hotel du Palais', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_location_url_meta">
											' . __('Location Website', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_location_url', ['default', 'events']) . '
											<span class="description">' . __('Eg: http://www.hotel-du-palais.com/', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_location_address_meta">
											' . __('Location Address', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_location_address', ['default', 'events']) . '
											<span class="description">' . __('Eg: 1 Avenue de l\'Imperatrice, 64200 Biarritz', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_offers_name_meta">
											' . __('Offer name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_name', ['default', 'events']) . '
											<span class="description">' . __('Eg: General admission', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_offers_cat_meta">' . __('Select your offer category', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_cat', ['default', 'events']) . '
										<span class="description">' . __('<strong>Authorized values: </strong>"Primary", "Secondary", "Presale", "Premium"', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_offers_price_meta">
											' . __('Price', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_price', ['default', 'events']) . '
											<span class="description">' . __('Eg: 10', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_offers_price_currency_meta">' . __('Select your currency', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_price_currency', ['default', 'events']) . '
										<span class="description">' . __('Eg: USD, EUR...', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_offers_availability_meta">' . __('Availability', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_availability', ['default', 'events']) . '
										<span class="description">' . __('Eg: InStock, SoldOut, PreOrder', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_rich_snippets_events_offers_valid_from_meta_date">' . __('Valid From', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_valid_from_date', ['date', 'events']) . '
										<span class="description">' . __('The date when tickets go on sale', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_rich_snippets_events_offers_valid_from_meta_time">' . __('Time', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_valid_from_time', ['time', 'events']) . '
										<span class="description">' . __('The time when tickets go on sale', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_offers_url_meta">
											' . __('Website to buy tickets', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_url', ['default', 'events']) . '
											<span class="description">' . __('Eg: https://fnac.com/', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_performer_meta">
											' . __('Performer name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_performer', ['default', 'events']) . '
											<span class="description">' . __('Eg: Lana Del Rey', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_status_meta">
											' . __('Event status', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_status', ['default', 'events']) . '
											<span class="description">' . __('<strong>Authorized values:</strong> "EventCancelled", "EventMovedOnline", "EventPostponed", "EventRescheduled", "EventScheduled"', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_events_attendance_mode_meta">
											' . __('Event attendance mode', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_attendance_mode', ['default', 'events']) . '
											<span class="description">' . __('<strong>Authorized values:</strong> "OfflineEventAttendanceMode", "OnlineEventAttendanceMode", "MixedEventAttendanceMode"', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-products">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Product schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/product') . '</p>';

    if (is_plugin_active('woocommerce/woocommerce.php')) {
        if (('no' == get_option('woocommerce_enable_reviews') && get_option('woocommerce_enable_reviews'))
                                        || ('no' == get_option('woocommerce_enable_review_rating') && get_option('woocommerce_enable_review_rating'))
                                        || ('no' == get_option('woocommerce_review_rating_required') && get_option('woocommerce_review_rating_required'))) {
            echo '<p class="seopress-notice error">' . __('To automatically add <strong>aggregateRating</strong> and <strong>Review</strong> properties to your schema, you have to enable <strong>User Reviews</strong> from WooCommerce settings.', 'wp-seopress-pro');
            echo '<br>' /* translators: %s: link to plugin settings page */ . sprintf(__('Please activate these options from <strong>WC settings</strong>, <strong>Products</strong>, <a href="%s"><strong>General tab</strong></a>:', 'wp-seopress-pro'), admin_url('admin.php?page=wc-settings&tab=products')) . '<br>';
        }
        if ('no' == get_option('woocommerce_enable_reviews') && get_option('woocommerce_enable_reviews')) {
            echo '<br><span class="dashicons dashicons-minus"></span>' . __('Enable product reviews', 'wp-seopress-pro');
        }
        if ('no' == get_option('woocommerce_enable_review_rating') && get_option('woocommerce_enable_review_rating')) {
            echo '<br><span class="dashicons dashicons-minus"></span>' . __('Enable star rating on reviews', 'wp-seopress-pro');
        }
        if ('no' == get_option('woocommerce_review_rating_required') && get_option('woocommerce_review_rating_required')) {
            echo '<br><span class="dashicons dashicons-minus"></span>' . __('Star ratings should be required, not optional', 'wp-seopress-pro');
        }
        if (('no' == get_option('woocommerce_enable_reviews') && get_option('woocommerce_enable_reviews'))
                                        || ('no' == get_option('woocommerce_enable_review_rating') && get_option('woocommerce_enable_review_rating'))
                                        || ('no' == get_option('woocommerce_review_rating_required') && get_option('woocommerce_review_rating_required'))) {
            echo '</p>';
        }

        //WooCommerce Structured data
        if ( ! function_exists('seopress_woocommerce_schema_output_option')) {
            function seopress_woocommerce_schema_output_option() {
                $seopress_woocommerce_schema_output_option = get_option('seopress_pro_option_name');
                if ( ! empty($seopress_woocommerce_schema_output_option)) {
                    foreach ($seopress_woocommerce_schema_output_option as $key => $seopress_woocommerce_schema_output_value) {
                        $options[$key] = $seopress_woocommerce_schema_output_value;
                    }
                    if (isset($seopress_woocommerce_schema_output_option['seopress_woocommerce_schema_output'])) {
                        return $seopress_woocommerce_schema_output_option['seopress_woocommerce_schema_output'];
                    }
                }
            }
            if ('1' != seopress_woocommerce_schema_output_option()) {
                echo '<p class="seopress-notice error">' /* translators: %s: link to plugin settings page */ . sprintf(__('You have not deactivated the default WooCommerce structured data type from our <a href="%s"><strong>PRO settings > WooCommerce tab</strong></a>. It\'s recommended to disable it to avoid any conflicts with your product schemas.', 'wp-seopress-pro'), admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_woocommerce')) . '</p>';
            }
        }
    } else {
        echo '<p class="seopress-notice error">' . __('WooCommerce is not enabled on your site. Some properties like <strong>aggregateRating</strong> and <strong>Review</strong> will not work out of the box.', 'wp-seopress-pro') . '</p>';
    }

    echo '<p>
										<label for="seopress_pro_rich_snippets_product_name_meta">
											' . __('Product name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_name', 'default') . '
											<span class="description">' . __('The name of your product', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_description_meta">' . __('Product description', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_description', 'default') . '
										<span class="description">' . __('The description of the product', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_img_meta">' . __('Thumbnail', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_img', 'image') . '
										<span class="advise">' . __('Pictures clearly showing the product, e.g. against a white background, are preferred.', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_price_meta">
											' . __('Product price', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_price', 'default') . '
											<span class="description">' . __('Eg: 30', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_price_valid_date">' . __('Product price valid until', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_price_valid_date', 'date') . '
										<span class="description">' . __('Eg: YYYY-MM-DD', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_sku_meta">
											' . __('Product SKU', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_sku', 'default') . '
											<span class="description">' . __('Eg: 0446310786', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_global_ids_meta">
											' . __('Product Global Identifiers type', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_global_ids', 'default') . '
											<span class="description">' . __('Eg: gtin8', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_global_ids_value_meta">
											' . __('Product Global Identifiers', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_global_ids_value', 'default') . '
											<span class="description">' . __('Eg: 925872', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_brand_meta">
											' . __('Product Brand', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_brand', 'default') . '
											<span class="description">' . __('eg: Apple', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_price_currency_meta">
											' . __('Product currency', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_price_currency', 'default') . '
											<span class="description">' . __('Eg: USD, EUR', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_condition_meta">' . __('Product Condition', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_condition', 'default') . '
										<span class="description">' . __('<strong>Authorized values:</strong> "NewCondition", "UsedCondition", "DamagedCondition", "RefurbishedCondition"', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_product_availability_meta">' . __('Product Availability', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_product_availability', 'default') . '
										<span class="description">' . __('<strong>Authorized values:</strong> "InStock", "InStoreOnly", "OnlineOnly", "LimitedAvailability", "SoldOut", "OutOfStock", "Discontinued", "PreOrder", "PreSale"', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-software-app">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Software App schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/software-app') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_softwareapp_name_meta">
											' . __('Software name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_softwareapp_name', 'default') . '
											<span class="description">' . __('The name of your app', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_softwareapp_os_meta">
											' . __('Operating system', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_softwareapp_os', 'default') . '
											<span class="description">' . __('The operating system(s) required to use the app', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_softwareapp_cat_meta">
											' . __('Application category', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_softwareapp_cat', 'default') . '
											<span class="description">' . __('<strong>Authorized values:</strong> "GameApplication", "SocialNetworkingApplication", "TravelApplication", "ShoppingApplication", "SportsApplication", "LifestyleApplication", "BusinessApplication", "DesignApplication", "DeveloperApplication", "DriverApplication", "EducationalApplication", "HealthApplication", "FinanceApplication", "SecurityApplication", "BrowserApplication", "CommunicationApplication", "DesktopEnhancementApplication", "EntertainmentApplication", "MultimediaApplication", "HomeApplication", "UtilitiesApplication", "ReferenceApplication"', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_softwareapp_price_meta">
											' . __('Price of your app', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_softwareapp_price', 'default') . '
											<span class="description">' . __('The price of your app (set "0" if the app is free of charge)', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_softwareapp_currency_meta">
											' . __('Currency', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_softwareapp_currency', 'default') . '
											<span class="description">' . __('Eg: USD, EUR...', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_softwareapp_rating_meta">
											' . __('Your rating', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_softwareapp_rating', 'rating') . '
											<span class="description">' . __('The item rating', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-services">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Service schema</strong> from the <a href="%s" target="_blank">Schema.org official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://schema.org/Service') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_service_name_meta">
											' . __('Service name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_name', 'default') . '
											<span class="description">' . __('The name of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_type_meta">
											' . __('Service type', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_type', 'default') . '
											<span class="description">' . __('The type of service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_description_meta">' . __('Service description', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_description', 'default') . '
										<span class="description">' . __('The description of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_img_meta">' . __('Image', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_img', 'image') . '
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_area_meta">' . __('Area served', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_area', 'default') . '
										<span class="description">' . __('The area served by your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_provider_name_meta">' . __('Provider name', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_provider_name', 'default') . '
										<span class="description">' . __('The provider name of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_lb_img_meta">' . __('Location image', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_lb_img', 'image') . '
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_provider_mobility_meta">' . __('Provider mobility (static or dynamic)', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_provider_mobility', 'default') . '
										<span class="description">' . __('The provider mobility of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_slogan_meta">' . __('Slogan', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_slogan', 'default') . '
										<span class="description">' . __('The slogan of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_street_addr_meta">
											' . __('Street Address', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_street_addr', 'default') . '
										<span class="description">' . __('The street address of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_city_meta">
											' . __('City', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_city', 'default') . '
										<span class="description">' . __('The city of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_state_meta">
											' . __('State', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_state', 'default') . '
											<span class="description">' . __('The state of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_pc_meta">
											' . __('Postal code', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_pc', 'default') . '
											<span class="description">' . __('The postal code of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_country_meta">
											' . __('Country', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_country', 'default') . '
											<span class="description">' . __('The country of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_lat_meta">
											' . __('Latitude', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_lat', 'default') . '
										<span class="description">' . __('The latitude of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_lon_meta">
											' . __('Longitude', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_lon', 'default') . '
										<span class="description">' . __('The longitude of your service', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_tel_meta">
											' . __('Telephone', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_tel', 'default') . '
										<span class="description">' . __('The telephone of your service (international format)', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_service_price_meta">
											' . __('Price range', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_service_price', 'default') . '
										<span class="description">' . __('The price range of your service', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-review">';
    echo '<p class="seopress-notice notice-info">' /* translators: %s: link documentation */ . sprintf(__('Learn more about the <strong>Review schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a><span class="dashicons dashicons-external"></span>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/review-snippet') . '</p>';

    echo '<p>
										<label for="seopress_pro_rich_snippets_review_item_meta">
											' . __('Review item name', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_review_item', 'default') . '
											<span class="description">' . __('The item name reviewed', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_review_item_type_meta">
											' . __('Review item type', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_review_item_type', 'default') . '
											<span class="description">' . __('<strong>Authorized values:</strong> "CreativeWorkSeason", "CreativeWorkSeries", "Episode", "Game", "MediaObject", "MusicPlaylist", "MusicRecording", "Organization"', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_review_img_meta">' . __('Review item image', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_review_img', 'image') . '
										<span class="description">' . __('Review item image URL', 'wp-seopress-pro') . '</span>
									</p>
									<p>
										<label for="seopress_pro_rich_snippets_review_rating_meta">
											' . __('Your rating', 'wp-seopress-pro') . '</label>
										' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_review_rating', 'rating') . '
										<span class="description">' . __('Your rating: scale from 1 to 5', 'wp-seopress-pro') . '</span>
									</p>
								</div>

								<div class="wrap-rich-snippets-custom">
									<p>
										<label for="seopress_pro_rich_snippets_custom_meta">
											' . __('Custom schema', 'wp-seopress-pro') . '</label>
											' . seopress_schemas_mapping_array('seopress_pro_rich_snippets_custom', 'custom') . '
									</p>';

    if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
        $seopress_docs_link['support']['schema']['dynamic'] = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-metas/';
    } else {
        $seopress_docs_link['support']['schema']['dynamic'] = 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/';
    }

    echo '<p class="description"><span class="seopress-help dashicons dashicons-external"></span>' /* translators: %s: link documentation */ . sprintf(__('<a href="%s" target="_blank">You can use dynamic variables in your schema.</a>', 'wp-seopress-pro'), $seopress_docs_link['support']['schema']['dynamic']) . '</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</td>
		</tr>';
}

add_action('save_post', 'seopress_schemas_save_metabox', 10, 2);
function seopress_schemas_save_metabox($post_id, $post) {
    //Nonce
    if ( ! isset($_POST['seopress_schemas_cpt_nonce']) || ! wp_verify_nonce($_POST['seopress_schemas_cpt_nonce'], plugin_basename(__FILE__))) {
        return $post_id;
    }

    //Post type object
    $post_type = get_post_type_object($post->post_type);

    //Check permission
    if ( ! current_user_can('edit_schemas', $post_id)) {
        return $post_id;
    }

    if (isset($_POST['seopress_pro_rich_snippets_rules'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_rules', $_POST['seopress_pro_rich_snippets_rules']);
    }
    if (isset($_POST['seopress_pro_rich_snippets_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_type', esc_html($_POST['seopress_pro_rich_snippets_type']));
    }
    //Article
    if (isset($_POST['seopress_pro_rich_snippets_article_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_type', esc_html($_POST['seopress_pro_rich_snippets_article_type']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_title'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_title', esc_html($_POST['seopress_pro_rich_snippets_article_title']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_title_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_title_cf', esc_html($_POST['seopress_pro_rich_snippets_article_title_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_title_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_title_tax', esc_html($_POST['seopress_pro_rich_snippets_article_title_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_title_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_title_manual_global', esc_html($_POST['seopress_pro_rich_snippets_article_title_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_img', esc_html($_POST['seopress_pro_rich_snippets_article_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_article_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_img_cf', esc_html($_POST['seopress_pro_rich_snippets_article_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_img_tax', esc_html($_POST['seopress_pro_rich_snippets_article_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_article_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_article_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_article_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_date'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_date', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_date']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_date_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_date_cf', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_date_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_date_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_date_tax', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_date_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_date_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_date_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_date_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_time', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_time_cf', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_time_tax', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_start_time_manual_time_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_start_time_manual_time_global', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_start_time_manual_time_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_date'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_date', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_date']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_date_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_date_cf', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_date_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_date_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_date_tax', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_date_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_date_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_date_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_date_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_time', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_time_cf', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_time_tax', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_article_coverage_end_time_manual_time_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_article_coverage_end_time_manual_time_global', esc_html($_POST['seopress_pro_rich_snippets_article_coverage_end_time_manual_time_global']));
    }
    //Local Business
    if (isset($_POST['seopress_pro_rich_snippets_lb_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_name', esc_html($_POST['seopress_pro_rich_snippets_lb_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_name_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_name_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_type', esc_html($_POST['seopress_pro_rich_snippets_lb_type']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_type_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_type_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_type_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_type_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_type_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_type_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_type_lb'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_type_lb', esc_html($_POST['seopress_pro_rich_snippets_lb_type_lb']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_type_manual_lb_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_type_manual_lb_global', esc_html($_POST['seopress_pro_rich_snippets_lb_type_manual_lb_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_img', esc_html($_POST['seopress_pro_rich_snippets_lb_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_lb_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_img_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_img_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_lb_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_lb_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_lb_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_street_addr'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_street_addr', esc_html($_POST['seopress_pro_rich_snippets_lb_street_addr']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_street_addr_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_street_addr_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_street_addr_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_street_addr_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_street_addr_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_street_addr_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_street_addr_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_street_addr_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_street_addr_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_city'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_city', esc_html($_POST['seopress_pro_rich_snippets_lb_city']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_city_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_city_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_city_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_city_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_city_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_city_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_city_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_city_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_city_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_state'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_state', esc_html($_POST['seopress_pro_rich_snippets_lb_state']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_state_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_state_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_state_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_state_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_state_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_state_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_state_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_state_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_state_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_pc'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_pc', esc_html($_POST['seopress_pro_rich_snippets_lb_pc']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_pc_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_pc_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_pc_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_pc_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_pc_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_pc_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_pc_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_pc_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_pc_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_country'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_country', esc_html($_POST['seopress_pro_rich_snippets_lb_country']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_country_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_country_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_country_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_country_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_country_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_country_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_country_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_country_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_country_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lat'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lat', esc_html($_POST['seopress_pro_rich_snippets_lb_lat']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lat_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lat_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_lat_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lat_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lat_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_lat_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lat_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lat_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_lat_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lon'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lon', esc_html($_POST['seopress_pro_rich_snippets_lb_lon']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lon_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lon_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_lon_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lon_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lon_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_lon_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_lon_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_lon_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_lon_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_website'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_website', esc_html($_POST['seopress_pro_rich_snippets_lb_website']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_website_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_website_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_website_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_website_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_website_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_website_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_website_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_website_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_website_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_tel'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_tel', esc_html($_POST['seopress_pro_rich_snippets_lb_tel']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_tel_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_tel_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_tel_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_tel_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_tel_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_tel_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_tel_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_tel_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_tel_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_price'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_price', esc_html($_POST['seopress_pro_rich_snippets_lb_price']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_price_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_price_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_price_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_price_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_price_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_price_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_price_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_price_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_price_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_serves_cuisine'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_serves_cuisine', esc_html($_POST['seopress_pro_rich_snippets_lb_serves_cuisine']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_serves_cuisine_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_serves_cuisine_cf', esc_html($_POST['seopress_pro_rich_snippets_lb_serves_cuisine_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_serves_cuisine_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_serves_cuisine_tax', esc_html($_POST['seopress_pro_rich_snippets_lb_serves_cuisine_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_serves_cuisine_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_serves_cuisine_manual_global', esc_html($_POST['seopress_pro_rich_snippets_lb_serves_cuisine_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_lb_opening_hours'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_lb_opening_hours', $_POST['seopress_pro_rich_snippets_lb_opening_hours']);
    }
    //FAQ
    if (isset($_POST['seopress_pro_rich_snippets_faq_q'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_q', esc_html($_POST['seopress_pro_rich_snippets_faq_q']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_faq_q_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_q_cf', esc_html($_POST['seopress_pro_rich_snippets_faq_q_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_faq_q_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_q_tax', esc_html($_POST['seopress_pro_rich_snippets_faq_q_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_faq_q_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_q_manual_global', esc_html($_POST['seopress_pro_rich_snippets_faq_q_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_faq_a'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_a', esc_html($_POST['seopress_pro_rich_snippets_faq_a']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_faq_a_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_a_cf', esc_html($_POST['seopress_pro_rich_snippets_faq_a_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_faq_a_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_a_tax', esc_html($_POST['seopress_pro_rich_snippets_faq_a_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_faq_a_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_faq_a_manual_global', esc_html($_POST['seopress_pro_rich_snippets_faq_a_manual_global']));
    }
    //Course
    if (isset($_POST['seopress_pro_rich_snippets_courses_title'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_title', esc_html($_POST['seopress_pro_rich_snippets_courses_title']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_title_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_title_cf', esc_html($_POST['seopress_pro_rich_snippets_courses_title_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_title_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_title_tax', esc_html($_POST['seopress_pro_rich_snippets_courses_title_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_title_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_title_manual_global', esc_html($_POST['seopress_pro_rich_snippets_courses_title_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_desc'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_desc', esc_html($_POST['seopress_pro_rich_snippets_courses_desc']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_desc_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_desc_cf', esc_html($_POST['seopress_pro_rich_snippets_courses_desc_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_desc_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_desc_tax', esc_html($_POST['seopress_pro_rich_snippets_courses_desc_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_desc_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_desc_manual_global', esc_html($_POST['seopress_pro_rich_snippets_courses_desc_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_school'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_school', esc_html($_POST['seopress_pro_rich_snippets_courses_school']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_school_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_school_cf', esc_html($_POST['seopress_pro_rich_snippets_courses_school_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_school_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_school_tax', esc_html($_POST['seopress_pro_rich_snippets_courses_school_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_school_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_school_manual_global', esc_html($_POST['seopress_pro_rich_snippets_courses_school_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_website'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_website', esc_html($_POST['seopress_pro_rich_snippets_courses_website']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_website_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_website_cf', esc_html($_POST['seopress_pro_rich_snippets_courses_website_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_website_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_website_tax', esc_html($_POST['seopress_pro_rich_snippets_courses_website_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_courses_website_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_courses_website_manual_global', esc_html($_POST['seopress_pro_rich_snippets_courses_website_manual_global']));
    }
    //Recipe
    if (isset($_POST['seopress_pro_rich_snippets_recipes_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_name', esc_html($_POST['seopress_pro_rich_snippets_recipes_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_name_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_name_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_desc'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_desc', esc_html($_POST['seopress_pro_rich_snippets_recipes_desc']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_desc_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_desc_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_desc_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_desc_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_desc_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_desc_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_desc_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_desc_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_desc_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cat'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cat', esc_html($_POST['seopress_pro_rich_snippets_recipes_cat']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cat_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cat_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_cat_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cat_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cat_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_cat_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cat_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cat_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_cat_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_img', esc_html($_POST['seopress_pro_rich_snippets_recipes_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_img_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_img_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_recipes_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_prep_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_prep_time', esc_html($_POST['seopress_pro_rich_snippets_recipes_prep_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_prep_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_prep_time_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_prep_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_prep_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_prep_time_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_prep_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_prep_time_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_prep_time_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_prep_time_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cook_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cook_time', esc_html($_POST['seopress_pro_rich_snippets_recipes_cook_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cook_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cook_time_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_cook_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cook_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cook_time_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_cook_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cook_time_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cook_time_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_cook_time_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_calories'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_calories', esc_html($_POST['seopress_pro_rich_snippets_recipes_calories']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_calories_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_calories_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_calories_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_calories_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_calories_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_calories_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_calories_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_calories_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_calories_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_yield'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_yield', esc_html($_POST['seopress_pro_rich_snippets_recipes_yield']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_yield_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_yield_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_yield_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_yield_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_yield_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_yield_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_yield_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_yield_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_yield_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_keywords'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_keywords', esc_html($_POST['seopress_pro_rich_snippets_recipes_keywords']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_keywords_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_keywords_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_keywords_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_keywords_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_keywords_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_keywords_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_keywords_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_keywords_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_keywords_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cuisine'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cuisine', esc_html($_POST['seopress_pro_rich_snippets_recipes_cuisine']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cuisine_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cuisine_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_cuisine_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cuisine_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cuisine_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_cuisine_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_cuisine_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_cuisine_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_cuisine_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_ingredient'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_ingredient', esc_html($_POST['seopress_pro_rich_snippets_recipes_ingredient']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_ingredient_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_ingredient_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_ingredient_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_ingredient_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_ingredient_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_ingredient_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_ingredient_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_ingredient_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_ingredient_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_instructions'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_instructions', esc_html($_POST['seopress_pro_rich_snippets_recipes_instructions']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_instructions_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_instructions_cf', esc_html($_POST['seopress_pro_rich_snippets_recipes_instructions_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_instructions_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_instructions_tax', esc_html($_POST['seopress_pro_rich_snippets_recipes_instructions_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_recipes_instructions_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_recipes_instructions_manual_global', esc_html($_POST['seopress_pro_rich_snippets_recipes_instructions_manual_global']));
    }
    //Job
    if (isset($_POST['seopress_pro_rich_snippets_jobs_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_name', esc_html($_POST['seopress_pro_rich_snippets_jobs_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_name_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_name_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_desc'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_desc', esc_html($_POST['seopress_pro_rich_snippets_jobs_desc']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_desc_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_desc_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_desc_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_desc_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_desc_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_desc_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_desc_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_desc_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_desc_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_date_posted'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_date_posted', esc_html($_POST['seopress_pro_rich_snippets_jobs_date_posted']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_date_posted_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_date_posted_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_date_posted_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_date_posted_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_date_posted_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_date_posted_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_date_posted_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_date_posted_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_date_posted_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_valid_through'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_valid_through', esc_html($_POST['seopress_pro_rich_snippets_jobs_valid_through']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_valid_through_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_valid_through_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_valid_through_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_valid_through_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_valid_through_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_valid_through_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_valid_through_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_valid_through_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_valid_through_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_employment_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_employment_type', esc_html($_POST['seopress_pro_rich_snippets_jobs_employment_type']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_employment_type_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_employment_type_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_employment_type_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_employment_type_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_employment_type_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_employment_type_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_employment_type_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_employment_type_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_employment_type_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_name', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_name_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_name_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_value'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_value', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_value']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_value_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_value_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_value_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_value_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_value_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_value_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_identifier_value_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_identifier_value_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_identifier_value_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_organization'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_organization', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_organization']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_organization_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_organization_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_organization_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_organization_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_organization_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_organization_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_organization_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_organization_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_organization_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_same_as', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_same_as_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_same_as_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_same_as_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_same_as_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_logo'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_logo', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_logo']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_logo_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_logo_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_street'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_street', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_street']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_street_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_street_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_street_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_street_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_street_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_street_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_street_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_street_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_street_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_locality'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_locality', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_locality']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_locality_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_locality_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_locality_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_locality_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_locality_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_locality_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_locality_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_locality_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_locality_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_region'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_region', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_region']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_region_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_region_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_region_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_region_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_region_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_region_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_address_region_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_address_region_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_address_region_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_postal_code'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_postal_code', esc_html($_POST['seopress_pro_rich_snippets_jobs_postal_code']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_postal_code_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_postal_code_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_postal_code_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_postal_code_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_postal_code_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_postal_code_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_postal_code_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_postal_code_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_postal_code_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_country'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_country', esc_html($_POST['seopress_pro_rich_snippets_jobs_country']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_country_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_country_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_country_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_country_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_country_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_country_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_country_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_country_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_country_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_remote'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_remote', esc_html($_POST['seopress_pro_rich_snippets_jobs_remote']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_remote_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_remote_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_remote_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_remote_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_remote_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_remote_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_remote_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_remote_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_remote_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_currency'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_currency', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_currency']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_currency_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_currency_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_currency_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_currency_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_currency_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_currency_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_currency_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_currency_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_currency_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_unit'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_unit', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_unit']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_unit_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_unit_cf', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_unit_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_unit_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_unit_tax', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_unit_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_jobs_salary_unit_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_jobs_salary_unit_manual_global', esc_html($_POST['seopress_pro_rich_snippets_jobs_salary_unit_manual_global']));
    }
    //Video
    if (isset($_POST['seopress_pro_rich_snippets_videos_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_name', esc_html($_POST['seopress_pro_rich_snippets_videos_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_name_cf', esc_html($_POST['seopress_pro_rich_snippets_videos_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_name_tax', esc_html($_POST['seopress_pro_rich_snippets_videos_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_videos_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_description'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_description', esc_html($_POST['seopress_pro_rich_snippets_videos_description']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_description_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_description_cf', esc_html($_POST['seopress_pro_rich_snippets_videos_description_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_description_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_description_tax', esc_html($_POST['seopress_pro_rich_snippets_videos_description_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_description_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_description_manual_global', esc_html($_POST['seopress_pro_rich_snippets_videos_description_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_img', esc_html($_POST['seopress_pro_rich_snippets_videos_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_videos_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_img_cf', esc_html($_POST['seopress_pro_rich_snippets_videos_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_img_tax', esc_html($_POST['seopress_pro_rich_snippets_videos_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_videos_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_videos_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_videos_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_duration'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_duration', esc_html($_POST['seopress_pro_rich_snippets_videos_duration']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_duration_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_duration_cf', esc_html($_POST['seopress_pro_rich_snippets_videos_duration_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_duration_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_duration_tax', esc_html($_POST['seopress_pro_rich_snippets_videos_duration_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_duration_manual_time_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_duration_manual_time_global', esc_html($_POST['seopress_pro_rich_snippets_videos_duration_manual_time_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_url'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_url', esc_html($_POST['seopress_pro_rich_snippets_videos_url']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_url_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_url_cf', esc_html($_POST['seopress_pro_rich_snippets_videos_url_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_url_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_url_tax', esc_html($_POST['seopress_pro_rich_snippets_videos_url_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_videos_url_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_videos_url_manual_global', esc_html($_POST['seopress_pro_rich_snippets_videos_url_manual_global']));
    }
    //Event
    if (isset($_POST['seopress_pro_rich_snippets_events_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_type', esc_html($_POST['seopress_pro_rich_snippets_events_type']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_type_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_type_cf', esc_html($_POST['seopress_pro_rich_snippets_events_type_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_type_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_type_tax', esc_html($_POST['seopress_pro_rich_snippets_events_type_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_type_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_type_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_type_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_name', esc_html($_POST['seopress_pro_rich_snippets_events_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_name_cf', esc_html($_POST['seopress_pro_rich_snippets_events_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_name_tax', esc_html($_POST['seopress_pro_rich_snippets_events_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc', esc_html($_POST['seopress_pro_rich_snippets_events_desc']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc_cf', esc_html($_POST['seopress_pro_rich_snippets_events_desc_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc_tax', esc_html($_POST['seopress_pro_rich_snippets_events_desc_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_desc_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_img', esc_html($_POST['seopress_pro_rich_snippets_events_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_events_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_img_cf', esc_html($_POST['seopress_pro_rich_snippets_events_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_img_tax', esc_html($_POST['seopress_pro_rich_snippets_events_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_events_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_events_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_events_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc', esc_html($_POST['seopress_pro_rich_snippets_events_desc']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc_cf', esc_html($_POST['seopress_pro_rich_snippets_events_desc_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc_tax', esc_html($_POST['seopress_pro_rich_snippets_events_desc_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_desc_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_desc_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_desc_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_date'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_date', esc_html($_POST['seopress_pro_rich_snippets_events_start_date']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_date_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_date_cf', esc_html($_POST['seopress_pro_rich_snippets_events_start_date_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_date_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_date_tax', esc_html($_POST['seopress_pro_rich_snippets_events_start_date_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_date_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_date_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_events_start_date_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_time', esc_html($_POST['seopress_pro_rich_snippets_events_start_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_time_cf', esc_html($_POST['seopress_pro_rich_snippets_events_start_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_time_tax', esc_html($_POST['seopress_pro_rich_snippets_events_start_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_start_time_manual_time_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_start_time_manual_time_global', esc_html($_POST['seopress_pro_rich_snippets_events_start_time_manual_time_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_date'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_date', esc_html($_POST['seopress_pro_rich_snippets_events_end_date']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_date_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_date_cf', esc_html($_POST['seopress_pro_rich_snippets_events_end_date_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_date_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_date_tax', esc_html($_POST['seopress_pro_rich_snippets_events_end_date_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_date_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_date_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_events_end_date_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_time', esc_html($_POST['seopress_pro_rich_snippets_events_end_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_time_cf', esc_html($_POST['seopress_pro_rich_snippets_events_end_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_time_tax', esc_html($_POST['seopress_pro_rich_snippets_events_end_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_end_time_manual_time_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_end_time_manual_time_global', esc_html($_POST['seopress_pro_rich_snippets_events_end_time_manual_time_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_date'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_date', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_date']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_date_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_date_cf', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_date_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_date_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_date_tax', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_date_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_date_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_date_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_date_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_time', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_time_cf', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_time_tax', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_previous_start_time_manual_time_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_previous_start_time_manual_time_global', esc_html($_POST['seopress_pro_rich_snippets_events_previous_start_time_manual_time_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_name', esc_html($_POST['seopress_pro_rich_snippets_events_location_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_name_cf', esc_html($_POST['seopress_pro_rich_snippets_events_location_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_name_tax', esc_html($_POST['seopress_pro_rich_snippets_events_location_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_location_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_url'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_url', esc_html($_POST['seopress_pro_rich_snippets_events_location_url']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_url_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_url_cf', esc_html($_POST['seopress_pro_rich_snippets_events_location_url_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_url_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_url_tax', esc_html($_POST['seopress_pro_rich_snippets_events_location_url_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_url_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_url_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_location_url_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_address'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_address', esc_html($_POST['seopress_pro_rich_snippets_events_location_address']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_address_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_address_cf', esc_html($_POST['seopress_pro_rich_snippets_events_location_address_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_address_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_address_tax', esc_html($_POST['seopress_pro_rich_snippets_events_location_address_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_location_address_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_location_address_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_location_address_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_name', esc_html($_POST['seopress_pro_rich_snippets_events_offers_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_name_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_name_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_cat'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_cat', esc_html($_POST['seopress_pro_rich_snippets_events_offers_cat']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_cat_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_cat_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_cat_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_cat_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_cat_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_cat_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_cat_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_cat_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_cat_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price_currency'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price_currency', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price_currency']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price_currency_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price_currency_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price_currency_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price_currency_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price_currency_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price_currency_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_price_currency_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_price_currency_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_price_currency_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_availability'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_availability', esc_html($_POST['seopress_pro_rich_snippets_events_offers_availability']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_availability_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_availability_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_availability_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_availability_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_availability_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_availability_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_availability_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_availability_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_availability_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_date', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_date_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_date_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_date_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_date_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_time', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_time_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_time_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time_manual_time_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_valid_from_time_manual_time_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_valid_from_time_manual_time_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_url'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_url', esc_html($_POST['seopress_pro_rich_snippets_events_offers_url']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_url_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_url_cf', esc_html($_POST['seopress_pro_rich_snippets_events_offers_url_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_url_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_url_tax', esc_html($_POST['seopress_pro_rich_snippets_events_offers_url_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_offers_url_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_offers_url_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_offers_url_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_performer'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_performer', esc_html($_POST['seopress_pro_rich_snippets_events_performer']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_performer_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_performer_cf', esc_html($_POST['seopress_pro_rich_snippets_events_performer_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_performer_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_performer_tax', esc_html($_POST['seopress_pro_rich_snippets_events_performer_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_performer_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_performer_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_performer_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_status'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_status', esc_html($_POST['seopress_pro_rich_snippets_events_status']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_status_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_status_cf', esc_html($_POST['seopress_pro_rich_snippets_events_status_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_status_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_status_tax', esc_html($_POST['seopress_pro_rich_snippets_events_status_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_status_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_status_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_status_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_attendance_mode'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_attendance_mode', esc_html($_POST['seopress_pro_rich_snippets_events_attendance_mode']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_attendance_mode_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_attendance_mode_cf', esc_html($_POST['seopress_pro_rich_snippets_events_attendance_mode_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_attendance_mode_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_attendance_mode_tax', esc_html($_POST['seopress_pro_rich_snippets_events_attendance_mode_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_events_attendance_mode_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_events_attendance_mode_manual_global', esc_html($_POST['seopress_pro_rich_snippets_events_attendance_mode_manual_global']));
    }
    //Product
    if (isset($_POST['seopress_pro_rich_snippets_product_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_name', esc_html($_POST['seopress_pro_rich_snippets_product_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_name_cf', esc_html($_POST['seopress_pro_rich_snippets_product_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_name_tax', esc_html($_POST['seopress_pro_rich_snippets_product_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_description'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_description', esc_html($_POST['seopress_pro_rich_snippets_product_description']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_description_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_description_cf', esc_html($_POST['seopress_pro_rich_snippets_product_description_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_description_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_description_tax', esc_html($_POST['seopress_pro_rich_snippets_product_description_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_description_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_description_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_description_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_img', esc_html($_POST['seopress_pro_rich_snippets_product_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_product_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_img_cf', esc_html($_POST['seopress_pro_rich_snippets_product_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_img_tax', esc_html($_POST['seopress_pro_rich_snippets_product_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_product_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_product_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_product_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price', esc_html($_POST['seopress_pro_rich_snippets_product_price']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_cf', esc_html($_POST['seopress_pro_rich_snippets_product_price_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_tax', esc_html($_POST['seopress_pro_rich_snippets_product_price_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_price_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_valid_date'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_valid_date', esc_html($_POST['seopress_pro_rich_snippets_product_price_valid_date']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_valid_date_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_valid_date_cf', esc_html($_POST['seopress_pro_rich_snippets_product_price_valid_date_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_valid_date_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_valid_date_tax', esc_html($_POST['seopress_pro_rich_snippets_product_price_valid_date_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_valid_date_manual_date_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_valid_date_manual_date_global', esc_html($_POST['seopress_pro_rich_snippets_product_price_valid_date_manual_date_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_sku'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_sku', esc_html($_POST['seopress_pro_rich_snippets_product_sku']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_sku_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_sku_cf', esc_html($_POST['seopress_pro_rich_snippets_product_sku_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_sku_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_sku_tax', esc_html($_POST['seopress_pro_rich_snippets_product_sku_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_sku_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_sku_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_sku_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids_cf', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids_tax', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids_value'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids_value', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids_value']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids_value_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids_value_cf', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids_value_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids_value_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids_value_tax', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids_value_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_global_ids_value_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_global_ids_value_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_global_ids_value_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_brand'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_brand', esc_html($_POST['seopress_pro_rich_snippets_product_brand']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_brand_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_brand_cf', esc_html($_POST['seopress_pro_rich_snippets_product_brand_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_brand_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_brand_tax', esc_html($_POST['seopress_pro_rich_snippets_product_brand_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_brand_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_brand_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_brand_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_currency'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_currency', esc_html($_POST['seopress_pro_rich_snippets_product_price_currency']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_currency_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_currency_cf', esc_html($_POST['seopress_pro_rich_snippets_product_price_currency_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_currency_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_currency_tax', esc_html($_POST['seopress_pro_rich_snippets_product_price_currency_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_price_currency_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_price_currency_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_price_currency_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_condition'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_condition', esc_html($_POST['seopress_pro_rich_snippets_product_condition']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_condition_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_condition_cf', esc_html($_POST['seopress_pro_rich_snippets_product_condition_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_condition_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_condition_tax', esc_html($_POST['seopress_pro_rich_snippets_product_condition_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_condition_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_condition_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_condition_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_availability'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_availability', esc_html($_POST['seopress_pro_rich_snippets_product_availability']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_availability_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_availability_cf', esc_html($_POST['seopress_pro_rich_snippets_product_availability_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_availability_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_availability_tax', esc_html($_POST['seopress_pro_rich_snippets_product_availability_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_product_availability_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_product_availability_manual_global', esc_html($_POST['seopress_pro_rich_snippets_product_availability_manual_global']));
    }
    //Software App
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_name', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_name_cf', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_name_tax', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_os'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_os', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_os']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_os_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_os_cf', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_os_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_os_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_os_tax', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_os_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_os_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_os_manual_global', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_os_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_cat'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_cat', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_cat']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_cat_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_cat_cf', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_cat_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_cat_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_cat_tax', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_cat_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_cat_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_cat_manual_global', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_cat_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_price'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_price', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_price']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_price_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_price_cf', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_price_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_price_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_price_tax', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_price_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_price_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_price_manual_global', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_price_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_currency'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_currency', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_currency']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_currency_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_currency_cf', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_currency_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_currency_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_currency_tax', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_currency_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_currency_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_currency_manual_global', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_currency_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_rating'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_rating', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_rating']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_rating_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_rating_cf', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_rating_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_rating_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_rating_tax', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_rating_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_softwareapp_rating_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_softwareapp_rating_manual_global', esc_html($_POST['seopress_pro_rich_snippets_softwareapp_rating_manual_global']));
    }
    //Service
    if (isset($_POST['seopress_pro_rich_snippets_service_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_name', esc_html($_POST['seopress_pro_rich_snippets_service_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_name_cf', esc_html($_POST['seopress_pro_rich_snippets_service_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_name_tax', esc_html($_POST['seopress_pro_rich_snippets_service_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_type', esc_html($_POST['seopress_pro_rich_snippets_service_type']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_type_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_type_cf', esc_html($_POST['seopress_pro_rich_snippets_service_type_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_type_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_type_tax', esc_html($_POST['seopress_pro_rich_snippets_service_type_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_type_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_type_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_type_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_description'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_description', esc_html($_POST['seopress_pro_rich_snippets_service_description']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_description_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_description_cf', esc_html($_POST['seopress_pro_rich_snippets_service_description_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_description_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_description_tax', esc_html($_POST['seopress_pro_rich_snippets_service_description_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_description_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_description_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_description_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_img', esc_html($_POST['seopress_pro_rich_snippets_service_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_service_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_img_cf', esc_html($_POST['seopress_pro_rich_snippets_service_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_img_tax', esc_html($_POST['seopress_pro_rich_snippets_service_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_service_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_service_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_service_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_area'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_area', esc_html($_POST['seopress_pro_rich_snippets_service_area']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_area_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_area_cf', esc_html($_POST['seopress_pro_rich_snippets_service_area_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_area_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_area_tax', esc_html($_POST['seopress_pro_rich_snippets_service_area_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_area_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_area_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_area_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_name'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_name', esc_html($_POST['seopress_pro_rich_snippets_service_provider_name']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_name_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_name_cf', esc_html($_POST['seopress_pro_rich_snippets_service_provider_name_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_name_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_name_tax', esc_html($_POST['seopress_pro_rich_snippets_service_provider_name_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_name_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_name_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_provider_name_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lb_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lb_img', esc_html($_POST['seopress_pro_rich_snippets_service_lb_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lb_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lb_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lb_img_cf', esc_html($_POST['seopress_pro_rich_snippets_service_lb_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lb_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lb_img_tax', esc_html($_POST['seopress_pro_rich_snippets_service_lb_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lb_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_mobility'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_mobility', esc_html($_POST['seopress_pro_rich_snippets_service_provider_mobility']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_mobility_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_mobility_cf', esc_html($_POST['seopress_pro_rich_snippets_service_provider_mobility_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_mobility_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_mobility_tax', esc_html($_POST['seopress_pro_rich_snippets_service_provider_mobility_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_provider_mobility_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_provider_mobility_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_provider_mobility_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_slogan'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_slogan', esc_html($_POST['seopress_pro_rich_snippets_service_slogan']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_slogan_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_slogan_cf', esc_html($_POST['seopress_pro_rich_snippets_service_slogan_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_slogan_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_slogan_tax', esc_html($_POST['seopress_pro_rich_snippets_service_slogan_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_slogan_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_slogan_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_slogan_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_street_addr'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_street_addr', esc_html($_POST['seopress_pro_rich_snippets_service_street_addr']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_street_addr_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_street_addr_cf', esc_html($_POST['seopress_pro_rich_snippets_service_street_addr_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_street_addr_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_street_addr_tax', esc_html($_POST['seopress_pro_rich_snippets_service_street_addr_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_street_addr_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_street_addr_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_street_addr_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_city'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_city', esc_html($_POST['seopress_pro_rich_snippets_service_city']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_city_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_city_cf', esc_html($_POST['seopress_pro_rich_snippets_service_city_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_city_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_city_tax', esc_html($_POST['seopress_pro_rich_snippets_service_city_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_city_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_city_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_city_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_state'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_state', esc_html($_POST['seopress_pro_rich_snippets_service_state']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_state_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_state_cf', esc_html($_POST['seopress_pro_rich_snippets_service_state_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_state_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_state_tax', esc_html($_POST['seopress_pro_rich_snippets_service_state_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_state_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_state_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_state_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_pc'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_pc', esc_html($_POST['seopress_pro_rich_snippets_service_pc']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_pc_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_pc_cf', esc_html($_POST['seopress_pro_rich_snippets_service_pc_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_pc_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_pc_tax', esc_html($_POST['seopress_pro_rich_snippets_service_pc_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_pc_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_pc_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_pc_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_country'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_country', esc_html($_POST['seopress_pro_rich_snippets_service_country']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_country_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_country_cf', esc_html($_POST['seopress_pro_rich_snippets_service_country_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_country_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_country_tax', esc_html($_POST['seopress_pro_rich_snippets_service_country_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_country_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_country_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_country_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lat'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lat', esc_html($_POST['seopress_pro_rich_snippets_service_lat']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lat_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lat_cf', esc_html($_POST['seopress_pro_rich_snippets_service_lat_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lat_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lat_tax', esc_html($_POST['seopress_pro_rich_snippets_service_lat_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lat_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lat_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_lat_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lon'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lon', esc_html($_POST['seopress_pro_rich_snippets_service_lon']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lon_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lon_cf', esc_html($_POST['seopress_pro_rich_snippets_service_lon_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lon_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lon_tax', esc_html($_POST['seopress_pro_rich_snippets_service_lon_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_lon_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_lon_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_lon_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_tel'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_tel', esc_html($_POST['seopress_pro_rich_snippets_service_tel']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_tel_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_tel_cf', esc_html($_POST['seopress_pro_rich_snippets_service_tel_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_tel_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_tel_tax', esc_html($_POST['seopress_pro_rich_snippets_service_tel_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_tel_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_tel_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_tel_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_price'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_price', esc_html($_POST['seopress_pro_rich_snippets_service_price']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_price_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_price_cf', esc_html($_POST['seopress_pro_rich_snippets_service_price_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_price_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_price_tax', esc_html($_POST['seopress_pro_rich_snippets_service_price_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_service_price_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_service_price_manual_global', esc_html($_POST['seopress_pro_rich_snippets_service_price_manual_global']));
    }
    //Review
    if (isset($_POST['seopress_pro_rich_snippets_review_item'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item', esc_html($_POST['seopress_pro_rich_snippets_review_item']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_item_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item_cf', esc_html($_POST['seopress_pro_rich_snippets_review_item_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_item_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item_tax', esc_html($_POST['seopress_pro_rich_snippets_review_item_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_item_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item_manual_global', esc_html($_POST['seopress_pro_rich_snippets_review_item_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_item_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item_type', esc_html($_POST['seopress_pro_rich_snippets_review_item_type']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_item_type_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item_type_cf', esc_html($_POST['seopress_pro_rich_snippets_review_item_type_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_item_type_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item_type_tax', esc_html($_POST['seopress_pro_rich_snippets_review_item_type_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_item_type_manual_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_item_type_manual_global', esc_html($_POST['seopress_pro_rich_snippets_review_item_type_manual_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_img'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_img', esc_html($_POST['seopress_pro_rich_snippets_review_img']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_img_manual_img_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_img_manual_img_global', esc_html($_POST['seopress_pro_rich_snippets_review_img_manual_img_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_img_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_img_cf', esc_html($_POST['seopress_pro_rich_snippets_review_img_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_img_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_img_tax', esc_html($_POST['seopress_pro_rich_snippets_review_img_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_img_manual_img_library_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_img_manual_img_library_global', esc_html($_POST['seopress_pro_rich_snippets_review_img_manual_img_library_global']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_img_manual_img_library_global_width'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_img_manual_img_library_global_width', esc_html($_POST['seopress_pro_rich_snippets_review_img_manual_img_library_global_width']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_img_manual_img_library_global_height'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_img_manual_img_library_global_height', esc_html($_POST['seopress_pro_rich_snippets_review_img_manual_img_library_global_height']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_rating'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_rating', esc_html($_POST['seopress_pro_rich_snippets_review_rating']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_rating_cf'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_rating_cf', esc_html($_POST['seopress_pro_rich_snippets_review_rating_cf']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_rating_tax'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_rating_tax', esc_html($_POST['seopress_pro_rich_snippets_review_rating_tax']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_review_rating_manual_rating_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_review_rating_manual_rating_global', esc_html($_POST['seopress_pro_rich_snippets_review_rating_manual_rating_global']));
    }
    //Custom
    if (isset($_POST['seopress_pro_rich_snippets_custom'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_custom', esc_html($_POST['seopress_pro_rich_snippets_custom']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_custom_manual_custom_global'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_custom_manual_custom_global', $_POST['seopress_pro_rich_snippets_custom_manual_custom_global']);
    }
}
