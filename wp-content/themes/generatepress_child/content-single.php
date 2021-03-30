<?php
/**
 * The template for displaying single posts.
 *
 * @package GeneratePress
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$postid = get_the_ID();
?>
<div class="margin-overlay container">

<article id="post-<?php the_ID(); ?>" class="single-resource default" <?php generate_do_microdata('article'); ?>>
    <div class="inside-article">
        <div class="row article-inner">
            <div class="entry-header">
                <?php
                /**
                 * generate_before_entry_title hook.
                 *
                 * @since 0.1
                 */
                do_action('generate_before_entry_title');

                if (generate_show_title()) {
                    the_title('<h1 class="entry-title title" itemprop="headline">', '</h1>');
                }

                /**
                 * generate_after_entry_title hook.
                 *
                 * @since 0.1
                 *
                 * @hooked generate_post_meta - 10
                 */
                //do_action('generate_after_entry_title');

                //@@BM building the date
                ?>
            </div><!-- .entry-header -->
            <?php
            /**
             * generate_after_entry_header hook.
             *
             * @since 0.1
             *
             * @hooked generate_post_image - 10
             */
            //do_action('generate_after_entry_header');
            ?>
        </div>
        <div class="entry-content" itemprop="text">
            
            <?php the_content(); ?>
        </div><!-- .entry-content -->

        <?php
        /**
         * generate_after_entry_content hook.
         *
         * @since 0.1
         *
         * @hooked generate_footer_meta - 10
         */
        //do_action('generate_after_entry_content');

        /**
         * generate_after_content hook.
         *
         * @since 0.1
         */
        do_action('generate_after_content');
        ?>

        </div><!-- .inside-article -->
    </article><!-- #post-## -->  
  </div>
</div>

