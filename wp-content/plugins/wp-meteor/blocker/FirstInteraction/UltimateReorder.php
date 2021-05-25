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

namespace WP_Meteor\Blocker\FirstInteraction;

/**
 * Provide Import and Export of the settings of the plugin
 */
class UltimateReorder extends Base
{
    public $adminPriority = -1;
    public $priority = 99;
    public $tab = 'ultimate';
    public $title = 'Maximum available speed';
    public $description = ""; //"Delays script loading to 2 seconds";
    public $disabledInUltimateMode = false;
    public $defaultEnabled = false;

    public $pattern = [['.*', '']];

    public function backend_display_settings()
    {
        echo '<div id="' . $this->id . '" class="ultimate"
                    data-prefix="' . $this->id . '" 
                    data-title="' . $this->title . '"></div>';
    }

    public function backend_save_settings($sanitized, $settings)
    {
        // $exists = isset($sanitized[$this->id]['enabled']);
        $sanitized[$this->id] = array_merge($settings[$this->id], $sanitized[$this->id] ?: []);
        $sanitized[$this->id]['enabled'] = true;
        return $sanitized;
    }

    /* triggered from wpmeteor_load_settings */
    public function load_settings($settings)
    {
        $settings[$this->id] = isset($settings[$this->id])
            ? $settings[$this->id]
            : ['enabled' => true];

        $settings[$this->id]['id'] = $this->id;
        $settings[$this->id]['delay'] = @$settings[$this->id]['delay'] ?: 1;
        $settings[$this->id]['after'] = 'REORDER';
        $settings[$this->id]['description'] = $this->description;
        return $settings;
    }

    public function frontend_rewrite($buffer, $settings)
    {
        // start excluding native WP Rocket images lazyload
        $buffer = preg_replace_callback('/<script([^>]*?)>(\s*window\.lazyLoadOptions\s*=)/i', function ($matches) {
            list($tag, $attrs, $content) = $matches;
            return "<script data-wpmeteor-nooptimize=\"true\" ${attrs}>${content}";
        }, $buffer);
        $buffer = preg_replace_callback('/<script[^>]*?>/i', function ($matches) {
            list ($tag) = $matches;
            if (preg_match('/wp-rocket\/assets\/js\/lazyload\/[^\/]+\/lazyload\.min\.js/i', $tag)) {
                return preg_replace('/<script/', '<script data-wpmeteor-nooptimize="true"', $tag);                
            }
            return $tag;
        }, $buffer);
        // end excluding native WP Rocket images lazyload

        $NATIVE_LAZYLOAD = false;
        // start excluding native Autoptimize images lazyload
        $buffer = preg_replace_callback('/<script([^>]*?)>(\s*window\.lazySizesConfig\s*=)/i', function ($matches) use (&$NATIVE_LAZYLOAD)  {
            $NATIVE_LAZYLOAD = true;
            list($tag, $attrs, $content) = $matches;
            return "<script data-wpmeteor-nooptimize=\"true\" ${attrs}>${content}";
        }, $buffer);
        $buffer = preg_replace_callback('/<script[^>]*?>/i', function ($matches) use (&$NATIVE_LAZYLOAD)  {
            $NATIVE_LAZYLOAD = true;
            list ($tag) = $matches;
            if (preg_match('/autoptimize\/classes\/external\/js\/lazysizes\.min\.js/i', $tag)) {
                return preg_replace('/<script/', '<script data-wpmeteor-nooptimize="true"', $tag);                
            }
            return $tag;
        }, $buffer);
        // end excluding nativeAutoptimize images lazyload

        if ($NATIVE_LAZYLOAD == true) {
            define('WPMETEOR_NATIVE_LAZYLOAD', true);
            $buffer = preg_replace('/(<head\b[^>]*?>)/', "\${1}<script data-wpmeteor-nooptimize=\"true\">var _wpmnl=!0;</script>", $buffer);
        }

        $buffer = preg_replace_callback('/<script[^>]*?>/i', function ($matches) {
            list($tag) = $matches;

            $EXTRA = constant('WPMETEOR_EXTRA_ATTRS') ?: '';

            $result = $tag;
            if (!preg_match('/\s+data-src=/i', $tag) 
                && !preg_match('/data-wpmeteor-nooptimize="true"/i', $tag)
                && !preg_match('/data-rocketlazyloadscript=/i', $tag, $matches)) {
                // if (preg_match('/\s+src=([\'"])(.*?)\1/i', $tag, $matches)) {
                if (preg_match('/\s+src=/i', $tag, $matches)) {
                    $result = preg_replace('/\s+src=/i', " ${EXTRA} data-wpmeteor-after=\"REORDER\" data-src=", $tag);
                    if (preg_match('/\s+type=/i', $tag)) {
                        $result = preg_replace('/\s+type=([\'"])text\/javascript\1/i', " type=\"javascript/blocked\"", $result);
                    } else {
                        $result = preg_replace('/<script/i', "<script type=\"javascript/blocked\"", $result);
                    }
                } else {
                    if (preg_match('/\s+type=/i', $tag)) {
                        $result = preg_replace('/\s+type=([\'"])text\/javascript\1/i', " ${EXTRA} data-wpmeteor-after=\"REORDER\" type=\"javascript/blocked\"", $tag);
                    } else {
                        $result = preg_replace('/<script/i', "<script ${EXTRA} data-wpmeteor-after=\"REORDER\" type=\"javascript/blocked\"", $tag);
                    }
                }
            }
            return preg_replace('/\s*data-wpmeteor-nooptimize="true"/i', '', $result);
        }, $buffer);

        // JetPack Likes workaround - lazyloading iframe so it don't window.postMessage early
        $buffer = preg_replace_callback('/<iframe[^>]*?>/i', function ($matches) {
            list($tag) = $matches;
            if (preg_match('/\s+src=(["\'])https?:\/\/widgets\.wp\.com/i', $tag)) {
                return preg_replace('/\s+src=/i', " " . constant('WPMETEOR_EXTRA_ATTRS') . " data-wpmeteor-after=\"REORDER\" data-src=", $tag);
            }
            return $tag;
        }, $buffer);
    
        return $buffer;
    }

    public function frontend_adjust_wpmeteor($wpmeteor, $settings)
    {
        if (!$settings[$this->id]['enabled']) {
            $wpmeteor['rdelay'] = 1000;
        } else {
            $wpmeteor['rdelay'] = (int) $settings[$this->id]['delay'] === 3
             ? 86400000 # one day
             : (int) $settings[$this->id]['delay'] * 1000;
        }
        return $wpmeteor;
    }
}
