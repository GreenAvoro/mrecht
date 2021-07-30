<?php
require_once('../../../../wp-load.php');
if(urldecode($_POST['submitbutton']) == 'Add To Quote') {
	echo "RWAR";
	$current_user = wp_get_current_user();
	$quote = get_user_meta($current_user->ID, 'quote', true);
	if(is_array($quote)){
		foreach($_POST as $name => $quantity) {	
			if(count(explode('-',$name)) == 3) {
				list($type,$product_id,$variation_id) = explode('-',$name);
				if($type == 'quantity' && $quantity > 0) {
					
					if(isset($variation_id) && $variation_id > 0) {
						$product = wc_get_product( $variation_id );
						$quote[$variation_id]['price'] = preg_replace('/[^0-9.]*/','',$product->get_price());
						$quote[$variation_id]['quantity'] = $quantity;
					} else {
						$product = wc_get_product( $product_id );
						$quote[$product_id]['price'] = preg_replace('/[^0-9.]*/','',$product->get_price());
						$quote[$product_id]['quantity'] = $quantity;
					}
				}			
			}
		}
	} else{
		header("Location: /my-account/quotes/");
		exit();
	}
	
	update_user_meta( $current_user->ID, 'quote', $quote );
	update_user_meta($current_user->ID, 'quote_status', 'Save');  
	$quote = get_user_meta($current_user->ID, 'quote', true);

	$_cartQty=0;
	foreach($quote as $product_id => $item) {
			$_cartQty++;	 	 	 	
	}
									
	header("Location: /my-account/quotes/");
	exit();	
} else {
	foreach($_POST as $name => $quantity) {
		list($type,$product_id,$variation_id) = explode('-',$name);
		if($type == 'quantity' && $quantity > 0) {
			if(!isset($variations)) {
				$product = new WC_Product_Variable( $product_id );
				$variations = $product->get_available_variations();			
			}
			$variationArray = array();
			foreach($variations as $variation) {
				if($variation['variation_id'] == $variation_id) {
					foreach($variation['attributes'] as $label => $attribute) {
						$variationArray[ucwords(str_replace('-',' ',str_replace('attribute_pa_','',$label)))] = $attribute;
					}
				}
			}

			WC()->cart->add_to_cart( $product_id,$quantity, $variation_id,$variationArray );						
		}
	}
	echo json_encode(array('message'=>'Added to the cart.'));
	exit();	
}

?>