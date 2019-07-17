<?php

	/**
	 * HYLOSH CRON
	 * --------------- 
	 */

 	ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); 
	# start tim 
	//echo("execution start-> " . $time_start);

	use Magento\Framework\App\Bootstrap; 
	require 'app/bootstrap.php'; 
	$bootstrap = Bootstrap::create(BP, $_SERVER);

	/* Get Object Manager */
	$objectManager = $bootstrap->getObjectManager();

	/* Set Area Code */
	$objectManager->get('\Magento\Framework\App\State')->setAreaCode('frontend');      

	/* Class Instances */
	$storeManager 				= $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
	$resource_connection		= $objectManager->get('Magento\Framework\App\ResourceConnection');
	$sellerproductLog    		= $objectManager->create('Lof\MarketPlace\Model\SellerProductFactory');
	$customMappingLog           = $objectManager->create('Retailinsights\Promotion\Model\PostTableFactory');
	$productRepository		    = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterfaceFactory');

	$postTableBack 				= $objectManager->create('Retailinsights\Promotion\Model\PostTableBackFactory');
	//$timezoneInterface = $bootstrap->getObjectManager()->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
	//$observerInterface 	 		= $objectManager->create('Magento\Framework\Event\ObserverInterface');
			
	# get connection
	$connection 				= $resource_connection->getConnection();
	$expiredRule =$postTableBack->create();
	
	/* Declarations */
	$base_url = $storeManager->getStore()->getBaseUrl();
	
	$unsuccessful_order_ids = [];
		
	//$date = new DateTime();
	//echo $timeStamp = $date->getTimestamp();
	date_default_timezone_set('Asia/Kolkata');
	$current_date = date("Y-m-d H:i:s");

	$attribute_query = "SELECT attribute_id FROM `mgeav_attribute` WHERE attribute_code = 'price'";
	$attribute_result = $connection->fetchall($attribute_query);
	$attributeId = $attribute_result[0]['attribute_id'];

	//promostoremapp Checking Expired Rules
	$expiredcustomrules_query  = "SELECT * FROM `mgretailinsights_promostoremapp` WHERE rule_type = 1 AND status = 1 and pend_date< TIMESTAMPADD(MINUTE,330,CURRENT_TIMESTAMP)";  // only catalog rules
	$expiredcustomrulesresult 	= $connection->fetchall($expiredcustomrules_query);

	if ( count($expiredcustomrulesresult) > 0) {
		
			//** Check Expiry */			
			foreach($expiredcustomrulesresult as $rule){
				if($rule['pend_date']!=null || $rule['pend_date']!=0){
					if(strtotime($current_date) > strtotime($rule['pend_date'])) {
						//echo strtotime($current_date); echo "<br>";
						//echo strtotime($rule['pend_date']);		
						
					$expiredRule->setRule($rule['rule']);
                    $expiredRule->setStoreId($rule['store_id']);
                    $expiredRule->setPstartDate($rule['pstart_date']);
                    $expiredRule->setPendDate($rule['pend_date']);
                    $expiredRule->setStoreName($rule['store_name']); 
                    $expiredRule->setSellerType($rule['seller_type']);
                    $expiredRule->setStatus($rule['status']);
                    $expiredRule->setRuleType($rule['rule_type']); 
                    $expiredRule->setDescription($rule['description']);
                    $expiredRule->setConditionsSerialized($rule['conditions_serialized']);
                    $expiredRule->setActionsSerialized($rule['actions_serialized']);
                    $expiredRule->setSimpleAction($rule['simple_action']);
                    $expiredRule->setDiscountAmount($rule['discount_amount']);
                       
                    $expiredRule->save();

						$update = "UPDATE `mgretailinsights_promostoremapp` SET `status` = 0 WHERE p_id = ".$rule['p_id'] ;
						print_r($update); echo "<br>";
						$connection->query($update);
						}
					}
			}
		}
	
	
	//mgretailinsights_promostoremapp_backup table
	$backuprules_query  = "SELECT * FROM `mgretailinsights_promostoremapp_backup` WHERE rule_type = 1 AND status = 1"; 
	$backuprules_result 	= $connection->fetchall($backuprules_query);

	
	//mgretailinsights_promostoremapp_backup 
	if (count($backuprules_result) > 0) {
		foreach($backuprules_result as $rule) {
			$storeId = $rule['store_id'];
			$skus = array();
			$conditionsarr=array();
			$backupRuleId = $rule['p_id'];
			$con_arr = json_decode($rule['conditions_serialized'] , true); 
			if(!empty($con_arr['conditions'])) {
				$conditionsarr = $con_arr['conditions'];
				foreach($conditionsarr as $ck => $con){  
					if($con['attribute']=='sku'){
						$skus[] = $con['value'];
					}
					if(!empty($con['conditions'])){
						foreach($con['conditions'] as $c_inn => $c_inn_val){
							if($c_inn_val['attribute']=='sku'){
								$skus[] = $c_inn_val['value'];
							}
						}
					}
				}
			}
			//print_r($skus); echo "</br>"; die;
			if(!empty($skus)) {
				$skus = explode(',', str_replace(' ','',$skus[0])); 
				//print_r($skus); die;
				foreach($skus as $sku) {
					$storeproducts_query = "SELECT mkprod.*,count(*) as cnt,catprod.value as mrp, product.sku as sku FROM `mglof_marketplace_product`as mkprod join `mgcatalog_product_entity_decimal` as catprod on catprod.entity_id=mkprod.product_id join mgcatalog_product_entity product on product.entity_id = catprod.entity_id where mkprod.seller_id = $storeId and catprod.attribute_id = $attributeId and product.sku = '$sku' " ;
					$cnt = $connection->rawFetchRow($storeproducts_query,'cnt');	
					//print_r($cnt); die;
					if($cnt){
						$storeproducts_result = $connection->fetchall($storeproducts_query);	
						foreach($storeproducts_result as $product){ 
							//print_r($storeproducts_result); echo "<br>";
							$mrp = $product['mrp'];		  
							$sellerType = $rule['seller_type'];
						if($sellerType== 1) {  
							 if((!empty($product['pickup_from_store_old'])) || ($product['pickup_from_store_old']!= NULL) || ($product['pickup_from_store_old']!=0)){ 
								$old_price = $product['pickup_from_store_old'];	
								$update = "UPDATE `mglof_marketplace_product` SET pickup_from_store = ".$old_price." , pickup_from_store_old = null WHERE entity_id =".$product['entity_id']." AND seller_id = ".$storeId;
								print_r($update);echo "<br>"; 
								$connection->query($update);
							 }
						}
						if($sellerType== 0) {				
							 if((!empty($product['doorstep_price_old'])) || ($product['doorstep_price_old']!= NULL) || ($product['doorstep_price_old']!=0)){ 
								$old_price = $product['doorstep_price_old'];									
								$update = "UPDATE `mglof_marketplace_product` SET doorstep_price = ".$old_price." , doorstep_price = null WHERE entity_id =".$product['entity_id']." AND seller_id = ".$storeId ;
								print_r($update);echo "<br>";
								$connection->query($update);	
							
							 	}
							}				
						}
					}
				}
			}
			$delete = "DELETE FROM `mgretailinsights_promostoremapp_backup`
			WHERE p_id = $backupRuleId";
			print_r($delete); echo "<br>";
			$connection->query($delete);				
		}
	}

	//mgretailinsights_promostoremapp table
	$customrules_query  = "SELECT * FROM `mgretailinsights_promostoremapp` WHERE rule_type = 1 AND status = 1";  // only catalog rules
	$customrules_result 	= $connection->fetchall($customrules_query);
	

	if ( count($customrules_result) > 0) {
		foreach($customrules_result as $rule) {
			$storeId = $rule['store_id'];
			$skus = array();
				$conditionsarr=array();
			$con_arr = json_decode($rule['conditions_serialized'] , true); 
			if(!empty($con_arr['conditions'])) {
				$conditionsarr = $con_arr['conditions'];
				foreach($conditionsarr as $ck => $con){  
					if($con['attribute']=='sku'){
						$skus[] = $con['value'];
					}
					if(!empty($con['conditions'])){
						foreach($con['conditions'] as $c_inn => $c_inn_val){
							if($c_inn_val['attribute']=='sku'){
								$skus[] = $c_inn_val['value'];
							}
						}
					}
				}
			}
			// print_r($skus);

			if(!empty($skus)){
				$skus = explode(',', str_replace(' ','',$skus[0])); 
				
				foreach($skus as $sku) {
					$storeproducts_query = "SELECT mkprod.*,count(*) as cnt,catprod.value as mrp, product.sku as sku FROM `mglof_marketplace_product`as mkprod join `mgcatalog_product_entity_decimal` as catprod on catprod.entity_id=mkprod.product_id join mgcatalog_product_entity product on product.entity_id = catprod.entity_id where mkprod.seller_id = $storeId and catprod.attribute_id = $attributeId and product.sku = '$sku' " ;
					$cnt = $connection->rawFetchRow($storeproducts_query,'cnt');	
					//print_r($cnt);
					if($cnt){
						$storeproducts_result = $connection->fetchall($storeproducts_query);
	
						foreach($storeproducts_result as $product){ 
							//print_r($product['mrp']); echo "<br>";
							$mrp = $product['mrp'];		  
							$sellerType = $rule['seller_type'];
						if($sellerType== 1){  
							 if((!empty($product['pickup_from_store'])) || ($product['pickup_from_store']!= NULL) || ($product['pickup_from_store']!=0)){ 
								$old_price = $product['pickup_from_store'];	
								if($rule['simple_action']=='by_fixed'){
									$discount =  $rule['discount_amount'];
									$new_price = $mrp - $discount;
								}else if($rule['simple_action']=='to_fixed'){
									$new_price = $rule['discount_amount'];
								}else{
									$discount = ($mrp * $rule['discount_amount'])/100 ;
									$new_price = $mrp - $discount;
								 }
								 $new_price = roundUp($new_price,2);
								 if(($product['pickup_from_store_old']==0) || ($product['pickup_from_store_old']==NULL)){ 
									 $update = "UPDATE `mglof_marketplace_product` SET pickup_from_store_old = ".$old_price." , pickup_from_store = ".$new_price." WHERE entity_id =".$product['entity_id']." AND seller_id = ".$storeId;
									 print_r($update);echo "<br>";
									 $connection->query($update);
								 }
							 }
						}
						if($sellerType== 0){  
				
							 if((!empty($product['doorstep_price'])) || ($product['doorstep_price']!= NULL) || ($product['doorstep_price']!=0)){ 
								$old_price = $product['doorstep_price']; 	
				
								if($rule['simple_action']=='by_fixed'){
									$discount =  $rule['discount_amount'];
									$new_price = $mrp - $discount;
								 }else{
									$discount = ($mrp * $rule['discount_amount'])/100 ;
									$new_price = $mrp - $discount;
								}
								$new_price = roundUp($new_price,2);
								if(($product['doorstep_price_old']==0) || ($product['doorstep_price_old']==NULL)){ 
									$update = "UPDATE `mglof_marketplace_product` SET doorstep_price_old = ".$old_price." , doorstep_price = ".$new_price." WHERE entity_id =".$product['entity_id']." AND seller_id = ".$storeId ;
									print_r($update);echo "<br>";
								 	$connection->query($update);
								
								
									}
							 	}
							}
						}
					}
				}
			}	
		}
	}

	function roundUp ( $value, $precision ) { 
        $pow = pow ( 10, $precision ); 
        return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
    }
?>
