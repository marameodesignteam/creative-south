<?php

/**
 * WP_Meteor
 *
 * @package   WP_Meteor
 * @author    Aleksandr Guidrevitch <alex@excitingstartup.com>
 * @copyright 2020 wp-meteor.com
 * @license   GPL 2.0+
 * @link      https://wp-meteor.com
 */

namespace WP_Meteor\Frontend;
/**
 * Enqueue stuff on the frontend
 */
class Rewrite extends Base
{
    public $priority = -3;
    /**
     * Initialize the class.
     *
     * @return void
     */
    public function register()
    {
        if (defined('LSCWP_V') && version_compare(constant('LSCWP_V'), '3.2.3.1 ', '>=')) {
            \add_action('litespeed_buffer_after', [$this, 'rewrite'], PHP_INT_MAX);
        } else if (defined('WP_ROCKET_VERSION') && !defined('SiteGround_Optimizer\VERSION')) {
            \add_action('rocket_buffer', [$this, 'rewrite'], PHP_INT_MAX);
        } else {
            \add_action('init', [$this, 'buffer_start'], $this->priority);
        }
    }

    public function buffer_start()
    {
        ob_start([$this, "rewrite"]);
    }

    public function rewrite($buffer)
    {
        if (!$this->canRewrite)
            return $buffer;

        $buffer = \apply_filters(WPMETEOR_TEXTDOMAIN . '-frontend-rewrite', $buffer, \wpmeteor_get_settings());
        return preg_replace('/<\/html>/i', "</html>\n<!-- Optimized with WP Meteor free Wordpress plugin https://wordpress.org/plugins/wp-meteor/ -->", $buffer);
    }
}
