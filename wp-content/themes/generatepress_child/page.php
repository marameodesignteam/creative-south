<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); 

//get_template_part('template-parts/content/content', 'header_template');

?>

<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
    <main id="main" <?php generate_do_element_classes( 'main' ); ?>>
        <?php

		
			
		while (have_posts()) : the_post();  ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php generate_do_microdata( 'article' ); ?>>
            <div class="inside-article">
                <div class="entry-content" itemprop="text">
                    <?php the_content(); ?>
                </div>
            </div>
        </article>


		<?php endwhile; ?>
        <?php

				/**
				 * generate_after_main_content hook.
				 *
				 * @since 0.1
				 */
				do_action('generate_after_main_content');
		?>
	<?php if (is_front_page()) :  ?>
		<!-- Write something -->
	<?php endif; ?>
    </main>
    <!-- #main -->


    
</div>
<!-- #primary -->
<?php
	/**
	 * generate_after_primary_content_area hook.
	 *
	 * @since 2.0
	 */
	do_action( 'generate_after_primary_content_area' );

	//generate_construct_sidebars();

get_footer();