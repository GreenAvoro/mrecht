<div class="main-dropdown shop">
    <div class="left-panel">
        <div class="left-panel-text">
            <?php
            // include(get_template_directory().'/includes/getCategories.php');
            $cats = getCategories(false);
            foreach($cats as $cat):
                if($cat->parent != 0) continue; // Only display root categories
                if($cat->name == "Uncategorized") continue;
            ?>
                <a href="<?=get_term_link($cat->slug, 'product_cat')?>" data-id="<?=$cat->term_id?>"><?=$cat->name?></a>
            <?php 
            endforeach;?>
        </div>
    </div>
    <div class="right-panel">
        <div class="my-close"><i class="fas fa-times"></i></div>
        <?php include(get_template_directory().'/templates/menu-subcategories.html.php');?>
    </div>
</div>