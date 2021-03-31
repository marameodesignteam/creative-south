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
    <?php if(is_front_page()) : ?>
    <div class="modal fade modalcustom" id="wellcomeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i class="fas fa-times" aria-hidden="true"></i>
        </button>
      <div class="modal-dialog modal-dialog-centered" role="document">
      
      <div class="modal-content">
        <div class="welcome-modal-content">
          <p>We acknowledge the Traditional Owners of the land on which we stand. We pay our respects to Elders past, present and emerging.</p>
          <p>We respect Aboriginal peoples as the First Peoples and custodians of NSW.</p>
        </div>
      </div>
      </div>
    </div>
    <?php endif; ?>
    <?php
    }
}
