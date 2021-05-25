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
class Enqueue extends Base
{

    public function register() {
        \add_filter(WPMETEOR_TEXTDOMAIN . '-frontend-rewrite', [$this, 'inject'], 2, 2);
        // LiteSpeed Cache compatibility is done by adding data-no-optimize="true"
        // WP-Rocket compatibility
        \add_filter('rocket_excluded_inline_js_content', [$this, 'exclude_js']);
        // Autoptimize compatibility
        \add_filter('autoptimize_filter_js_exclude', [$this, 'exclude_js']);
    }

    public function exclude_js($excluded_js)
    {
        if (is_array($excluded_js)) {
            $excluded_js[] = '_wpmeteor=';
        } else {
            $excluded_js .= ',_wpmeteor=,';
        }
        return $excluded_js;
    }

    public function inject($buffer, $settings)
    {
        if (!$this->canRewrite)
            return $buffer;

        // $_wpmeteor = \apply_filters(WPMETEOR_TEXTDOMAIN . '-frontend-adjust-wpmeteor', [], \wpmeteor_get_settings());
        $_wpmeteor = \apply_filters(WPMETEOR_TEXTDOMAIN . '-frontend-adjust-wpmeteor', [], \wpmeteor_get_settings());
        $_wpmeteor['v'] = WPMETEOR_VERSION;

        // LiteSpeed Cache  data-no-optimize=\"true\"
        return preg_replace('/(<head\b[^>]*?>)/i', "\${1}<script data-wpmeteor-nooptimize=\"true\" " . constant('WPMETEOR_EXTRA_ATTRS') . ">var _wpmeteor=" . json_encode($_wpmeteor) . ";</script>", $buffer, 1);
    }
}
