<?php
/**
* Plugin Name: Creative South Custom Gutemberg Blocks
* Plugin URI: https://marameodesign.com
* Description: Custom Gutember Blocks for Creative South
* Version: 1.0
* Author: Marameo Design
**/

function block_variation_enqueue() {
  wp_enqueue_script(
    'block-variation-script',
    plugins_url( 'blocks.js', __FILE__ ),
    array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
    filemtime( plugin_dir_path( __FILE__ ) . '/blocks.js' )
  );
}
add_action( 'enqueue_block_editor_assets', 'block_variation_enqueue' );


function mmd_blocks_category($categories, $post) {
  array_unshift($categories, array(
    'slug' => 'mmd-custom-blocks',
    'title' => __('Creative South Custom Blocks', 'mmd-mmd-custom-blocks'),
  ));
  return $categories;
}

add_filter('block_categories', 'mmd_blocks_category', 10, 2);

function mmd_acf_init() {

    // check function exists
  if (function_exists('acf_register_block')) {
  
    acf_register_block(array(
      'name'				=> 'carousel',
      'title'				=> __('Carousel Block'),
      'description'		=> __('A Carousel Block'),
      'render_callback'	=> 'mmd_blocks_acf_block_render_cb',
      'category'			=> 'mmd-custom-blocks',
      'icon'				=> 'align-left',
      'keywords'			=> array( 'carousel'),
      'mode'      => 'edit',
      'supports' => array( 'mode' => false, 'anchor' => true ),
    ));

    acf_register_block(array(
      'name'				=> 'accordion',
      'title'				=> __('Accordion Block'),
      'description'		=> __('A Accordion Block'),
      'render_callback'	=> 'mmd_blocks_acf_block_render_cb',
      'category'			=> 'mmd-custom-blocks',
      'icon'				=> 'align-left',
      'keywords'			=> array( 'accordion' ),
      'mode'      => 'edit',
      'supports' => array( 'mode' => false, 'anchor' => true ),
    ));

    acf_register_block(array(
      'name'				=> 'primary-content',
      'title'				=> __('Primary Content Block'),
      'description'		=> __('A Primary Content Block'),
      'render_callback'	=> 'mmd_blocks_acf_block_render_cb',
      'category'			=> 'mmd-custom-blocks',
      'icon'				=> 'align-left',
      'keywords'			=> array( 'primary', 'content' ),
      'mode'      => 'edit',
      'supports' => array( 'mode' => false, 'anchor' => true ),
    ));
  }
}
add_action('acf/init', 'mmd_acf_init');

function mmd_blocks_acf_block_render_cb($block) {
  $slug = str_replace('acf/', '', $block['name']);
  if (file_exists(plugin_dir_path(__FILE__) . "templates/content-{$slug}.php")) {
    include(plugin_dir_path(__FILE__) . "templates/content-{$slug}.php");
  }
}
