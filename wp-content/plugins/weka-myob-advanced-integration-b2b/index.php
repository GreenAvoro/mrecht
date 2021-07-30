<?php

/**

 * Plugin Name: MYOB Advanced WooCommerce Integration B2B

 * Plugin URI: http://www.wekaonline.co.nz

 * Description: Sends data from WooCommerce to MYOB Advanced

 * Version: 3.0.1

 * Author: Weka Online

 * Author URI: https://www.wekaonline.co.nz

 * License: GPL2

*/



error_reporting(E_ALL);

ini_set('display_errors', 0);



defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



add_action('admin_print_styles','weka_advanced_integration_css_init');

function weka_advanced_integration_css_init() {

	wp_enqueue_style( 'weka-integration-css', plugins_url( '/css/weka-integration.css', __FILE__ ),false,date('his') );

}



add_action('wp_enqueue_scripts','ava_test_init');

function ava_test_init() {

    wp_enqueue_script( 'ava-test-js', plugins_url( '/js/weka-integration.js', __FILE__ ));

}



add_action('admin_menu', 'weka_advanced_integration_menu');



//add_action('admin_menu', 'advanced_integration_menu');



function weka_advanced_integration_menu() {

	add_menu_page('MYOB Advanced Integration', 'MYOB Integration', 'administrator', 'advanced_woo_integration', 'advanced_woo_integration_settings_page', 'dashicons-admin-generic');

}



// This is used to generate a crypto secured string for the WooCommerce Webhook Secret

function wekaGenerateRandomString()

{

	$bytes = random_bytes(5);

	$str = bin2hex($bytes);

	$result = base64_encode($str);

	return $result;

}



function wekaGenerateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')

{

	$sets = array();

	if(strpos($available_sets, 'l') !== false)

		$sets[] = 'abcdefghjkmnpqrstuvwxyz';

	if(strpos($available_sets, 'u') !== false)

		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';

	if(strpos($available_sets, 'd') !== false)

		$sets[] = '23456789';

	if(strpos($available_sets, 's') !== false)

		$sets[] = '!@#$%&*?';



	$all = '';

	$password = '';

	foreach($sets as $set)

	{

		$password .= $set[array_rand(str_split($set))];

		$all .= $set;

	}



	$all = str_split($all);

	for($i = 0; $i < $length - count($sets); $i++)

		$password .= $all[array_rand($all)];



	$password = str_shuffle($password);



	if(!$add_dashes)

		return $password;



	$dash_len = floor(sqrt($length));

	$dash_str = '';

	while(strlen($password) > $dash_len)

	{

		$dash_str .= substr($password, 0, $dash_len) . '-';

		$password = substr($password, $dash_len);

	}

	$dash_str .= $password;

	return $dash_str;

}



//Generate the WooCommerce Webhook and return the Webhook Secret string

function createWekaOrderCreationWebhook()

{

	$secret = wekaGenerateStrongPassword(16,false,'lud');

	

	$user = wp_get_current_user();

	$user_id = $user->ID;

	

	$webhook = new WC_Webhook();

	$webhook->set_user_id($user_id); // User ID used while generating the webhook payload.

	$webhook->set_topic( 'order.created'); // Event used to trigger a webhook.

	$webhook->set_secret( $secret ); // Secret to validate webhook when received.

	$webhook->set_name( 'Weka Online Order Creation' ); // Secret to validate webhook when received.

	$webhook->set_delivery_url( 'https://advancedweka.wekaonline.co.nz/Webhooks/woocommerce_order_receive.php' ); // URL where webhook should be sent.

	$webhook->set_status( 'active' ); // Webhook status.

	$save = $webhook->save();

	return $secret;

}



// delete all webhook that are currently being sent to advancedweka

function deleteWekaWebhooks() {

	global $wpdb;

	$results = $wpdb->get_results( "SELECT webhook_id, delivery_url FROM {$wpdb->prefix}wc_webhooks" ); // Get all WooCommerce Webhooks

	foreach($results as $result)

	{

		if(strpos($result->delivery_url, 'advancedweka.wekaonline') !== false) // Delete any webhook being sent to advancedweka.wekaonline

		{

			$wh = new WC_Webhook();

			$wh->set_id($result->webhook_id);

			$wh->delete();

		}

	}

}



function wekaAdvancedSelectBox($foreach,$default='') {

	$return='';

	foreach($foreach as $field) {

		if(isset($default)) {

			if($default == $field)

				$selectMe = 'selected="selected"';

			else

				$selectMe='';			

		}

		else

			$selectMe='';

		

		$return.='<option value="'.$field.'" '.$selectMe.'>'.$field.'</option>';

	}

	return $return;

}



