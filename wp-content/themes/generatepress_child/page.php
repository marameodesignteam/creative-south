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
		<div class="region-selection full-width background-black">
			<div class="container">
				<h2 class="title-section">Choose the region you would like to explore</h2>
				<div class="row">
					<div class="col-md-6 region-selection-map">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/CSouth_Map_01.svg" alt="Southern Tablesands Arts">
					</div>
					<div class="col-md-6 region-selection-list">
						<ul>
							<li><a href="">Wollondilly</a></li>
							<li><a href="">Upper Lachlan</a></li>
							<li><a href="">Hilltops</a></li>
							<li><a href="">Wingecarribee (Southern Highlands)</a></li>
							<li><a href="">Goulburn Mulwaree</a></li>
							<li><a href="">Yass Valley </a></li>
							<li><a href="">Queanbeyan Palerang</a></li>
							<li><a href="">Eurobodalla</a></li>
							<li><a href="">Bega Valley</a></li>
							<li><a href="">Snowy Monaro</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
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