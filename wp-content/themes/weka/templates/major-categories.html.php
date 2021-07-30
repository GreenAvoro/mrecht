

<div class="categories-list">
    <?php 
    for($i=1;$i<=4;$i++):
        if(get_option('major_category_'.$i)){
            $category_data = get_term_by('slug', get_option('major_category_'.$i), 'product_cat');
            $category_title = $category_data->name;
        }else{
            $category_title = "No Category Set";
        }
        ?>
        <a href="/product-category/<?=$category_data->slug?>" class="category-item-img">
            <div class="category-item-img-container" style="background-image: url('<?=get_template_directory_uri()?>/img/backgrounds/background<?=$i?>.jpg')"></div>
            <p class="product-carousel-title"><?=$category_title?></p>
        </a>
        <?php 
    endfor;
    ?>
</div>