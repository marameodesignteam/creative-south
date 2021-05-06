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
      // $mediaAttached = get_attached_media('', 41);  
      
      // foreach($mediaAttached as $item) : 
      //   $type = $item->post_mime_type;
      //   //guid
      // endforeach;
      $hide_footer = get_field('hide_footer');
      if(!$hide_footer) :
      ?>
    <footer class="site-info" <?php generate_do_microdata('footer'); ?>>
    
    <div class="footer-block">
      <div class="container">
        <div class="row">
          <div class="col-md-3">
              <a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/logo-footer.png" alt="Creative South"></a>
          </div>
          <div class="col-md-2">
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
          <div class="col-md-2">
              <div class="footer-description">Submit your business for inclusion on the Creative South website</div>
          </div>
          <div class="col-md-2">
            <div class="menu-footer">
              <h2 id="footer-menu-nav-2-id" class="sr-only">Footer Navigation</h2>
                <nav aria-labelledby="footer-menu-nav-2-id">
                    <ul>
                      <li><a href="/contact-us">Contact Us</a></li>
                      <li><a href="/legals">Legals</a></li>
                    </ul>
              </nav>
            </div>
          </div>
          <div class="col-md-3 partners">
                <ul class="d-flex">
                  <li><a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/southern-tablesands-arts.png" alt="Southern Tablesands Arts"></a></li>
                  <li><a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/south-east-arts.png" alt="South East Arts"></a></li>
                  <li><a href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/nsw.png" alt="NSW Goverment"></a></li>
                </ul>
          </div>
        </div>
      </div>
    </div>
      <script>
          function showOnMap(place_id) {
            jQuery("#post-" + place_id + " .place_title").click();
            // jQuery("#post-" + place_id + " .collapseTour-item").slideDown();
            // jQuery("#post-" + place_id + " .more-info").addClass('active');
          }

          function showLessInfo(place_id) {
            jQuery("#post-" + place_id + " .collapseTour-item").slideUp();
            jQuery("#post-" + place_id + " .more-info").removeClass('active');
          }

          function showMoreInfo(place_id) {
            jQuery("#post-" + place_id + " .collapseTour-item").slideDown();
            jQuery("#post-" + place_id + " .more-info").addClass('active');
          }

          function showOnMapDes(place_id){
              jQuery("#post-" + place_id + " .place_title").click();
              // jQuery("#post-" + place_id + " .more-info").toggleClass('active');
              // jQuery("#post-" + place_id + " .collapseTour-item").slideToggle();
          }
          
          function scrollToMoreInfoMobi(place_id) {
            // Close all location descriptions except the opening one place.id
            jQuery(".collapseTour-item").each(function() {
              var current_id = this.id.replace('collapseTour-', '');
              if (current_id != place_id) {
                if (jQuery('#post-' + current_id + ' .more-info.active').length) {
                  jQuery('#collapseTour-' + current_id).slideUp();
                  jQuery('#post-' + current_id + ' .more-info').removeClass('active');
                }
              } else {
                if (!jQuery('#post-' + current_id + ' .more-info.active').length) {
                  jQuery('#collapseTour-' + current_id).slideDown();
                  jQuery('#post-' + current_id + ' .more-info').addClass('active');
                }
              }
            });

            setTimeout(function() {
              jQuery('html, body').animate({
                scrollTop: jQuery('#fullMap').height() + 75
              }, 250);

              var position = jQuery('#post-' + place_id + ' .position').text();
              var i;
              var top_position = 0;
              for (i = 1; i < position; i++) {
                if (jQuery('#listing-item-' + i).length) {
                  top_position += jQuery('#listing-item-' + i).height() + 25;
                }
              }

              $('.listing-map').animate({
                scrollTop: top_position
              }, 250);
            }, 250);
          }

          function clickFavourite(place_id) {
            jQuery('#mylist-' + place_id).click();
          }
          jQuery('body').on('DOMSubtreeModified', '.mylist_btn', function(){
            var elem_html = jQuery(this).html();
            var fake_btn_id = '#' + this.id + '_fake';
            if (elem_html.indexOf('Add') !== -1) {
              jQuery(fake_btn_id).html('<span class="add-text"><i class="fas fa-plus-circle" aria-hidden="true"></i>Add to Trip</span>');
            }
            if (elem_html.indexOf('Delete') !== -1) {
              jQuery(fake_btn_id).html('<span class="delete-text"><i class="fas fa-minus-circle" aria-hidden="true"></i>Delete from Trip</span>');
            }
          });

          jQuery('body').on('click','.tour-list .js-gd-remove-mylist.btn-tour',function() {
              location.reload();
	        });

      </script>
    </footer><!-- .site-info -->
    <?php endif; if(is_front_page()) : ?>
    <div class="modal fade modalcustom" id="welcomeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <!-- <i class="fas fa-times" aria-hidden="true"></i> -->
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/close-menu.png" alt="Loading">
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
    <!-- Loader -->
    <div class="map-loader js-map-loader"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/map-loader.svg" alt="Loading"></div>
    <?php
    }
}