function advanced_woo_integration_settings_page() {

	global $wpdb;

	

	$weka_enable_order_push_existing = get_option( 'weka_enable_order_push'); // current state of the Order Push

	$update_advancedweka_options = 0;



	if(count($_POST) > 1) { // Check whether an update has been triggered

		foreach($_POST as $name=>$value) {

			if(is_array($value))

				$value = json_encode($value);

			if($name == 'weka_password') {

				if(is_null($value) || $value == '')

					continue;

			}

			update_option( $name, $value );

		}

		extract($_POST);

		$username = get_option( 'weka_username'); // Username of the advancedweka login

		$password = get_option( 'weka_password'); // Pasword of the advancedweka login

		if(isset($_POST['weka_products'])) { // Update the Products options on advancedweka

			$update_advancedweka_options = 1;

			if(isset($weka_enable_matrix_items)) {

				$options['matrix_products'] = $_POST['weka_products'];

			}

			$options['simple_products'] = $_POST['weka_products'];

		}

		if(isset($_POST['weka_customers'])) { // Update the Customer options on advancedweka

			$update_advancedweka_options = 1;

			$options['customers'] = $_POST['weka_customers'];

		}

		if(isset($_POST['weka_sales_orders'])) { // Update the Sales Order options on advancedweka

			$update_advancedweka_options = 1;

			$options['sales_orders'] = $_POST['weka_sales_orders'];

		}

		if(isset($_POST['weka_shipping'])) { // Update the Sales Order options on advancedweka

			$update_advancedweka_options = 1;

			$options['shipping'] = $_POST['weka_shipping'];

		}

		if(isset($_POST['weka_settings'])) { // Update the General Settings options on advancedweka

			$update_advancedweka_options = 1;

			$options['settings'] = $_POST['weka_settings'];

		}
		

		if($submit == 'Update General Settings') {

			update_option( 'weka_enable_sales_prices', $weka_enable_sales_prices );

			update_option( 'weka_enable_order_push', $weka_enable_order_push );

			update_option( 'weka_enable_quotes', $weka_enable_quotes );

			update_option( 'weka_enable_invoices', $weka_enable_invoices );

			update_option( 'weka_integration_opportunity_tax', $weka_integration_opportunity_tax / 100 );

			

			if($weka_enable_sales_prices == 1) {

				$options['simple_products']['filter_all_salesprices_sync'] = 'yes';

				$options['simple_products']['filter_salesprice_sync'] = 'yes';

				// Create the database table

				$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}woocommerce_customer_pricing` (`id` int(11) NOT NULL AUTO_INCREMENT, `product_sku` varchar(150) NOT NULL, `role` varchar(150) NOT NULL, `price` decimal(11,2) NOT NULL , `quantity` int(11) NOT NULL default '0', `last_updated` datetime NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

			}

			else {

				$options['simple_products']['filter_all_salesprices_sync'] = 'no';

				$options['simple_products']['filter_salesprice_sync'] = 'no';

			}

			

			if($weka_enable_quotes == 1) {

				// Create the database tables for the Quotes System

				$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}quotes` (`ID` int(11) NOT NULL AUTO_INCREMENT, `advanced_id` varchar(150) NOT NULL, `Subject` varchar(255) NULL, `Status` varchar(50) NOT NULL , `CustomerID` int(11) NOT NULL default '0', `Amount` decimal(11,2) NOT NULL,`CompanyName` varchar(255) NULL,`Email` varchar(255) NULL,`FirstName` varchar(255) NULL,`LastName` varchar(255) NULL,`created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				

				$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}quotes_lines` (`ID` int(11) NOT NULL AUTO_INCREMENT, `quote_id` int(11) NOT NULL default '0', `product_id` int(11) NOT NULL default '0', `price` decimal(12,2) NOT NULL,`quantity` int(11) NOT NULL,`amount` decimal(12,2) NOT NULL, PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

			}

			

			if($weka_enable_invoices == 1) {

				// Create the database tables for the Invoice System

				$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}invoices` (`ID` int(11) NOT NULL AUTO_INCREMENT, `advanced_id` varchar(150) NOT NULL, `Amount` decimal(12,2) NOT NULL,`Balance` decimal(12,2) NOT NULL, `CustomerID` int(11) NOT NULL default '0',`AdvanceCustomerID` varchar(150) NOT NULL, `Date` timestamp NULL,`DueDate` datetime NULL,`ReferenceNbr` varchar(100) NOT NULL,`Status` varchar(50) NOT NULL, PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				

				$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}invoices_lines` (`id` int(11) NOT NULL AUTO_INCREMENT, `InvoiceID` int(11) NOT NULL default '0', `Amount` decimal(12,2) NOT NULL, `advanced_id` varchar(150) NOT NULL,`InventoryID` int(11) NOT NULL,`Description` varchar(255) NOT NULL, `LineNbr` varchar(100) NOT NULL, `OrderNbr` int(11) NOT NULL, `Quantity` int(11) NOT NULL, `ShipmentNbr` varchar(100) NOT NULL, `UnitPrice` decimal(12,2) NOT NULL, PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

			}

			

			if(isset($weka_enable_order_push) && $weka_enable_order_push_existing != 1) { // Checks whether an Order Webhook needs to be created

				$webhook_secret = createWekaOrderCreationWebhook();

				$update_advancedweka_options = 1;

				$options['settings']['s_webhook_secret'] = $webhook_secret;

			}

			else if ($weka_enable_order_push_existing == 1 && !isset($weka_enable_order_push)) { // Checks whether the Order Webhook needs to be deleted

				deleteWekaWebhooks();

				$update_advancedweka_options = 1;

				$options['settings']['s_webhook_secret'] = '';

			}

		}

		if($submit == 'Update Product Settings')

			update_option( 'weka_enable_matrix_items', $weka_enable_matrix_items );



		if($update_advancedweka_options == 1) { // Post the $options array to advancedweka to update the settings / options

			$options = json_encode($options);

			$url = 'https://advancedweka.wekaonline.co.nz/rest/options.php';

			$curl = new WekaAdvancedCurl();

			$advancedProduct = $curl->postForm($url, $username,$password,$options);

		}



		$_SESSION['done'] = 1;

	}



	if($_SESSION['done'] == 1) { // Display a notification message on update

		?>





<div class="notice notice-success is-dismissible">

  <p>

    <?php _e( 'Settings Updated', 'integration-settings' ); ?>

  </p>

</div>

<?php	

	}

	

	$username = get_option( 'weka_username'); // Username of the advancedweka login

	$password = get_option( 'weka_password'); // Pasword of the advancedweka login

	

	//Get the active tab from the $_GET param

	$default_tab = 'settings'; // Set the default tab

	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;



	$_SESSION['done'] = 0;

	

	?>

	<div class="wrap">

    <!-- Print the page title -->

    <!-- Display the various tabs of the plugin -->

    <nav class="nav-tab-wrapper">

      <a href="?page=advanced_woo_integration&tab=settings" class="nav-tab <?php if($tab==='settings'):?>nav-tab-active<?php endif; ?>">Settings</a>

	<?php if(!empty($username) && $username != '' && !empty($password) && $password != '') {

		?>

		<a href="?page=advanced_woo_integration&tab=products" class="nav-tab <?php if($tab==='products'):?>nav-tab-active<?php endif; ?>">Products</a>

		<a href="?page=advanced_woo_integration&tab=customers" class="nav-tab <?php if($tab==='customers'):?>nav-tab-active<?php endif; ?>">Customers</a>

		<a href="?page=advanced_woo_integration&tab=orders" class="nav-tab <?php if($tab==='orders'):?>nav-tab-active<?php endif; ?>">Orders</a>

		<?php

	}

	?>

    </nav>



    <div class="tab-content">

    <?php 

	switch($tab) : // display the currently selected tab

	case 'settings':

		$weka_enable_sales_prices = get_option( 'weka_enable_sales_prices');

		$weka_enable_order_push = get_option( 'weka_enable_order_push');

		$weka_enable_quotes = get_option( 'weka_enable_quotes');

		$weka_enable_invoices = get_option( 'weka_enable_invoices');

		$defaultTax = get_option('weka_integration_opportunity_tax') * 100;

	

		$weka_settings = get_option('weka_settings');

		$weka_settings = json_decode($weka_settings,true);

	

	$readonly = '';

	if(isset($password))

		$readonly = 'readonly';

	

		/**

		 * Display the form fields for the general settings page.

		 **/

		echo '

		<form method="post" action="" onsubmit="return confirm(\'Do you want to update the settings?\');">

		<h2>General Settings</h2>

		<table class="form-table">

		<tbody>

			<tr>

		<th scope="row"><label for="newrole">Weka Username</label></th>

		<td><input name="weka_username" id="weka_username" value="'.$username.'" class="regular-text" type="text"></td>

		</tr>

			<tr>

		<th scope="row"><label for="newrole">Weka Password</label></th>

		<td><input name="weka_password" id="weka_password" value="'.$password.'" class="regular-text" type="password"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Admin Email</label></th>

		<td><input name="weka_settings[s_client_email]" value="'.$weka_settings['s_client_email'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Admin Alt Email</label></th>

		<td><input name="weka_settings[s_client_email_2]" value="'.$weka_settings['s_client_email_2'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Enable MYOB Sales Prices Sync</label></th>

		<td><input type="checkbox" name="weka_enable_sales_prices" id="weka_enable_sales_prices" value="1" ' . checked( 1, $weka_enable_sales_prices, false ) . '></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Enable Sales Order Sync</label></th>

		<td><input type="checkbox" name="weka_enable_order_push" id="weka_enable_order_push" value="1" ' . checked( 1, $weka_enable_order_push, false ) . '></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Enable MYOB Quotes Sync</label></th>

		<td><input type="checkbox" name="weka_enable_quotes" id="weka_enable_quotes" value="1" ' . checked( 1, $weka_enable_quotes, false ) . '></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Quote Tax Amount</label></th>

		<td><input name="weka_integration_opportunity_tax" id="weka_integration_opportunity_tax" value="'.$defaultTax.'" class="regular-text" type="text">%</td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Enable MYOB Sales Invoices Sync</label></th>

		<td><input type="checkbox" name="weka_enable_invoices" id="weka_enable_invoices" value="1" ' . checked( 1, $weka_enable_invoices, false ) . '></td>

		</tr>

		';



		/**

		 * Display the form fields for the WooCommerce settings section.

		 **/

		echo '</tbody></table>

		<h2>WooCommerce Settings</h2>

		<table class="form-table">

		<tbody>

			<tr>

		<th scope="row"><label for="newrole">Active URL</label></th>

		<td><input name="weka_settings[s_website_address]" value="'.$weka_settings['s_website_address'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">API Consumer Key</label></th>

		<td><input name="weka_settings[s_consumer_key]" value="'.$weka_settings['s_consumer_key'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">API Consumer Secret</label></th>

		<td><input name="weka_settings[s_consumer_secret]" value="'.$weka_settings['s_consumer_secret'].'" class="regular-text" type="password"></td>

		</tr>

		</tbody></table>';

		

		/**

		 * Display the form fields for the MYOB Advanced settings section.

		 **/

		echo '

		<h2>MYOB Advanced Settings</h2>

		<table class="form-table">

		<tbody>

			<tr>

		<th scope="row"><label for="newrole">URL</label></th>

		<td><input name="weka_settings[s_advanced_url]" value="'.$weka_settings['s_advanced_url'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Username</label></th>

		<td><input name="weka_settings[s_advanced_username]" value="'.$weka_settings['s_advanced_username'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Password</label></th>

		<td><input name="weka_settings[s_advanced_password]" value="'.$weka_settings['s_advanced_password'].'" class="regular-text" type="password"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Company</label></th>

		<td><input name="weka_settings[s_advanced_company]" value="'.$weka_settings['s_advanced_company'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Branch</label></th>

		<td><input name="weka_settings[s_advanced_branch]" value="'.$weka_settings['s_advanced_branch'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Extension</label></th>

		<td><input name="weka_settings[s_advanced_extension]" value="'.$weka_settings['s_advanced_extension'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Version</label></th>

		<td><input name="weka_settings[s_advanced_version]" value="'.$weka_settings['s_advanced_version'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Push Notification Secret</label></th>

		<td><input name="weka_settings[s_advanced_webhook_secret]" value="'.$weka_settings['s_advanced_webhook_secret'].'" class="regular-text" type="password"></td>

		</tr>

		</tbody></table>

		<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Update General Settings" type="submit" /></p>

		</form>';

        break;

	case 'products': // This will be the Products settings page

		$weka_products = get_option('weka_products'); // The array of the product options

		$weka_products = json_decode($weka_products,true);

		$weka_enable_matrix_items = get_option( 'weka_enable_matrix_items');

	

		// Get all product fields from a MYOB Advanced Stock Item

		$url = 'https://advancedweka.wekaonline.co.nz/rest/products.php?getProductFields=1';

		$curl = new WekaAdvancedCurl();

		$advancedProduct = json_decode($curl->postForm($url, $username,$password,''),true);

	

		// Generate select box options for all available attributes for a MYOB Advanced Stock Item

		$selecting='';

		foreach($advancedProduct['Attributes'] as $Attribute) {

			if($weka_products['attribute_show_on_web'] == $Attribute['AttributeID']['value'])

				$selecton = ' selected=selected';

			else

				$selecton = '';

			$selecting .= '<option value="'.$Attribute['AttributeID']['value'].'"'.$selecton.'>'.$Attribute['AttributeID']['value'].'</option>';

		}

		// Get the existing values from the Products options

		$filter_category_required = $filter_image_required = $filter_manage_stock = $allow_backorders = $post_as_draft = $sync_title = $sync_description = $sync_regular_price = $sync_sales_price = $sync_weight = '';

		if(isset($weka_products['filter_category_required']))

			$filter_category_required = $weka_products['filter_category_required'];

		if(isset($weka_products['filter_image_required']))

			$filter_image_required = $weka_products['filter_image_required'];

		if(isset($weka_products['filter_manage_stock']))

			$filter_manage_stock = $weka_products['filter_manage_stock'];

		if(isset($weka_products['default_warehouse']))

			$default_warehouse = $weka_products['default_warehouse'];

		if(isset($weka_products['allow_backorders']))

			$allow_backorders = $weka_products['allow_backorders'];

		if(isset($weka_products['post_as_draft']))

			$post_as_draft = $weka_products['post_as_draft'];

		if(isset($weka_products['sync_title']))

			$sync_title = $weka_products['sync_title'];

		if(isset($weka_products['sync_description']))

			$sync_description = $weka_products['sync_description'];

		if(isset($weka_products['sync_regular_price']))

			$sync_regular_price = $weka_products['sync_regular_price'];

		if(isset($weka_products['sync_sales_price']))

			$sync_sales_price = $weka_products['sync_sales_price'];

		if(isset($weka_products['sync_weight']))

			$sync_weight = $weka_products['sync_weight'];

		

		/**

		 * Display the form fields for the MYOB Advanced Products Tab.

		 **/

        echo '

		<h2>Product Settings</h2>

		<form method="post" action="" onsubmit="return confirm(\'Do you want to update the settings?\');"><table class="form-table">

		<tbody>

		<tr>

		<th scope="row"><label for="newrole">Sync Matrix Items</label></th>

		<td><input type="checkbox" name="weka_enable_matrix_items" id="weka_enable_matrix_items" value="1" ' . checked( 1, $weka_enable_matrix_items, false ) . '></td>

		</tr>

		<tr>

		<th>Display on Web Attribute:</th><td><select name="weka_products[attribute_show_on_web]" class="regular-text"><option value="no">No Attribute</option>'.$selecting.'</select></td>

		</tr>

		<tr>

		<th>Sync Product Categories</th>

		<td><select name="weka_products[filter_category_required]" class="regular-text"><option value="no">No</option><option value="yes" '; if($filter_category_required == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Product Image</th>

		<td><select name="weka_products[filter_image_required]" class="regular-text"><option value="no">No</option><option value="yes" '; if($filter_image_required == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Stock</th>

		<td><select name="weka_products[filter_manage_stock]" class="regular-text"><option value="no">No</option><option value="yes" '; if($filter_manage_stock == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th scope="row"><label>Default Warehouse for Stock</label></th>

		<td><input name="weka_products[default_warehouse]" id="weka_products[default_warehouse]" value="'.$weka_products['default_warehouse'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th>Allow Backorders</th>

		<td><select name="weka_products[allow_backorders]" class="regular-text"><option value="no">No</option><option value="yes" '; if($allow_backorders == 'yes') { echo 'selected="selected"'; } echo '>Yes</option><option value="notify" '; if($allow_backorders == 'notify') { echo 'selected="selected"'; } echo '>Allow, but notify</option><strong></strong></select></td>

		</tr>

		<tr>

		<th>New Product Status</th>

		<td><select name="weka_products[post_as_draft]" class="regular-text"><option value="no">Published</option><option value="yes" '; if($post_as_draft == 'yes') { echo 'selected="selected"'; } echo '>Draft</option></select></td>

		</tr>

		<tr>

		<th>Sync Product Title</th>

		<td><select name="weka_products[sync_title]" class="regular-text"><option value="no">No</option><option value="yes" '; if($sync_title == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Product Description</th>

		<td><select class="form-control" name="weka_products[sync_description]"><option value="no">No</option><option value="yes" '; if($sync_description == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Regular Price</th>

		<td><select name="weka_products[sync_regular_price]" class="regular-text"><option value="no">No</option><option value="yes" '; if($sync_regular_price == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Sales Price</th>

		<td><select name="weka_products[sync_sales_price]" class="regular-text"><option value="no">No</option><option value="yes" '; if($sync_sales_price == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Weight</th>

		<td><select name="weka_products[sync_weight]" class="regular-text"><option value="no">No</option><option value="yes" '; if($sync_weight == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		';



		echo '</tbody></table>

		<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Update Product Settings" type="submit" /></p>

		</form>';

        break;

	case 'customers': // This will be the Customers settings page

		$weka_customers = get_option('weka_customers'); // The array of Customer options

		$weka_customers = json_decode($weka_customers,true);

	

		// Get the existing values from the Customer options

		$create_web_user = ''; $sync_billing_address = ''; $sync_shipping_address = '';

		if(isset($weka_customers['create_web_user']))

			$create_web_user = $weka_customers['create_web_user'];

		if(isset($weka_customers['sync_billing_address']))

			$sync_billing_address = $weka_customers['sync_billing_address'];

		if(isset($weka_customers['sync_shipping_address']))

			$sync_shipping_address = $weka_customers['sync_shipping_address'];



		/**

		 * Display the form fields for the MYOB Advanced Customer Tab.

		 **/

        echo '

		<h2>Customer Settings</h2>

		<form method="post" action="" onsubmit="return confirm(\'Do you want to update the settings?\');"><table class="form-table">

		<tbody>

		<tr>

		<th>Create new users on Web</th>

		<td><select name="weka_customers[create_web_user]" class="regular-text"><option value="no">No</option><option value="yes" '; if($create_web_user == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Billing Address</th>

		<td><select name="weka_customers[sync_billing_address]" class="regular-text"><option value="no">No</option><option value="yes" '; if($sync_billing_address == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Sync Shipping Address</th>

		<td><select name="weka_customers[sync_shipping_address]" class="regular-text"><option value="no">No</option><option value="yes" '; if($sync_shipping_address == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Default Tax Registration</label></th>

		<td><input name="weka_customers[filter_tax_registration_value]" id="weka_customers[filter_tax_registration_value]" value="'.$weka_customers['filter_tax_registration_value'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Default Shipping Branch</label></th>

		<td><input name="weka_customers[shipping_branch_value]" id="weka_customers[shipping_branch_value]" value="'.$weka_customers['shipping_branch_value'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Default Price Class</label></th>

		<td><input name="weka_customers[filter_price_class_value]" id="weka_customers[filter_price_class_value]" value="'.$weka_customers['filter_price_class_value'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Default Customer Class</label></th>

		<td><input name="weka_customers[filter_customer_class_value]" id="weka_customers[filter_customer_class_value]" value="'.$weka_customers['filter_customer_class_value'].'" class="regular-text" type="text"></td>

		</tr>

		';



		echo '</tbody></table>

		<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Update Customer Settings" type="submit" /></p>

		</form>';

        break;

	case 'orders': // This will be the Sales Order settings page

		$weka_sales_orders = get_option('weka_sales_orders'); // The array of Sales Order options

		$weka_sales_orders = json_decode($weka_sales_orders,true);

	

		$weka_shipping = get_option('weka_shipping'); // The array of Sales Order options

		$weka_shipping = json_decode($weka_shipping,true);

	

		// Get the existing values from the Sales Order options

		$override_hold_status = $filter_GST_inclusive_required = $filter_freight_line_item = '';

		if(isset($weka_sales_orders['override_hold_status']))

			$override_hold_status = $weka_sales_orders['override_hold_status'];

		if(isset($weka_sales_orders['filter_GST_inclusive_required']))

			$filter_GST_inclusive_required = $weka_sales_orders['filter_GST_inclusive_required'];

		if(isset($weka_sales_orders['filter_freight_line_item']))

			$filter_freight_line_item = $weka_sales_orders['filter_freight_line_item'];



		/**

		 * Display the form fields for the MYOB Advanced Sales Order Tab.

		 **/

        echo '

		<h2>Sales Order Settings</h2>

		<form method="post" action="" onsubmit="return confirm(\'Do you want to update the settings?\');">

		<table class="form-table">

		<tbody>

		<tr>

		<th>Prevent Hold Status in MYOB Advanced</th>

		<td><select name="weka_sales_orders[override_hold_status]" class="regular-text"><option value="no">No</option><option value="yes" '; if($override_hold_status == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>GST Inclusive Line Item Amounts</th>

		<td><select name="weka_sales_orders[filter_GST_inclusive_required]" class="regular-text"><option value="no">No</option><option value="yes" '; if($filter_GST_inclusive_required == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th>Include Discount Info on Line Items</th>

		<td><select name="weka_sales_orders[filter_discount_info_required]" class="regular-text"><option value="no">No</option><option value="yes" '; if($filter_discount_info_required == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Customer ID for all SO</label></th>

		<td><input name="weka_sales_orders[default_customer_id]" id="weka_sales_orders[default_customer_id]" value="'.$weka_sales_orders['default_customer_id'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Default Branch for SO</label></th>

		<td><input name="weka_sales_orders[filter_branch_value]" id="weka_sales_orders[filter_branch_value]" value="'.$weka_sales_orders['filter_branch_value'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label for="newrole">Default Freight Tax for SO</label></th>

		<td><input name="weka_sales_orders[default_freight_tax_value]" id="weka_sales_orders[default_freight_tax_value]" value="'.$weka_sales_orders['default_freight_tax_value'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th>Sync Freight as a line Item</th>

		<td><select name="weka_sales_orders[filter_freight_line_item]" class="regular-text"><option value="no">No</option><option value="yes" '; if($filter_freight_line_item == 'yes') { echo 'selected="selected"'; } echo '>Yes</option></select></td>

		</tr>

		<tr>

		<th scope="row"><label>Freight Line Item SKU</label></th>

		<td><input name="weka_sales_orders[default_freight_sku]" id="weka_sales_orders[default_freight_sku]" value="'.$weka_sales_orders['default_freight_sku'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label>Default Order Type for SO</label></th>

		<td><input name="weka_sales_orders[default_order_type]" id="weka_sales_orders[default_order_type]" value="'.$weka_sales_orders['default_order_type'].'" class="regular-text" type="text"></td>

		</tr>

		<tr>

		<th scope="row"><label>Default Warehouse for SO</label></th>

		<td><input name="weka_sales_orders[default_warehouse]" id="weka_sales_orders[default_warehouse]" value="'.$weka_sales_orders['default_warehouse'].'" class="regular-text" type="text"></td>

		</tr>

		';

		echo '</tbody></table>';

		echo '<h3 style="width:100%; clear:both;">Connect Payment Methods</h3>';

		echo '<table class="form-table">';

		echo '<thead><tr><th>WooCommerce</th><td><b>MYOB Advanced</b></td></tr></thead><tbody>';

	

		// Get Payment Methods from WooCommcer Class and convert to an array

		$WooPaymentList = WC()->payment_gateways->payment_gateways();

		$WooPaymentList = json_encode($WooPaymentList);

		$WooPaymentList = json_decode($WooPaymentList,true);

	

	

		// Get all Payment Methods from MYOB Advanced

		$url = 'https://advancedweka.wekaonline.co.nz/rest/orders.php?getPaymentMethods=1';

		$curl = new WekaAdvancedCurl();

		$advancedPaymentSelect = json_decode($curl->postForm($url, $username,$password,''),true);

	

		foreach($WooPaymentList as $identity => $row) {

			$PaymentMethodValue = '';

			if(isset($weka_sales_orders['payment_method-'.$row['id']]))

				$PaymentMethodValue = $weka_sales_orders['payment_method-'.$row['id']];

			$PaymentMethodFeeValue = '';

			if(isset($weka_sales_orders['payment_method_fee-'.$row['id']]))

				$PaymentMethodFeeValue = $weka_sales_orders['payment_method_fee-'.$row['id']];

			$PaymentMethodFeeField = '';

			if(isset($weka_sales_orders['payment_method_fee_field-'.$row['id']]))

				$PaymentMethodFeeField = $weka_sales_orders['payment_method_fee_field-'.$row['id']];

			$selectMYOB = wekaAdvancedSelectBox($advancedPaymentSelect,$PaymentMethodValue);

			echo '<tr><td scope="row"><label>'.$row['title'].'</label></td><td><select name="weka_sales_orders[payment_method-'.$row['id'].']" class="regular-text"><option value="">Not Required</option>'.$selectMYOB.'</select></td>';			

		}

		echo '</tbody></table>';

		echo '<h3 style="width:100%; clear:both;">Connect Shipping Methods</h3>';

		echo '<table class="form-table">';

		echo '<thead><tr><th>WooCommerce</th><td><b>MYOB Advanced</b></td></tr></thead><tbody>';



		$WooShippingList = WC()->shipping->get_shipping_methods();

			

		// Get all Shipping Methods from MYOB Advanced

		$url = 'https://advancedweka.wekaonline.co.nz/rest/orders.php?getShippingMethods=1';

		$curl = new WekaAdvancedCurl();

		$advancedShippingSelect = json_decode($curl->postForm($url, $username,$password,''),true);

		foreach($WooShippingList as $identity => $row) {

			$rowArray = json_encode($row);

			$rowArray = json_decode($row,true);

			if(isset($rowArray['method_title'])){

				$title = $rowArray['method_title'];

			}

			else {

				$title = $row->method_title;

			}

			if(isset($rowArray['id'])){

				$id = $rowArray['id'];

			}

			else {

				$id = $row->id;

			}

			$ShippingMethodValue = '';

			if(isset($weka_shipping['shipping_method-'.$id])){

				$ShippingMethodValue = $weka_shipping['shipping_method-'.$id];

			}

			$selectMYOBShipping = wekaAdvancedSelectBox($advancedShippingSelect,$ShippingMethodValue);

			echo '<tr><td>'.$title.'</td><td><select name="weka_shipping[shipping_method-'.$id.']" class="regular-text"><option value="">Not Required</option>'.$selectMYOBShipping.'</select></td></tr>';			

		}

		echo '</tbody></table>';

		echo '

		<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Update Order Settings" type="submit" /></p>

		</form>';

        break;

    endswitch; ?>

    </div>

  </div>

<?php

}







class WekaAdvancedCurl

{       



    public $cookieJar = "";



    public function __construct($cookieJarFile = 'cookies.txt') {

        $this->cookieJar = $cookieJarFile;

    }



    function setup($username,$password)

    {

        $header = array();

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";

        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";

        $header[] =  "Cache-Control: max-age=0";

        $header[] =  "Connection: keep-alive";

        $header[] = "Keep-Alive: 300";

        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";

        $header[] = "Accept-Language: en-us,en;q=0.5";

        $header[] = "Pragma: "; // browsers keep this blank.





		curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");  

        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($this->curl,CURLOPT_AUTOREFERER, true);

        curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($this->curl,CURLOPT_RETURNTRANSFER, true);  

    }





    function get($url)

    { 

        $this->curl = curl_init($url);

        $this->setup();



        return $this->request();

    }



    function getAll($reg,$str)

    {

        preg_match_all($reg,$str,$matches);

        return $matches[1];

    }



    function postForm($url, $username,$password, $fields, $referer='')

    {

        $this->curl = curl_init($url);

        $this->setup($username,$password);

        curl_setopt($this->curl, CURLOPT_URL, $url);

        curl_setopt($this->curl, CURLOPT_POST, 1);

        curl_setopt($this->curl, CURLOPT_REFERER, $referer);

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, array('input'=>$fields));

        return $this->request();

    }

	

	function postOptions($url, $username,$password, $fields, $referer='')

    {

        $this->curl = curl_init($url);

        $this->setup($username,$password);

        curl_setopt($this->curl, CURLOPT_URL, $url);

        curl_setopt($this->curl, CURLOPT_POST, 1);

        curl_setopt($this->curl, CURLOPT_REFERER, $referer);

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);

        return $this->request();

    }



    function getInfo($info)

    {

        $info = ($info == 'lasturl') ? curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL) : curl_getinfo($this->curl, $info);

        return $info;

    }



    function request()

    {

        return curl_exec($this->curl);

    }

}



