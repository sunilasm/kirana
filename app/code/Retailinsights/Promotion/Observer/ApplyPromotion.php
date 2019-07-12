<?php
namespace Retailinsights\Promotion\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Retailinsights\Promotion\Model\PostTableFactory;
use Retailinsights\Promotion\Model\PromoTableFactory;
use \Magento\Store\Model\StoreManagerInterface;


class ApplyPromotion implements ObserverInterface
{
  protected $_productRepository;
  protected $_cart;
  protected $quoteRepository;
  protected $_promoFactory;
  protected $_quoteAddressFactory;
  protected $_connection;
  protected $_storeManager;
  protected $_mgQuote;


  public function __construct(
   \Magento\Catalog\Model\ProductRepository $productRepository,
   \Magento\Checkout\Model\Cart $cart,
   PostTableFactory $PostTableFactory ,
   StoreManagerInterface $storeManager,
   PromoTableFactory $promoFactory,
   \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
   \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
   \Magento\Framework\App\ResourceConnection $_connection,
   \Magento\Quote\Model\Quote $mgQuote
   )
  {
      $this->_productRepository = $productRepository;
      $this->_storeManager = $storeManager;
      $this->_cart = $cart;
      $this->_PostTableFactory = $PostTableFactory;
      $this->_promoFactory = $promoFactory;
      $this->quoteRepository = $quoteRepository;
      $this->_quoteAddressFactory = $quoteAddressFactory;
      $this->_connection = $_connection;
      $this->_mgQuote = $mgQuote;
  }
  public function execute(\Magento\Framework\Event\Observer $observer)
  {  
    $connection = $this->_connection->getConnection();
    $quoteId = $observer->getData('quoteid');     
    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jannath.log'); 
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);
    $logger->info("Delete Start");  
    $logger->info("Request Method: ".$_SERVER['REQUEST_METHOD']);  

      //Deleting promotion data in custom table 
      $base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
       
        $delData = $this->_promoFactory->create()->getCollection()
        ->addFieldToFilter('cart_id', $quoteId);
        foreach($delData->getData() as $k => $val){     
          if($val['cart_id']==$quoteId){
              $itemInfo = json_decode($val['item_qty'],true);
                foreach($itemInfo as $k => $itemArray){
                  foreach($itemArray as $key => $value){
                    $itemData = json_decode($value);
                    if(isset($itemData->qty) && !empty($itemData->qty)) {
                      $quoteResult = $this->getQuoteQty($itemData->id);
                      $quoteQty = $quoteResult['qty'];
                      $quoteProdId = $quoteResult['product_id'];
                      $quoteSeller = $quoteResult['seller_id'];                      
                      if($itemData->type == 'BXGY' || $itemData->type == 'BWGY' ){
                        if($quoteQty == $itemData->qty){
                          $logger->info("RemoveItem BXGY");
                          $logger->info($quoteId);
                          $logger->info($itemData->id);
                          $deleteItem = $this->removeItem($quoteId, $itemData->id);
                        } else {
                          $qty = ($quoteQty - $itemData->qty);
                          $logger->info("Update BXGY ELSE ");
                          $updateCart = $this->updateItem($quoteId,$itemData->id,$qty,$quoteProdId,$quoteSeller);
                          //$logger->info($updateCart);
                         
                        }
                      }
                      if($itemData->type == 'BXGX'){
                        $logger->info("Update Item BXGX");
                        $qty = (($quoteQty - $itemData->qty)==0) ? $quoteQty : ($quoteQty - $itemData->qty);
                        $updateCart = $this->updateItem($quoteId,$itemData->id,$qty,$quoteProdId,$quoteSeller);
                        //$logger->info($updateCart);
                      }
                      // $logger->info('Qty quote  '.$quoteQty);
                      // $logger->info("PUT payload-----".$quoteId."-----".$itemData->id."-----".$qty."-----".$quoteProdId."-----".$quoteSeller);
                    }
                  }
                }
            
            $deletePrev = $this->_promoFactory->create();
            $deletePrev->load($val['ap_id']);
            $deletePrev->delete();
          }
        }

        $logger->info("Delete End");

        $quote = $this->quoteRepository->getActive($quoteId);
        $quoteItems = $quote->getItems();
        $mappedRulesArray = $this->getCustomTableRules();
        
