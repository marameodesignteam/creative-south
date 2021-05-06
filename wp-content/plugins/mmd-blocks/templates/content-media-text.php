<?php
/**
 * Block Name:  Primary Content Block
 *
 * This is the template that displays the Primary Content block.
 */

$content = get_field( 'content' );
$sidebar_images = get_field('sidebar_images');
$col_class_content = ($sidebar_images && count($sidebar_images > 0)) ? 'col-md-7' : 'col-12';
$col_class_sidebar = ($sidebar_images && count($sidebar_images > 0)) ? 'col-md-5' : 'col-12';

if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}else{
    $id = 'primary-content-' . $block['id'];
}
?>
<?php if($content) : ?>
<div id="<?php echo $id; ?>" class="media-text-blocks primary-content-blocks custom-blocks full-width background-black">
    <div class="in_custom-block">
       <div class="container">
            <div class="primary-content">
                <div class="row">
                    <div class="<?php echo $col_class_content;?>">
                        <?php echo $content; ?>
                    </div>
                    <?php if($sidebar_images && !empty($sidebar_images)) : ?>
                    <div class="<?php echo $col_class_sidebar;?>">
                        <?php foreach($sidebar_images as $item) : 
                            $imageArr = $item["image"];
                            $image = wp_get_attachment_image( $imageArr, 'large' );   
                        ?>
                            <div class="sidebar-image">
                                <?php echo $image;?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
       </div>
    </div>
</div>
<?php endif; ?>