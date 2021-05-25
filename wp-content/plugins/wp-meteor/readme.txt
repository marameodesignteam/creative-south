=== WP Meteor Page Speed Optimization Topping ===
Contributors: aguidrevitch
Donate link: 
Tags: pagespeed, optimize, performance, page, speed
Requires at least: 4.5
Tested up to: 5.7
Stable tag: trunk
Requires PHP: 5.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WP Meteor is a completely new way of optimizing your page speed. It works even on top of your existing optimizations.

== Description ==

WP Meteor is a completely new way of optimizing your page speed. It works even on top of your existing optimizations, including:

* Autoptimize
* WP Rocket
* WP Total Cache
* WP Super Cache
* LiteSpeed Cache

WP Meteor is not compatible with:

* Nitropack
* Phastpress

WP Meteor is known to have delay issues with:

* Elementor Offcanvas addon

= WARNING =

The plugin might not work for someone, that's expected - delaying scripts has its downsides. Also, the plugin logics is quite complicated, so bugs happen.

If your site is business-critical or e-commerce, please test carefully all your business-critical pages, forms and/or your checkout process. Feel free to [create an Issue](https://wordpress.org/support/plugin/wp-meteor/#new-topic-0) if something doesn't work.

The plugin is made to leave no trace on the disk or in the database upon deactivation and removal, so don't hesitate to install and test, may be your site suits well for that kind of optimization.

Don't forget to test the site carefully after plugin installation. If you are not happy with the result - you can either uninstall the plugin, or [create an Issue](https://wordpress.org/support/plugin/wp-meteor/#new-topic-0), I will either fix the problem, or list a compatibility issue here.

= ONE MORE WARNING =

Infinite delay will delay your GA and GTM until user interaction. I would not recommed to use it, but it is still there because multiple users requested it. Use it with caution and at your own risk. 

= HOW IT WORKS =

If user doesn't start interacting with page immediately, WP Meteor postpones loading and firing scripts until after page gets rendered, giving you **2x-5x** boost in your Pagespeed metric.

This postponement in script loading greatly improves perceived load times for your visitors. It also significantly improves the following **important SEO metrics**:

* [Page Speed](https://developers.google.com/speed/pagespeed/insights/)
* [Largest Contentful Paint (LCP)](https://web.dev/lcp/)
* [Time To Interactive (TTI)](https://web.dev/tti/)
* [Total Blocking Time (TBT)](https://web.dev/tbt/)

In May 2021, Google will incorporate [multiple page speed metrics](https://developers.google.com/search/blog/2020/11/timing-for-page-experience) as a search signal in the ranking algorythm

= PREVIEW BEFORE INSTALL = 

Test your site here, before you even install the plugin: 

[https://preview.wp-meteor.com/](https://preview.wp-meteor.com/). 

Though the preview is not perfect, as fonts and images might be (and will be) missing due to the CORS policies, you'll still be able to quickly evaluate the plugin. Keep in mind, if the preview doesn't work, that's not the end of the world, as the plugin still might work when installed on your site!

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-meteor` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings -> WP Meteor screen to configure the plugin


== Frequently Asked Questions ==

= How to exclude a page from optimization =

Use `wpmeteor_enable` filter and return false to completely disable WP Meteor, see [Issue](https://wordpress.org/support/topic/exclude-a-script-from-lazy-load/) for an example

= How to exclude a script from optimization =

I'm working on an UI for that, but meanwhile, you can use data-wpmeteor-nooptimize="true" script attribute to exclude it from optimization. You might also need to exclude jQuery if your excluded script relies on it, you've been warned!

= Is it good for SEO? =

Yes, because all that matters is visitor experience, and WP Meteor improves that experience with a lot faster load times

= How can I easily compare speed with/without WP Meteor ? =

Once WP Meteor is enabled, you can add **?wpmeteordisable** query string parameter to a page URL in order to load the page without optimizations

== Changelog ==

1.5.1 - Emulate Elementor Powerpack Pro menu
1.5.0 - wpmeteor_enabled filter added, to allow to disable optimizations ocasionally
1.4.9 - document.write override allowed for those who know how to do it better (egDivi editor)
1.4.8 - Divi theme builder compatibility fixed
1.4.7 - SiteGround Optimizer + WP Rocket issue fixed
1.4.6 - Elementor Offcanvas double animation fix [Issue](https://wordpress.org/support/topic/menu-submenu-not-showing-in-elementor/)
1.4.5 - Elementor Offcanvas animations suppressed [Issue](https://wordpress.org/support/topic/menu-submenu-not-showing-in-elementor/)
1.4.4 - Elementor Entrance Animations issues fixed
1.4.3 - Elementor Entrance Animations support added
1.4.2 - Removed override for currentTarget for replayed events, fixes some navigation menus
1.4.1 - For WP Rocket compatibility, using rocket_buffer filter to inject javascript
1.4.0 - Getting rid of { passive: true } for replayed pointer events
1.3.9 - DOMContentLoaded propagation to window object, proper event handler bindings
1.3.8 - Better fronted detection to avoid rewriting AJAX and REST requests
1.3.7 - Proper contexts for domcontentloaded and window.onload event handlers
1.3.6 - Better jQuery.ready handling
1.3.5 - CookieBot compatibility [Issue](https://wordpress.org/support/topic/error-with-my-website-2/) fixed 
1.3.4 - Stopping click propagation when capturing events [Issue](https://wordpress.org/support/topic/great-plugin-but-causes-double-tap-issue-with-safari/)
1.3.3 - Better script loading in Firefox, scripts with both src and inline loading fixed [Issue](https://wordpress.org/support/topic/checkout-page-error-12/)
1.3.2 - Better delayed events for mobile [Issue](https://wordpress.org/support/topic/great-plugin-but-causes-double-tap-issue-with-safari/)
1.3.1 - Click handling in mobile safari improved [Issue](https://wordpress.org/support/topic/great-plugin-but-causes-double-tap-issue-with-safari/)
1.3.0 - Gutenberg save failure fixed
1.2.9 - jQuery mockup fixed to support window.load inside ready() function
1.2.8 - Bug breaking header tags fixed
1.2.7 - Phastpress compatibility dropped
1.2.6 - Minor improvement to fire domcontentloaded and window.onload for non-optimized scripts
1.2.5 - Support for Autoptimize native lazyload
1.2.4 - Delayed click/mouseover/mouseout events support added
1.2.3 - Native WP Rocket lazyload support
1.2.2 - Phastpress compatibility
1.2.1 - stripped lazysizes added in, with bgsizes plugin
1.2.0 - minor cleanup
1.1.9 - simple lazyload polyfill
1.1.8 - infinite delay added
1.1.7 - rewriting redone to support Google AMP and other plugins that initialize late
1.1.6 - working support Beaver Builder / Edit Mode
1.1.5 - working support Elementor / Edit Mode, Google AMP, AMP for WP
1.1.4 - support for Elementor / Edit Mode
1.1.3 - support for AMP for WP plugin added
1.1.2 - support for Google AMP plugin added
1.1.1 - readme.txt updated, warning added that it might not work for someone
1.1.0 - JetPack compatibility fixes
1.0.9 - ?wpmeteornopreload added to allow testing disabling preload
1.0.8 - data-cfasync="false" added to optimized scripts if behind CloudFlare
1.0.7 - ?wpmeteorcfasync parameter added to test disabling CloudFlare optimizations
1.0.6 - another iteration on domcontentloaded and window.onload handlers, jQuery mock rewrite
1.0.5 - better handling for broken domcontentloaded and window.onload handlers, better jQuery mock
1.0.4 - better cleanup on plugin deactivation
1.0.3 - readme.txt updated
1.0.2 - readme.txt updated
1.0.1 - minor Settings panel improvements
1.0.0 - initial release
