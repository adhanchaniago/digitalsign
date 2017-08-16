<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate product object
include_once '../objects/product.php';
 
$database = new Database();
$db = $database->getConnection();
 
$product = new Product($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));

//print_r($data);
 
if (!empty($data)) {

	// set product property values
	$product->name = $data->name;
	$product->description = $data->description;
	$product->pdf_url = $data->pdf_url;
	$product->pdf_password = $data->pdf_password;
	$product->category_id = $data->category_id;
	$product->created = date('Y-m-d H:i:s');
	//exit;

	// Sign PDF by index_json.php
	$sign_url = "http://localhost/~ton/digitalsign/index_json.php?url="."$data->pdf_url"."&pdf_password="."$data->pdf_password";

	//echo "$sign_url";
	$sign_api = json_decode(file_get_contents($sign_url));
	//print_r($sign_api);

	//echo "$sign_api->message\n";

	if($sign_api->message=="PDF was signed.") {
		// Insert URL Original - Signed PDF to DB
		$product->org_pdf = $sign_api->org_url;
		$product->sign_pdf = $sign_api->sign_url;

		//echo "$product->org_pdf"."$product->sign_pdf";
		echo '{';
			echo '"message": "PDF was signed.",';
			echo '"name": "'.$product->name.'",';
			echo '"description": "'.$product->description.'",';
			echo '"org_url": "'.$product->org_pdf.'",';
			echo '"sign_url": "'.$product->sign_pdf.'",';
			echo '"pdf_password": "'.$product->pdf_password.'",';
			echo '"created": "'.$product->created.'"';
		echo '}';

		//exit;

		// create the product
		if($product->create()){
			//echo '{';
			//   echo '"message": "Product was created."';
			//echo '}';
		}

	}

	else{
		echo '{';
			echo '"message": "Unable to create product PDF file invalid."';
		echo '}';
	}

}
else{
	echo '{';
		echo '"message": "Unable to create product."';
	echo '}';
}

?>