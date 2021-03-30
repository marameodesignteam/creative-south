<?php
/**
 * The template for displaying Archive pages.
 *
 * @package GeneratePress
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();
$cpt = get_post_type();

?>
<div class="archive-cont full-width feat-news post-card">
	<div id="primary" <?php generate_do_element_classes('content'); ?>>
		<main id="main" <?php generate_do_element_classes('main'); ?>>
			<div class="container">
			<?php
            /**
             * generate_before_main_content hook.
             *
             * @since 0.1
             */
            do_action('generate_before_main_content');

            if (have_posts()) :

                /**
                 * generate_archive_title hook.
                 *
                 * @since 0.1
                 *
                 * @hooked generate_archive_title - 10
                 */
                do_action('generate_archive_title'); ?>
                <div class="post-list-container post-list post-list-resources">
				    <ul class="row">

					<?php while (have_posts()) : the_post(); ?>

                    <li class="col-xl-4 col-lg-6 col-md-6">

						<?php

              /*
               * Include the Post-Format-specific template for the content.
               * If you want to override this in a child theme, then include a file
               * called content-___.php (where ___ is the Post Format name) and that will be used instead.
               */
              $cpt = get_post_type();
            //   if (is_post_type_archive('insights')) {
            //       get_template_part('content-insights', get_post_format());
            //   } elseif (is_post_type_archive('portfolio')) {
            //       get_template_part('content-portfolio', get_post_format());
            //   } else {
            //       get_template_part('content', get_post_format());
            //   } 
                get_template_part( 'content', get_post_format() );
              ?>

						</li>

					<?php	endwhile; ?>

			    </ul>
            </div>        
				<?php	/**
                 * generate_after_loop hook.
                 *
                 * @since 2.3
                 */
                do_action('generate_after_loop');

                generate_content_nav('nav-below');

            else :

                get_template_part('no-results', 'archive');

            endif;

            /**
             * generate_after_main_content hook.
             *
             * @since 0.1
             */
            do_action('generate_after_main_content');
            ?>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php

	/**
	 * generate_after_primary_content_area hook.
	 *
	 * @since 2.0
	 */
	do_action('generate_after_primary_content_area');

	//generate_construct_sidebars();

	get_footer();
