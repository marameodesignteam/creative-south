<?php
/**
 * Plugin Name: Breeze
 * Description: Breeze is a WordPress cache plugin with extensive options to speed up your website. All the options including Varnish Cache are compatible with Cloudways hosting.
 * Version: 1.1.11
 * Text Domain: breeze
 * Domain Path: /languages
 * Author: Cloudways
 * Author URI: https://www.cloudways.com
 * License: GPL2
 * Network: true
 */

/**
 * @copyright 2017  Cloudways  https://www.cloudways.com
 *
 *  This plugin is inspired from WP Speed of Light by JoomUnited.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! defined( 'BREEZE_PLUGIN_DIR' ) ) {
	define( 'BREEZE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'BREEZE_VERSION' ) ) {
	define( 'BREEZE_VERSION', '1.1.11' );
}
if ( ! defined( 'BREEZE_SITEURL' ) ) {
	define( 'BREEZE_SITEURL', get_site_url() );
}
if ( ! defined( 'BREEZE_MINIFICATION_CACHE' ) ) {
	define( 'BREEZE_MINIFICATION_CACHE', WP_CONTENT_DIR . '/cache/breeze-minification/' );
}
if ( ! defined( 'BREEZE_CACHEFILE_PREFIX' ) ) {
	define( 'BREEZE_CACHEFILE_PREFIX', 'breeze_' );
}
if ( ! defined( 'BREEZE_CACHE_CHILD_DIR' ) ) {
	define( 'BREEZE_CACHE_CHILD_DIR', '/cache/breeze-minification/' );
}
if ( ! defined( 'BREEZE_WP_CONTENT_NAME' ) ) {
	define( 'BREEZE_WP_CONTENT_NAME', '/' . wp_basename( WP_CONTENT_DIR ) );
}
if ( ! defined( 'BREEZE_BASENAME' ) ) {
	define( 'BREEZE_BASENAME', plugin_basename( __FILE__ ) );
}

define( 'BREEZE_CACHE_DELAY', true );
define( 'BREEZE_CACHE_NOGZIP', true );
define( 'BREEZE_ROOT_DIR', str_replace( BREEZE_WP_CONTENT_NAME, '', WP_CONTENT_DIR ) );

// Compatibility checks
require_once BREEZE_PLUGIN_DIR . 'inc/plugin-incompatibility/class-breeze-incompatibility-plugins.php';

// Helper functions.
require_once BREEZE_PLUGIN_DIR . 'inc/helpers.php';
require_once BREEZE_PLUGIN_DIR . 'inc/functions.php';

//action to purge cache
require_once( BREEZE_PLUGIN_DIR . 'inc/cache/purge-varnish.php' );
require_once( BREEZE_PLUGIN_DIR . 'inc/cache/purge-cache.php' );
require_once( BREEZE_PLUGIN_DIR . 'inc/cache/purge-per-time.php' );
// Handle post exclude if shortcode.
require_once( BREEZE_PLUGIN_DIR . 'inc/class-exclude-pages-by-shortcode.php' );

// Activate plugin hook
register_activation_hook( __FILE__, array( 'Breeze_Admin', 'plugin_active_hook' ) );
//Deactivate plugin hook
register_deactivation_hook( __FILE__, array( 'Breeze_Admin', 'plugin_deactive_hook' ) );

require_once( BREEZE_PLUGIN_DIR . 'inc/breeze-admin.php' );

if ( is_admin() || 'cli' === php_sapi_name() ) {

	require_once( BREEZE_PLUGIN_DIR . 'inc/breeze-configuration.php' );
	//config to cache
	require_once( BREEZE_PLUGIN_DIR . 'inc/cache/config-cache.php' );

	//cache when ecommerce installed
	require_once( BREEZE_PLUGIN_DIR . 'inc/cache/ecommerce-cache.php' );
	add_action(
		'init',
		function () {
			new Breeze_Ecommerce_Cache();
		},
		0
	);

} else {
	$cdn_conf        = breeze_get_option( 'cdn_integration' );
	$basic_conf      = breeze_get_option( 'basic_settings' );
	$config_advanced = breeze_get_option( 'advanced_settings' );

	if ( ! empty( $cdn_conf['cdn-active'] )
		 || ! empty( $basic_conf['breeze-minify-js'] )
		 || ! empty( $basic_conf['breeze-minify-css'] )
		 || ! empty( $basic_conf['breeze-minify-html'] )
		 || ! empty( $config_advanced['breeze-defer-js'] )
		 || ! empty( $config_advanced['breeze-move-to-footer-js'] )
	) {
		// Call back ob start
		ob_start( 'breeze_ob_start_callback' );
	}
}

// Call back ob start - stack
function breeze_ob_start_callback( $buffer ) {
	$conf = breeze_get_option( 'cdn_integration' );
	// Get buffer from minify
	$buffer = apply_filters( 'breeze_minify_content_return', $buffer );

	if ( ! empty( $conf ) || ! empty( $conf['cdn-active'] ) ) {
		// Get buffer after remove query strings
		$buffer = apply_filters( 'breeze_cdn_content_return', $buffer );
	}

	// Return content
	return $buffer;
}

// Minify

require_once( BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minify-main.php' );
require_once( BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minification-cache.php' );
add_action(
	'init',
	function () {
		new Breeze_Minify();

	},
	0
);
// CDN Integration
if ( ! class_exists( 'Breeze_CDN_Integration' ) ) {
	require_once( BREEZE_PLUGIN_DIR . 'inc/cdn-integration/breeze-cdn-integration.php' );
	require_once( BREEZE_PLUGIN_DIR . 'inc/cdn-integration/breeze-cdn-rewrite.php' );
	add_action(
		'init',
		function () {
			new Breeze_CDN_Integration();
		},
		0
	);
}

// Refresh cache for ordered products.
require_once BREEZE_PLUGIN_DIR . 'inc/class-breeze-woocommerce-product-cache.php';
/**
 * This function will update htaccess files after the plugin update is done.
 *
 * This function runs when WordPress completes its upgrade process.
 * It iterates through each plugin updated to see if ours is included.
 *
 * The plugin must be active while updating, otherwise this will do nothing.
 *
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/upgrader_process_complete
 * @since 1.1.3
 *
 * @param array $upgrader_object
 * @param array $options
 */
