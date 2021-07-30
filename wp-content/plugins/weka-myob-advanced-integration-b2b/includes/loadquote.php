<?php

require_once($_SERVER["DOCUMENT_ROOT"].'/wp-load.php');
global $wpdb;

$current_user = wp_get_current_user();
$quote=array();
$results = $wpdb->get_results( "SELECT product_id,price,quantity FROM {$wpdb->prefix}quotes_lines WHERE quote_id = '".$_REQUEST['quoteID']."'" );
if(count($results) > 0) {
	foreach($results as $result) {
		$quote[$result->product_id]['price'] = $result->price;
		$quote[$result->product_id]['quantity'] = $result->quantity;
	}
}

update_user_meta( $current_user->ID, 'quote', $quote );
update_user_meta( $current_user->ID, 'quote_current', $_REQUEST['quoteID'] );
update_user_meta( $current_user->ID, 'quote_status', 'Saved' );


header("location:/my-account/quotes/");
exit();
?>