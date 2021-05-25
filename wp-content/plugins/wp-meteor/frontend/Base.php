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

use WP_Meteor\Engine\Base as Engine_Base;

abstract class Base extends Engine_Base
{
    public $priority = null;
    public $canRewrite = true;

    /**
     * Initialize the class.
     *
     * @return boolean
     */
    public function initialize()
    {
        if (preg_match('/wpmeteordisable/', $_SERVER['QUERY_STRING'])) {
            return;
        }

        if (defined('NITROPACK_VERSION')) {
            return;
        }

        if (defined('PHASTPRESS_VERSION')) {
            return;
        }

        add_action('wp', [$this, 'wp_hook'], $this->priority);
        // add_action('template_redirect', [$this, 'template_redirect_hook'], $this->priority);

        $this->register();
    }

    public function wp_hook()
    {
        $is = new \WP_Meteor\Engine\Is_Methods();
        if (!$is->is_frontend()) {
            $this->canRewrite = false;
            return;
        }

        if (!apply_filters('wpmeteor_enabled', true)) {
            $this->canRewrite = false;
        }

        if (function_exists('is_amp_endpoint') && \is_amp_endpoint()) {
            $this->canRewrite = false;
            return;
        }

        if (function_exists('ampforwp_is_amp_endpoint') && \ampforwp_is_amp_endpoint()) {
            $this->canRewrite = false;
            return;
        }

        if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->canRewrite = false;
            return;
        }

        if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->preview->is_preview_mode()) {
            $this->canRewrite = false;
            return;
        }

        if (class_exists('\FLBuilderModel') && \FLBuilderModel::is_builder_active()) {
            $this->canRewrite = false;
            return;
        }

        if (function_exists('vc_is_inline') && \vc_is_inline()) {
            $this->canRewrite = false;
            return;
        }

        if (function_exists('et_core_is_builder_used_on_current_request') && \et_core_is_builder_used_on_current_request()) {
            $this->canRewrite = false;
            return;
        }
    }

    public abstract function register();
}