function breeze_after_plugin_update_done( $upgrader_object, $options ) {
	// If an update has taken place and the updated type is plugins and the plugins element exists.
	if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
		// Iterate through the plugins being updated and check if ours is there
		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin == BREEZE_BASENAME ) {
				// If the install is multi-site, we will add the option for all the blogs.
				if ( is_multisite() ) {
					$blogs = get_sites();
					if ( ! empty( $blogs ) ) {
						foreach ( $blogs as $blog_data ) {
							$blog_id = $blog_data->blog_id;
							switch_to_blog( $blog_id );
							// Add the option for each blog.
							// The visit on any blog will trigger the update to happen.
							add_option( 'breeze_new_update', 'yes', '', false );

							restore_current_blog();
						}
					}
				} else {
					// Add a new option to inform the install that a new version was installed.
					add_option( 'breeze_new_update', 'yes', '', false );
				}
			}
		}
	}
}

add_action( 'upgrader_process_complete', 'breeze_after_plugin_update_done', 10, 2 );

/**
 * This function is checking on init if there is a need to update htaccess.
 */
function breeze_check_for_new_version() {
	// When permalinks are reset, we also reset the config files.
	if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) ) {
		$to_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'permalink';
		if ( 'permalink' !== $to_action ) {
			check_admin_referer( 'options-options' );
		} else {
			check_admin_referer( 'update-permalink' );
		}
		// If the WP install is multi-site

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		// import these file in front-end when required.
		if ( ! class_exists( 'Breeze_Ecommerce_Cache' ) ) {
			//cache when ecommerce installed
			require_once( BREEZE_PLUGIN_DIR . 'inc/cache/ecommerce-cache.php' );
		}

		// import these file in front-end when required.
		if ( ! class_exists( 'Breeze_ConfigCache' ) ) {
			//config to cache
			require_once( BREEZE_PLUGIN_DIR . 'inc/cache/config-cache.php' );
		}

		if ( is_multisite() ) {
			// For multi-site we need to also reset the root config-file.
			Breeze_ConfigCache::factory()->write_config_cache( true );

			$blogs = get_sites();
			if ( ! empty( $blogs ) ) {
				foreach ( $blogs as $blog_data ) {
					$blog_id = $blog_data->blog_id;
					switch_to_blog( $blog_id );

					// if the settings are inherited, then we do not need to refresh the config file.
					$inherit_option = get_option( 'breeze_inherit_settings' );
					$inherit_option = filter_var( $inherit_option, FILTER_VALIDATE_BOOLEAN );

					// If the settings are not inherited from parent blog, then refresh the config file.
					if ( false === $inherit_option ) {
						// Refresh breeze-cache.php file
						Breeze_ConfigCache::factory()->write_config_cache();
					}

					restore_current_blog();
				}
			}
		} else {
			// For single site.
			// Refresh breeze-cache.php file
			Breeze_ConfigCache::factory()->write_config_cache();
		}
	}

	// This process can also be started by Wp-CLI.
	if ( ! empty( get_option( 'breeze_new_update', '' ) ) ) {
		// This needs to happen only once.
		if ( class_exists( 'Breeze_Configuration' ) && method_exists( 'Breeze_Configuration', 'update_htaccess' ) ) {
			Breeze_Configuration::update_htaccess();

		}

		// import these file in front-end when required.
		if ( ! class_exists( 'Breeze_Ecommerce_Cache' ) ) {
			//cache when ecommerce installed
			require_once( BREEZE_PLUGIN_DIR . 'inc/cache/ecommerce-cache.php' );
		}

		// import these file in front-end when required.
		if ( ! class_exists( 'Breeze_ConfigCache' ) ) {
			//config to cache
			require_once( BREEZE_PLUGIN_DIR . 'inc/cache/config-cache.php' );
		}

		// If the WP install is multi-site
		if ( is_multisite() ) {
			// For multi-site we need to also reset the root config-file.
			Breeze_ConfigCache::factory()->write_config_cache( true );

			$blogs = get_sites();
			if ( ! empty( $blogs ) ) {
				foreach ( $blogs as $blog_data ) {
					$blog_id = $blog_data->blog_id;
					switch_to_blog( $blog_id );

					// if the settings are inherited, then we do not need to refresh the config file.
					$inherit_option = get_option( 'breeze_inherit_settings' );
					$inherit_option = filter_var( $inherit_option, FILTER_VALIDATE_BOOLEAN );

					// If the settings are not inherited from parent blog, then refresh the config file.
					if ( false === $inherit_option ) {
						// Refresh breeze-cache.php file
						Breeze_ConfigCache::factory()->write_config_cache();
					}

					// Remove the option from all the blogs, meaning each one of them was already updated.
					delete_option( 'breeze_new_update' );

					restore_current_blog();
				}
			}
		} else {
			// For single site.

			// Refresh breeze-cache.php file
			Breeze_ConfigCache::factory()->write_config_cache();

			delete_option( 'breeze_new_update' );
		}
	}
}

