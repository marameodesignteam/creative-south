<?php
/**
 * Block Name:  Accordion Block
 *
 * This is the template that displays the Accordion block.
 */

$heading = get_field( 'heading' );
$accordion = get_field('accordion_item');

if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}else{
    $id = 'accordion-' . $block['id'];
}
?>
<div id="<?php echo $id; ?>" class="accordion-blocks custom-blocks full-width background-black">
    <div class="in_custom-block">
       <div class="container">
       <?php if($heading) : ?>
            <h2 class="title-section"><?php echo $heading;?></h2>
            <?php endif;?>
       <?php 
        if (is_array($accordion) && count($accordion) > 0) :
            //$accordion_id .=  generateRandomString(20);
            $accordion_id .=  'cs-accordion-' . $block['id'];
        ?>
       <div class="accordion" id="<?php echo $accordion_id; ?>">
            <?php foreach ($accordion as $key=>$collapsible) :
            //$show = ($key == 0) ? " show" : "";
            //$aria_expanded = ($key == 0) ? "true" : "false";
            $imageArr = $collapsible["image"];
            $image = wp_get_attachment_image( $imageArr, 'large' );   
            ?>
            <div class="card collapsible">
                <header role="tab" class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="collapse-header">
                                <button type="button" class="collapse-button" data-toggle="collapse" data-target="#section<?php echo $key."-".$accordion_id; ?>" aria-expanded="<?php echo $aria_expanded; ?>" aria-controls="section<?php echo $key."-".$accordion_id; ?>" id="title<?php echo $key."-".$accordion_id; ?>">
                                <?php echo $collapsible["title"]; ?>
                                <i class="fas fa-plus" aria-hidden="true"></i></button>
                            </h3>
                        </div>
                    </div>
                </header>   
                <div class="collapse-body-wrapper collapse<?php echo $show; ?>" id="section<?php echo $key."-".$accordion_id; ?>" aria-labelledby="title<?php echo $key."-".$accordion_id; ?>"   data-parent="#<?php echo $accordion_id; ?>">
                    <div class="row">
                        <div class="col-md-6 collapse-body">
                             <?php echo $collapsible["content"]; ?>
                        </div>
                        <div class="col-md-6 collapse-image">
                            <?php echo $image; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
       </div>
    </div>
</div>