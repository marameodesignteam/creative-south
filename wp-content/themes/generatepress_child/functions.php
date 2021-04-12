<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */
include_once(get_stylesheet_directory(__FILE__) . '/env.php');
include_once(get_stylesheet_directory(__FILE__) . '/helpers.php');
include("functions/styles-script.php");
include("functions/header.php");
include("functions/footer.php");
include("functions/image-sizes.php");

 //add menus programmatically

 function wpb_custom_new_menu()
 {
     register_nav_menu('header-menu', __('Header Menu'));
     register_nav_menu('footer-menu', __('Footer Menu'));
 }

 add_action('init', 'wpb_custom_new_menu');

//add options page
// if( function_exists('acf_add_options_page') ) {
// 	acf_add_options_page();
// }

//Add excerpt for page
add_post_type_support( 'page', 'excerpt' );

///Remove posts from side menu
//add_action('admin_menu', 'remove_default_post_type');
function remove_default_post_type() {
  remove_menu_page('edit.php');
}



function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function custom_style() {
    ?>
    <style type="text/css">
        .components-editor-notices__dismissible {
            display: none !important;
        } 
        body.gutenberg-editor-page .editor-post-title__block, body.gutenberg-editor-page .editor-default-block-appender, body.gutenberg-editor-page .editor-block-list__block {
            max-width: none !important;
        }
        .block-editor__container .wp-block {
            max-width: none !important;
        }
        /*code editor*/
        .edit-post-text-editor__body {
            max-width: none !important;	
            margin-left: 2%;
            margin-right: 2%;
        }
    </style>
<?php }
add_action( 'admin_footer', 'custom_style' );

add_filter('acf/save_post', 'update_marker_id', 20);
function update_marker_id($post_id) {
    error_log('>>>>>>>>>>>');
    error_log(get_post_type($post_id));
  if ( get_post_type($post_id) != 'trip_locations' ) {
    return;
  }
  $category_id = get_post_meta($post_id, 'category_id' );
  error_log($category_id);
  error_log($post_id);
  error_log('<<<<<<<');
  update_post_meta( $post_id, '_wpgmp_metabox_marker_id', serialize( [$category_id] ) ) ;
}
