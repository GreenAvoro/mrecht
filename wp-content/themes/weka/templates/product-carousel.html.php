<!-- 
    This template file requires:
    - $category_data - WP_TERM
    - $category_title - STRING
 -->


<h3 class="product-carousel-header"><?=$category_title?></h3>
<div class="owl-carousel">
    <?php
    $args = [
        'post_type'     => 'product',
        'post_status'   => 'publish',
        'tax_query'     => [[
            'taxonomy'      => 'product_cat',
            'field'         => 'slug',
            'terms'         => $category_data->slug
        ]]
    ];
    $q = new WP_Query($args);
    if($q->have_posts()):while($q->have_posts()):$q->the_post();?>
        <div class="product-carousel-item" >
            <?=get_the_post_thumbnail()?>
            <p class="product-carousel-title"><?=the_title()?></p>
            <a href="<?=the_permalink()?>" class="common-button">VIEW ACCESSORY</a>
        </div>
    <?php endwhile;
    else:?>
    <p class="no-products">No Products Found</p>

    <?php endif; ?>
</div>