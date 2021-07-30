<!-- 
    This template requires:
    -$categories - ARRAY of WP_TERM

 -->


<div class="categories-list">
    <?php
    $killCounter = 0;//Kill the loop at $killCount iterations regardless
    $killCount = 100;
    $counter = 1;
    $i = -1;
    $categoryGroupedNumber = 8;
    while($counter <= $categoryGroupedNumber){
        ++$i;
        ++$killCounter;
        if($killCounter >= $killCount) break;

        
        //Only echo something if the category is present
        if(isset($categories[$i])){
            if($categories[$i]->name == 'Uncategorized') continue;
            if($categories[$i]->slug == get_option('homepage_carousel_1')) continue;
            if($categories[$i]->slug == get_option('homepage_carousel_2')) continue;
            ob_start();?>
            <a href="/product-category/<?=$categories[$i]->slug?>" class="category-item">
                <?=$categories[$i]->name?>
            </a>
            <?php echo ob_get_clean();
            $counter++;
        }
        
    }
    $categories = array_splice($categories,8);
    ?>
</div>