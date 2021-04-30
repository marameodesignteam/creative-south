<?php
/**
 * Block Name:  Primary Content Block
 *
 * This is the template that displays the Primary Content block.
 */

$content = get_field( 'content' );

if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}else{
    $id = 'primary-content-' . $block['id'];
}
?>
<?php if($content) : ?>
<div id="<?php echo $id; ?>" class="primary-content-blocks custom-blocks full-width background-black">
    <div class="in_custom-block">
       <div class="container">
            <div class="primary-content">
                <div class="row">
                    <div class="col creative-south-logo">
                        <img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/logo-header.png" alt="Creative South">
                    </div>
                    <div class="col col-auto cultural-logo">
                         <img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/cultural-treasure-maps.png" alt="Cultural Treasure Maps">
                    </div>
                </div>  
                <div class="primary-content-content">
                    <?php echo $content; ?>
                </div>
                <div class="second-navigation">
                    <h2 id="second-menu-nav-id" class="sr-only">Second Navigation</h2>
                    <nav aria-labelledby="second-menu-nav-id">
                        <ul class="row">
                            <li class="col-lg-3 col-md-6"><a href="#regionExplore">Choose </br> a REGION </br> to explore</a></li>
                            <li class="col-lg-3 col-md-6"><a href="#categoryDiscover">Select </br> a CATEGORY </br> like to discover</a></li>
                            <li class="col-lg-3 col-md-6"><a href="/southern-stories/">See </br> southern </br> HIGHLIGHTS</a></li>
                            <li class="col-lg-3 col-md-6"><a href="/map">See </br> EVERYTHING</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
       </div>
    </div>
</div>
<?php endif; ?>