<?php

/**
 * WP_Meteor
 *
 * @package   WP_Meteor
 * @author    Alex Guidrevitch <alex@excitingstartup.com>
 * @copyright 2020 wp-meteor.com
 * @license   GPL 2.0+
 * @link      https://wp-meteor.com
 */

namespace WP_Meteor\Frontend;
/**
 * Enqueue stuff on the frontend
 */
class EnqueueBlocker extends Base
{

    public function register()
    {
        \add_filter(WPMETEOR_TEXTDOMAIN . '-frontend-rewrite', [$this, 'inject'], 1, 2);
        // LiteSpeed Cache compatibility is done by adding data-no-optimize="true"
        // WP-Rocket compatibility
        \add_filter('rocket_excluded_inline_js_content', [$this, 'exclude_js']);
        // Autoptimize compatibility
        \add_filter('autoptimize_filter_js_exclude', [$this, 'exclude_js']);
    }

    public function exclude_js($excluded_js)
    {
        if (is_array($excluded_js)) {
            $excluded_js[] = '_wpmeteor.';
        } else {
            $excluded_js .= ', _wpmeteor.,';
        }
        return $excluded_js;
    }

    public function inject($buffer, $settings)
    {
        if (!$this->canRewrite)
            return $buffer;

        if (preg_match('/wpmeteordebug/', $_SERVER['QUERY_STRING'])) {
            $script = file_get_contents(__DIR__ . '/../assets/js/public/public-debug.js');
            $script = preg_replace('/\/\/# sourceMappingURL=public-debug.js.map/', '//# sourceMappingURL=' . \plugins_url('assets/js/public/public-debug.js.map', WPMETEOR_PLUGIN_ABSOLUTE), $script);
        } else {
            $script = file_get_contents(__DIR__ . '/../assets/js/public/public.js');
            $script = preg_replace('/\/\/# sourceMappingURL=public.js.map/', '', $script);
        }
        return preg_replace('/(<head\b[^>]*?>)/', "\${1}<script data-wpmeteor-nooptimize=\"true\" " . constant('WPMETEOR_EXTRA_ATTRS') . ">$script</script>", $buffer, 1);
    }
}