// Adding Meta container box to the admin sales_order page for resyncing an order

add_action( 'add_meta_boxes', 'weka_advanced_add_meta_boxes' );

function weka_advanced_add_meta_boxes()

{

	add_meta_box( 'weka_other_fields', __('MYOB Advanced Integration','woocommerce'), 'weka_advanced_add_other_fields_for_MYOB', 'shop_order', 'side', 'low' );

}



function weka_advanced_add_other_fields_for_MYOB($post)

{

	$advanced_id = get_post_meta( $post->ID, 'advanced_id', true ) ? get_post_meta( $post->ID, 'advanced_id', true ) : ''; // Get the current advanced_id value



	echo '<div class="weka-loader"></div>

			<div class="myob-section">

			<input type="hidden" id="weka_order_id" value="'.$post->ID.'"/>

			';

	if($advanced_id == '' || $advanced_id == 'no') { // display button to resync order if it has not been synced

		echo '<input type="hidden" id="weka_token339kro4" value="489fk94or" />

		<ul>

		<li class="resync-weka-li"><span id="weka_advanced_id"></span></li>

		<li class="resync-weka-li"><input class="resyncWekaAdvanced button-primary button" type="button" id="weka-sync-order" value="Sync Order"></li>

		</ul>';

	}

	else { // Display the MYOB Advanced ID for an order that has been synced

		echo '<span id="weka_advanced_id">Order Nbr: <span id="weka_advanced_number">'.$advanced_id.'</span></span>

		<input class="resyncWekaAdvanced button-primary button" type="button" id="weka-sync-order" value="Resync Order">';

	}

	echo '</div>';

}



