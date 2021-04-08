<?php
/**
 * Block Name:  Category Matrix Block
 *
 * This is the template that displays the Category Matrix block.
 */

$categories_matrix = get_field('categories_matrix');
$title = get_field('title');
$text = get_field('text');
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}else{
    $id = 'category-matrix-' . $block['id'];
}
if($categories_matrix && count($categories_matrix) > 0) :
?>

<div id="<?php echo $id; ?>" class="category-matrix-blocks custom-blocks full-width background-black">
    <div class="in_custom-block">
       <div class="container">
            <div class="category-matrix-inner">
                <h2 class="title-section"><?php echo $title;?></h2>
                <div class="categories-matrix">
                    <?php foreach($categories_matrix as $key => $item) :
                        $title = $item['title'];     
                        $description = $item['description'];  
                        $image = wp_get_attachment_image_url( $item['image'], 'large' );   
                        $color_overlay =  $item['color_overlay'];  
                        $link = $item['link'];  
                        $div = ($link)  ? 'a' : 'div';
                        $href = ($link) ? 'href='.$link['url'].'' : '';
                        $grid_width2 = '';
                        if($key == 0 || $key == 7){
                            $grid_width2 = 'grid-item--width2';
                        }
                    ?>
                    
                    <div class="grid-item <?php echo $grid_width2; ?>">
                        <<?php echo $div . ' ' . $href ?> class="category-matrix-item">
                            <div class="category-matrix-item__image">
                                <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
                            </div>
                            <div class="category-matrix-item__infor">
                                <h3 class="category-matrix-item__title"><?php echo $title; ?></h3>
                                <div class="category-matrix-item__description"><?php echo $description; ?></div>
                            </div>
                            <div class="bg-overlay" aria-hidden="true" style="background-color: <?php echo $color_overlay; ?>"></div>
                        </<?php echo $div; ?>>
                    </div>
                    <?php endforeach; ?>
                </ul>
            </div> 
            <div class="text-bottom"><?php echo $text;?></div>
       </div>
    </div>
</div>
<?php endif; ?>