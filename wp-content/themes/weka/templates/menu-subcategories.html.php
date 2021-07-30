<?php
$categories = [];
//$cats is declared in dropdown.html.php
foreach($cats as $cat){
    $categories[$cat->parent][] = $cat;     
}

foreach($categories as $k => $cat){
    //i.e. it's a sub category
    if($k != 0):?>
        <div class="menu-subcategory-grid" data-id="<?=$k?>">
            <?php 
            foreach($cat as $subcat):?>
            <div class="menu-subcategory-list">
                <a href="/product-category/<?=$subcat->slug?>" class="menu-subcategory-header"><?=$subcat->name?></a>
                <?php
                $args = [
                    'post_type'     => 'product',
                    'post_status'   => 'publish',
                    'posts_per_page'=> 10,
                    'tax_query'     => [[
                        'taxonomy'      => 'product_cat',
                        'field'         => 'term_id',
                        'terms'         => $subcat->term_id,
                        'operator'      => 'IN'
                    ]]
                ];
                $products = new WP_Query($args);
                if($products->have_posts()):while($products->have_posts()):$products->the_post();?>
                    <a href="<?=the_permalink()?>" class="menu-subcategory-product"><?=the_title()?></a>
                <?php endwhile;endif;?>
            </div>
            <?php endforeach; ?>
        </div>
    <!-- It's a root category -->
    <?php else: 
        foreach($cat as $cat):?>
            <div class="menu-subcategory-grid" data-id="<?=$cat->term_id?>">
                <?php
                $args = [
                    'post_type'     => 'product',
                    'post_status'   => 'publish',
                    'posts_per_page'=> 10,
                    'tax_query'     => [[
                        'taxonomy'      => 'product_cat',
                        'field'         => 'term_id',
                        'terms'         => $cat->term_id,
                        'operator'      => 'IN'
                    ]]
                ];
                $products = new WP_Query($args);
                if($products->have_posts()):while($products->have_posts()):$products->the_post();
                    $productCategories = get_the_terms(get_the_ID(), 'product_cat');
                    // print_r($productCategories);
                    $current_cat_has_subcat = false;
                    foreach($productCategories as $productCat){
                        if(isset($categories[$productCat->term_id])){
                            $current_cat_has_subcat = true;
                        }
                    }
                    if($current_cat_has_subcat)continue;
                ?>
                    <a href="<?=the_permalink()?>" class="menu-subcategory-product"><?=the_title()?></a>
                <?php endwhile;endif;?>
            </div>

    <?php endforeach;endif;

}