//Call ajax

add_action('wp_ajax_resyncWekaAdvanced', 'resyncWekaAdvanced');

function resyncWekaAdvanced() {  

	$order_id = $_POST['weka_order_id'];

	

	delete_post_meta($_POST['weka_order_id'], 'advanced_id');

    $advanced_response = weka_advanced_integration_woocommerce_resync_order( $_POST['weka_order_id'] );

	if($advanced_response == '' || $advanced_response == 'no') {

		$output['error'] = 1;

		$output['result'] = 'An error occurred, please try again.';

		

	}

	else {

		$output['error'] = 0;

		$output['result'] = 'Order Nbr: <span id="weka_advanced_number">'.$advanced_response.'</span>';

	}

	echo json_encode($output);

    exit;

}



//In your case you can add script in your style

//Add script to resync an order

add_action('admin_head','weka_advanced_ajax_script');

function weka_advanced_ajax_script(){ ?>

    <script>

    jQuery(document).ready(function ($) {

        $('.resyncWekaAdvanced').on('click', function () {

            var weka_order_id = $('#weka_order_id').val();

			if (confirm('This will unlink the web order with any existing SO in MYOB Advanced and create a new SO.\n\n Are you sure you would like to sync this order to MYOB Advanced?')) {

				$.ajax({

				  type: 'POST',

				  url: ajaxurl,

				  data: 'weka_order_id=' + weka_order_id+'&action=resyncWekaAdvanced',

				  dataType: 'json',

					beforeSend: function() {

						jQuery('.weka-loader').show();

						jQuery('.myob-section').hide();

					},

					complete: function (response) {

						jQuery('.weka-loader').hide();

						jQuery('.myob-section').show();

						jQuery('#weka_advanced_id').html(response.responseJSON.result);

						if(response.responseJSON.error == 0) {

							//jQuery('#weka-sync-order').hide();

						}

					}

				});

			}

        }); 

    });

    </script>

<?php

}



function weka_advanced_integration_woocommerce_resync_order( $order_id ) { // Function to resync an order	

	$user_id = get_post_meta( $order_id, '_customer_user', true );

	$customerID = get_user_meta( $user_id, 'advanced_id', true ); 	

	$username = get_option( 'weka_username');

	$password = get_option( 'weka_password');



	// string length should be 10

	if(strlen($customerID) > 20) {

		$customerID = '';

	}

	

	$create_customer = 1;

	$weka_sales_orders = get_option('weka_sales_orders'); // The array of Sales Order options

	$weka_sales_orders = json_decode($weka_sales_orders,true);

	if(isset($weka_customers['default_customer_id'])) { // Check if a default Customer ID has been set

		if($order_options['default_customer_id'] != '') {

			$customerID = $order_options['default_customer_id'];

			$customerID = urlencode($customerID);

			$create_customer = 0;

		}

	}

	if($create_customer == 1) // Create a Customer in MYOB Advanced if needs to

	{

		if($user_id > 0) {

			if($customerID == '' || is_null($customerID)) {

				$url = 'https://advancedweka.wekaonline.co.nz/integrators/customers.php?order='.$order_id;

				$curl = new WekaAdvancedCurl();

				$customerID = $curl->postForm($url, $username,$password,'');		

			}	

		} else {

				$url = 'https://advancedweka.wekaonline.co.nz/integrators/customers.php?order='.$order_id;

				$curl = new WekaAdvancedCurl();

				$customerID = $curl->postForm($url, $username,$password,'');				

		}

	}



	// Send the Sales Order to the integration to be pushed to MYOB Advanced

	$url = 'https://advancedweka.wekaonline.co.nz/integrators/sales_orders.php?CustomerID='.$customerID.'&updateSalesOrder='.$order_id;

	$curl = new WekaAdvancedCurl();

	$advanceNumber = $curl->postForm($url, $username,$password,'');	

	return $advanceNumber;

}





