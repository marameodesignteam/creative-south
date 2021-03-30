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
        </div>
      </div>
    </div>
    </footer><!-- .site-info -->
    <?php
    }
}
