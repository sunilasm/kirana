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
	//$observerInterface 	 		= $objectManager->create('Magento\Framework\Event\ObserverInterface');
			
	# get connection
	$connection 				= $resource_connection->getConnection();
	
	/* Declarations */
	$base_url = $storeManager->getStore()->getBaseUrl();
	$admin_credentials= [
		"username" => "sunil.n",
		"password" => "admin123"
	];
	$unsuccessful_order_ids = [];
		
	$date = new DateTime();
	$timeStamp = $date->getTimestamp();

			
	$customrules_query  = "SELECT * FROM `mgretailinsights_promostoremapp` WHERE rule_type = 1 AND status = 1";  // only catalog rules
	$customrules_result 	= $connection->fetchall($customrules_query);
	

	if ( count($customrules_result) <= 0) {
		# stop if no products or rules
		exit();
	}
	$attribute_query = "SELECT attribute_id FROM `mgeav_attribute` WHERE attribute_code = 'price'";
	$attribute_result = $connection->fetchall($attribute_query);
	$attributeId = $attribute_result[0]['attribute_id'];
	
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
		if(!empty($skus)){
			$skus = explode(',', str_replace(' ','',$skus[0])); 
			//print_r($skus);
			foreach($skus as $sku) {
				$storeproducts_query = "SELECT mkprod.*,count(*) as cnt,catprod.value as mrp, product.sku as sku FROM `mglof_marketplace_product`as mkprod join `mgcatalog_product_entity_decimal` as catprod on catprod.entity_id=mkprod.product_id join mgcatalog_product_entity product on product.entity_id = catprod.entity_id where mkprod.seller_id = $storeId and catprod.attribute_id = $attributeId and product.sku = '$sku' " ;
				$cnt = $connection->rawFetchRow($storeproducts_query,'cnt');	
				//print_r($cnt);
				if($cnt){
					$storeproducts_result = $connection->fetchall($storeproducts_query);

					foreach($storeproducts_result as $product){ 
						//print_r($storeproducts_result); echo "<br>";
						$mrp = $product['mrp'];		  
						$sellerType = $rule['seller_type'];
					if($sellerType== 1){  
					   if((!empty($product['pickup_from_store'])) || ($product['pickup_from_store']!= NULL) || ($product['pickup_from_store']!=0)){ 
						  $old_price = $product['pickup_from_store'];	
						  if($rule['simple_action']=='by_fixed'){
								$discount =  $rule['discount_amount'];
								$new_price = $mrp - $discount;
						   }else{
								$discount = ($mrp * $rule['discount_amount'])/100 ;
								$new_price = $mrp - $discount;
						   }
						   $new_price = roundUp($new_price,2);
						   if(($product['pickup_from_store_old']==0) || ($product['pickup_from_store_old']==NULL)){ 
							   $update = "UPDATE `mglof_marketplace_product` SET pickup_from_store_old = ".$old_price." , pickup_from_store = ".$new_price." WHERE entity_id =".$product['entity_id']." AND seller_id = ".$storeId;
							   print_r($update);echo "<br>";
							   //print_r("pick");
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
							  //print_r($update);echo "<br>";
							  //print("door");
							 $connection->query($update);
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
