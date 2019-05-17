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
		"username" => "faizal.h",
		"password" => "Admin@123"
	];
	$unsuccessful_order_ids = [];
		
	$date = new DateTime();
	$timeStamp = $date->getTimestamp();

	$sellerprod_query 	= "SELECT * FROM `mglof_marketplace_product`";
	$sellerprod_result 	= $connection->fetchall($sellerprod_query);
			
	$customrules_query  = "SELECT * FROM `mgretailinsights_promostoremapp` WHERE rule_type = 1 AND status = 1";  // only catalog rules
	$customrules_result 	= $connection->fetchall($customrules_query);
   
	/**	
	 * Get Admin Token
	 * --------------- 
	*/
	$admin_token_url = "rest/V1/integration/admin/token";
	$ch = curl_init($base_url.$admin_token_url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($admin_credentials));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Content-Type: application/json", 
		"Content-Lenght: " . strlen(json_encode($admin_credentials))
	));
	$admin_token = curl_exec($ch);
	
	if ((count($sellerprod_result) <= 0)  || (count($customrules_result) <= 0)) {
		# stop if no products or rules
		exit();
	}

	foreach($customrules_result as $rule){
	  $storeId = $rule['store_id'];
		$storeproducts_query = "SELECT * FROM `mglof_marketplace_product` where seller_id = ".$storeId ;
		$storeproducts_result = $connection->fetchall($storeproducts_query);
        foreach($storeproducts_result as $product){
		   if((!empty($product['pickup_from_store'])) || ($product['pickup_from_store']!= NULL) || ($product['pickup_from_store']!=0)){ 
			  $old_price = $product['pickup_from_store'];			  
			  if($rule['simple_action']=='by_fixed'){
					$discount =  $rule['discount_amount'];
					$new_price = $old_price - $discount;
			   }else{
				    $discount = ($old_price * $rule['discount_amount'])/100 ;
				    $new_price = $old_price - $rule['discount_amount'];
			   }
			   if(($product['pickup_from_store_old']==0) || ($product['pickup_from_store_old']==NULL)){ 
			       $update = "UPDATE `mglof_marketplace_product` SET pickup_from_store_old = ".$old_price." , pickup_from_store = ".$new_price." WHERE entity_id =".$product['entity_id']." AND seller_id = ".$storeId;
			       $connection->query($update);
			   }
		   }

		   if((!empty($product['doorstep_price'])) || ($product['doorstep_price']!= NULL) || ($product['doorstep_price']!=0)){ 
			  $old_price = $product['doorstep_price']; 			  
			  if($rule['simple_action']=='by_fixed'){
					$discount =  $rule['discount_amount'];
					$new_price = $old_price - $discount;
		   	  }else{
					$discount = ($old_price * $rule['discount_amount'])/100 ;
					$new_price = $old_price - $rule['discount_amount'];
			  }
			  if(($product['doorstep_price_old']==0) || ($product['doorstep_price_old']==NULL)){ 
			      $update = "UPDATE `mglof_marketplace_product` SET doorstep_price_old = ".$old_price." , doorstep_price = ".$new_price." WHERE entity_id =".$product['entity_id']." AND seller_id = ".$storeId ;
				  $connection->query($update);
			  }
		   }

		}
	  
	}
	


	
?>