// Simple, grouped and external products



$weka_enable_sales_prices = get_option( 'weka_enable_sales_prices');

if($weka_enable_sales_prices) { // Display the Customer specific sales prices for products if enabled

	add_filter('woocommerce_product_variation_get_regular_price', 'weka_advanced_custom_price', 10, 2 );

	add_filter('woocommerce_product_variation_get_price', 'weka_advanced_custom_price' , 10, 2 );



	add_filter('woocommerce_product_get_regular_price', 'weka_advanced_custom_price', 10, 2 );

	add_filter('woocommerce_product_get_price', 'weka_advanced_custom_price' , 10, 2 );



	// Variations (of a variable product)

	add_filter('woocommerce_variation_get_price', 'weka_advanced_custom_variation_price', 10, 3 );

	add_filter('woocommerce_variation_get_regular_price', 'weka_advanced_custom_variation_price', 10, 3 );

	

	add_action( 'woocommerce_before_calculate_totals', 'weka_product_quantity_discounter', 20, 1 );

}





function weka_advancedrestrictly_get_current_user_role() {

	if( is_user_logged_in() ) {

		$user = wp_get_current_user();

		$role = ( array ) $user->roles;

		return $role[0];

	} else {

		return false;

	}

}



// Function to get the Customer Specific price for the simple product (ability for Quantity Price Break as well)

function weka_advanced_custom_price( $price, $product ) {

	if($price == 0)

		return $price;



	global $wpdb;

	if( is_user_logged_in() ) {

		$user = wp_get_current_user();

		$role = ( array ) $user->roles;

		$PriceClass = $role[0];

		$customerID = get_user_meta( $user->ID, 'advanced_id', true );

	}

	else {

		$PriceClass = 'retail';

	}

	wc_delete_product_transients($product->get_id());

	

	// Check whether the price has been discounted due to the quantity price breaks

	$price_array = $wpdb->get_results("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = '".$PriceClass."' AND `product_sku` = '".$product->get_sku()."' AND `quantity` >= 0 ORDER BY id DESC");

	if(is_array($price_array)) {

		if(count($price_array) > 0) {

			foreach($price_array as $price_result) {

				if($price_result->price == $price)

					return $price;

			}

		}

	}

	if(isset($customerID)){

		if(strlen($customerID) > 0) {

			$specific = $wpdb->get_var("SELECT price FROM `wp_woocommerce_customer_pricing` WHERE `role` = '".$customerID."' AND `product_sku` = '".$product->get_sku()."' AND `quantity` <= 0 ORDER BY id DESC");

			if($specific > 0)

				return $specific;

		}

	}

	$new = $wpdb->get_var("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = '".$PriceClass."' AND `product_sku` = '".$product->get_sku()."' AND `quantity` <= 0 ORDER BY id DESC");

	if($new > 0)

		return $new;

	else {

		$new = $wpdb->get_var("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = 'RETAIL' AND `product_sku` = '".$product->get_sku()."' AND `quantity` <= 0 ORDER BY id DESC");

		if($new > 0)

			return $new;

		else

			return $price;

	}

}



// Function to get the Customer Specific price for the variation product (ability for Quantity Price Break as well)

function weka_advanced_custom_variation_price( $price, $variation, $product ) {

	global $wpdb;

	if( is_user_logged_in() ) {

		$user = wp_get_current_user();

		$role = ( array ) $user->roles;

		$PriceClass = $role[0];

		$customerID = get_user_meta( $user->ID, 'advanced_id', true );

	}

	else {

		$PriceClass = 'retail';

	}

	wc_delete_product_transients($variation->get_id());



	// Check whether the price has been discounted due to the quantity price breaks

	$price_array = $wpdb->get_results("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = '".$PriceClass."' AND `product_sku` = '".$variation->get_sku()."' AND `quantity` >= 0 ORDER BY id DESC");

	if(is_array($price_array)) {

		if(count($price_array) > 0) {

			foreach($price_array as $price_result) {

				if($price_result->price == $price)

					return $price;

			}

		}

	}

	if(isset($customerID)){

		if(strlen($customerID) > 0) {

			$specific = $wpdb->get_var("SELECT price FROM `wp_woocommerce_customer_pricing` WHERE `role` = '".$customerID."' AND `product_sku` = '".$variation->get_sku()."' AND `quantity` <= 0 ORDER BY id DESC");

			if($specific > 0)

				return $specific;

		}

	}

	$new = $wpdb->get_var("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = '".$PriceClass."' AND `product_sku` = '".$variation->get_sku()."' AND `quantity` <= 0");

	if($new > 0)

		return $new;

	else {

		$new = $wpdb->get_var("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = 'RETAIL' AND `product_sku` = '".$variation->get_sku()."' AND `quantity` <= 0");

		if($new > 0)

			return $new;

		else

			return $price;

	}

	

}



// Additional function to accommodate Quantity Price Breaks on the cart and checkout page

function weka_product_quantity_discounter( $cart ) {

	global $wpdb;

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )

        return;



    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )

        return;

	

	if( is_user_logged_in() ) {

		$user = wp_get_current_user();

		$role = ( array ) $user->roles;

		$PriceClass = $role[0];

	}

	else {

		$PriceClass = 'retail';

	}

	

	$PriceClass = strtoupper($PriceClass);



    // Loop through the cart items

    foreach( $cart->get_cart() as $cart_item ){

        $product_id  = $cart_item['product_id'];

		if($cart_item['variation_id'] > 0)

			$product_id = $cart_item['variation_id'];

		$product = wc_get_product( $product_id );

		$product_sku = $product->get_sku();

        $quantity    = $cart_item['quantity'];

		/*

		 * Get the largest quantity price break for that product.

		 * i.e customer has 10 in the cart, this product has price breaks for 0, 4, 8, 24. Will need to get the price for the price break of 8

		 * */

		$new_price = $wpdb->get_var("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = '".$PriceClass."' AND `product_sku` = '".$product_sku."' AND `quantity` <= ".$quantity." ORDER BY quantity DESC");

		if($new_price > 0) {

			$cart_item['data']->set_price($new_price); // set the new discounted calculated price

		}else {

			$new_price = $wpdb->get_var("SELECT price FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `role` = 'RETAIL' AND `product_sku` = '".$product_sku."' AND `quantity` <= ".$quantity." ORDER BY quantity DESC");

			if($new > 0)

				$cart_item['data']->set_price($new_price); // set the new discounted calculated price

		}

		

    }

}



function weka_price($price) {

	$price = preg_replace("/[^0-9\.]/", "",$price);

	$price = '$'.number_format($price,2);

	return $price;

}



