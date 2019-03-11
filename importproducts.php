<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);
//$TableName = $resource->getTableName('grocery_pharma_review_sheet');
$TableName = 'grocery_pharma_review_sheet';
$TableAttributeOptionName = 'mgeav_attribute_option_value';
$token = '';
$finalresult = '';
$limit = (isset($_GET['limit'])) ? $_GET['limit'] : 1;
$page = (isset($_GET['page'])) ? (($_GET['page'] - 1) * $limit) : 10;

	try{
		$connection = mysqli_connect('localhost', 'kirana', 'Kirana@aws123', 'kirana_qa_new');
		if(!$connection) {
			throw new Exception('Could not connect to database!');
		}
		//print_r($connection);exit;
		
		$query = "SELECT * FROM ".$TableName." WHERE `size` LIKE '%kg%' AND `local_data_uploaded` = 0 ORDER by ID ASC LIMIT ".$page.",".$limit;
		print_r($query);exit;
		$result = $connection->query($query);
		$product  = array();
		if(count($result))
		{

			/* fetch associative array */

			while ($row = $result->fetch_assoc()) {
			// Get pack type attribute value
			$query1 = "SELECT * FROM ".$TableAttributeOptionName;
			$result1 = $connection->query($query1);
			$pack_type = '';
			while ($row1 = $result1->fetch_array()) {
				if($row['uom'] == $row1['value']){
					$pack_type = $row1['option_id'];
				}
				//echo "value_id-->".$row1['value_id'].'---'."option_id-->".$row1['value_id'].'---'."value-->".$row1['value']."<br>";
			}
			$urlFormat = preg_replace("/[\s_]/", "-", strtolower($row['item']));
			$url_key = $urlFormat.''.$row['id'];
			// print_r($url_key);exit;

			//echo "pack_type-->".$pack_type;
				$product['product'] = array(
					"sku" => "SKU".$row['article'],
					"name" => ucwords($row['item']),
					"attribute_set_id" => 4,
					"price" => $row['price'],
					'status' => 1,
					"visibility" => 4,
					"type_id" => 'simple',
					"weight" => (int) filter_var($row['size'], FILTER_SANITIZE_NUMBER_INT),
					"extension_attributes" => array(
						"category_links" => array(
							"position" => 0,
							"category_id" => 2
						),
						"stock_item" => array(
							"qty" => "10000",
							"is_in_stock" => true
						)
					),
					"custom_attributes" => array(
						array(
							"attribute_code" => "category_ids",
							"value" => ["2","71"]
						),
						array(
							"attribute_code" => "description",
							"value" => preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $row['long_description'])
						),
						array(
							"attribute_code" => "short_description",
							"value" => preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $row['description'])
						),
						array(
							"attribute_code" => "product_size",
							"value" => $row['size']
						),
						array(
							"attribute_code" => "kirana_locality",
							"value" => 30
						),
						array(
							"attribute_code" => "pack_type",
							"value" => 19
						),
						array(
							"attribute_code" => "private_item",
							"value" => 0
						),
						array(
							"attribute_code" => "kirana_type",
							"value" => 26
						),
						array(
							"attribute_code" => "kirana_availability",
							"value" => 1
						),
						array(
							"attribute_code" => "brand",
							"value" => $row['brand']
						),
						array(
							"attribute_code" => "lo",
							"value" => $row['lo']
						),
						array(
							"attribute_code" => "variant",
							"value" => $row['variant']
						),
						array(
							"attribute_code" => "kirana_group",
							"value" => $row['kirana_group']
						),
						array(
							"attribute_code" => "product",
							"value" => $row['product']
						),
						array(
							"attribute_code" => "brand1",
							"value" => $row['brand1']
						),
						array(
							"attribute_code" => "approval",
							"value" => 2
						),
						array(
							"attribute_code" => "url_key",
							"value" => $url_key
						)
					)
				);
				print_r($product); echo "</br>";
					echo "--".$row['article']."--";
				 	echo $product_id = createProduct($product);
				 	if(is_int($product_id)){
				 		$query2 = "UPDATE ".$TableName." SET `local_data_uploaded`= '".$product_id."' WHERE `article` = '".$row['article']."'";
				 	}else{
				 		$query2 = "UPDATE ".$TableName." SET `local_data_uploaded`= '".$product_id."' WHERE `article` = '".$row['article']."'";
				 	}
				 	// print_r($query2);exit;
					$result2 = $connection->query($query2);
					echo "--".$result2."<br/>";

			}
		}
		//print_r($product_id.'---');
		$product = json_encode($product, 1);
		//echo "<pre>".print_r($product,true);
		//echo $query; exit;
		//$retval = mysql_query( $sql, $conn );
	}catch(Exception $e){
		echo $e->getMessage();
		exit(10);
	}
	
	
	
	function createProduct($productData=[]){
		$baseUrl = 'http://127.0.0.1/kirana_store/';
		//print_r($productData['product']['sku']);exit;	

		$newArtical = str_replace("SKU","",$productData['product']['sku']);
	    //echo "herer";
	    //print_r($newArtical);exit;

	    $log_directory = 'pub/media/Product_Images';
		$results_array = array();
		if (is_dir($log_directory))
		{
		        if ($handle = opendir($log_directory))
		        {
		                //Notice the parentheses I added:
		                while(($file = readdir($handle)) !== FALSE)
		                {
		                	//print_r($file);
		                	$newFileName = strstr($file, '.', true);
							//print_r($newFileName.'==='.$newArtical);
		                    //$results_array[] = $file;
		                	if($newFileName == $newArtical){
		                        $results_array[] = $file;
		                	}
		                }
		                	// print_r($results_array);
		                closedir($handle);
		        }
		}

		if(count($results_array)){

			global $token;
			if(!empty($productData)){
				if(!$token)
				{	
					$userData = array("username" => "sunil.n", "password" => "Admin@123");
					$ch = curl_init("$baseUrl".''."rest/V1/integration/admin/token");
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
				
					$token = curl_exec($ch);
				}
				//echo $token; exit;
				$ch = curl_init("$baseUrl".''."rest/V1/products");
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

				$result = curl_exec($ch);
				$result = json_decode($result, 1);
				//print_r($result);exit;

				
					
					//Output findings
					 if(count($results_array)){

						foreach($results_array as $value)
						{
						    $imageName = $value;
						    $file_parts = pathinfo($imageName);
						    if($file_parts['extension'] == 'jpg' || $file_parts['extension'] == 'jpeg' || $file_parts['extension'] == 'JPG' || $file_parts['extension'] == 'JPEG'){
						    	$newContentType = 'image/jpeg';
						    }
						    if($file_parts['extension'] == 'png'){
						    	$newContentType = 'image/png';
						    }
						    //echo $file_parts['extension'] . '<br />';
						}

					$filePath = $baseUrl.''.$log_directory.'/'.$imageName;
					$imagedata = file_get_contents("$filePath");
		             // alternatively specify an URL, if PHP settings allow
					$base64 = base64_encode($imagedata);
					//print_r($base64);
					//exit;


					// Product Image set 
					$productImage['entry'] = array(
						"media_type" => "image",
						"label" => "Image",
						"position" => 1,
						"disabled" => false,
						"types" => array(
								"image",
								"small_image",
		            			"thumbnail"
						),
						"content" => array(
							"base64_encoded_data" => $base64,
							"type" => "$newContentType",
							"name" => "$imageName",
						)

					);
					if(isset($result['id'])){

						$ch = curl_init("$baseUrl".''."rest/V1/products/".$result['sku']."/media");
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productImage));
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

						$result1 = curl_exec($ch);
						$result1 = json_decode($result1, 1);
					}
					//print_r($productImage);exit;
					//print_r("http://127.0.0.1/kirana_store/rest/V1/products/".$result['sku']."/media");exit;

				}
			    
				//print_r($result);exit;
				//UPDATE `grocery_pharma_review_sheet` SET `dev_data_uploaded`= 0 WHERE `dev_data_uploaded`= 'No';
				
				
				$resultStatus = isset($result['id']) ? $result['id'] : $result['message'];
				//print_r($resultStatus);exit;
				return $resultStatus;
			}
		}else{
			$resultStatus = "image not found";
			// $resultStatus = isset($result['id']) ? $result['id'] : $result['message'];
				// print_r($resultStatus);exit;
			return $resultStatus;
		}
	}
?>
