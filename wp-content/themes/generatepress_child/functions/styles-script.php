<?php

 //remove generate press styles
add_action('after_setup_theme', 'remove_generate_press_css');
function remove_generate_press_css()
{
    remove_action('wp_enqueue_scripts', 'generate_enqueue_dynamic_css', 50);
    remove_action('wp_enqueue_scripts', 'generate-style-css', 50);
    remove_action('wp_enqueue_scripts', 'generate_enqueue_google_fonts', 50);
}

add_action('wp_enqueue_scripts', function () {
    wp_deregister_script('generate-style');
}, 50);


// remove wordpress's jquery, add mine
function my_jquery_enqueue()
{
    wp_deregister_script('jquery');

    wp_enqueue_script('jquery', trailingslashit(get_stylesheet_directory_uri())  . 'js/jquery.min.js', array(), "3.4.1", true);

    wp_enqueue_script('jquery');
}

add_action('wp_enqueue_scripts', 'generatepress_child_enqueue_scripts', 100);
function generatepress_child_enqueue_scripts()
{
    $version = '1.0.9';
    // wp_enqueue_style('font-open-sans-pro', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap', null, null);
    // wp_enqueue_style('font-awesome-official-css', 'https://use.fontawesome.com/releases/v5.9.0/css/all.css', null, null);
    wp_enqueue_style('main-style', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array(), $version);
    wp_enqueue_script('main-js', trailingslashit(get_stylesheet_directory_uri()) . 'js/main.min.js', ['jquery'], array(), time());
}

//styles and scripts
if (!function_exists('generate_scripts')) {
    function generate_scripts()
    {
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $dir_uri = get_template_directory_uri();

        if (function_exists('wp_script_add_data')) {
            wp_enqueue_script('generate-classlist', $dir_uri . "/js/classList{$suffix}.js", [], GENERATE_VERSION, true);
            wp_script_add_data('generate-classlist', 'conditional', 'lte IE 11');
        }

        wp_enqueue_script('generate-menu', $dir_uri . "/js/menu{$suffix}.js", [], GENERATE_VERSION, true);
        wp_enqueue_script('generate-a11y', $dir_uri . "/js/a11y{$suffix}.js", [], GENERATE_VERSION, true);

        if ('click' === generate_get_option('nav_dropdown_type') || 'click-arrow' === generate_get_option('nav_dropdown_type')) {
            wp_enqueue_script('generate-dropdown-click', $dir_uri . "/js/dropdown-click{$suffix}.js", ['generate-menu'], GENERATE_VERSION, true);
        }

        if ('enable' === generate_get_option('nav_search')) {
            wp_enqueue_script('generate-navigation-search', $dir_uri . "/js/navigation-search{$suffix}.js", ['generate-menu'], GENERATE_VERSION, true);
        }

        if ('enable' === generate_get_option('back_to_top')) {
            wp_enqueue_script('generate-back-to-top', $dir_uri . "/js/back-to-top{$suffix}.js", [], GENERATE_VERSION, true);
        }

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            //wp_enqueue_script('comment-reply');
        }
    }
}
