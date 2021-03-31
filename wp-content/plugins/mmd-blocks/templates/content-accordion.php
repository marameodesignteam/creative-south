<?php
/**
 * Block Name:  Accordion Block
 *
 * This is the template that displays the Accordion block.
 */

$heading = get_field( 'heading' );
$content = get_field( 'content' );
$accordion = get_field('accordion_item');
$button = get_field('button');

if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}else{
    $id = 'accordion-' . $block['id'];
}
?>
<div id="<?php echo $id; ?>" class="accordion-blocks custom-blocks">
    <div class="in_custom-block">
       <div class="container">
       <?php if($heading) : ?>
            <h2 class="main-title"><?php echo $heading;?></h2>
            <?php endif;
            if($content) :?>
            <div class="copy copy-bottom"><?php echo $content; ?></div>
            <?php endif; ?>
       <?php 
        if (is_array($accordion) && count($accordion) > 0) :
            $accordion_id .=  generateRandomString(20);
        ?>
       <div class="accordion" id="<?php echo $accordion_id; ?>">
            <?php foreach ($accordion as $key=>$collapsible) :
            $show = ($key == 0) ? " show" : "";
            $aria_expanded = ($key == 0) ? "true" : "false";
            ?>
            <div class="card collapsible">
                <header role="tab" class="card-header">
                    <h3 class="collapse-header">
                        <button type="button" class="collapse-button" data-toggle="collapse" data-target="#section<?php echo $key."-".$accordion_id; ?>" aria-expanded="<?php echo $aria_expanded; ?>" aria-controls="section<?php echo $key."-".$accordion_id; ?>" id="title<?php echo $key."-".$accordion_id; ?>">
                        <?php echo $collapsible["title"]; ?>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i></button>
                    </h3>
                </header>   
                <div class="collapse-body copy collapse<?php echo $show; ?>" id="section<?php echo $key."-".$accordion_id; ?>" aria-labelledby="title<?php echo $key."-".$accordion_id; ?>"   data-parent="#<?php echo $accordion_id; ?>">
                    <div>
                    <?php echo $collapsible["content"]; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if ( $button ) : ?>
            <div class="buttons text-center">
                <a class="button violet" href="<?php echo esc_url( $button['url'] ); ?>" target="<?php echo esc_attr( $button['target'] ); ?>"><span><?php echo esc_html( $button['title'] ); ?></span></a>
            </div>
            <?php endif; ?>
       </div>
    </div>
</div>