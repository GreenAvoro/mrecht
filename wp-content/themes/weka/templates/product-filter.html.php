<?php

echo '<div class="category-grid">';	
ob_start()?>
<div class="filter-container">
    <form action="/shop" method="GET">
        <div class="close-filter">
            <i class="fas fa-times" aria-hidden="true"></i>
        </div>
        <div id="reset-filter">
            <i class="fas fa-times-circle"></i>
            <p>Reset Filter</p>
        </div>
        <div class="filter-button-wrap">
            <input type="submit" value="Filter" id="filter-submit" name='filter-categories'>
        </div>
        <?php 
        $categories = getCategories(false);
        foreach($categories as $category):
            if($category->parent != 0)continue;?>
            <div class="filter-row">
                <div class="main-cat">
                    <p><?=$category->name?></p>
                    <i class="fas fa-plus"></i>
                    <i class="fas fa-minus" style="display: none"></i>
                </div>
                <div class="filter-row-subcategories">
                    <div class="filter-input-container">
                        <input type="checkbox" name=<?=$category->slug?>>
                        <p>All</p>
                    </div>
                    <?php
                    foreach($categories as $cat2):
                        if($cat2->parent == $category->term_id):?>
                            <div class="filter-input-container">
                                <input type="checkbox" name="<?=$cat2->slug?>">
                                <p><?=$cat2->name?></p>
                            </div>
                        <?php
                        endif;
                    endforeach;?>
                </div>
            </div>
            
        <?php
        endforeach;?>
    </form>
</div>

<?php echo ob_get_clean();