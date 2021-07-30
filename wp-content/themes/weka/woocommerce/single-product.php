<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>
	<div class="container custom-single-product">
	<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>
	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
		
	?>
	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>
	</div>
	<div class="single-product-info">
		<div class="container">
			<div class="single-product-info-container">
				<div class="header-tabs">
					<p class="header-tab 1 active">Features</p>
					<p class="header-tab 2">Delivery</p>
					<p class="header-tab 3">Returns</p>
				</div>
				<div class="single-product-info-tab tab1 active">
					<?= get_the_content() == '' ? '<p style="font-size: 2em;color: #676767;">No Feature Description Available</p>':get_the_content() ?>
				</div>

				<div class="single-product-info-tab tab2">
					<h4>Non-Account & Cash Account Customers</h4>
					<p>Shipping & handling charges will be determined at the time you place your order. Charges (ex GST) are per order and based on the Post Code / Country of the delivery address you have chosen.</p>
					<h4>Charges as follows:</h4>
					<p><i>Australia</i></p>
					<ul>
						<li>Victoria – from $10.50</li>
						<li>All other States & Territories – from $12.00</li>
						<li>For Orders Containing Large / Bulky / Hazardous items, we will contact you with Shipping Charges, hence no Shipping Charges will be shown at the time you place your order. ** Do NOT Pay until we have advised of shipping charges</li>
					</ul>
					<p><i>International (outside of Australian Territory)</i></p>
					<ul>
						<li>We will contact you with Shipping Charges, hence no Shipping Charges will be shown at the time you place your order.  ** Do NOT Pay until we have advised of shipping charges</li>
					</ul>

					<h4>Account Customers</h4>
					<p>For our Account Customers shipping will be determined at dispatch time depending on your chosen method of delivery. Hence no Shipping Charges will be shown at the time you place your order.</p>

					<a href="/terms-conditions/shipping-delivery/">More Information</a>
				</div>

				<div class="single-product-info-tab tab3">
					<h4>Returns (excluding Damaged Goods or Lost in Transit)</h4>
					<p>Returns will only be accepted within 7 days from the date that the goods are delivered and must be returned in the condition and packaging that they were supplied.</p>
					<div style="font-size: .85em">
						<p><i>Note:</i></p>
						<p><i>Any refunds due to goods incorrectly ordered or not suitable will incur a 5% handling fee of the value of the goods returned.</i></p>
						<p><i>Returns for RIRI Zips will not be accepted</i></p>
					</div>
					<br>
					<h4>Damaged Goods</h4>
					<p>Any damaged goods must be notified to us within 7 days of receipt quoting your WEB Order Reference Number.</p>
					<br>
					<h4>Goods Lost in Transit</h4>
					<p>If you have not received your goods within 7 days of order please notify us quoting your WEB Order Reference Number.</p>
					<a href="/terms-conditions/returns/">More Information</a>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
	<?php
		// woocommerce_output_related_products();
		
	?>
		<h3 class="product-carousel-header">Cords & Tapes</h3>
		<div class="owl-carousel">
			<?php
			global $post;
			
			$args = [
				'post_type'     => 'product',
				'post_status'   => 'publish',
				'tax_query'     => [[
					'taxonomy'      => 'product_cat',
					'field'         => 'slug',
					'terms'         => 'cords-and-tapes'
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
	</div>
<?php
get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */

