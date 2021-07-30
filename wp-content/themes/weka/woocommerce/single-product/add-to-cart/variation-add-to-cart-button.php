<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
	<?php
	$atts = $product->get_attributes();
	$variations = $product->get_available_variations();
	if(array_key_exists('pa_tape-colour', $atts)):
		?>

		<div class="colour-picker">
			<p class="variation-label">Or choose a colour below:</p>
			<div class="colour-picker-colours">
			<?php
			$coloursUsed = [];
			foreach($variations as $var):
				$varColour = $var['attributes']['attribute_pa_tape-colour'];
				$varColourName = get_term_by( 'slug', $varColour, 'pa_tape-colour');
				$varColourName = $varColourName->name;
				if(!in_array($varColour,$coloursUsed)):
					$coloursUsed[] = $varColour;
					$varImage = $var['image']['url'];
					?>
					<div class="colour-picker-item tape hide" data-slug="<?=$varColour?>">
						<img src="<?=$varImage?>">
						<p><?=$varColourName?></p>
					</div>
				<?php 
				endif;
			endforeach; ?>
			</div>
		</div>
	<?php
	elseif(array_key_exists('pa_zip-colour', $atts)):
		?>

		<div class="colour-picker">
			<p class="variation-label">Or choose a colour below:</p>
			<div class="colour-picker-colours">
			<?php
			$coloursUsed = [];
			foreach($variations as $var):
				$varColour = $var['attributes']['attribute_pa_zip-colour'];
				$varColourName = get_term_by( 'slug', $varColour, 'pa_zip-colour');
				$varColourName = $varColourName->name;
				
				if(!in_array($varColour,$coloursUsed)):
					$coloursUsed[] = $varColour;
					$varImage = $var['image']['url'];
					?>
					<div class="colour-picker-item zip hide" data-slug="<?=$varColour?>">
						<img src="<?=$varImage?>">
						<p><?=$varColourName?></p>
					</div>
				<?php 
				endif;
			endforeach; ?>
			</div>
		</div>
	<?php
	endif;


	do_action( 'woocommerce_before_add_to_cart_quantity' );
	woocommerce_quantity_input(
		array(
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
		)
	);

	do_action( 'woocommerce_after_add_to_cart_quantity' );
	?>
	<div class="custom-price-container">
		<p id="my-custom-price"></p>
		<p id="my-custom-per-item-price"></p>
	</div>
	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
