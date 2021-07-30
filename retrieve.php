<?php
global $wpdb;

// Current site prefix
$prefix = $wpdb->prefix;

	require_once("wp-load.php");
	
	function price($price) {
		return preg_replace("/[^0-9\.]/", "",$price);
	}
	function dated($date) {
		if($date != '') {
			list($date,$time) = explode('T',$date);
			
			return $date;
		}
	}

	if($_GET['token'] == 'kadf8fkaldrofk' && $_GET['client'] == 'lfasd9kkro') {
		if(isset($_GET['product_sku'])) {
			$product = $wpdb->get_var("SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = '_sku' AND `meta_value` = '".$_GET['product_sku']."'");
			$data = json_decode($_REQUEST['data']);
			$donotUpdate = array('product_sku','token','client','type');
			$table = "{$wpdb->prefix}postmeta";
			echo $product;
			foreach($_REQUEST as $key => $value) {
				$data = array('meta_value' => $value);	
				$wpdb->update($table,$data,"post_id = '$product' AND meta_key = '$key'");
			}
		}
		if(isset($_GET['execute_order'])) {
			if($_GET['execute_order'] == 66) {
				$wpdb->query("DELETE FROM `{$wpdb->prefix}woocommerce_customer_pricing`");
			} else if($_GET['execute_order'] == 88) {
				$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}woocommerce_group_pricing` (`id` int(11) NOT NULL AUTO_INCREMENT, `discount_code` int(11) NOT NULL, `customerClass` varchar(150) NOT NULL, `customerClass` varchar(150) NOT NULL NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
				
				$wpdb->query("DELETE FROM `{$wpdb->prefix}woocommerce_group_pricing`");
			}
			 else if($_GET['execute_order'] == 99) {
				$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}woocommerce_customer_pricing` (`id` int(11) NOT NULL AUTO_INCREMENT, `product_sku` varchar(150) NOT NULL, `role` varchar(150) NOT NULL, `price` decimal(11,2) NOT NULL , `quantity` int(11) NOT NULL default '0', `last_updated` datetime NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
			}
		}
		if(isset($_GET['email'])) {
			$user = get_user_by( 'email', urldecode($_GET['email']) );
			echo json_encode($user);		
		} else if(isset($_GET['customer_price'])) {
			global $wpdb;
			$product = $wpdb->get_var("SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = '_sku' AND `meta_value` = '".$_GET['product']."'");
			
			if($_GET['customer_price'] > 0) {
				$table = "{$wpdb->prefix}woocommerce_group_pricing_import";
				$data = array('role' => $_GET['role'], 'product_id' => $product, 'price' => $_GET['customer_price']);
				$format = array('%s','%d','%f');
				$wpdb->insert($table,$data,$format);
				$my_id = $wpdb->insert_id;			
				echo $wpdb->last_query.' '.$wpdb->last_result;	
			}
		}
		else if(isset($_GET['customer_price_update'])) { // Check if the custom price exists, if it doesn't then insert a new row
			global $wpdb;
			
			$today = date("Y-m-d");
			
			if($_GET['customer_price_update'] > 0) {
				$table = "{$wpdb->prefix}woocommerce_customer_pricing";
				$price_id = $wpdb->get_var("SELECT id FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE product_sku = '".$_GET['product']."' AND `role` = '".$_GET['role']."'");
				if(isset($_GET['quantity'])) {
					$breakQty = $_GET['quantity'];
				}
				else {
					$breakQty = 0;
				}
				if($price_id == '')
					$price_id=0;
				if($price_id > 0) {
					 $data = array('role' => $_GET['role'], 'product_sku' => $_GET['product'], 'price' => $_GET['customer_price_update'], 'last_updated' => $today, 'quantity' => $breakQty);
					 $wpdb->update($table,$data,array('id' => $price_id));
				 }
				 else {
					$data = array('role' => $_GET['role'], 'product_sku' => $_GET['product'], 'price' => $_GET['customer_price_update'], 'last_updated' => $today, 'quantity' => $breakQty);
					$wpdb->insert($table,$data);
					$my_id = $wpdb->insert_id;	
				 }		
				echo $wpdb->last_query.' '.$wpdb->last_result;	
			}
		}
		else if (isset($_GET['customer_price_end'])) {
			$last_week = date("Y-m-d", strtotime( '-5 days' ) );
			$wpdb->query("DELETE FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE last_updated <= '{$last_week}';");
		}
		else if(isset($_GET['customer_role_price'])) {
			 global $wpdb;
			
			 $price_id = $wpdb->get_var("SELECT ID FROM `{$wpdb->prefix}price` WHERE post_id = '".$_GET['product']."' AND `role` = '".$_GET['role']."'");
				if($price_id == '')
					$price_id=0;
			 
			 $table = "{$wpdb->prefix}price";
			 if($price_id > 0) {
				 $data = array('role' => $_GET['role'], 'post_id' => $_GET['product'], 'price' => $_GET['customer_role_price']);
				 $wpdb->update($table,$data,array('ID' => $price_id));
			 }
			 else {
				 $data = array('role' => $_GET['role'], 'post_id' => $_GET['product'], 'price' => $_GET['customer_role_price']);
				 $wpdb->insert($table,$data);
				 $my_id = $wpdb->insert_id;
			 }
		}
		else if(isset($_GET['customer_role_price_delete'])) {
			 global $wpdb;
			
			 $price_id = $wpdb->get_var("SELECT ID FROM `{$wpdb->prefix}price` WHERE post_id = '".$_GET['product']."' AND `role` = '".$_GET['role']."'");
				if($price_id == '')
					$price_id=0;
			 
			 $table = "{$wpdb->prefix}price";
			 if($price_id > 0) {
				 $wpdb->query("DELETE FROM `{$wpdb->prefix}price` WHERE ID = '$price_id'");
			 }
		}
		else if(isset($_GET['discount_code'])) {
			global $wpdb;

			if($_GET['discount_code'] > 0) {
				$table = "{$wpdb->prefix}woocommerce_group_pricing";
				$data = array('discount_code' => $_GET['discount_code'], 'customerClass' => $_GET['customerClass'], 'itemClass' => $_GET['itemClass']);
				$format = array('%s','%d','%d');
				$wpdb->insert($table,$data,$format);
				$my_id = $wpdb->insert_id;				
			}
		} else if(isset($_GET['advanced_SO_id'])) { // Get the Order ID from the MYOB Advanced SO ID
			echo $wpdb->get_var("SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = 'advanced_id' AND `meta_value` = '".$_GET['advanced_SO_id']."'");
		}  else if(isset($_GET['woo_order_id']) && isset($_GET['advanced_product_name'])) { // Get the Order ID from the MYOB Advanced SO ID
			echo $wpdb->get_var("SELECT order_item_id FROM `{$wpdb->prefix}woocommerce_order_items` WHERE `order_item_name` = '".$_GET['advanced_product_name']."' AND `order_id` = '".$_GET['woo_order_id']."'");
		} else if(isset($_GET['advanced_tax_name']) && isset($_GET['advanced_tax_zone'])) { // Get the Tax ID from the MYOB Advanced Tax Name and Tax Zone
			echo $wpdb->get_var("SELECT tax_rate_id FROM `{$wpdb->prefix}woocommerce_tax_rates` WHERE `tax_rate_name` = '".$_GET['advanced_tax_name']."' AND `tax_rate_country` = '".$_GET['advanced_tax_zone']."'");
		} else if(isset($_GET['get_woo_tax_rate'])) { // Get all tax rates from WooCommerce
			echo $wpdb->get_var("SELECT tax_rate_id FROM `{$wpdb->prefix}woocommerce_tax_rates` LIMIT 1 OFFSET ".$_GET['get_woo_tax_rate']);
		} else if(isset($_GET['advanced_discount_code'])) { // Get the Coupon ID from the MYOB Advanced Discount Code
			echo $wpdb->get_var("SELECT ID FROM `{$wpdb->prefix}posts` WHERE `post_title` = '".$_GET['advanced_discount_code']."' AND `post_type` = 'shop_coupon'");
		} else if(isset($_GET['advanced_shipping_zone'])) { // Get the Zone ID from the MYOB Advanced Shipping Zone
			echo $wpdb->get_var("SELECT zone_id FROM `{$wpdb->prefix}woocommerce_shipping_zones` WHERE `zone_name` = '".$_GET['advanced_shipping_zone']."'");
		} else if(isset($_GET['woo_shipping_zone_id']) && isset($_GET['advanced_shipping_method'])) { // Get the Method ID from the MYOB Advanced Shipping Zone and Method
			echo $wpdb->get_var("SELECT instance_id FROM `{$wpdb->prefix}woocommerce_shipping_zone_methods` WHERE `zone_id` = '".$_GET['woo_shipping_zone_id']."'  AND `method_id` = '".$_GET['advanced_shipping_method']."'");
		} else if(isset($_GET['advanced_product_code'])) { // Get the Coupon ID from the MYOB Advanced Discount Code
			echo $wpdb->get_var("SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = '_sku' AND `meta_value` = '".$_GET['advanced_product_code']."'");
		} else if(isset($_GET['advanced_customer_id'])) { // Get the Coupon ID from the MYOB Advanced Discount Code
			echo $wpdb->get_var("SELECT user_id FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'advanced_id' AND `meta_value` = '".$_GET['advanced_customer_id']."'");
		} else if($_GET['role']) {
			$user_id = wp_update_user( array( 'ID' => $_GET['user_id'], 'role' => strtolower($_GET['role']) ) );	
		} else if(isset($_GET['invoiceupdate'])) {
			echo $wpdb->get_var("SELECT advanced_id FROM `{$wpdb->prefix}invoices` WHERE Status != 'Closed' limit {$_GET['invoiceupdate']},1");			
		} else if(isset($_GET['quoteupdate'])) {
			echo $wpdb->get_var("SELECT advanced_id FROM `{$wpdb->prefix}quotes` WHERE Status IN ('New','Open') limit {$_GET['quoteupdate']},1");			
		} else if(isset($_GET['updatequote'])) {
			$table = "{$wpdb->prefix}quotes";
			$data = array('advanced_id' => $_GET['OpportunityID']);	
			$quote_id = $_GET['updatequote'];
			$wpdb->update($table,$data,"ID = '".$quote_id."'");	
		} else if(isset($_GET['getquote'])) {
			echo json_encode($wpdb->get_results("SELECT * FROM `{$wpdb->prefix}quotes` WHERE `ID` = '".$_GET['getquote']."'"));			
		} else if(isset($_GET['getquote_products'])) {
			echo json_encode($wpdb->get_results("SELECT * FROM `{$wpdb->prefix}quotes_lines` WHERE quote_id = '".$_GET['getquote_products']."'"));			
		} else if(isset($_GET['getinvoice'])) {
			echo json_encode($wpdb->get_results("SELECT * FROM `{$wpdb->prefix}invoices` WHERE `ID` = '".$_GET['getinvoice']."'"));			
		} else if(isset($_GET['getinvoice_products'])) {
			echo json_encode($wpdb->get_results("SELECT * FROM `{$wpdb->prefix}invoices_lines` WHERE invoice_id = '".$_GET['getinvoice_products']."'"));			
		} else if(isset($_GET['updateinvoice'])) {
			$table = "{$wpdb->prefix}invoices";
			$data = array('advanced_id' => $_GET['InvoiceID']);	
			$invoice_id = $_GET['updateinvoice'];
			$wpdb->update($table,$data,"ID = '".$invoice_id."'");	
		} else if(isset($_GET['ReferenceNbr'])) {
			global $wpdb;
			extract($_GET);
			if(isset($woo_customer_id)) 
				$customer = $woo_customer_id;
			else
				$customer = $wpdb->get_var("SELECT user_id FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'advanced_id' AND `meta_value` = '".$CustomerID."'");
			if($customer == '')
				$customer=0;
			
			$table = "{$wpdb->prefix}invoices";
			$data = array(
				'advanced_id' => $ReferenceNbr,	
				'Amount' => price($InvoiceAmount),
				'Balance' => price($Balance),
				'CustomerID' => $customer,
				'AdvanceCustomerID' => $CustomerID,
				'Date' => dated($Date),
				'DueDate' => dated($DueDate),
				'ReferenceNbr' => $ReferenceNbr,
				'Status' => $Status);
			
			if(isset($CustomerOrder))
				$data['customer_order'] = $CustomerOrder;
			if(isset($Terms))
				$data['Terms'] = $Terms;
			if(isset($BillingAttention))
				$data['Attention'] = $BillingAttention;
			if(isset($BillingPhone1))
				$data['Phone'] = $BillingPhone1;
			if(isset($BillingCompanyName))
				$data['BillingCompany'] = $BillingCompanyName;
			if(isset($BillingAddressLine1))
				$data['BillingAddress'] = $BillingAddressLine1;
			if(isset($BillingCity))
				$data['BillingCity'] = $BillingCity;
			if(isset($BillingState))
				$data['BillingState'] = $BillingState;
			if(isset($BillingPostalCode))
				$data['BillingPostCode'] = $BillingPostalCode;
			if(isset($BillingCompanyName))
				$data['DeliveryCompany'] = $BillingCompanyName;
			if(isset($ShippingAddressLine1))
				$data['DeliveryAddress'] = $ShippingAddressLine1;
			if(isset($ShippingCity))
				$data['DeliveryCity'] = $ShippingCity;
			if(isset($ShippingState))
				$data['DeliveryState'] = $ShippingState;
			if(isset($ShippingPostalCode))
				$data['DeliveryPostCode'] = $ShippingPostalCode;
			if(isset($CashDiscount))
				$data['CashDiscount'] = $CashDiscount;
			
			$invoice_id = $wpdb->get_var("SELECT ID FROM `{$wpdb->prefix}invoices` WHERE `advanced_id` = '".$ReferenceNbr."'");
			
			if($invoice_id > 0) {
				$wpdb->update($table,$data,array('ID' => $invoice_id));
				$wpdb->query("DELETE FROM `{$wpdb->prefix}invoices_lines` WHERE InvoiceID = '$invoice_id'");
			} else {
				$wpdb->insert($table,$data);
				$invoice_id = $wpdb->insert_id;
			}
			$table = "{$wpdb->prefix}invoices_lines";
			$invoice['Details'] = array();

			foreach($_GET as $num => $value) {
				if(count(explode('-',$num)) == 2) {				
					list($line,$name) = explode('-',$num);
					$invoice['Details'][$line][$name] = $value;
				}
			}
			
			foreach($invoice['Details'] as $line) {
				$product = $wpdb->get_var("SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = '_sku' AND `meta_value` = '".$line['InventoryID']."'");
				if($product == '')
					$product=0;
				
				$orderNbr = $wpdb->get_var("SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = 'advanced_id' AND `meta_value` = '".$line['OrderNbr']."'");
				if($orderNbr == '')
					$orderNbr=0;
					
				if($line['InventoryID'] == '' || is_null($line['InventoryID']) || !isset($line['InventoryID'])) {
					$product = 0;
				}
				
				if(isset($line['ShipmentNbr']))
					$ShipmentNbr = $line['ShipmentNbr'];
				else
					$ShipmentNbr = '';
				
				$data = array(
					'Amount' => price($line['Amount']),
					'InvoiceID' => $invoice_id,
					'Description' => $line['TransactionDescr'],
					'InventoryID' => $product,
					'LineNbr' => $line['LineNbr'],
					'OrderNbr' => $orderNbr,
					'Quantity' => $line['Qty'],
					'ShipmentNbr' => $ShipmentNbr,
					'UnitPrice' => price($line['UnitPrice'])
				);
				if(isset($line['LotSerialNbr']))
					$data['SerialNbr'] = $line['LotSerialNbr']; // Need to update before going live
				$wpdb->insert($table,$data);				
			}
		} else if(isset($_GET['BusinessAccount'])) { // Updating/creating an Opportunity in WooCommerce
			global $wpdb;
			extract($_GET);
			$customer = $wpdb->get_var("SELECT user_id FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'advanced_id' AND `meta_value` = '".$BusinessAccount."'");
			
			if($customer == '')
				$customer=0;
			
			$table = "{$wpdb->prefix}quotes";
			$data = array(
				'advanced_id' => $OpportunityID,	
				'Amount' => price($Amount),
				'Subject' => $Subject,
				'CustomerID' => $customer,
				'CompanyName' => $CompanyName,
				'Email' => $Email,
				'FirstName' => $FirstName,
				'LastName' => $LastName,
				'Status' => $Status);
				
			/*
			$table = "{$wpdb->prefix}quotes";
			$data = array(
				'advanced_id' => $OpportunityID,
				'Amount' => price($Amount),
				'Total' => price($Total),
				'Subject' => $Subject,
				'CustomerID' => $customer,
				'CompanyName' => $BusinessAccount,
				'Email' => $Email,
				'FirstName' => $FirstName,
				'LastName' => $LastName,
				'Status' => $Status,
				'Stage' => $Stage,
				'advanced_order_id' => $OrderNbr
			);
			*/
						
			$quote_id = $wpdb->get_var("SELECT ID FROM `{$wpdb->prefix}quotes` WHERE `advanced_id` = '".$OpportunityID."'");
	
			if($quote_id > 0) {
				$wpdb->update($table,$data,array('ID' => $quote_id));
				print_r($wpdb->print_error());
				$wpdb->query("DELETE FROM `{$wpdb->prefix}quotes_lines` WHERE quote_id = '$quote_id'");
			} else {
				$wpdb->insert($table,$data);	
				$quote_id = $wpdb->insert_id;
			}
			
			$table = "{$wpdb->prefix}quotes_lines";
			$quote['Products'] = array();

			foreach($_GET as $num => $value) {
				if(count(explode('-',$num)) == 2) {				
					list($line,$name) = explode('-',$num);
					$quote['Products'][$line][$name] = $value;
				}
			}
			
			foreach($quote['Products'] as $line) {
				$product = $wpdb->get_var("SELECT post_id FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = '_sku' AND `meta_value` = '".$line['InventoryID']."'");
				if($product == '')
					$product=0;
				
				$data = array(
					'amount' => price($line['Amount']),
					'quote_id' => $quote_id,
					'product_id' => $product,
					'quantity' => $line['Qty'],
					'price' => price($line['UnitPrice']));
				$wpdb->insert($table,$data);
			}
		} else if(isset($_GET['file']) && isset($_REQUEST['product']) && isset($_REQUEST['name'])) {
			$wpdb->query("CREATE TABLE IF NOT EXISTS `product_file` (`pf_id` int(11) NOT NULL AUTO_INCREMENT, `pf_name` int(11) NOT NULL, `pf_product` int(11) NOT NULL,`pf_file` longtext NOT NULL, PRIMARY KEY (`pf_id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
			if($_REQUEST['action'] == 'add') {
				$wpdb->query("INSERT INTO `product_file` (pf_name,pf_file,pf_product)  VALUES ('{$_REQUEST['name']}','{$_REQUEST['file']}','{$_REQUEST['product']}');");
			} else if($_REQUEST['action'] == 'remove') {
				$wpdb->query("DELETE FROM `product_file` WHERE pf_name='{$_REQUEST['name']}' AND pf_file='{$_REQUEST['file']}' AND pf_product='{$_REQUEST['product']}';");				
			}
		}
	}
?>