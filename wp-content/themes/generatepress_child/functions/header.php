<?php
//change the order, logo first
if (! function_exists('generate_header_items')) {
  /**
   * Build the header contents.
   * Wrapping this into a function allows us to customize the order.
   *
   * @since 1.2.9.7
   */
  function generate_header_items()
  {
      generate_construct_logo();
      //generate_construct_header_widget();
      //generate_navigation_position();
  }
}
// add h1 to logo if home
if (! function_exists('generate_construct_logo')) {
  function generate_construct_logo()
  {
      $logo_url = (function_exists('the_custom_logo') && get_theme_mod('custom_logo')) ? wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full') : false;
      $logo_url = ($logo_url) ? $logo_url[0] : generate_get_option('logo');

      $logo_url = esc_url(apply_filters('generate_logo', $logo_url));
      $retina_logo_url = esc_url(apply_filters('generate_retina_logo', generate_get_option('retina_logo')));

      // If we don't have a logo, bail.
      if (empty($logo_url)) {
          return;
      }

      /**
       * generate_before_logo hook.
       *
       * @since 0.1
       */
      do_action('generate_before_logo');
      if (is_front_page()) {
          $attr = apply_filters('generate_logo_attributes', array(
              'class' => 'header-image',
              'alt'   => esc_attr(apply_filters('generate_logo_title', get_bloginfo('name', 'display'))),
              'src'   => $logo_url,
              'title' => esc_attr(apply_filters('generate_logo_title', get_bloginfo('name', 'display'))),
          ));
      }else{
          $attr = apply_filters('generate_logo_attributes', array(
              'class' => 'header-image',
              'alt'   => esc_attr(apply_filters('generate_logo_title', 'Home page - ' . get_bloginfo('name', 'display'))),
              'src'   => $logo_url,
              'title' => esc_attr(apply_filters('generate_logo_title', 'Home page - ' . get_bloginfo('name', 'display'))),
          ));
      }

      if ('' !== $retina_logo_url) {
          $attr['srcset'] = $logo_url . ' 1x, ' . $retina_logo_url . ' 2x';

          // Add dimensions to image if retina is set. This fixes a container width bug in Firefox.
          if (function_exists('the_custom_logo') && get_theme_mod('custom_logo')) {
              $data = wp_get_attachment_metadata(get_theme_mod('custom_logo'));

              if (! empty($data)) {
                  $attr['width'] = $data['width'];
                  $attr['height'] = $data['height'];
              }
          }
      }

      $attr = array_map('esc_attr', $attr);

      $html_attr = '';
      foreach ($attr as $name => $value) {
          $html_attr .= " $name=" . '"' . $value . '"';
      }

      $logoHtml = '<div class="site-logo">
          <a href="%1$s" rel="home">
              <img %3$s />
          </a>
      </div>';


      if (is_front_page()) {
          $logoHtml = '<div class="site-logo">

              <h1><img %3$s /></h1>

      </div>';
      }

      // Print our HTML.
  echo apply_filters('generate_logo_output', sprintf( // WPCS: XSS ok, sanitization ok.
      $logoHtml,
      esc_url(apply_filters('generate_logo_href', home_url('/'))),
      esc_attr(apply_filters('generate_logo_title', get_bloginfo('name', 'display'))),
      $html_attr
  ), $logo_url, $html_attr);

      /**
       * generate_after_logo hook.
       *
       * @since 0.1
       */
      do_action('generate_after_logo');
  }
}
//my header
if (! function_exists('generate_construct_header')) {
    add_action('generate_header', 'generate_construct_header');
    /**
     * Build the header.
     *
     * @since 1.3.42
     */
    function generate_construct_header()
    {
        ?>
    <div class="the-header">
   <header id="masthead" <?php generate_do_element_classes('header'); ?> <?php generate_do_microdata('header'); ?>>
      <div class="custom-fixed-header">
     <div <?php generate_do_element_classes('inside_header'); ?>>
     <!-- <div class="container insideHeader"> -->
     <div class="insideHeader">
        <div class="in_header">
          <button class="menu-toggle justify-content-center align-items-center"
                aria-controls="menu-mobile" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
                <i class="fas fa-times" aria-hidden="true"></i>
                <span class=" sr-only mobile-menu">Menu</span>
          </button>
          <?php
                /**
                 * generate_before_header_content hook.
                 *
                 * @since 0.1
                 */
                //do_action('generate_before_header_content');

        // Add our main header items.
        generate_header_items(); ?>
          <!-- <div class="header-right d-xl-flex d-lg-flex d-md-none d-none"> -->
          <div class="header-right d-xl-flex d-lg-flex">
            <div class="in_menu">
            <div class="overlay" aria-hidden="true"></div>
            <div id="menu-mobile">
              <div class="in_menu-mobile">
                <nav aria-labelledby="primarymenutitle" id="site-navigation" itemtype="https://schema.org/SiteNavigationElement" itemscope="itemscope" class="navbar navbar-expand-lg main-navigation">
                    <h2 class="sr-only" id="primarymenutitle">Primary menu</h2> 
                    <?php wp_nav_menu(array('menu'=>'primary-menu', 'theme_location'=> "primary-menu")); ?>
                    
                  </nav>
              </div>
            </div>
          </div>
          <div class="header-search">
            <button type="button" role="button" class="js-search-form"><span class="sr-only">Search form</span><i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </div>
          </div>
          <!-- Search popup -->
          <div class="search-from-container popup" role="dialog"
               id="search-form" aria-modal="true">
            <div class="bg-searchform"></div>
            <div class="popup-inner" role="document">
              <div class="popup-content">
                <div class="popup-body">
                  <form method="get" class="search-form"
                        action="<?php echo home_url(); ?>">
                    <label>
                      <span class="screen-reader-text">Search for:</span>
                      <input type="search" class="search-field"
                             placeholder="Search …" value="" name="s"
                             title="Search for:">
                    </label>
                    <button type="submit" class="search-submit"><span
                        class="sr-only">Search</span><i class="fas fa-search"
                                                        aria-hidden="true"></i>
                    </button>
                    <button type="button" data-dismiss="modal" class="sr-only">
                      Close
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     </div><!-- .inside-header -->
     </div>
   </header><!-- #masthead -->
   </div>    
   <?php
    }
}
