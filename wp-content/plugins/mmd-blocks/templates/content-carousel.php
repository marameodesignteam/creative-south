<?php
/**
 * Block Name: Carousel
 *
 * This is the template that displays the carousel block.
 */
// common fields for all ridbc blocks


if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}else{
    $id = 'carousel-' . $block['id'];
}

$slides     = get_field( 'slides' );
$button_full = get_field('button');
$num_slides = count( $slides );


$arrowBackgroundPrev = "data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23FFFFFF' viewBox='0 0 8 8'%3e%3cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3e%3c/svg%3e";

$arrowBackgroundNext = "data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23FFFFFF' viewBox='0 0 8 8'%3e%3cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3e%3c/svg%3e";


if ( is_array( $slides ) && $num_slides > 0 ) :

    ?>
<div class="carousel-blocks custom-blocks full-width">
    <div id="<?php echo $id; ?>" class="carousel slide" data-keyboard="true" data-ride="false" data-interval="false">
        <style>
            /* setting as inline style */
            .carousel-control-prev-icon {
                background-image: url("<?php echo
    $arrowBackgroundPrev; ?>");
            }

            .carousel-control-next-icon {
                background-image: url("<?php echo
    $arrowBackgroundNext; ?>");
            }

        </style>
        <div class="site-width">
            <ul class="carousel-inner">

                <?php for ( $i = 0; $i < $num_slides; $i ++ ) :

                    $slide = $slides[ $i ];

                    $active = ( $i == 0 ) ? "active" : "";

                    $style = $slide["carousel_style"];

                    $image = wp_get_attachment_image( $slide["slide_background_image"], "carousel-size", FALSE, "class=mobile-vis-" . $mobile_vis );

                    ?>

                    <li class="carousel-item <?php echo $active; ?>">
                        <div class="slide-img"><?php echo $image; ?></div>
                    </li>

                <?php endfor; ?>

            </ul>

            <?php if ( $num_slides > 1 ) : ?>

                <a class="carousel-control-prev" href="#<?php echo $id; ?>"
                role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon"
                        aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#<?php echo $id; ?>"
                role="button" data-slide="next">
                    <span class="carousel-control-next-icon"
                        aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>

            <?php endif; ?>
        </div>
    </div>   
</div>     
<?php endif; ?>
<?php if(!is_front_page()) : ?>
<div class="header-block full-width background-black">
    <div class="header-block-inner">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/main-logo.png" alt="Southern Tablesands Arts">
        <div class="page-entry-title">
            <div class="container">
                <h2 class="entry-title-section"><?php the_title(); ?></h2>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
