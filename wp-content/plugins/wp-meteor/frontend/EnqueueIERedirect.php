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
class EnqueueIERedirect extends Base
{

    public function register()
    {
        \add_filter(WPMETEOR_TEXTDOMAIN . '-frontend-rewrite', [$this, 'inject'], 1, 2);
    }

    public function inject($buffer, $settings)
    {
        if (!$this->canRewrite)
            return $buffer;
        $script = file_get_contents(__DIR__ . '/../assets/js/public/ie-redirect.js');
        return preg_replace('/(<head\b[^>]*?>)/', "\${1}<script data-wpmeteor-nooptimize=\"true\" " . constant('WPMETEOR_EXTRA_ATTRS') . ">$script</script>", $buffer, 1);
    }
}
