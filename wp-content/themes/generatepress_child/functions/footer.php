<?php

//my footer
if (! function_exists('generate_construct_footer')) {
    add_action('generate_footer', 'generate_construct_footer');
    /**
     * Build our footer.
     *
     * @since 1.3.42
     */
    function generate_construct_footer()
    {
        ?>
    <footer class="site-info" <?php generate_do_microdata('footer'); ?>>
    <div class="footer-block">
      <div class="container">
        <div class="row">
          <div class="col-md-3">
              <a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/logo-footer.png" alt=""></a>
          </div>
          <div class="col-md-4">
            <div class="menu-footer">
              <h2 id="footer-menu-nav-id" class="sr-only">Main Navigation</h2>
                <nav aria-labelledby="footer-menu-nav-id">
                    <?php
                      wp_nav_menu(
                      array(
                          'menu' => 'primary-menu',
                          'menu_class' => 'primary-menu',
                      )
                    ); ?>
              </nav>
            </div>
          </div>
          <div class="col-md-5 partners">
                <ul class="d-flex">
                  <li><a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/southern-tablesands-arts.png" alt="Southern Tablesands Arts"></a></li>
                  <li><a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/south-east-arts.png" alt="South East Arts"></a></li>
                  <li><a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/nsw.png" alt="NSW Goverment"></a></li>
                </ul>
          </div>
        </div>
      </div>
    </div>
    </footer><!-- .site-info -->
    <?php
    }
}
