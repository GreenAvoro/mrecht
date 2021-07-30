<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("classes/class.database.php");
include_once("classes/class.Validator.php");
include_once("classes/class.dataobject.php");
include_once("classes/class.products.php");
include_once("classes/class.curl.php");

extract($_GET);

require_once('WooCommerce/Client.php');
require_once('WooCommerce/HttpClient/BasicAuth.php');
require_once('WooCommerce/HttpClient/HttpClient.php');
require_once('WooCommerce/HttpClient/HttpClientException.php');
require_once('WooCommerce/HttpClient/OAuth.php');
require_once('WooCommerce/HttpClient/Options.php');
require_once('WooCommerce/HttpClient/Request.php');
require_once('WooCommerce/HttpClient/Response.php');

use Automattic\WooCommerce\Client;

$settings = new dataobject('settings');
$settings->selectObjectByField('s_username',$_SERVER['REDIRECT_REMOTE_USER'],'`s_username` ASC');

$woocommerce = new Client($settings->s_website_address,$settings->s_consumer_key,$settings->s_consumer_secret,['wp_api' => true,'version' => 'wc/v2','query_string_auth' => true]);

$Advancedproducts = new advanceProducts();


if(isset($ProductID)) {
	if($_REQUEST['sku'] != '') {
		$advanceProducts = $Advancedproducts->getProductBySku(urlencode($_REQUEST['sku']));

		if(is_array($advanceProducts)) {
			foreach($advanceProducts as $advanceProduct) {
				$id = $advanceProduct['id'];
				// $sku = $advanceProduct['InventoryID']['value'];
				$sku = $advanceProduct['InventoryID']['value'];
				$data = [
					'meta_data' => [
							[
								'key' => 'advanced_id',
								'value' => $id										
							]										
					]
				];				
				$result = $woocommerce->put('products/'.$_REQUEST['ProductID'], $data);

				echo json_encode(array('result'=>'success'));
				exit();
			}
		}
		echo json_encode(array('result'=>'failure','outcome'=>'The product does not existing in MYOB Advanced'));
		exit();			
	}
}

unlink('cookies.txt');
?>