if(isset($_REQUEST['quote']) && get_option( 'weka_enable_quotes') == 1) {

	

	add_action('init','weka_quote_request');

	function weka_quote_request(){

		$current_user = wp_get_current_user();

		$quote = get_user_meta($current_user->ID, 'quote', true); 

		$quote_current = get_user_meta($current_user->ID, 'quote_current', true); 

		

		switch($_REQUEST['quote']) {

			case 'remove':

				global $wpdb;

				$quote_id = $_REQUEST['quote_id'];

				$product_id = $_REQUEST['product'];

				$wpdb->query("DELETE FROM {$wpdb->prefix}quotes_lines WHERE quote_id = '$quote_id' AND product_id = '$product_id'");

				unset($quote[$_REQUEST['product']]);

				if(count($quote[$_REQUEST['product']]) == 0)

					unset($quote[$_REQUEST['product']]);

				update_user_meta( $current_user->ID, 'quote', $quote );	

				update_user_meta($current_user->ID, 'quote_status', 'Save');	

			break;

			case 'update':

				$quote[$_REQUEST['product']]['quantity'] = $_REQUEST['quantity'];

				update_user_meta( $current_user->ID, 'quote', $quote );	

				update_user_meta($current_user->ID, 'quote_status', 'Save');  

				

				echo '$'.number_format($_REQUEST['quantity']*$quote[$_REQUEST['product']]['price'],2);

			break;

			case 'save':

				global $wpdb;



				$table = "{$wpdb->prefix}quotes_lines";

				$quote_id = $_REQUEST['quote_id'];

				$quote_id = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}quotes WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

				if($quote_id == 0 OR $quote_id == '') {

					$quote_id = $wpdb->insert("{$wpdb->prefix}quotes",array('CustomerID'=>$current_user->ID,'Status'=>'Open','Subject'=>$_REQUEST['name']));

					

					$quote_id = $wpdb->insert_id;

					

					$quote_amount = 0;

					foreach($quote as $product_id => $item) {

						$data = array(

							'quote_id' => $quote_id,

							'product_id' => $product_id,

							'quantity' => $item['quantity'],

							'price' => $item['price'],

							'amount' => $item['price']*$item['quantity']					

						);

						$wpdb->insert($table,$data);

						$quote_amount += ($item['price']*$item['quantity']);	 	 	 	

					}

					

					

					$defaultTax = get_option('weka_integration_opportunity_tax');

					$quote_total = $quote_amount * (1+$defaultTax);

					$wpdb->query("UPDATE {$wpdb->prefix}quotes SET Amount = '".$quote_amount."', Total = '".$quote_total."' WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

					

					update_user_meta($current_user->ID, 'quote_current', $quote_id);

					

					// Will need to push quote update through to MYOB

					$advanceNumber = $wpdb->get_var("SELECT advanced_id FROM {$wpdb->prefix}quotes WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

					$user_id = $current_user->ID;

					$customerID = get_user_meta( $user_id, 'advanced_id', true ); 	

					$username = get_option( 'weka_username');

					$password = get_option( 'weka_password');



					if($rawr){

						if($user_id > 0) {

							if($customerID == '') {

								$url = 'https://advancedweka.wekaonline.co.nz/integrators/customers.php?create='.$user_id;

								$curl = new wekaAdvancedCurl();

								

								$customerID = $curl->postForm($url, $username,$password,'');	

								update_user_meta( $user_id, 'advance_id', $customerID );				

							}	

						}

						

						// Send the Quote to MYOB Advanced on save.

						$url = 'https://advancedweka.wekaonline.co.nz/integrators/opportunities.php?updateOpportunity='.$quote_id;

				

						$curl = new wekaAdvancedCurl();

						$advanceNumber = $curl->postForm($url, $username,$password,'');

					}

					

					

					

					

					echo 'We have created a new quote named ' . $_REQUEST['name'];		

				} else {

					$wpdb->query("UPDATE {$wpdb->prefix}quotes SET Subject = '".addslashes(stripslashes($_REQUEST['name']))."' WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

					$wpdb->query("DELETE FROM {$wpdb->prefix}quotes_lines WHERE quote_id = '$quote_id'");

					$quote_amount = 0;

					foreach($quote as $product_id => $item) {

							$data = array(

								'quote_id' => $quote_id,

								'product_id' => $product_id,

								'quantity' => $item['quantity'],

								'price' => $item['price'],

								'amount' => $item['price']*$item['quantity']					

							);

							$new_row = $wpdb->insert($table,$data);

							$quote_amount += ($item['price']*$item['quantity']);	 	 	 	

					}

					$defaultTax = get_option('weka_integration_opportunity_tax');

					$quote_total = $quote_amount * (1+$defaultTax);

					$wpdb->query("UPDATE {$wpdb->prefix}quotes SET Amount = '".$quote_amount."', Total = '".$quote_total."' WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

					update_user_meta($current_user->ID, 'quote_current', ''); 

					echo 'We have updated the existing quote ' . $_REQUEST['name']; 				

				}

				

				$advanceNumber = $wpdb->get_var("SELECT advanced_id FROM {$wpdb->prefix}quotes WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

				$user_id = $current_user->ID;

				$customerID = get_user_meta( $user_id, 'advanced_id', true ); 	

				$username = get_option( 'weka_username'); // Username of the advancedweka login

				$password = get_option( 'weka_password');

				

				if($user_id > 0) {

					if($customerID == '') {

						$url = 'https://advancedweka.wekaonline.co.nz/integrators/customers.php?create='.$user_id;

						$curl = new wekaAdvancedCurl();

						$customerID = $curl->postForm($url, $username,$password,'');	

						update_user_meta( $user_id, 'advance_id', $customerID );				

					}	

				}

				

				// Send the Quote to MYOB Advanced on save.

				$url = 'https://advancedweka.wekaonline.co.nz/integrators/opportunities.php?updateOpportunity='.$quote_id;

				$curl = new wekaAdvancedCurl();

				$advanceNumber = $curl->postForm($url, $username,$password,'');

		

				update_user_meta($current_user->ID, 'quote_status', 'Saved');  

				update_user_meta($current_user->ID, 'quote', '');  

			break;

			case 'delete':

				global $wpdb;

				$quote_id = $_REQUEST['quoteID'];

				$myposts = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}quotes WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

				

				$advanced_id = $wpdb->get_var("SELECT advanced_id FROM {$wpdb->prefix}quotes WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

				$username = get_option( 'weka_username'); // Username of the advancedweka login

				$password = get_option( 'weka_password');

				

				foreach ( $myposts as $mypost ) 

				{

					$wpdb->query("DELETE FROM {$wpdb->prefix}quotes WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_id'");

					$wpdb->query("DELETE FROM {$wpdb->prefix}quotes_lines WHERE quote_id = '$quote_id'");

				}

				if($quote_current==$quote_id) {

					update_user_meta($current_user->ID, 'quote', '');  

				}

				

				$status = 'Lost';

				

				$url = 'https://advancedweka.wekaonline.co.nz/integrators/opportunities.php?updateOpportunity='.$advanced_id.'&OpportunityStatus='.$status;

				$curl = new wekaAdvancedCurl();

				$advanceNumber = $curl->postForm($url, $username,$password,'');

				header("location: /my-account/quotes/");

			break;

			case 'new':

				update_user_meta($current_user->ID, 'quote_current', '');

				update_user_meta($current_user->ID, 'quote', '');  

				header("location: /my-account/quotes/");

			break;

		}

		exit();

	}

}



function my_account_header() {

    global $post;



	$pages['edit-account'] = 'EDIT DETAILS';

	$pages['edit-address'] = 'EDIT ADDRESS';

	$pages['orders'] = 'MY ORDERS';

	$pages['orders/?reports=1'] = 'MY INVOICES';

	$pages['quotes'] = 'MY QUOTES';	

	$pages['customer-logout'] = 'LOGOUT';

	echo '<h1 class="h1title">MY ACCOUNT</h1>

						

	';

	if ( is_user_logged_in() ) {

		echo '<ul id="aboutsubnav">';

		$sections = explode('/',$_SERVER[REQUEST_URI]);

		

		if($sections[2]=='')

			$class = ' class="activesub"';

		else

			$class = '';

		$now = $sections[2];

		

		

		echo '<li><a href="/my-account/"'.$class.'>MY ACCOUNT</a></li>';

		

		foreach($pages as $slug => $page) {

			if($_REQUEST['reports'] == 1 && $slug == 'orders/?reports=1')



				$class=' class="activesub"';			

			else if(($slug == $now && !isset($_REQUEST['reports'])) || ($slug == 'my-account' && $post->post_name == 'register')) {

				$class=' class="activesub"';			

			} else {

				$class="";

			}



			if($slug != 'my-account')

				$slug = 'my-account/'.$slug;

			

			if($slug == 'my-account/customer-logout')

				echo '<li><a href="'.wp_logout_url('/my-account/') .'"'.$class.'>'.$page.'</a></li>';

			else

				echo '<li><a href="/'.$slug.'/"'.$class.'>'.$page.'</a></li>';

		}

		echo '

							</ul>';		

	}



}



// Enable the shortcode if the Customer wants to use Weka Invoices and Quotes

//add_shortcode( 'my_account_header', 'my_account_header' );



// define the woocommerce_after_add_to_cart_form callback 

function action_woocommerce_after_add_to_cart_form(  ) { 

	ob_start();?>

	<form action="/wp-content/plugins/weka-myob-advanced-integration-b2b/includes/addtocart.php" method="POST" id="add-to-quote-submit-form">

		<input type="hidden" name='quantity-test-test' value='0'>

		<input type="submit" id='add-to-quote-submit' name='submitbutton' value="Add To Quote">

	</form>

	<?php echo ob_get_clean();

}; 

add_action( 'woocommerce_after_add_to_cart_form', 'action_woocommerce_after_add_to_cart_form', 10, 0 ); 



function weka_add_premium_support_endpoint() {

    add_rewrite_endpoint( 'quotes', EP_ROOT | EP_PAGES );

	add_rewrite_endpoint( 'invoices', EP_ROOT | EP_PAGES );	

	add_rewrite_endpoint( 'product_returns', EP_ROOT | EP_PAGES );	

}

 

add_action( 'init', 'weka_add_premium_support_endpoint' );

 

 

// ------------------

// 2. Add new query var

 

function weka_premium_support_query_vars( $vars ) {

	$enable_quotes = get_option( 'weka_enable_quotes');

	$enable_invoices = get_option( 'weka_enable_invoices');

	$enable_product_returns = get_option('weka_enable_product_returns');

	if($enable_quotes == 1) {

		$vars[] = 'quotes';	

	}

	if($enable_invoices == 1) {

		$vars[] = 'invoices';

	}

	if($enable_product_returns == 1) {

		$vars[] = 'product_returns';

	}

    return $vars;

}

 

add_filter( 'query_vars', 'weka_premium_support_query_vars', 0 );

 

 

// ------------------

// 3. Insert the new endpoint into the My Account menu

 

function weka_add_premium_support_link_my_account( $items ) {

	$enable_quotes = get_option( 'weka_enable_quotes');

	$enable_invoices = get_option( 'weka_enable_invoices');

	$enable_product_returns = get_option('weka_enable_product_returns');

	if($enable_quotes == 1) {

    	$items['quotes'] = 'My Quotes';

	}

	if($enable_invoices == 1) {

		$items['invoices'] = 'My Invoices';

	}

	if($enable_product_returns == 1) {

		$items['product_returns'] = 'Product Returns';

	}

    return $items;

}

 

add_filter( 'woocommerce_account_menu_items', 'weka_add_premium_support_link_my_account' );



 

// ------------------

// 4. Add content to the new endpoint



function quotes_content() {

	

	

	if( !is_user_logged_in() ) {

		header("location: /my-account/");

		exit();

	}

	global $wpdb;

	$current_user = wp_get_current_user();

	$quote = get_user_meta($current_user->ID, 'quote', true);  

	$quote_data = get_user_meta($current_user->ID, 'quote_current', true); 

	

	$defaultTax = get_option('weka_integration_opportunity_tax');

		

	$quote_name = $wpdb->get_var("SELECT Subject FROM {$wpdb->prefix}quotes WHERE CustomerID = '{$current_user->ID}' AND ID = '$quote_data'");

	

	if($quote_data > 0) {

		global $wpdb;

		$quote_id = $quote_data;	

	} else {

		$quote_name = '';

		$quote_id = '';		

	}

	

	

	echo '<div class="woocommerce">';

	if(is_array($quote)) {

		$quote_error = get_user_meta( $current_user->ID, 'quote_image_error', true );

		$quote_success = get_user_meta( $current_user->ID, 'quote_image_success', true );

		$quote_status = get_user_meta( $current_user->ID, 'quote_status', true );

		$show_details = get_user_meta( $current_user->ID, 'quote_details', true );

		$details = get_user_meta( $current_user->ID);

		

		if($quote_status == '')

			$quote_status = 'Save';

		echo '<div class="print-only" style="width:100%; height: auto;">

				<h2 style="font-weight: bold;line-height: 50px; margin-right: 30px">Quote: '.$quote_name.'</h2>

			</div>

			<style>

				@media print {

					.woocommerce-MyAccount-navigation {

						display: none !important;

					}

					.woocommerce-MyAccount-content {

						width: 100% !important;

					}

					.no-print {

						display: none !important;

					}

					.footer-mailchimp {

						display: none !important;

					}

					footer {

						display: none !important;	

					}

				}

			</style>

		

			<h2 class="no-print">My Quote</h2>

			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" id="print-table" cellspacing="0">

				

				<tr class="no-print">

					<td colspan="4">

						<input type="text" id="quote-name" style="width:100%;font-weight: bold; color: #014d67; font-size:22px;" placeholder="Enter quote name..." value="'.stripslashes($quote_name).'" />

					</td>

					</td>

					<td colspan="2" style="text-align:left; font-size: .8em">

						<button type="button" class="button quote_button" style=" margin-right: 9px; margin-bottom: 0px;" onclick="window.location=\'/?quote=delete&amp;quoteID='.$quote_id.'\'">Delete</button>

						<button type="button" class="button quote_button" style=" margin-right: 9px; margin-bottom: 0px;" onclick="window.print()">Print</button>

						<button type="button" class="button quote_button" style=" margin-right: 9px; margin-bottom: 0px;" id="savequote" data-id="'.$quote_id.'">'.$quote_status.'</button>

					</td>						

				</tr>

				<tr>

					<th class="product-thumbnail">&nbsp;</th>

					<th class="product-name">Product</th>

					<th class="product-price">Price</th>

					<th class="product-quantity">Quantity</th>

					<th class="product-subtotal">Total</th>

					<th class="product-remove no-print">Remove Item</th>

				</tr>

				

		

			<tbody>';

			$totalPrice = 0;

			foreach($quote as $product_id => $item) {

					$product_permalink = get_the_permalink($product_id);

					$_product = wc_get_product( $product_id );

					$row_id = $product_id;

					?>

					<style>

						.quote-item img{

							max-height: 7em;

							width: auto;

						}

						.quote-line-attribute {

							margin: 0;

							font-size: .8em;

							color: #58585E;



						}

					</style>

					<tr class="woocommerce-cart-form__cart-item quote-item" id="row-<?php echo $row_id; ?>">

						<td class="product-thumbnail"><?php

							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image() );



							if ( ! $product_permalink ) {

								echo $thumbnail;

							} else {

								printf( '%s', $thumbnail );

							}

							?>

						</td>

						<td class="product-name"><?php

							$veta = get_post_meta( $product_id );

							$size = '';

							if(in_array($veta['_sku'][0],$metricOnly)) {

								$size = ' - ' . $veta['attribute_size'][0];

							} else {

								if($veta['attribute_size'][0] != '')

									$size = ' - ' . $veta['attribute_size'][0];

								else if(isset($veta['attribute_size'][0]))

									$size = ' - ' . $veta['attribute_size'][0];								

							}



							$name = explode(' -',$_product->get_name());

							echo '<a href="'.$product_permalink.'">'.$name[0].'</a></br>

							'.$size;	

							if($_product->is_type('variation')){

								foreach($_product->get_attributes() as $key=>$val):?>

									<p class="quote-line-attribute"><?=$val?></p>

								<?php	

								endforeach;							

							}						



						?>

						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">

						<?php echo wc_price($item['price']); ?>

						</td>

						<td class="quantity">

							<input type="number" size="4" step="1" min="0" data-product_id="<?php echo $product_id; ?>" class="input-text qty text quantityupdate" value="<?php echo $item['quantity']; ?>" data-subtotal="#subtotal-<?php echo $row_id; ?>" />

							

						</td>

						<td class="product-subtotal" id="subtotal-<?php echo $row_id; ?>"><?php

							echo wc_price(($item['price']*$item['quantity']));

							$totalPrice+=$item['price']*$item['quantity'];

						?>

						</td>

						<td class="product-remove no-print" style="text-align: center;">

						<a class="remove removequote" style="display: inline-block;" data-row="#row-<?php echo $row_id; ?>" data-product_id="<?php echo $product_id; ?>" data-quote_id="<?php echo $quote_id; ?>">&times;</a>

						</td>

					</tr>

			<?php

			

		}

		echo '

		</tbody>

		</table>

		<div class="cart-collaterals">

			<div class="cart_totals ">

			  <h2>Quote totals</h2>

			  <table class="shop_table shop_table_responsive" cellspacing="0">

				<tbody>

				  <tr class="cart-subtotal">

					<th>Subtotal</th>

					<td data-title="Subtotal"><span class="woocommerce-Price-amount amount">'.wc_price($totalPrice).'</td>

				  </tr>

				  <tr class="tax-rate tax-rate-nz-gst-1">

					<th>GST</th>

					<td data-title="GST">'.wc_price($totalPrice*$defaultTax).'</td>

				  </tr>

				  <tr class="order-total">

					<th>Total</th>

					<td data-title="Total"><strong>'.wc_price($totalPrice*(1+$defaultTax)).'</strong></td>

				  </tr>

				  

				</tbody>

			  </table>

			  <div class="wc-proceed-to-checkout"> 

			  	<a href="/wp-content/plugins/weka-myob-advanced-integration-b2b/includes/addtoquote.php" class="no-print checkout-button alt wc-forward" style="display: block;

					text-align: center;

					margin-bottom: 1em;

					font-size: 1.25em;

					padding: 1em; line-height: 1;

					cursor: pointer;

					position: relative;

					text-decoration: none;

					overflow: visible;

					font-weight: 700;

					border-radius: 4px;

					left: auto;

					color: #515151;

					background-color: #E50019;

					border: 0;

					display: inline-block;

					background-image: none;

					-webkit-box-shadow: none;

					box-shadow: none;

					text-shadow: none; background-color: #E50019 !important;

					color: #ffffff !important; 

					width:100%;">

						Add Quote To Cart

				</a>

			</div>

		</div>	

	</div>

	<p class="print-only" style="font-size:10px;"><i><strong>Terms and Conditions of Quotation:</strong><br/>

		All prices are quoted excluding GST. All prices are quoted excluding freight and courier charges. All goods subject to prior sale. Non-returnable unless negotiated prior. Prices are subject to complete order, any variance will require a re-quote. This quote is valid for 14 days or while current stocks last. All procured goods are non-returnable. A 10% handling charge will incur on returned goods and 20% if after 14 days of invoice. If Invoice is not paid by due date, a loader of 7.5% will be added for every month overdue. S Fisher & Sons Ltdق│└s liability is limited to the cost of replacement of any products supplied by it so that its total liability will not exceed the price of the goods.

		</i>

	</p>	';

	} else {

		echo '

		<div style="text-align:center;">

			<h2>Create a new quote</h2>

			<input type="text" id="quote-name" style="width:320px; text-align:center; font-size:20px;" placeholder="Enter quote name here to begin" value="" /><br>

			<button type="button" class="button quote_button" style=" margin-right: 9px; margin-bottom: 9px;" id="savequote" data-id="create">Create</button>	

		</div>

		';

	}

	if(isset($_REQUEST['quote_from_date'])) {

		if($_REQUEST['quote_from_date'] != '') {

			update_user_meta( $current_user->ID, 'quote_from_date', $_REQUEST['quote_from_date'] );

		}

	}





	if(isset($_REQUEST['quote_to_date'])) {

		if($_REQUEST['quote_to_date'] != '') {



			update_user_meta( $current_user->ID, 'quote_to_date', $_REQUEST['quote_to_date'] );

		}

	}



	$to_date = get_user_meta( $current_user->ID, 'quote_to_date', true );

	$from_date = get_user_meta( $current_user->ID, 'quote_from_date', true );

	

	if($_REQUEST['reset'] == 'yes') {

		$from_date = '';

		$to_date = '';

		update_user_meta( $current_user->ID, 'quote_from_date', '' );

		update_user_meta( $current_user->ID, 'quote_to_date', '' );

	}

	if($from_date == '')

		$from_date = date('d/m/Y',mktime(0,0,0,date('n')-1,date('d'),date('Y')));



	if($to_date == '')

		$to_date = date('d/m/Y');

	

	global $wpdb;

	

	list($day,$month,$year) = explode('/',$from_date);

	$query_from = $year.'-'.$month.'-'.$day;

	list($day,$month,$year) = explode('/',$to_date);

	$query_to = $year.'-'.$month.'-'.$day;

	$results = $wpdb->get_results("SELECT *  FROM `{$wpdb->prefix}quotes` WHERE `CustomerID` = '{$current_user->ID}' AND (`Status` = 'Open' OR `Status` = 'New')

	AND created >= '$query_from 00:00:00' AND created <= '$query_to 23:59:59';" );

		

		echo '<div class="no-print"><h2>Quote History</h2>

<form action="" class="cp_order_form">

  <label for="from">From</label>

  <input id="from_range" class="date_range datepicker" name="quote_from_date" value="'.$from_date.'" type="text">

  <label for="to">to</label>

  <input id="to_range" class="date_range datepicker" name="quote_to_date" value="'.$to_date.'" type="text">

  <button class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" id="apply_range">Apply</button>

  <a href="/my-account/quotes/?reset=yes" class="woocommerce-button reset-btn button">Reset</a>

</form>		



            

            <script>

                jQuery( function($) {

                    var dateFormat = "dd/mm/yy",

                        from = $( "#from_range" )

                            .datepicker({

                                defaultDate: "+1w",

                                changeMonth: true,

                                numberOfMonths: 1,

                                dateFormat : dateFormat

                            })

                            .on( "change", function() {

                                to.datepicker( "option", "minDate", getDate( this ) );

                            }),

                        to = $( "#to_range" ).datepicker({

                            defaultDate: "+1w",

                            changeMonth: true,

                            numberOfMonths: 1,

                            dateFormat : dateFormat

                        })

                                       .on( "change", function() {

                                           from.datepicker( "option", "maxDate", getDate( this ) );

                                       });



                    function getDate( element ) {

                        var date;

                        try {

                            date = $.datepicker.parseDate( dateFormat, element.value );

                        } catch( error ) {

                            date = null;

                        }



                        return date;

                    }



                } );

            </script>	';

	if(count($results) > 0):?>

	

		

			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0" style="margin-top: 5px;">

			<thead><th>Quote Title</th><th>Date Created</th><th>Value</th><th>Remove Quote</th><th></th></thead>

			<?php

			foreach($results as $result):

				$value = $wpdb->get_var("SELECT sum(price*quantity) as total FROM {$wpdb->prefix}quotes_lines WHERE quote_id = {$result->ID} GROUP BY quote_id");

				?>

				<tr><td><a href="/wp-content/plugins/weka-myob-advanced-integration-b2b/includes/loadquote.php?quoteID=<?=$result->ID?>"><?=stripslashes($result->Subject)?></a></td><td><?=date('jS F Y',strtotime($result->created))?></td>

				<td><?=$value?></td>

				<td>

				



				<a class="remove removequote" style="display: inline-block;" href="/?quote=delete&quoteID=<?=$result->ID?>">&times;</a>

				

				</td>

				<td><a href="/wp-content/plugins/weka-myob-advanced-integration-b2b/includes/loadquote.php?quoteID=<?=$result->ID?>">Edit</a></td>

				</tr>

			<?php endforeach; ?>	

		</table>

	<?php 

	else: ?>

		

		<p style="margin: 1em; font-size: 2em; font-weight: 500; text-align: center">No Quotes Found</p>

	<?php 

	endif;

	echo '</div><div id="snackbar"></div><div id="successbar"></div></div>';

}

 

add_action( 'woocommerce_account_quotes_endpoint', 'quotes_content' );



add_shortcode( 'quotes_content', 'quotes_content' );



/**** Opportunities/Quotes section End ****/



/**** Invoice Section Start ****/

function invoices_content() {

	

	

	if( !is_user_logged_in() ) {

		header("location: /my-account/");

		exit();

	}

	global $wpdb;

	$current_user = wp_get_current_user();

	

	echo '<div class="woocommerce">';

	if(isset($_REQUEST['invoice_from_date'])) {

		if($_REQUEST['invoice_from_date'] != '') {

			update_user_meta( $current_user->ID, 'invoice_from_date', $_REQUEST['invoice_from_date'] );

		}

	}





	if(isset($_REQUEST['invoice_to_date'])) {

		if($_REQUEST['invoice_to_date'] != '') {



			update_user_meta( $current_user->ID, 'invoice_to_date', $_REQUEST['invoice_to_date'] );

		}

	}



	$to_date = get_user_meta( $current_user->ID, 'invoice_to_date', true );

	$from_date = get_user_meta( $current_user->ID, 'invoice_from_date', true );

	

	if($_REQUEST['reset'] == 'yes') {

		$from_date = '';

		$to_date = '';

		update_user_meta( $current_user->ID, 'invoice_from_date', '' );

		update_user_meta( $current_user->ID, 'invoice_to_date', '' );

	}

	if($from_date == '')

		$from_date = date('d/m/Y',mktime(0,0,0,date('n')-1,date('d'),date('Y')));



	if($to_date == '')

		$to_date = date('d/m/Y');

	

	global $wpdb;

	

	list($day,$month,$year) = explode('/',$from_date);

	$query_from = $year.'-'.$month.'-'.$day;

	list($day,$month,$year) = explode('/',$to_date);

	$query_to = $year.'-'.$month.'-'.$day;

	

	$results = $wpdb->get_results("SELECT *  FROM `{$wpdb->prefix}invoices` WHERE `CustomerID` = '{$current_user->ID}'

	AND DueDate >= '$query_from 00:00:00' AND DueDate <= '$query_to 23:59:59' ORDER BY ReferenceNbr DESC;" );

	



		

		echo '<div class="no-print"><h2>Invoice History</h2>

<form action="" class="cp_order_form">

  <label for="from">From</label>

  <input id="from_range" class="date_range datepicker" name="invoice_from_date" value="'.$from_date.'" type="text">

  <label for="to">to</label>

  <input id="to_range" class="date_range datepicker" name="invoice_to_date" value="'.$to_date.'" type="text">

  <button class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" id="apply_range">Apply</button>

  <a href="/my-account/invoices/?reset=yes" class="woocommerce-button reset-btn button">Reset</a>

</form>		



            

            <script>

                jQuery( function($) {

                    var dateFormat = "dd/mm/yy",

                        from = $( "#from_range" )

                            .datepicker({

                                defaultDate: "+1w",

                                changeMonth: true,

                                numberOfMonths: 1,

                                dateFormat : dateFormat

                            })

                            .on( "change", function() {

                                to.datepicker( "option", "minDate", getDate( this ) );

                            }),

                        to = $( "#to_range" ).datepicker({

                            defaultDate: "+1w",

                            changeMonth: true,

                            numberOfMonths: 1,

                            dateFormat : dateFormat

                        })

                                       .on( "change", function() {

                                           from.datepicker( "option", "maxDate", getDate( this ) );

                                       });



                    function getDate( element ) {

                        var date;

                        try {

                            date = $.datepicker.parseDate( dateFormat, element.value );

                        } catch( error ) {

                            date = null;

                        }



                        return date;

                    }



                } );

            </script>	';

	if(count($results) > 0) {

		echo'

		<div class="invoices-header" style="margin-top: 5px ">

		<div class="col-xs-2"><label>Reference Nbr</label></div>

		<div class="col-xs-2"><label>Status</label></div>

		<div class="col-xs-2"><label>Due Date</label></div>

		<div class="col-xs-2"><label>Amount</label></div>

		<div class="col-xs-2"><label>Balance</label></div>

		</div>

		<div class="panel-group" id="invoice-panels">

		';

		foreach($results as $result) {

			$amount = $result->Amount;

			$balance = $result->Balance;

			echo '<div class="panel panel-default">

              <div class="panel-heading invoices-main">

                <div class="col-xs-2"><label><a data-toggle="collapse" href="#collapse-'.$result->ID.'" data-parent="#invoice-panels">'.stripslashes($result->ReferenceNbr).'</a></label></div>

                <div class="col-xs-2"><label>'.stripslashes($result->Status).'</label></div>

                <div class="col-xs-2"><label>'.date('d/m/Y',strtotime($result->DueDate)).'</label></div>

                <div class="col-xs-2"><label>'.weka_price($amount).'</label></div>

				<div class="col-xs-2"><label>'.weka_price($balance).'</label></div>

				<div class="col-xs-2">'; if ($balance > 0) { echo '<button type="button" class="button invoice_button" style=" margin-right: 9px; margin-bottom: 9px;" class="payInvoice" id="payInvoice-'.$result->ID.'" data-id="pay">Pay Now</button>'; }

			echo '</div>

             	</div>

              <div id="collapse-'.$result->ID.'" class="panel-collapse collapse">';

			$invoice_lines = $wpdb->get_results("SELECT *  FROM `{$wpdb->prefix}invoices_lines` WHERE `InvoiceID` = '".$result->ID."';" );

			if(count($invoice_lines) > 0) {

				echo '<div class="invoices-line-header panel-heading">

					<div class="col-xs-2"><label>Order Nbr</label></div>

					<div class="col-xs-2"><label>Product Name</label></div>

					<div class="col-xs-2"><label>Product SKU</label></div>

					<div class="col-xs-2"><label>Unit Price</label></div>

					<div class="col-xs-2"><label>Quantity</label></div>

					<div class="col-xs-2"><label>Amount</label></div>

					</div>';

				foreach($invoice_lines as $item) {

					$product_id = $item->InventoryID;

					$invoice_product = wc_get_product( $product_id );

					$meta = get_post_meta( $product_id );

					$size = '';

					if(in_array($meta['_sku'][0],$metricOnly)) {

						$size = ' - ' . $meta['attribute_size'][0];

					} else {

						if($meta['attribute_size'][0] != '')

							$size = ' - ' . $meta['attribute_size'][0];

						else if(isset($veta['attribute_size'][0]))

							$size = ' - ' . $meta['attribute_size'][0];								

					}



					$name = explode(' -',$invoice_product->get_name());

					echo '<div class="panel-body invoices-line">

					<div class="col-xs-2"><strong>'.$item->OrderNbr.'</strong></div>

					<div class="col-xs-2"><strong>'.$name[0].'</strong></br>

					'.$size.'</div>

					<div class="col-xs-2">'.$invoice_product->get_sku().'</div>

					<div class="col-xs-2">'.weka_price($item->UnitPrice).'</div>

					<div class="col-xs-2">'.$item->Quantity.'</div>

					<div class="col-xs-2">'.weka_price($item->Amount).'</div>

					</div>';

				}

			}

			echo ' </div>

			</div>';

		}

		echo '</div>';

	}else{

		ob_start();?>

			<h4 style="font-size: 4em;margin: 1em 0;color: #959595;">No Invoices Found</h4>

		<?php echo ob_get_clean();

	}

	echo '</div><div id="snackbar"></div><div id="successbar"></div></div>';

}

 

add_action( 'woocommerce_account_invoices_endpoint', 'invoices_content' );



add_shortcode( 'invoices_content', 'invoices_content' );

/**** Invoices Section End ****/