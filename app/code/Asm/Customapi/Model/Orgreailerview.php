<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\OrgnizedretailerInterface;
use Retailinsights\Promotion\Model\PostTableFactory;
use Retailinsights\Promotion\Model\PromoTableFactory;
 
class Orgreailerview implements OrgnizedretailerInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $_sellerCollection;
    protected $_productCollectionFactory;
    protected $_connection;
    protected $_promoFactory;

    public function __construct(
       PostTableFactory $PostTableFactory ,
       PromoTableFactory $promoFactory,
       \Magento\Framework\App\RequestInterface $request,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Asm\Geolocation\Helper\Data $helperData,
       \Magento\Framework\App\ResourceConnection $_connection,
       \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
       $this->request = $request;
       $this->_sellerCollection = $sellerCollection;
       $this->helperData = $helperData;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->quoteFactory = $quoteFactory;
       $this->_productCollectionFactory = $productCollectionFactory;
       $this->_PostTableFactory = $PostTableFactory;
       $this->_connection = $_connection;
       $this->_promoFactory = $promoFactory;
    }

    public function orgreailer() 
    {
        $connection = $this->_connection->getConnection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $quote = $this->quoteFactory->create()->load($post['quote_id']);
        $flag = 0;
        $sellerData = '';
        if($post['latitude'] != '' && $post['longitude'] != '')
        {
            $ranageSeller = $this->getInRangeSeller($post['latitude'], $post['longitude']);
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ishu.log'); 
            $logger = new \Zend\Log\Logger();$logger->addWriter($writer);
            $logger->info($ranageSeller);
            $items = $quote->getAllItems();
            $response = array();
            $i = 0;
            foreach($ranageSeller as $orgretailer)
            {
            $logger->info("STORE  ".$orgretailer);

                $tempSellerProductArray = array();
                $tempSellerProductIdArray = array();
                $presentProducts = array();
                $seller_products = array();
                $seller_productsNew = array();
                $productPresentCollArray = array();
                $productNotPresentCollArray = array();
                $presentSubTotalArray = array();
                // Seller Product Collection
                $sellerCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $orgretailer));
                foreach($sellerCollection as $sellcoll):
                    $tempSellerProductArray[$sellcoll['product_id']][] = $sellcoll['seller_id'];
                    $tempSellerProductIdArray[] = $sellcoll['product_id'];
                    $seller_products[$sellcoll->getProduct_id()] = $sellcoll->getData();
                endforeach;

                // Get Orgnized Retailer deatils
                $sellerCollectionDetails = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $orgretailer));
                $sellerData = array();
                foreach($sellerCollectionDetails as $sellcoll):
                    $sellerData = $sellcoll->getData();
                    //Set contact number
                    if ($sellerData['contact_number']) {
                        if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['contact_number'],  $matches )){
                           $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                           $sellerData['contact_number'] = $result;
                        }
                     }

       		   //Set kirana landline
        		    if ($sellerData['telephone']){
           			   if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['telephone'],  $matches )){
                		   $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                		   $sellerData['telephone'] = $result;
            			}
        		     }
		    //Set kirana fax
                    if ($sellerData['kirana_fixed_line']) {
                        if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['kirana_fixed_line'],  $matches ) )
                        {
                           $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                           $sellerData['kirana_fixed_line'] = $result;
                        }
                    }

		        endforeach;

                // Quote Data
                $cartSubTotal = 0;
                $orderLevelDisc = 0;
		        $cartPresentProducts = 0;
                $cartNotPresentProducts = 0;               
	            foreach ($items as $item) 
                {
                $totalDiscount  = 0;

                    $collection = $this->_productCollectionFactory->create();
                    $collection->addAttributeToSelect('*');
                    $collection->addAttributeToSort('price', 'asc');
                    $produt_found = 0;
                    // print_r($item->getSku());exit;
                    // If seller have products.
                    if(count($tempSellerProductArray))
                    {
                        $collection->addFieldToFilter('entity_id', array('in' => $tempSellerProductIdArray));

                        if($item->getName() != null){
                            $collection->addFieldToFilter([['attribute' => 'sku', 'like' => '%'.$item->getSku().'%']]);
                        }
                        
                        $products = $collection->getData();
                        $mappedRulesArray = $this->getCustomTableRules();  
                        
                        foreach ($collection as $product)
                        {
                            if(!$produt_found){
                                $productCollectionData = $product->getData();

                                if(array_key_exists($product->getId(), $seller_products)){
                                    $productCollectionData['pickup_from_store'] = $seller_products[$product->getId()]['pickup_from_store'];
                                }
                                $productCollectionData['quote_qty'] = $item->getQty();
                                $collectionNew['seller_id'] = $item->getSeller_id();
                                $productPresentCollArray[] = $productCollectionData;
                                //print_r($seseller_products); exit;
                                if(isset($mappedRulesArray[$item->getSeller_id()])){ 
                                    foreach($mappedRulesArray[$item->getSeller_id()] as $k => $promo) {
                                         $description = json_decode($promo['description'],true);
                                         $ruleCode = $description['code'];
                                         $ruleId = $promo['p_id'];
                                         $product_price = 0;
                                         if(isset($seller_products[$product->getId()]['pickup_from_store'])){
                                            $product_price = $seller_products[$product->getId()]['pickup_from_store'];
                                         }else{
                                            $product_price = $item->getPrice(); 
                                         }
                                        if($ruleCode == "BXGX" && ($orgretailer==$promo['store_id'])){
                                          //  $logger->info("in bxgx dicount");
                                            $actionArr = json_decode($promo['actions_serialized'], true);
                                            $ruleSku = $this->getActionSku($actionArr);
                                            $skubxgx = '';
                                            foreach($actionArr['conditions'] as $ck => $con){
                                                if($con['attribute']=='sku'){
                                                    $skubxgx = $con['value'];
                                                }
                                            }
                                            if($item->getSku() == $skubxgx){
                                                $totalDiscount  += $this->applyBxgxBxgy($post['quote_id'],$item->getId(),$product_price,'BXGX');
                                            }
                                           // $logger->info($totalDiscount." BXGX discount");
                                        }
                                        if($ruleCode == "BXGY" && ($orgretailer==$promo['store_id'])){
                                           // $logger->info("in bxgy dicount");
                                            $totalDiscount  += $this->applyBxgxBxgy($post['quote_id'],$item->getId(),$product_price,'BXGY');
                                           // $logger->info($totalDiscount." BXGY discount");
                                        }
                                        if($ruleCode == "BXGOFF" && ($orgretailer==$promo['store_id'])){
                                           // $logger->info("in BXGOFF dicount");
                                            $totalDiscount  +=  $this->applyBxgoff($promo,$product_price,$item->getSku(),$item->getQty(),'BXGOFF',$item->getSeller_id());
                                           // $logger->info($totalDiscount." BXGOFF discount");
                                        }
                                        if($ruleCode == "BXGPOFF" && ($orgretailer==$promo['store_id'])){
                                           // $logger->info("in BXGPOFF dicount");
                                            $totalDiscount  +=  $this->applyBxgoff($promo,$product_price,$item->getSku(),$item->getQty(),'BXGPOFF',$item->getSeller_id());
                                           // $logger->info($totalDiscount." BXGPOFF discount");
                                        }
                                        if($ruleCode == "BNXAF" && ($orgretailer==$promo['store_id'])){
                                            $itemPriceTotal = 0;
                                            $actionArr = json_decode($promo['actions_serialized'], true);
                                            $ruleSku = array();
                                            foreach($actionArr['buy_product'] as $k => $v){
                                              $ruleSku[$v['sku']] = $v['qty'];
                                            }
                                            $ruleSkuLen = sizeof($ruleSku);
                                            $quantity = $item->getQty();
                                            foreach($ruleSku as $rule_sku =>$sku_qty){
                                            $qtyFactor = floor($quantity/$sku_qty);
                                            $qtyCheck = ($quantity%$sku_qty);
                          
                                              if(($rule_sku == $item->getSku()) && ($sku_qty <= $quantity)){
                                                $itemPriceTotal += $item->getPrice()*$quantity;
                                                $fixedPrice = $promo['discount_amount']; 
                                                $disc_amt = ($fixedPrice*$qtyFactor);
                                                $additional_item = 0;
                                                if(($quantity > $sku_qty) && ($qtyCheck!=0)){
                                                  $additional_item = $item->getPrice();  //($quantity - $sku_qty)*
                                                }
                                                $totalDiscount += ($itemPriceTotal -  $disc_amt)-$additional_item;
                          
                                                // if(isset($sellerAmount[$sellerId])) {
                                                //   $sellerAmount[$sellerId] -= $discountBnxaf;
                                                // }
                                              }
                                            }

                                        }
                                    }
                                }
                        
                                if(isset($seller_products[$product->getId()]['pickup_from_store']))
                                {
                                    $cartSubTotal += ($seller_products[$product->getId()]['pickup_from_store'] * $item->getQty());
                                    $logger->info(" subtotal without store promo discount  ". $cartSubTotal);

                                    $cartSubTotal = ($cartSubTotal - $totalDiscount);
                                }
                              
                                $cartPresentProducts += $item->getQty();
                                $produt_found = 1;
                            }
                        }
                    }
                    $logger->info(" subtotal with @@@ discount  ". $cartSubTotal);
                    // If product not present with store.
                    if($produt_found == 0)
                    {
                        // print_r($item->getProduct_id());exit;
                        $sellerCollectionNew = $this->_sellerProductCollection->getCollection()->addFieldToFilter('product_id', array('in' => $item->getProduct_id()));
                        // print_r($sellerCollectionNew->getData());exit;
                        foreach($sellerCollectionNew as $sellcoll):
                            $seller_productsNew[$sellcoll->getProduct_id()] = $sellcoll->getData();
                        endforeach;

                        $collectionNew = $this->_productCollectionFactory->create();
                        $collectionNew->addAttributeToSelect('*');
                        $collectionNew->addFieldToFilter('entity_id', ['in' => $item->getProduct_id()]);

                        foreach($collectionNew as $product):
                            // print_r($product->getId());
                            // print_r($seller_products);
                            $collectionNew = $product->getData();
                            $collectionNew['quote_qty'] = $item->getQty();
                            $collectionNew['seller_id'] = $item->getSeller_id();
                            if(array_key_exists($product->getId(), $seller_productsNew)){
                                $collectionNew['pickup_from_store'] = $seller_productsNew[$product->getId()]['pickup_from_store'];
                            }
			        $cartNotPresentProducts += $item->getQty();
                            $productNotPresentCollArray[] = $collectionNew;
                        endforeach;
                    }           
                }
                    ///////////////////
                    $sellerAmount = [];
                    $sellerAmount[$orgretailer] = $cartSubTotal;
                    foreach($sellerAmount as $seller_id => $org_total) {
                        if(isset($mappedRulesArray[$seller_id])){ 
                            foreach($mappedRulesArray[$seller_id] as $k => $promo) {
                                $description = json_decode($promo['description'],true);
                                $ruleCode = $description['code'];
                                $ruleId = $promo['p_id'];
                                $discountAmount = $promo['discount_amount'];
                                if($ruleCode == "BWGO" && ($orgretailer==$promo['store_id'])){  
                                    $logger->info("in BWGO dicount");
                                    $orderLevelDisc = $this->checkPromoBuyWorth($promo['p_id'], $seller_id, 0, $discountAmount,$org_total);
                                    $logger->info($orderLevelDisc." bwgo discount");
                                }
                                if($ruleCode == "BWGOP" && ($orgretailer==$promo['store_id'])){  
                                    $logger->info("in BWGOP dicount");
                                    $orderLevelDisc = $this->checkPromoBuyWorth($promo['p_id'], $seller_id, 1, $discountAmount,$org_total);
                                    $logger->info($orderLevelDisc." bwgop discount");
                                }
                                if($ruleCode == "BWGY" && ($orgretailer==$promo['store_id'])) {
                                    $logger->info("in BWGY dicount");
                                    $actionArr = json_decode($promo['actions_serialized'], true);
                                    $getProdSku = $getProdQty = $getProdId = '';
                                    foreach($actionArr['base_subtotal'] as $k => $v){ 
                                      $operator = $v['operator'];
                                      $baseSubtotal = $v['fixed_amount'];
                                    }
                                    if($operator == ">") {
                                        if($org_total > $baseSubtotal) {
                                            $orderLevelDisc = $this->getOrderLevelDisc($actionArr['get_product'],$seller_id);
                                        }
                                    }
                                    if($operator == ">=") {
                                        if($org_total >= $baseSubtotal) {
                                            $orderLevelDisc = $this->getOrderLevelDisc($actionArr['get_product'],$seller_id);
                                        }
                                    }
                                    if($operator == "<") {
                                        if($org_total < $baseSubtotal) {
                                            $orderLevelDisc = $this->getOrderLevelDisc($actionArr['get_product'],$seller_id);
                                        }
                                      }
                                    if($operator == "<=") {
                                        if($org_total <= $baseSubtotal) {
                                            $orderLevelDisc = $this->getOrderLevelDisc($actionArr['get_product'],$seller_id);
                                        }
                                    }
                                    if($operator == "==") {
                                        if($org_total == $baseSubtotal) {
                                            $orderLevelDisc = $this->getOrderLevelDisc($actionArr['get_product'],$seller_id);
                                        }
                                    } 
                                    $logger->info($orderLevelDisc." bwgy discount");

                 
                                }
                            }
                        }
                    }
                    $logger->info(" subtotal without order level discount  ". $cartSubTotal);

                    $cartSubTotal = ($cartSubTotal - $orderLevelDisc);
                    ///////////////////
                $cartSummeryArray = array('total_item_count' => ($cartPresentProducts + $cartNotPresentProducts), 'present_item_count' => $cartPresentProducts, 'not_present_item_count' => $cartNotPresentProducts, 'sub_total' => number_format((float)$cartSubTotal, 2, '.', ''));

                $response[$i]['store'] = $sellerData;
                $response[$i]['present_data'] = $productPresentCollArray;
                $response[$i]['not_present_data'] = $productNotPresentCollArray;
                $response[$i]['cart_summary'] = $cartSummeryArray;
                $i++;
            }
        }
        if(count($response)){
            $response = $this->sort_by_present_item_count($response);
            $final_response = array();
		    $org_return_count = 3;
            if(count($response) < 3)
            {
                $org_return_count = count($response);
            }
            for($i=0; $i<$org_return_count; $i++)
            {
                $final_response[$i] = $response[$i];
            }
            $response = $final_response;
        }
        $data = $response;
        return $data;
    }

    private function sort_by_present_item_count($array) 
    {
        $sorter = array();
        $ret = array();
        reset($array);
        $count_array = array();
        foreach($array as $key => $store)
        {
            $count_array[$key] = $store['cart_summary']['present_item_count'];
        }
        arsort($count_array);
        $response = array();
        foreach($count_array as $key => $value)
        {
            $response[] = $array[$key];
        }

        for($i=0; $i<count($response); $i++)
        {
            $temp = $i+1;
            if($temp < count($response))
            { 
                if($response[$i]['cart_summary']['present_item_count'] > 0)
                {
                    if($response[$i]['cart_summary']['present_item_count'] == $response[$temp]['cart_summary']['present_item_count'])
                    {
                        if($response[$i]['cart_summary']['sub_total'] > $response[$temp]['cart_summary']['sub_total'])
                        {
                            $temp_array = $response[$i];
                            $response[$i] = $response[$temp];
                            $response[$temp] = $temp_array;
                        }
                    }
                }
            }
        }
        return $response;
    }

    public function getInRangeSeller($lat, $lon){
        $selerIdArray = array();
        $rangeSetting = $this->helperData->getGeneralConfig('enable');
        $rangeInKm = $this->helperData->getGeneralConfig('range_in_km');
        if($rangeSetting == 1){
            if($rangeInKm){
                $distance = $rangeInKm; //your distance in KM
            }else{
                $distance = 1; //your distance in KM
            }
        }else{
            $distance = 1; //your distance in KM
        }
        
        $R = 6371; //constant earth radius. You can add precision here if you wish
        $maxLat = $lat + rad2deg($distance/$R);
        $minLat = $lat - rad2deg($distance/$R);
        $maxLon = $lon + rad2deg(asin($distance/$R) / cos(deg2rad($lat)));
        $minLon = $lon - rad2deg(asin($distance/$R) / cos(deg2rad($lat)));
        // filter collection in range of lat and long
        $sellerCollection = $this->_sellerCollection->getCollection()
        ->setOrder('position','ASC')
        ->addFieldToFilter('group_id',array('neq'=>1))
        ->addFieldToFilter('geo_lat',array('gteq'=>$minLat))
        ->addFieldToFilter('geo_lng',array('gteq'=>$minLon))
        ->addFieldToFilter('geo_lat',array('lteq'=>$maxLat))
        ->addFieldToFilter('geo_lng',array('lteq'=>$maxLon))
        ->addFieldToFilter('status',1);
        //->setPageSize(3);
        // get Seller id's
        $sellerData = $sellerCollection->getData();


        foreach($sellerData as $seldata):
            $selerIdArray[] = $seldata['seller_id'];
            
        endforeach;
	//print_r($selerIdArray);exit;
        return  $selerIdArray;
    }
   
    public function getCustomTableRules() {
        $mapped_rules = [];
        $mapped_data = $this->_PostTableFactory->create()->getCollection()
        ->setOrder('p_id','ASC')
        ->addFieldToFilter('seller_type',1)
        ->addFieldToFilter('status',1)
        ->addFieldToFilter('rule_type', array('neq' => 1));
        $count = 0;
        foreach ($mapped_data->getData() as $k => $promo) {            
            if(isset($mapped_rules[$promo['store_id']])) {
                array_push($mapped_rules[$promo['store_id']],$promo);
            } else {
                $mapped_rules[$promo['store_id']] = array($promo);
            }            
            //$count++;
        }
        //return $count;
        return $mapped_rules; 
    }
    public function getActionSku($action_array){
      $actionSkus = array();
      if(!empty($action_array)) {
        $conditionsarr = $action_array['conditions'];
        foreach($conditionsarr as $ck => $con){
            if($con['attribute']=='sku'){
                $actionSkus[] = $con['value'];
            }
            if(!empty($con['conditions'])){
                foreach($con['conditions'] as $c_inn => $c_inn_val){
                    if($c_inn_val['attribute']=='sku'){
                        $actionSkus[] = $c_inn_val['value'];
                    }
                }
            }
        }
      }
      return $actionSkus;
    }
    public function getActionQuantity($action_array){
        $actionQty = 0;
        if(!empty($action_array)) {
          $conditionsarr = $action_array['conditions'];
          foreach($conditionsarr as $ck => $con){
              if($con['attribute']=='quote_item_qty'){
                  $actionQty = $con['value'];
              }
          }
        }
        return $actionQty;
    }

    public function applyBxgxBxgy($quoteId,$itemId,$price,$type){
        $discPrice = 0;
        $applyPromoData = $this->_promoFactory->create()->getCollection()
        ->addFieldToFilter('cart_id', $quoteId);
        foreach($applyPromoData as $key => $val){
            $itemInfo = json_decode($val['item_qty'],true);
            foreach($itemInfo as $k => $itemArray){
                foreach($itemArray as $key => $value){
                  $itemData = json_decode($value);
                  if(isset($itemData->qty)) {
                      if(($itemData->id == $itemId) && ($itemData->type == $type)){
                          $discPrice = ($price*$itemData->qty);
                      }
                  }
                }
            }
        }
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ishu.log'); 
        $logger = new \Zend\Log\Logger();$logger->addWriter($writer);
       // $logger->info($quoteId.".......".$itemId.".......".$price.".........".$discPrice);
        return $discPrice;
    }

   
    public function applyBxgoff($promo,$product_price,$itemSku,$itemQty,$type,$seller_id){
        $prDiscount =0;  
        $description = json_decode($promo['description'],true);
        $action_arr = json_decode($promo['actions_serialized'] , true); 
        $actionSerSkus = $this->getActionSku($action_arr);
        if(in_array($itemSku, $actionSerSkus)){   //applypromo
          $ruleQty = $this->getActionQuantity($action_arr);
          $discountFactor =  floor($itemQty/$ruleQty); 
          if(($description['code'] == $type)  && ($seller_id == $promo['store_id'])){
            if($type == 'BXGPOFF'){              
                $prDiscount = ($discountFactor * $product_price * $ruleQty * $promo['discount_amount'])/100 ;
            }else{
                $prDiscount = ($discountFactor * $promo['discount_amount']);          
            }
          }
        }
        return $prDiscount;   
    }
    public function getAppliedDiscount($sellerAmount,$discountAmount,$percent){
        $appliedDiscount = 0;
        if($percent == 1){              
            $appliedDiscount = $sellerAmount*$discountAmount/100;
        } else {
            $appliedDiscount = $discountAmount;         
        }
        return $appliedDiscount;
    }
    public function getOrderLevelDisc($actionArr,$seller_id){
        $connection = $this->_connection->getConnection();
        $orderLevelDisc = 0;
        foreach($actionArr as $k => $v){ 
            $getProdSku = $v['sku'];
            $getProdQty = $v['qty'];
            $productQry  = "SELECT * FROM `mgcatalog_product_entity` WHERE sku ='".$getProdSku."'" ;
            $productResult 	= $connection->rawFetchRow($productQry);
            $getProdId = $productResult['entity_id'];  
            $priceQry = "SELECT * FROM `mglof_marketplace_product`WHERE product_id = ".$getProdId." and seller_id =".$seller_id;
            $priceResult 	= $connection->rawFetchRow($priceQry);
            $orderLevelDisc = $priceResult['pickup_from_store']; 
        }
        return $orderLevelDisc;
    }
    public function  checkPromoBuyWorth($customPromoId, $sellerId, $percent, $discountAmount,$sellerAmount) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ishu.log'); 
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
    
        $promoEntry = array();
        $getAppliedDiscount = 0;
        $rule_data = $this->_PostTableFactory->create()->getCollection()
        ->addFieldToFilter('p_id',$customPromoId);
        $promotionData = $rule_data->getData();
        $conditions_arr = json_decode($promotionData[0]['conditions_serialized'] , true); 
       
        $getConditionsParameters = $this->getConditionsParameters($conditions_arr);
     
        foreach ($getConditionsParameters as $operator => $value) {
            if($operator == ">") {
                if($sellerAmount > $value){
                    if($discountAmount > 0) {
                        $getAppliedDiscount = $this->getAppliedDiscount($sellerAmount,$discountAmount,$percent);
                    }
                }
            }
            if($operator == "==") {
              if($sellerAmount == $value){
                  if($discountAmount > 0) {
                      $getAppliedDiscount = $this->getAppliedDiscount($sellerAmount,$discountAmount,$percent);
                  }
              }
            }
            if($operator == "<") {
              if($sellerAmount < $value){
                  if($discountAmount > 0) {
                    $getAppliedDiscount = $this->getAppliedDiscount($sellerAmount,$discountAmount,$percent);
                  }
              }
            }
            if($operator == ">=") {
              if($sellerAmount >= $value){
                  if($discountAmount > 0) {
                    $getAppliedDiscount = $this->getAppliedDiscount($sellerAmount,$discountAmount,$percent);
                  }
              }
            }
            if($operator == "<=") {
              if($sellerAmount <= $value){
                  if($discountAmount > 0) {
                    $getAppliedDiscount = $this->getAppliedDiscount($sellerAmount,$discountAmount,$percent);
                  }
              }
           }    
        }
        return $getAppliedDiscount;
     }
     public function getConditionsParameters($conditions_arr){
        $condition = array();
        if(!empty($conditions_arr)) {
            $conditionarray = $conditions_arr['conditions'][0];
            $condition[$conditionarray['operator']] = $conditionarray['value'];
          }
          return $condition;
    }
}
 