        ///////////***BWGO,BWGY,BWGPO */
        $sellerAmount = [];
        $sellerAmountQry="SELECT sum(row_total) as sum_row_total,seller_id FROM `mgquote_item` WHERE quote_id = $quoteId  GROUP BY seller_id";
        $sellerAmountResult= $connection->fetchall($sellerAmountQry);
        foreach($sellerAmountResult as $key => $value) {
         $sellerAmount[$value['seller_id']] = $value['sum_row_total'];

        }
        $logger->info($sellerAmount);

        $logger->info("Promotions Start");
        ///////////////////////////////
        $promoFinalEntry = [];
        $promoFinalEntry['discount'] = array();
        $promoFinalEntry['item'] = array();
        $checkPromo = array();
        $total_disc = 0;
        $bnxafCount = [];
        $bnxgoCount = [];
        $itemPriceTotal = 0;

        foreach($quoteItems as $key => $value) {
          $sellerId = $quoteItems[$key]->getSellerId();
          $sku = $quoteItems[$key]->getSku();
          $quantity = $quoteItems[$key]->getQty();
          if(isset($mappedRulesArray[$sellerId])){ 
            foreach($mappedRulesArray[$sellerId] as $k => $promo) {

             

              $description = json_decode($promo['description'],true);
              $ruleCode = $description['code'];
              $ruleId = $promo['p_id'];
             
              if($ruleCode == "BXGOFF"){  
                $logger->info("BXGOFF Start");
                $checkPromo = $this->checkPromoBxgoff($promo['p_id'], $sellerId, $sku, $quantity, 0, $quoteItems[$key]->getPrice());
                array_push($promoFinalEntry , $checkPromo);
                //------
                if(isset($checkPromo)){
                  foreach($checkPromo as $a => $b){
                    $thisPromoDisc = 0;
                      foreach($b as $c => $d){
                        $e = json_decode($d);
                        $thisPromoDisc = $e->amount;
                        if(isset($sellerAmount[$sellerId]) && $thisPromoDisc > 0) {
                          $sellerAmount[$sellerId] -= $thisPromoDisc;
                        }
                      }
                    }
                }
                //------
                $logger->info("BXGOFF Ends");

              }
              if($ruleCode == "BXGPOFF"){
                $logger->info("BXGPOFF Start");
                $checkPromo = $this->checkPromoBxgoff($promo['p_id'], $sellerId, $sku, $quantity, 1, $quoteItems[$key]->getPrice());
                array_push($promoFinalEntry , $checkPromo);
                 //------
                 if(isset($checkPromo)){
                  foreach($checkPromo as $a => $b){
                    $thisPromoDisc = 0;
                      foreach($b as $c => $d){
                        $e = json_decode($d);
                        $thisPromoDisc = $e->amount;
                        if(isset($sellerAmount[$sellerId]) && $thisPromoDisc > 0) {
                          $sellerAmount[$sellerId] -= $thisPromoDisc;
                        }
                      }
                    }
                }
                //------
                $logger->info("BXGPOFF End");
              }
              if($ruleCode == "BNXAF"){
                $logger->info("BNXAF Start");
                $discountBnxaf = 0;
                  $actionArr = json_decode($promo['actions_serialized'], true);
                  $ruleSku = array();
                  foreach($actionArr['buy_product'] as $k => $v){
                    $ruleSku[$v['sku']] = $v['qty'];
                  }
                  $ruleSkuLen = sizeof($ruleSku);
                  foreach($ruleSku as $rule_sku =>$sku_qty){
                  $qtyFactor = floor($quantity/$sku_qty);
                  $qtyCheck = ($quantity%$sku_qty);

                    if(($rule_sku == $sku) && ($sku_qty <= $quantity)){
                      if(isset($bnxafCount[$ruleId])) {
                        $bnxafCount[$ruleId]++;
                      } else {
                        $bnxafCount[$ruleId] = 1;
                      }
                      $itemPriceTotal += $quoteItems[$key]->getPrice()*$quantity;
                      $fixedPrice = $promo['discount_amount']; 
                      $disc_amt = ($fixedPrice*$qtyFactor);
                      $additional_item = 0;
                      if(($quantity > $sku_qty) && ($qtyCheck!=0)){
                        $additional_item = $quoteItems[$key]->getPrice();  //($quantity - $sku_qty)*
                      }
                      $discountBnxaf = ($itemPriceTotal -  $disc_amt)-$additional_item;

                      if(isset($sellerAmount[$sellerId])) {
                        $sellerAmount[$sellerId] -= $discountBnxaf;
                      }

                      $logger->info("BNXAF Price disc".$discountBnxaf);
                      $logger->info("BNXAF Price total".$itemPriceTotal);
                    }
                  }
                  if(isset($bnxafCount[$ruleId])){
                    if($ruleSkuLen == $bnxafCount[$ruleId]){ 
                      $sku = $ruleSku;
                      $checkPromo = $this->checkPromoBnxaf($discountBnxaf,$sellerId);
                      array_push($promoFinalEntry , $checkPromo);
                      $bnxafCount[$ruleId] = 0;
                    }
                  }
                $logger->info("BNXAF End");  
              }
              if($ruleCode == "BNXG1O") {
                $logger->info("BNXG1O Start");
                $actionArr = json_decode($promo['actions_serialized'], true);
                $ruleSku = array();
                foreach($actionArr['buy_product'] as $k => $v){
                  $ruleSku[$v['sku']] = $v['qty'];
                }
                $ruleSkuLen = sizeof($ruleSku);
                foreach($ruleSku as $rule_sku =>$sku_qty){
                  if(($rule_sku == $sku) && ($sku_qty <= $quantity)){
                    if(isset($bnxgoCount[$ruleId])) {
                      $bnxgoCount[$ruleId]++;
                    } else {
                      $bnxgoCount[$ruleId] = 1;
                    }
                  }
                }
                $discount_bnxgo = 0;
                if(isset($bnxgoCount[$ruleId])){
                  if($ruleSkuLen == $bnxgoCount[$ruleId]){ 
                    foreach($actionArr['discount_product'] as $k=>$v){
                      if($v['sku'] == $sku){
                        $discount_bnxgo = ($quoteItems[$key]->getPrice()*$v['discount_product'])/100;
                      }
                    } 
                    $checkPromo = $this->checkPromoBnxgo($discount_bnxgo,$sellerId);
                    array_push($promoFinalEntry , $checkPromo);
                    $bnxgoCount[$ruleId] = 0;
                    if(isset($sellerAmount[$sellerId])) {
                      $sellerAmount[$sellerId] -= $discount_bnxgo;
                    }
                  }
                }
                $logger->info("BNXG1O End");  
              }
              if($ruleCode == "BXGX"){ 
                $logger->info('BXGX Start');
                $ruleSku = $skuQty = 0;
                $actionArr = json_decode($promo['actions_serialized'], true);
                $ruleSku = $this->getActionSku($actionArr);
                foreach($actionArr['conditions'] as $ck => $con){
                    if($con['attribute']=='quote_item_qty'){
                        $skuQty = $con['value'];
                    }
                }
                if(($ruleSku[0] == $sku ) && ($skuQty <= $quantity)){ 
                  $prodQty = 1;
                  if($quantity == $skuQty){
                    $qtyFactor = 1;
                   }else{
                    $qtyFactor = floor($quantity/$skuQty); 
                   }
                  $prodQty = ($promo['discount_amount']*$qtyFactor);
                  $discPrice = ($quoteItems[$key]->getPrice()*$prodQty); 
                  $checkPromo  = $this->internalAddtoCart($quoteId,$sku,$prodQty,$quoteItems[$key]->getProductId(),$sellerId,$discPrice,$quoteItems[$key]->getItemId(),$quantity,'BXGX');
                  array_push($promoFinalEntry , $checkPromo);                 
                }
                $logger->info('BXGX End');
              }
              if($ruleCode == "BXGY"){
                $logger->info('BXGY Start');
                $actionArr = json_decode($promo['actions_serialized'], true);
                $ruleSku = array();
                $getProdSku = $getProdQty = $getProdId = '';
                foreach($actionArr['get_product'] as $k => $v){ 
                  $getProdSku = $v['sku'];
                  $getProdQty = $v['qty'];
                }
                $productQry  = "SELECT * FROM `mgcatalog_product_entity` WHERE sku ='".$getProdSku."'" ;
                $productResult 	= $connection->rawFetchRow($productQry);
                $getProdId = $productResult['entity_id'];  
                $priceQry = "SELECT * FROM `mglof_marketplace_product`WHERE product_id = ".$getProdId." and seller_id =".$sellerId;
                $priceResult 	= $connection->rawFetchRow($priceQry);
                $discPrice = $priceResult['pickup_from_store']; 
                foreach($actionArr['buy_product'] as $k => $v){ 
                 if(($v['sku'] == $sku ) && ($v['qty'] <= $quantity)){
                   if($quantity == $v['qty']){
                    $qtyFactor = 1;
                   }else{
                    $qtyFactor = floor($quantity/$v['qty']);
                   }
                    $getProdQty  = ($getProdQty*$qtyFactor);
                    $discPrice = ($discPrice*$getProdQty); 
                    $checkPromo = $this->internalAddtoCart($quoteId,$getProdSku,$getProdQty,$getProdId,$sellerId,$discPrice,$quoteItems[$key]->getItemId(),$quantity,'BXGY');
                    array_push($promoFinalEntry , $checkPromo);
                 }
                }
                $logger->info('BXGY End');
              }
            }
            
          }
        }
        $logger->info("Promotions End");
        $logger->info("Order Level Promotions Start");
        foreach($sellerAmount as $seller_id => $org_total) {
          $logger->info('org_total');
          $logger->info($org_total);

          if(isset($mappedRulesArray[$seller_id])){ 
              foreach($mappedRulesArray[$seller_id] as $k => $promo) {
                  $description = json_decode($promo['description'],true);
                  $ruleCode = $description['code'];
                  $ruleId = $promo['p_id'];
                  $discountAmount = $promo['discount_amount'];
                  if($ruleCode == "BWGO"){  
                      $logger->info('BWGO Start');
                      $checkPromo = $this->checkPromoBuyWorth($promo['p_id'], $seller_id, 0, $discountAmount,$org_total);
                      array_push($promoFinalEntry , $checkPromo);
                      $logger->info('BWGO End');
                  }
                  if($ruleCode == "BWGOP"){  
                    $logger->info('BWGOP Start');
                    $checkPromo = $this->checkPromoBuyWorth($promo['p_id'], $seller_id, 1, $discountAmount,$org_total);
                    array_push($promoFinalEntry , $checkPromo);
                    $logger->info('BWGOP End');
                  }
                  if($ruleCode == "BWGY") {
                    $logger->info('BWGY Start');
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jannath.log'); 
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                      $actionArr = json_decode($promo['actions_serialized'], true);
                      $getProdSku = $getProdQty = $getProdId = '';
                      foreach($actionArr['base_subtotal'] as $k => $v){ 
                        $operator = $v['operator'];
                        $baseSubtotal = $v['fixed_amount'];
                      }
                      if($operator == ">") {
                        if($org_total > $baseSubtotal) {
                          foreach($actionArr['get_product'] as $k => $v){ 
                            $getProdSku = $v['sku'];
                            $getProdQty = $v['qty'];
                            $productQry  = "SELECT * FROM `mgcatalog_product_entity` WHERE sku ='".$getProdSku."'" ;
                            $productResult 	= $connection->rawFetchRow($productQry);
                            $getProdId = $productResult['entity_id'];  
                            $priceQry = "SELECT * FROM `mglof_marketplace_product`WHERE product_id = ".$getProdId." and seller_id =".$seller_id;
                            $priceResult 	= $connection->rawFetchRow($priceQry);
                            $discPrice = $priceResult['pickup_from_store']; 
                            $checkPromo = $this->internalAddtoCart($quoteId,$getProdSku,$getProdQty,$getProdId,$seller_id,$discPrice,null,null,'BWGY');
                            array_push($promoFinalEntry , $checkPromo);
                          }
                        }
                      }
                      if($operator == ">=") {
                        if($org_total >= $baseSubtotal) {
                          foreach($actionArr['get_product'] as $k => $v){ 
                            $getProdSku = $v['sku'];
                            $getProdQty = $v['qty'];
                            $productQry  = "SELECT * FROM `mgcatalog_product_entity` WHERE sku ='".$getProdSku."'" ;
                            $productResult 	= $connection->rawFetchRow($productQry);
                            $getProdId = $productResult['entity_id'];  
                            $priceQry = "SELECT * FROM `mglof_marketplace_product`WHERE product_id = ".$getProdId." and seller_id =".$seller_id;
                            $priceResult 	= $connection->rawFetchRow($priceQry);
                            $discPrice = $priceResult['pickup_from_store']; 
                            $checkPromo = $this->internalAddtoCart($quoteId,$getProdSku,$getProdQty,$getProdId,$seller_id,$discPrice,null,null,'BWGY');
                            array_push($promoFinalEntry , $checkPromo);
                          }
                        }
                      }  
                      if($operator == "<") {
                        if($org_total < $baseSubtotal) {
                          foreach($actionArr['get_product'] as $k => $v){ 
                            $getProdSku = $v['sku'];
                            $getProdQty = $v['qty'];
                            $productQry  = "SELECT * FROM `mgcatalog_product_entity` WHERE sku ='".$getProdSku."'" ;
                            $productResult 	= $connection->rawFetchRow($productQry);
                            $getProdId = $productResult['entity_id'];  
                            $priceQry = "SELECT * FROM `mglof_marketplace_product`WHERE product_id = ".$getProdId." and seller_id =".$seller_id;
                            $priceResult 	= $connection->rawFetchRow($priceQry);
                            $discPrice = $priceResult['pickup_from_store']; 
                            $checkPromo = $this->internalAddtoCart($quoteId,$getProdSku,$getProdQty,$getProdId,$seller_id,$discPrice,null,null,'BWGY');
                            array_push($promoFinalEntry , $checkPromo);
                          }
                        }
                      }
                      if($operator == "<=") {
                        if($org_total <= $baseSubtotal) {
                          foreach($actionArr['get_product'] as $k => $v){ 
                            $getProdSku = $v['sku'];
                            $getProdQty = $v['qty'];
                            $productQry  = "SELECT * FROM `mgcatalog_product_entity` WHERE sku ='".$getProdSku."'" ;
                            $productResult 	= $connection->rawFetchRow($productQry);
                            $getProdId = $productResult['entity_id'];  
                            $priceQry = "SELECT * FROM `mglof_marketplace_product`WHERE product_id = ".$getProdId." and seller_id =".$seller_id;
                            $priceResult 	= $connection->rawFetchRow($priceQry);
                            $discPrice = $priceResult['pickup_from_store']; 
                            $checkPromo = $this->internalAddtoCart($quoteId,$getProdSku,$getProdQty,$getProdId,$seller_id,$discPrice,null,null,'BWGY');
                            array_push($promoFinalEntry , $checkPromo);
                          }
                        }
                      }
                      if($operator == "==") {
                        if($org_total == $baseSubtotal) {
                          foreach($actionArr['get_product'] as $k => $v){ 
                            $getProdSku = $v['sku'];
                            $getProdQty = $v['qty'];
                            $productQry  = "SELECT * FROM `mgcatalog_product_entity` WHERE sku ='".$getProdSku."'" ;
                            $productResult 	= $connection->rawFetchRow($productQry);
                            $getProdId = $productResult['entity_id'];  
                            $priceQry = "SELECT * FROM `mglof_marketplace_product`WHERE product_id = ".$getProdId." and seller_id =".$seller_id;
                            $priceResult 	= $connection->rawFetchRow($priceQry);
                            $discPrice = $priceResult['pickup_from_store']; 
                            $checkPromo = $this->internalAddtoCart($quoteId,$getProdSku,$getProdQty,$getProdId,$seller_id,$discPrice,null,null,'BWGY');
                            array_push($promoFinalEntry , $checkPromo);
                          }
                        }
                      } 
                    $logger->info('BWGY End');

                  }
              }
          }
       }
       $logger->info("Order Level Promotions End");      
       foreach($promoFinalEntry as $key => $value){
          if(isset($value)){
            foreach($value as $k => $v){
              if($k == "discount"){
                array_push($promoFinalEntry['discount'],$v);
              }
              if($k == "item"){
                array_push($promoFinalEntry['item'],$v);
              }
            }
          }
        }
        $logger->info($promoFinalEntry);
        foreach($promoFinalEntry['discount'] as $k => $v){
          foreach($v as $a => $amt){
            $amount = json_decode($amt, true);
            $total_disc += $amount['amount'];
          }
        }
        $logger->info("Total Discount: ".$total_disc);
        if($total_disc  > 0){
          $this->_promoFactory->create()->setData(
            array(
            'item_qty' => json_encode($promoFinalEntry['item']),
            'cart_id' => $quoteId,
            'promo_code_id' => '',
            'promo_discount'=> json_encode($promoFinalEntry['discount']),
            'total_discount'=> $total_disc 
            )        
          )->save(); 
          $logger->info("Save");
        }
        $quote = $this->_mgQuote->loadActive(($quoteId));
        $subTotal = $quote->getBaseSubtotal();
        $newSubTotal = ($subTotal - $total_disc);
        $total_disc = '-'.$total_disc; 
       // $logger->info($subTotal."------".$total_disc."------".$newSubTotal);
       
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
          $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
          $connection = $this->_connection->getConnection();
         
