<?php
require_once('../../../../wp-load.php');
global $woocommerce;
$current_user = wp_get_current_user();
$quote = get_user_meta($current_user->ID, 'quote', true);  

if(isset($quote)) {
	if(count($quote) > 0) {
		foreach($quote as $product_id => $variants) {
			try{
				$product = new WC_Product_Variable( $product_id );
			}catch(Exception $e){
				$parent_id = wc_get_product($product_id)->get_parent_id();
				$product = new WC_Product_Variable( $parent_id );
			}
			$variations = $product->get_available_variations();			
			
			foreach($variants as $variation_id => $details) {
				foreach($variations as $variation) {
					if($variation['variation_id'] == $parent_id) {
						foreach($variation['attributes'] as $label => $attribute) {
							$variationArray[ucwords(str_replace('-',' ',str_replace('attribute_pa_','',$label)))] = $attribute;
						}
					}
				}			
			}
			WC()->cart->add_to_cart( $product_id,$variants['quantity'], $variation_id,$variationArray );
		}

		update_user_meta($current_user->ID, 'quote', '');  
	}
}

header("location:/cart/");
exit();
?>