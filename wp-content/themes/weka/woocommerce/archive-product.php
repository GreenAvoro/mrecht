<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
// do_action( 'woocommerce_before_main_content' );
?>
<div class="container">
<header class="products-archive-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="archive-header"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>
	<?php
		if ( function_exists('yoast_breadcrumb') && !is_page( array('homepage') ) ) {
			yoast_breadcrumb( '<p id="breadcrumbs" class="category-page">','</p>' );
		}
	?>
		
	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	// do_action( 'woocommerce_archive_description' );
	?>
</header>
<?php
if(isset($_GET['filter-categories']) && $_GET['filter-categories'] == 'Filter'){
	if(is_shop() || is_product_category() || is_product_tag()){
		$terms = [];
		foreach($_GET as $key => $value){
			if($key != 'filter-categories'){
				if($value == 'on'){
					$terms[] = $key;
				}
			}
		}
		$order_by = (isset($_GET['orderby'])?$_GET['orderby']:null);
		if(explode('-', $order_by)[0] == 'price'){
			$args = [
				'post_type'			=> 'product',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'orderby'			=> 'meta_value_num',
				'meta_key'			=> '_'.explode('-',$order_by)[0],
				'order'				=> explode('-',$order_by)[1],
				'tax_query'			=> [[
					'taxonomy'	=> 'product_cat',
					'field'		=>	'slug',
					'terms'		=> 	$terms,
					'operator' 	=> 'IN'
	
				]]
			];
		}elseif(explode('-', $order_by)[0] == 'date'){
			$args = [
				'post_type'			=> 'product',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'orderby'			=> explode('-',$order_by)[0],
				'tax_query'			=> [[
					'taxonomy'	=> 'product_cat',
					'field'		=>	'slug',
					'terms'		=> 	$terms,
					'operator' 	=> 'IN'
	
				]]
			];
		}else{
			$args = [
				'post_type'			=> 'product',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'orderby'			=> explode('-',$order_by)[0],
				'tax_query'			=> [[
					'taxonomy'	=> 'product_cat',
					'field'		=>	'slug',
					'terms'		=> 	$terms,
					'operator' 	=> 'IN'
	
				]]
			];
		}
		if(count($terms) == 0){
			$args = [
				'post_type'			=> 'product',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
			];
		}
		$loop = new WP_Query($args);
		if($loop->have_posts()){
			do_action( 'woocommerce_before_shop_loop' );
			include(get_template_directory().'/templates/product-filter.html.php');

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( $loop->have_posts() ) {
					$loop->the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );

			echo "</div>";

		}else{
			include(get_template_directory().'/templates/product-filter.html.php');
			ob_start();?>
			
			<p class="no-products large">No Products Found...</p>
			<?php echo ob_get_clean();
			echo '</div>';
		}
	}
}


elseif ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );
	
	include(get_template_directory().'/templates/product-filter.html.php');

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );

	echo "</div>";
} else {
	ob_start();?>
	<p class="no-products large">No Products Found...</p>
	<?php echo ob_get_clean();
}


/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
// do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );
?>
</div>
<?php
get_footer( 'shop' );
