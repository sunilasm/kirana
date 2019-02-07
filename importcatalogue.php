<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

//use \Magento\Framework\App\Bootstrap;
//require 'app/bootstrap.php';

//$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
//$resource = \Magento\Framework\App\ResourceConnection; //$objectManager->get('Magento\Framework\App\ResourceConnection');
//$connection = $resource->getConnection('custom');
//print_r($connection);exit;
//$TableName = $resource->getTableName('mglof_marketplace_seller');

	try{
		$connection = mysqli_connect('localhost', 'root', '', 'kirana_dev');
		if(!$connection) {
			throw new Exception('Could not connect to database!');
		}
		
		$importFolder ='kirana_details.csv';
		$row = 1;
		$result =array();
		if(($handle = fopen($importFolder, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				$result[]=$data;
				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
			}
			fclose($handle);
		}
		//echo "<pre>";
		//print_r($result);exit;
		if(isset($result) && !empty($result)){
			
			foreach($result as $key=>$parseResponse){
				if($key==0){
					continue;
				}
			//	print_r($parseResponse);exit;
				$storeid	 		= isset($parseResponse[0]) ? $parseResponse[0] : '';
				$name		 		= isset($parseResponse[1]) ? $parseResponse[1] : '';
				$picturepath  		= isset($parseResponse[2]) ? $parseResponse[2] : '';
				$participating		= isset($parseResponse[3]) ? $parseResponse[3] : '';
				$mobile1			= isset($parseResponse[4]) ? $parseResponse[4] : '';
				$mobile2			= isset($parseResponse[5]) ? $parseResponse[5] : '';
				$smartphone			= isset($parseResponse[6]) ? $parseResponse[6] : '';
				$dataconn			= isset($parseResponse[7]) ? $parseResponse[7] : '';
				$tabwithdataconn 	= isset($parseResponse[8]) ? $parseResponse[8] : '';
				$landline			= isset($parseResponse[9]) ? $parseResponse[9] : '';
				$city				= isset($parseResponse[10]) ? $parseResponse[10] : '';
				$area				= isset($parseResponse[11]) ? $parseResponse[11] : '';
				$sector				= isset($parseResponse[12]) ? $parseResponse[12] : '';
				$landmark			= isset($parseResponse[13]) ? $parseResponse[13] : '';
				$others_info		= $sector.','.$landmark.','.$area;
				$pincode			= isset($parseResponse[14]) ? $parseResponse[14] : '';
				$storetype			= isset($parseResponse[15]) ? $parseResponse[15] : '';
				$storesize			= isset($parseResponse[16]) ? $parseResponse[16] : '';
				$discountavailable 	= isset($parseResponse[17]) ? $parseResponse[17] : '';
				$discounttype	  	= isset($parseResponse[18]) ? $parseResponse[18] : '';
				$discountpercent  	= isset($parseResponse[19]) ? $parseResponse[19] : '';
				$discountcriteria 	= isset($parseResponse[20]) ? $parseResponse[20] : '';
				$daysnotworking		= isset($parseResponse[21]) ? $parseResponse[21] : '';
				$starttime			= isset($parseResponse[22]) ? $parseResponse[22] : '';
				$closingtime		= isset($parseResponse[23]) ? $parseResponse[23] : '';
				$deliver			= isset($parseResponse[24]) ? $parseResponse[24] : '';
				$deliverytime		= isset($parseResponse[25]) ? $parseResponse[25] : '';
				$minimumdeliveryamount = isset($parseResponse[26]) ? $parseResponse[26] : '';
				$knowsenglish		= isset($parseResponse[27]) ? $parseResponse[27] : '';
				$whatsapp			= isset($parseResponse[28]) ? $parseResponse[28] : '';
				$address			= isset($parseResponse[29]) ? $parseResponse[29] : '';
				$email				= isset($parseResponse[30]) ? $parseResponse[30] : '';
				$ownername			= isset($parseResponse[31]) ? $parseResponse[31] : '';
				$lstnumber			= isset($parseResponse[32]) ? $parseResponse[32] : '';
				$cstnumber			= isset($parseResponse[33]) ? $parseResponse[33] : '';
				$pannumber			= isset($parseResponse[34]) ? $parseResponse[34] : '';
				$vatnumber			= isset($parseResponse[35]) ? $parseResponse[35] : '';
				$tinnumber			= isset($parseResponse[36]) ? $parseResponse[36] : '';
				$longitude			= isset($parseResponse[37]) ? $parseResponse[37] : '';
				$lattitude			= isset($parseResponse[38]) ? $parseResponse[38] : '';
				$capturestarttime	= isset($parseResponse[39]) ? $parseResponse[39] : '';
				$captureendtime		= isset($parseResponse[40]) ? $parseResponse[40] : '';
				$imeino				= isset($parseResponse[41]) ? $parseResponse[41] : '';
				$imsino				= isset($parseResponse[42]) ? $parseResponse[42] : '';
				$mac				= isset($parseResponse[43]) ? $parseResponse[43] : '';
				$androidid			= isset($parseResponse[44]) ? $parseResponse[44] : '';
				$Date				= isset($parseResponse[45]) ? $parseResponse[45] : '';
				$F48				= isset($parseResponse[46]) ? $parseResponse[46] : '';
				$Start_Time			= isset($parseResponse[47]) ? $parseResponse[47] : '';
				$F50				= isset($parseResponse[48]) ? $parseResponse[48] : '';
				$End_Time			= isset($parseResponse[49]) ? $parseResponse[49] : '';
				$F52				= isset($parseResponse[50]) ? $parseResponse[50] : '';
				$F53				= isset($parseResponse[51]) ? $parseResponse[51] : '';
				$F54				= isset($parseResponse[52]) ? $parseResponse[52] : '';

				/********* Create Customer ************/
				$customerId=0;
				/**** filtter valid email address *****/
				$emailValidateFlag=false;
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$emailValidateFlag=true;
				}
					if(!empty($email) && $emailValidateFlag === true){
						if(!empty($name)){
							$getsplit=explode(" ",$name);
							$lastname= $getsplit[count($getsplit)-1];
						}
						$customerData = [
							'customer' => [
								"email" 	=> $email,
								"firstname" => $name,
								"lastname" 	=> $lastname ?? "",
								"storeId" 	=> 1,
								"websiteId" => 1
							],
							"password" => "Demo@1234"
						];
						//$customerId =createCustomer($customerData);	
					}
				/********* End ********/
				/**/
				
				if($storetype=='Chemist'){
					$storetype ='MED';
				}else{
					$storetype ='KIR';
				}
				if($whatsapp =='Yes'){
					$whatsapp ='Yes';
				}else{
					$whatsapp ='No';
				}
				$createdate = date('Y-m-d H:i:s');
				
				
					$InsertData = [				
						'name' 				=> $name,
						'url_key'			=> trim(preg_replace('/\s+/', ' ', $name)),
						'group_id' 			=> '1',
						'sale' 				=> '0',
						'commission_id' 	=> '0',
						'image' 			=> $picturepath,
						'thumbnail' 		=> $picturepath,
						'page_title' 		=> $name,
						'creation_time' 	=> $createdate,
						'update_time' 		=> $createdate,
						'address' 			=> $address,
						'status' 			=> $participating,
						'position' 			=> $storeid,
						'customer_id' 		=> $customerId,
						'email' 			=> $email,
						'created_at' 		=> $createdate,
						'others_info' 		=> $others_info,
						'shop_title' 		=> $name,
						'country' 			=> 'India',
						'store_id' 			=> '1',
						'contact_number'	=> $mobile1,
						'city' 				=> $city,
						'region' 			=> 'Maharashtra',
						'postcode' 			=> $pincode,
						'telephone' 		=> $mobile2,
						'geo_lat' 			=> $lattitude,
						'geo_lng' 			=> $longitude,
						'store_type' 		=> $storetype,
						'24by7_shop' 		=> 'No',
						'opening_time' 		=> $starttime,
						'closeing_time' 	=> $closingtime,
						'non_working_days' 	=> $daysnotworking,
						'lsn' 				=> $lstnumber,
						'cst' 				=> $cstnumber,
						'pan' 				=> $pannumber,
						'vat' 				=> $vatnumber,
						'tin' 				=> $tinnumber,
						'smart_phone' 		=> $whatsapp, //need to add condition
						'knows_english' 	=> $knowsenglish,
						'kirana_type' 		=> $storetype, //need to add condition
						'kirana_owner' 		=> $ownername,
						'kirana_fixed_line' => $landline,
						'imeino' 			=> $imeino,
						'imsino' 			=> $imsino,
						'mac' 				=> $mac,
						'androidid' 		=> $androidid,
						'storesize' 		=> $storesize,
						'deliver' 			=> $deliver,
						'deliverytime' 		=> $deliverytime,
						'minimumdeliveryamount' => $minimumdeliveryamount,
						'discountavailable' => $discountavailable,
						'discounttype' 		=> $discounttype,
						'discountpercent' 	=> $discountpercent,
						'discountcriteria' 	=> $discountcriteria,
						'dataconn' 			=> $dataconn,
						'tabwithdataconn' 	=> $tabwithdataconn,
						'smartphone' 		=> $smartphone
					];
					
				$insertquery = "INSERT INTO " . $TableName . "(".implode(',',array_keys($InsertData)).") VALUES ('".implode("','",$InsertData)."')";
				mysqli_query($connection, $insertquery);
			}
		
		}
	}catch(Exception $e){
		echo $e->getMessage();
		exit(1);
	}
	
	
	
	function createCustomer($customerData=[]){
		
		if(!empty($customerData)){
			$userData = array("username" => "admin", "password" => "admin@123");
			$ch = curl_init("http://127.0.0.1/demo-kirana/rest/V1/integration/admin/token");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
			
			$token = curl_exec($ch);
			
			$ch = curl_init("http://127.0.0.1/demo-kirana/rest/V1/customers");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

			$result = curl_exec($ch);
			
			$result = json_decode($result, 1);
			
			$resultStatus = isset($result['id']) ? $result['id'] : 0;
			//echo '<pre>';print_r($result);
			return $resultStatus;
		}
	}
?>