          $sqlQuoteAdd = "Update mgquote_address Set subtotal=".$subTotal.", base_subtotal=".$subTotal.", subtotal_with_discount =".$newSubTotal.", base_subtotal_with_discount=".$newSubTotal.",  grand_total=".$newSubTotal.",  base_grand_total=".$newSubTotal.",	discount_amount=".$total_disc.", base_discount_amount =".$total_disc." where quote_id =".$quoteId ;
          $connection->query($sqlQuoteAdd);
  
          $sqlQuote = "Update mgquote Set subtotal=".$subTotal.", base_subtotal =".$subTotal.", subtotal_with_discount =".$newSubTotal.", base_subtotal_with_discount=".$newSubTotal.", grand_total=".$newSubTotal.", base_grand_total=".$newSubTotal." where entity_id = ".$quoteId ;
          $connection->query($sqlQuote);
          $logger->info("Queries Saved");
 

  }
  public function getQuoteQty($id){
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $this->_connection->getConnection();
    $quoteQry  = "SELECT * FROM `mgquote_item` WHERE item_id ='".$id."'" ;
    $Result 	= $connection->rawFetchRow($quoteQry);
    return $Result;
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

  public function checkPromoBxgoff($customPromoId, $sellerId, $sku, $quantity, $percent, $price){
      $promoEntry = array();
      $item = $discount = array();
      $rule_data = $this->_PostTableFactory->create()->getCollection()
      ->addFieldToFilter('p_id',$customPromoId);
      $promotionData = $rule_data->getData();
      $action_arr = json_decode($promotionData[0]['actions_serialized'] , true); 
      $actionSerSkus = $this->getActionSku($action_arr);
      if(in_array($sku, $actionSerSkus)){   //applypromo
        $ruleQty = $this->getActionQuantity($action_arr);
        $itemQty = $quantity ;
        $discountFactor =  floor($itemQty/$ruleQty); 
        $prDiscount =0;
        $type ='';
        if($percent == 1){              
              $prDiscount = ($discountFactor * $price * $ruleQty * $promotionData[0]['discount_amount'])/100 ;
              $type = 'BXGPOFF';
          }else{
              $prDiscount = ($discountFactor * $promotionData[0]['discount_amount']);          
              $type = 'BXGOFF';
          }
        if($prDiscount > 0){
          $discount['amount'] = $prDiscount;
          $discount['seller'] = $sellerId;
          $discount['type'] = $type;
          //$item['id'] = '';
          //$item['qty'] = '';
          $promoEntry['discount'] = [];
          //$promoEntry['item'] = [];

          array_push($promoEntry['discount'],json_encode($discount));
          //array_push($promoEntry['item'],json_encode($item));
        }
      }
      return $promoEntry;
  }
 
  public function checkPromoBnxaf($discountpromo,$sellerId){ 
    $promoEntry = array();
    $item = $discount = array();
    if($discountpromo > 0){
      $discount['amount'] = $discountpromo;
      $discount['seller'] = $sellerId;
      $discount['type'] = 'BNXAF';
      //$item['id'] = '';
      //$item['qty'] = '';
      $promoEntry['discount'] = [];
      //$promoEntry['item'] = [];
      array_push($promoEntry['discount'],json_encode($discount));
      //array_push($promoEntry['item'],json_encode($item));
     
      return $promoEntry;
    }

  }
  public function checkPromoBnxgo($discountpromo,$sellerId){ 
    $promoEntry = array();
    $item = $discount = array();
    if($discountpromo > 0){
      $discount['amount'] = $discountpromo;
      $discount['seller'] = $sellerId;
      $discount['type'] = 'BNXG1O';
      //$item['id'] = '';
      //$item['qty'] = '';
      $promoEntry['discount'] = [];
      //$promoEntry['item'] = [];
      array_push($promoEntry['discount'],json_encode($discount));
      //array_push($promoEntry['item'],json_encode($item));
     
      return $promoEntry;
    }

  }

  public function internalAddtoCart($cart_id,$sku_to_add,$sku_qty,$product_id,$seller_id,$discountpromo,$proditemId,$quoteQty,$promoType)
    {
      $base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
      $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
      $logger = new \Zend\Log\Logger();
      $logger->addWriter($writer);
     // $logger->info('in internal add to cart');
      //$logger->info($cart_id."------".$sku_to_add."------".$sku_qty."------".$product_id."------".$seller_id."------".$discountpromo."----SERVER---".$_SERVER['REMOTE_ADDR']."---".$_SERVER['SERVER_ADDR']);

      if($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR']){
       // $logger->info('inside if    '.$_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
          $post_req= [
            'cart_item' => [
              'quote_id' => $cart_id,
              'sku' => $sku_to_add,
              'qty' => $sku_qty,
            ],
            'product_id' => $product_id,
            'seller_id' => $seller_id,
            'price_type' => 1
          ];
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
          $baseUrl = $storeManager->getStore()->getBaseUrl();
          $userData = array("username" => "adminapi", "password" => "Admin@123");
          $ch = curl_init($baseUrl."rest/V1/integration/admin/token");
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
          $token = curl_exec($ch);
          $ch = curl_init($baseUrl."rest/V1/carts/mine/items");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_req));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

        $promoEntry = array();
        $item = $discount =  $parentId =  array();
        if($discountpromo > 0){
          $addToCart = curl_exec($ch);
         // $logger->info($addToCart);
          curl_close($ch);
          $addedData = json_decode($addToCart,true);
          $discount['amount'] = $discountpromo;
          $discount['seller'] = $seller_id;
          $discount['type'] = $promoType;
          if($promoType== 'BXGY' || $promoType == 'BWGY'){
           $item_id = $addedData['item_id'];
           $parentId = $proditemId;
          }else{
            $item_id = $proditemId;  //bxgx
            $parentId = $item_id;
          }
          $item['id'] = $item_id;
          $item['qty'] = $sku_qty;
          $item['type'] = $promoType;
          $item['parent'] = $parentId;
          $promoEntry['discount'] = [];
          $promoEntry['item'] = [];
          array_push($promoEntry['discount'],json_encode($discount));
          array_push($promoEntry['item'],json_encode($item));
          return $promoEntry;
        }
      }
    }
  public function removeItem($quoteId, $itemId){
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
      $baseUrl = $storeManager->getStore()->getBaseUrl();
      $token = $this->adminToken();
      $ch = curl_init($baseUrl."rest/V1/carts/".$quoteId."/items/".$itemId);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
      $result = curl_exec($ch);
      $result = json_decode($result, 1);
      //print_r($result);exit;
  }

  public function updateItem($quoteId,$itemId,$qty,$product_id,$seller_id){
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    $baseUrl = $storeManager->getStore()->getBaseUrl();
    $token = $this->adminToken();
    $post_req= [
      'cartItem' => [
        'item_id' => $itemId,
        'qty' => $qty,
        'quote_id' => $quoteId
      ],
      'product_id' => $product_id,
      'seller_id' => $seller_id,
      'price_type' => 1
    ];
    $ch = curl_init($baseUrl."rest/V1/carts/".$quoteId."/items/".$itemId);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_req));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
    $result = curl_exec($ch);
    $result = json_decode($result, 1);
    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);
    return $result;
  }
  
  public function adminToken(){
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    $baseUrl = $storeManager->getStore()->getBaseUrl();
    $userData = array("username" => "adminapi", "password" => "Admin@123");
    $ch = curl_init($baseUrl."rest/V1/integration/admin/token");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
    $token = curl_exec($ch);
    return $token;
  }

  public function  checkPromoBuyWorth($customPromoId, $sellerId, $percent, $discountAmount,$sellerAmount) {
    // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jannath.log'); 
    // $logger = new \Zend\Log\Logger();
    // $logger->addWriter($writer);

    $promoEntry = array();
    $discount = array();
    $rule_data = $this->_PostTableFactory->create()->getCollection()
    ->addFieldToFilter('p_id',$customPromoId);
    $promotionData = $rule_data->getData();
    $conditions_arr = json_decode($promotionData[0]['conditions_serialized'] , true); 
    //$action_arr = json_decode($promotionData[0]['action_serialized'] , true); 

    // $logger->info($rule_data->getData());

    $getConditionsParameters = $this->getConditionsParameters($conditions_arr);
 
    foreach ($getConditionsParameters as $operator => $value) {
      $type ='';
        if($operator == ">") {
            if($sellerAmount > $value){
                if($discountAmount > 0) {
                    if($percent == 1){
                        $type = 'BWGOP';       
                        $appliedDiscount = $sellerAmount*$discountAmount/100;
                    } else {
                        $type = 'BWGO';
                        $appliedDiscount = $discountAmount;         
                    }
                    $discount['amount'] = $appliedDiscount;
                    $discount['seller'] = $sellerId;
                    $discount['type'] = $type;
                    $promoEntry['discount'] = [];
 
                    array_push($promoEntry['discount'],json_encode($discount));
                }
            }
        }
        if($operator == "==") {
          if($sellerAmount == $value){
              if($discountAmount > 0) {
                  if($percent == 1){ 
                      $type = 'BWGOP';            
                      $appliedDiscount = $sellerAmount*$discountAmount/100;
                  } else {
                      $type = 'BWGO'; 
                      $appliedDiscount = $discountAmount;         
                  }
                  $discount['amount'] = $appliedDiscount;
                  $discount['seller'] = $sellerId;
                  $discount['type'] = $type;
                  $promoEntry['discount'] = [];

                  array_push($promoEntry['discount'],json_encode($discount));
              }
          }
        }
        if($operator == "<") {
          if($sellerAmount < $value){
              if($discountAmount > 0) {
                  if($percent == 1){    
                      $type = 'BWGOP';           
                      $appliedDiscount = $sellerAmount*$discountAmount/100;
                  } else {
                      $type = 'BWGO'; 
                      $appliedDiscount = $discountAmount;         
                  }
                  $discount['amount'] = $appliedDiscount;
                  $discount['seller'] = $sellerId;
                  $discount['type'] = $type;
                  $promoEntry['discount'] = [];

                  array_push($promoEntry['discount'],json_encode($discount));
              }
          }
        }
        if($operator == ">=") {
          if($sellerAmount >= $value){
              if($discountAmount > 0) {
                  if($percent == 1){  
                      $type = 'BWGOP';             
                      $appliedDiscount = $sellerAmount*$discountAmount/100;
                  } else {
                      $type = 'BWGO'; 
                      $appliedDiscount = $discountAmount;         
                  }
                  $discount['amount'] = $appliedDiscount;
                  $discount['seller'] = $sellerId;
                  $discount['type'] = $type;
                  $promoEntry['discount'] = [];

                  array_push($promoEntry['discount'],json_encode($discount));
              }
          }
        }
        if($operator == "<=") {
          if($sellerAmount <= $value){
              if($discountAmount > 0) {
                  if($percent == 1){    
                    $type = 'BWGOP';           
                      $appliedDiscount = $sellerAmount*$discountAmount/100;
                  } else {
                    $type = 'BWGO'; 
                      $appliedDiscount = $discountAmount;         
                  }
                  $discount['amount'] = $appliedDiscount;
                  $discount['seller'] = $sellerId;
                  $discount['type'] = $type;
                  $promoEntry['discount'] = [];

                  array_push($promoEntry['discount'],json_encode($discount));
              }
          }
       }    
    }
    return $promoEntry;
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
