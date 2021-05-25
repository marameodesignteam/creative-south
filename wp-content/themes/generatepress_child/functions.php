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
add_action('admin_menu', 'remove_default_post_type');
function remove_default_post_type() {
	remove_menu_page('edit.php');
    remove_menu_page( 'edit-comments.php' );
}

//Disable comments
function df_disable_comments_status() {
	return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);


// Allow editors to see access the Menus page under Appearance but hide other options
// Note that users who know the correct path to the hidden options can still access them
function hide_menu() {
    $user = wp_get_current_user();
   
   // Check if the current user is an Editor
   if ( in_array( 'editor', (array) $user->roles ) ) {
       
       // They’re an editor, so grant the edit_theme_options capability if they don’t have it
       if ( ! current_user_can( 'edit_theme_options' ) ) {
           $role_object = get_role( 'editor' );
           $role_object->add_cap( 'edit_theme_options' );
       }
       
       // Hide the Themes page
       remove_submenu_page( 'themes.php', 'themes.php' );
       // Hide the Widgets page
       remove_submenu_page( 'themes.php', 'widgets.php' );
       // Hide the Customize page
       remove_submenu_page( 'themes.php', 'customize.php' );
       // Remove Customize from the Appearance submenu
       global $submenu;
       unset($submenu['themes.php'][6]);
   }
}
add_action('admin_menu', 'hide_menu', 10);

	/* Script Admin */
function my_script(){ 
    $user = wp_get_current_user();
    if ( in_array( 'editor', (array) $user->roles ) ) {?>
    <style type="text/css">#dashboard_primary,#icl_dashboard_widget,
        #adminmenu li.menu-top#menu-appearance .wp-submenu li:last-child{
            display: none;                
        }
    </style><?php
}}
 add_action( 'admin_footer', 'my_script' );

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
        body .acf-fields>.acf-field.acf-field-6073ab2527699{
            display: none !important;
        }
    </style>
<?php }
add_action( 'admin_footer', 'custom_style' );

function custom_style_login() {
	?>
    <style type="text/css">
        body.login-action-login{
            background: #000;
        }
        .login h1 a {
            background-image: url( <?php echo get_stylesheet_directory_uri(); ?>/images/logo-header.png);
            background-size: 100% auto;
            height: 84px;
            width: 239px;
        }
        .login #backtoblog a, .login #nav a, .login h1 a{
            color: #fff;
        }
        .login #backtoblog a:hover, .login #nav a:hover, .login h1 a:hover{
            color: #ee3897;
        }
        .wp-core-ui .button-primary{
            background:#ee3897;
            border-color: #ee3897;
        }
        .wp-core-ui .button-primary:hover,
        .wp-core-ui .button-primary:focus{
            border-color: #ee3897;
            background: #fff;
            color: #ee3897;
        }
        .wp-social-login-provider-list img {
            max-width:100%;
        }
        .login form .input:focus, .login form input[type=checkbox]:focus, .login input[type=text]:focus{
            border-color: #ee3897;
            box-shadow: 0 0 0 1px #ee3897;
        }
        .login .button.wp-hide-pw .dashicons{
            color: #ee3897;
        }
       
    </style>
<?php }
add_action( 'login_head', 'custom_style_login' );

add_action( 'wp_ajax_update_map_params', 'update_map_params' );
add_action( 'wp_ajax_nopriv_update_map_params', 'update_map_params' );

function update_map_params() {
	$args = array(
		'post_type' => 'trip_locations',
		'posts_per_page' => -1
	);
	$query = new WP_Query($args);
	if ($query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			$category_names = get_post_meta($post_id, 'category_names' );

			$categs = [
				1 => 'Festivals',
				2 => 'Galleries & Studios',
				3 => 'Creative Retail & Markets',
				4 => 'Public Art',
				8 => 'Museum & Heritage',
				9 => 'Music & Performance',
				21 => 'Performing Arts Venue',
			];

			$category_id = [];
			if (!empty($category_names[0])) {

				$names = explode(',', $category_names[0]);
				foreach ($names as $name) {
					foreach ($categs as $cid => $cate) {
						if ($cate == trim($name)) $category_id[] = $cid;
					}
				}

				error_log(print_r($category_id, 1));
				update_post_meta( $post_id, '_wpgmp_metabox_marker_id', serialize($category_id) ) ;

			}
			update_post_meta( $post_id, '_wpgmp_map_id', serialize( [2] ) ) ;
		}
		wp_reset_postdata();
	}

	wp_die();
}

add_filter( 'body_class', 'itinerary_class_to_body' );
function itinerary_class_to_body( $classes ) {
	if (!empty($_GET['itinerary'])) {
		$classes[] = 'share-itinerary';
	}

	return $classes;
}

// /* ============================= Update New Regions ============================= */
// add_action('wp_ajax_trip_locations', 'trip_locations');
// add_action('wp_ajax_nopriv_trip_locations', 'trip_locations');
// function trip_locations() {
//   $trip_locations = new WP_Query([
//     'post_type' => 'trip_locations',
//     'posts_per_page' => -1,
//     'post_status' => 'publish',
//   ]);

//   foreach ($trip_locations->posts as $trip) {
//     $regions = get_field('regions',$trip->ID);
//     update_field('regions_new', $regions, $trip->ID);
//   }
// }
// /* ============================= Update OLD Regions ============================= */
// add_action('wp_ajax_trip_locations2', 'trip_locations2');
// add_action('wp_ajax_nopriv_trip_locations2', 'trip_locations2');
// function trip_locations2() {
//   $trip_locations = new WP_Query([
//     'post_type' => 'trip_locations',
//     'posts_per_page' => -1,
//     'post_status' => 'publish',
//   ]);

//   foreach ($trip_locations->posts as $trip) {
//     $regions_new = get_field('regions_new',$trip->ID);
//     update_field('regions', $regions_new, $trip->ID);
//   }
// }