add_action( 'admin_init', 'breeze_check_for_new_version', 99 );


add_action( 'wp_login', 'refresh_config_files', 10, 2 );

/**
 * Handles the config file reset.
 *
 * @param string $user_login $user->user_login
 * @param object $user WP_User
 *
 * @since 1.1.5
 */
function refresh_config_files( $user_login, $user ) {
	if ( in_array( 'administrator', (array) $user->roles, true ) ) {
		//The user has the "administrator" role
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
		// import these file in front-end when required.
		if ( ! class_exists( 'Breeze_Ecommerce_Cache' ) ) {
			//cache when ecommerce installed
			require_once( BREEZE_PLUGIN_DIR . 'inc/cache/ecommerce-cache.php' );
		}

		// import these file in front-end when required.
		if ( ! class_exists( 'Breeze_ConfigCache' ) ) {
			//config to cache
			require_once( BREEZE_PLUGIN_DIR . 'inc/cache/config-cache.php' );
		}
		if ( is_multisite() ) {
			$blogs = get_sites();
			// For multi-site we need to also reset the root config-file.
			Breeze_ConfigCache::factory()->write_config_cache( true );

			if ( ! empty( $blogs ) ) {
				foreach ( $blogs as $blog_data ) {
					$blog_id = $blog_data->blog_id;
					switch_to_blog( $blog_id );

					// if the settings are inherited, then we do not need to refresh the config file.
					$inherit_option = get_option( 'breeze_inherit_settings' );
					$inherit_option = filter_var( $inherit_option, FILTER_VALIDATE_BOOLEAN );
					// If the settings are not inherited from parent blog, then refresh the config file.
					if ( false === $inherit_option ) {
						// Refresh breeze-cache.php file
						Breeze_ConfigCache::factory()->write_config_cache();
					}
					restore_current_blog();
				}
			}
		} else {
			$current_file = WP_CONTENT_DIR . '/breeze-config/breeze-config.php';
			if ( file_exists( $current_file ) ) {
				$current_data = include $current_file; //phpcs:ignore
				if ( mb_strtolower( $current_data['homepage'] ) !== get_site_url() ) {
					// For single site.
					// Refresh breeze-cache.php file
					Breeze_ConfigCache::factory()->write_config_cache();
				}
			}
		}
	}
}