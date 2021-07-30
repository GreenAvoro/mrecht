<?php
// $categories is needed for category-block.html.php so it can display the category blocks
$categories = getCategories();


?>

<script>
	let bannerStart = 2
	let bannerEnd = 4
	function changeBanner($){
		$(".custom-home-banner").css("background-image", "url(<?=get_template_directory_uri()?>/img/banner-"+bannerStart+".jpg)")
		bannerStart = bannerStart >= bannerEnd ? 1 : bannerStart += 1

	}
</script>

<div class="container">
    <div class="custom-home-banner" style="background-image: url(<?=get_template_directory_uri()?>/img/banner-1.jpg)">
        <div class="overlay overlay-10"></div>
        <div class="custom-home-banner-content">
            <h1 class="custom-home-banner-text">We have all the accessories</h1>
            <a class="common-button red banner" href="<?=get_template_directory_uri()?>/shop/">SHOP STUDENT SUPPLIES</a>
        </div>
    </div>
    

    <!--
        Loop through the categories first 8 root categories to display
        in the category block
     -->
    <?php include(get_template_directory().'/templates/category-block.html.php'); ?>


    <?php if(get_option('homepage_carousel_1')):
        $category_data = get_term_by('slug', get_option('homepage_carousel_1'), 'product_cat');
        $category_title = $category_data->name;
        include(get_template_directory().'/templates/product-carousel.html.php');
    ?>
    <?php else: ?>
            <h3 class="product-carousel-header">Medical Masks & Accessories</h3>
            <div class="product-carousel">
                <?php
                $args = [
                    'post_type'     => 'product',
                    'post_status'   => 'publish'
                ];
                $q = new WP_Query($args);
                if($q->have_posts()):while($q->have_posts()):$q->the_post();?>
                    <div class="product-carousel-item">
                        <?=get_the_post_thumbnail()?>
                        <p class="product-carousel-title"><?=the_title()?></p>
                        <p class="product-carousel-desc"><?=get_the_excerpt()?></p>
                        <a href="<?=the_permalink()?>" class="common-button">VIEW ACCESSORY</a>
                    </div>
                <?php endwhile;endif;?>
            </div>
    <?php endif; ?>

    <h3 class="product-carousel-header">An accessory for every need</h3>
    <?php include(get_template_directory().'/templates/major-categories.html.php');?>

    <!-- 
        Loop through the next 8 root categories and display them in the second block
     -->
    <?php include(get_template_directory().'/templates/category-block.html.php'); ?>

    <?php if(get_option('homepage_carousel_2')):
        $category_data = get_term_by('slug', get_option('homepage_carousel_2'), 'product_cat');
        $category_title = $category_data->name;
        include(get_template_directory().'/templates/product-carousel.html.php');
    ?>
        
    <?php else: ?>
            <h3 class="product-carousel-header">Medical Masks & Accessories</h3>
            <div class="product-carousel">
                <?php
                $args = [
                    'post_type'     => 'product',
                    'post_status'   => 'publish'
                ];
                $q = new WP_Query($args);
                if($q->have_posts()):while($q->have_posts()):$q->the_post();?>
                    <div class="product-carousel-item">
                        <?=get_the_post_thumbnail()?>
                        <p class="product-carousel-title"><?=the_title()?></p>
                        <p class="product-carousel-desc"><?=get_the_excerpt()?></p>
                        <a href="<?=the_permalink()?>" class="common-button">VIEW ACCESSORY</a>
                    </div>
                <?php endwhile;endif;?>
            </div>
    <?php endif; ?>

    <?php include(get_template_directory().'/templates/recommended.html.php'); ?>

</div>