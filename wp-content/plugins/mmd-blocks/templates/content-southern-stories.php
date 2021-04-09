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
    $id = 'southern-stories-' . $block['id'];
}
?>
<div id="<?php echo $id; ?>" class="southern-stories-blocks custom-blocks full-width background-black">
    <div class="in_custom-block">
       <div class="container">
            <div class="southern-stories-content">
                <h2 class="title-section">Snapshots from the Creative South</h2>
                <p class="subtitle">
                    <strong>Check out stories from the people who collect them - <a href="">click for more ></a></strong>
                </p>
                <div class="southern-stories-images">
                    <ul class="d-flex">
                        <li><a class="image-circle" href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/southern-tablesands-arts.png" alt="Southern Tablesands Arts"></a></li>
                        <li><a class="image-circle" href="/"><img src=" <?php echo get_stylesheet_directory_uri(); ?>/images/south-east-arts.png" alt="South East Arts"></a></li>
                    </ul>
                </div>
            </div>
       </div>
    </div>
</div>