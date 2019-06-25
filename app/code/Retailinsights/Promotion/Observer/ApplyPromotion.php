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
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
       // $logger->info('ApplyPromo Observer');
        //Deleting promotion data in custom table 
        $base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
       
        $delData = $this->_promoFactory->create()->getCollection()
        ->addFieldToFilter('cart_id', $quoteId);
        foreach($delData->getData() as $k => $val){           
          if($val['cart_id']==$quoteId){
              $itemInfo = json_decode($val['item_qty'],true);
              $itemsToDel = json_decode($itemInfo[0][0],true);
              if(isset($itemsToDel)){
                foreach($itemsToDel as $k => $v){
                  //$logger->info($v.'==++++=='.$k);
                   if($k == 'id'){
                    $deleteItem = $this->removeItem($quoteId, $v);
                   }
                }
              }
             
            // $quoteAddressQuery  = "SELECT * FROM `mgquote_address` WHERE quote_id =".$quoteId ;
            // $quoteAddressResult 	= $connection->rawFetchRow($quoteAddressQuery);
            // $logger->info($quoteAddressResult);          
            
            // $previousDiscount = '-'.$val['total_discount'];
            // $subTotal = $quoteAddressResult['subtotal_with_discount'];
            // $newSubTotal = ($subTotal - $previousDiscount);
            
            // $sqlQuoteAdd = "Update mgquote_address Set subtotal=".$newSubTotal.", base_subtotal=".$newSubTotal.", subtotal_with_discount =".$subTotal.", base_subtotal_with_discount=".$subTotal.",  grand_total=".$newSubTotal.",  base_grand_total=".$newSubTotal.",	discount_amount=".$previousDiscount.", base_discount_amount =".$previousDiscount." where quote_id =".$quoteId ;
            // //$connection->query($sqlQuoteAdd);
    
            // $sqlQuote = "Update mgquote Set subtotal=".$newSubTotal.", base_subtotal =".$newSubTotal.", subtotal_with_discount =".$subTotal.", base_subtotal_with_discount=".$subTotal.", grand_total=".$newSubTotal.", base_grand_total=".$newSubTotal." where entity_id = ".$quoteId ;
            // //$connection->query($sqlQuote);
            // $logger->info($sqlQuoteAdd.$sqlQuote);
            $deletePrev = $this->_promoFactory->create();
            $deletePrev->load($val['ap_id']);
            $deletePrev->delete();
          }
        }

        $quote = $this->quoteRepository->getActive($quoteId);
        $quoteItems = $quote->getItems();
        $mappedRulesArray = $this->getCustomTableRules();
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
                $checkPromo = $this->checkPromoBxgoff($promo['p_id'], $sellerId, $sku, $quantity, 0, $quoteItems[$key]->getPrice());
                array_push($promoFinalEntry , $checkPromo);
              }
              if($ruleCode == "BXGPOFF"){
                $checkPromo = $this->checkPromoBxgoff($promo['p_id'], $sellerId, $sku, $quantity, 1, $quoteItems[$key]->getPrice());
                array_push($promoFinalEntry , $checkPromo);
              }
              if($ruleCode == "BNXAF"){
                  $actionArr = json_decode($promo['actions_serialized'], true);
                  $ruleSku = array();
                  foreach($actionArr['buy_product'] as $k => $v){
                    $ruleSku[$v['sku']] = $v['qty'];
                  }
                  $ruleSkuLen = sizeof($ruleSku);
                  foreach($ruleSku as $rule_sku =>$sku_qty){
                    if(($rule_sku == $sku) && ($sku_qty <= $quantity)){
                      if(isset($bnxafCount[$ruleId])) {
                        $bnxafCount[$ruleId]++;
                      } else {
                        $bnxafCount[$ruleId] = 1;
                      }
                      $itemPriceTotal += $quoteItems[$key]->getPrice();
                    }
                  }
                  if(isset($bnxafCount[$ruleId])){
                    if($ruleSkuLen == $bnxafCount[$ruleId]){ 
                      $sku = $ruleSku;
                      $fixedPrice = $promo['discount_amount'];
                      $discountBnxaf = ($itemPriceTotal - $fixedPrice);
                      $checkPromo = $this->checkPromoBnxaf($discountBnxaf,$sellerId);
                      array_push($promoFinalEntry , $checkPromo);
                      $bnxafCount[$ruleId] = 0;
                    }
                  }
                 
                 
              }
              if($ruleCode == "BNXG1O"){ 
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
                  }
                }

              }
              if($ruleCode == "BXGX"){ 
               // $logger->info('in BXGX');
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
                  $checkPromo  = $this->internalAddtoCart($quoteId,$sku,$prodQty,$quoteItems[$key]->getProductId(),$sellerId,$discPrice);
                  array_push($promoFinalEntry , $checkPromo);
                }
               
                
              }
              if($ruleCode == "BXGY"){
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
                 //  $logger->info($quantity.'--__--__--'.$v['qty']);
                   if($quantity == $v['qty']){
                    $qtyFactor = 1;
                   }else{
                    $qtyFactor = floor($quantity/$v['qty']);
                   }
                    $getProdQty  = ($getProdQty*$qtyFactor);
                    $discPrice = ($discPrice*$getProdQty); 
                    $checkPromo = $this->internalAddtoCart($quoteId,$getProdSku,$getProdQty,$getProdId,$sellerId,$discPrice);
                    array_push($promoFinalEntry , $checkPromo);
                 }
                }
              }
            }
            
          }
        }
           

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
        foreach($promoFinalEntry['discount'] as $k => $v){
          foreach($v as $a => $amt){
            $amount = json_decode($amt, true);
            $total_disc += $amount['amount'];
          }
        }  
       
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
        if($percent == 1){              
              $prDiscount = ($discountFactor * $price * $ruleQty * $promotionData[0]['discount_amount'])/100 ;
          }else{
              $prDiscount = ($discountFactor * $promotionData[0]['discount_amount']);          
          }
        if($prDiscount > 0){
          $discount['amount'] = $prDiscount;
          $discount['seller'] = $sellerId;
          $item['id'] = '';
          $item['qty'] = '';
          $promoEntry['discount'] = [];
          $promoEntry['item'] = [];

          array_push($promoEntry['discount'],json_encode($discount));
          array_push($promoEntry['item'],json_encode($item));
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
      $item['id'] = '';
      $item['qty'] = '';
      $promoEntry['discount'] = [];
      $promoEntry['item'] = [];
      array_push($promoEntry['discount'],json_encode($discount));
      array_push($promoEntry['item'],json_encode($item));
     
      return $promoEntry;
    }

  }
  public function checkPromoBnxgo($discountpromo,$sellerId){ 
    $promoEntry = array();
    $item = $discount = array();
    if($discountpromo > 0){
      $discount['amount'] = $discountpromo;
      $discount['seller'] = $sellerId;
      $item['id'] = '';
      $item['qty'] = '';
      $promoEntry['discount'] = [];
      $promoEntry['item'] = [];
      array_push($promoEntry['discount'],json_encode($discount));
      array_push($promoEntry['item'],json_encode($item));
     
      return $promoEntry;
    }

  }

  public function internalAddtoCart($cart_id,$sku_to_add,$sku_qty,$product_id,$seller_id,$discountpromo)
    {
      $base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
      $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
      $logger = new \Zend\Log\Logger();
      $logger->addWriter($writer);
    //  $logger->info('in internal add to cart');
    //  $logger->info($cart_id."------".$sku_to_add."------".$sku_qty."------".$product_id."------".$seller_id."------".$discountpromo."----SERVER---".$_SERVER['REMOTE_ADDR']."---".$_SERVER['SERVER_ADDR']);

      if($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR']){
        //$logger->info('inside if    '.$_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
          $post_req= [
            'cart_item' => [
              'quote_id' => $cart_id,
              'sku' => $sku_to_add,
              'qty' => $sku_qty
            ],
            'product_id' => $product_id,
            'seller_id' => $seller_id,
            'price_type' => 1
          ];
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
          $baseUrl = $storeManager->getStore()->getBaseUrl();
          $userData = array("username" => "sunil.n", "password" => "admin1234");
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

          // $result = curl_exec($ch);
         //-------------------------------------
        //  $curl = curl_init();
        //  curl_setopt_array($curl, array(
        //    CURLOPT_URL => "http://hylosh.theretailinsights.co/rest/V1/carts/mine/items",
        //    CURLOPT_RETURNTRANSFER => true,
        //    CURLOPT_ENCODING => "",
        //    CURLOPT_MAXREDIRS => 10,
        //    CURLOPT_TIMEOUT => 30,
        //    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //    CURLOPT_CUSTOMREQUEST => "POST",
        //    CURLOPT_POSTFIELDS => json_encode($post_req),
        //    CURLOPT_HTTPHEADER => array(
        //      "Accept: */*",
        //      "Authorization:".$_SERVER['REDIRECT_HTTP_AUTHORIZATION'],
        //      "Cache-Control: no-cache",
        //      "Connection: keep-alive",
        //      "Content-Type: application/json",
        //      "Host: hylosh.theretailinsights.co",
        //      "Postman-Token: 46fc30cc-b975-4424-a3ac-ff3fb4ae5ac3,7273b2b7-3cdb-4cce-ae21-e48e77239e5f",
        //      "User-Agent: PostmanRuntime/7.15.0",
        //      "accept-encoding: gzip, deflate",
        //      "cache-control: no-cache",
        //      "cookie: PHPSESSID=c45c6f3986590010a388a8f97d4688d4"
        //    ),
        //  ));
         
        $promoEntry = array();
        $item = $discount = array();
        if($discountpromo > 0){
          $addToCart = curl_exec($ch);
         // $logger->info($addToCart);
          curl_close($ch);

          $addedData = json_decode($addToCart,true);
          $discount['amount'] = $discountpromo;
          $discount['seller'] = $seller_id;
          $item['id'] = $addedData['item_id'];
          $item['qty'] = $sku_qty;
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
      $userData = array("username" => "sunil.n", "password" => "admin1234");
      $ch = curl_init($baseUrl."rest/V1/integration/admin/token");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

      $token = curl_exec($ch);
      $ch = curl_init($baseUrl."rest/V1/carts/".$quoteId."/items/".$itemId);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
      $result = curl_exec($ch);
      $result = json_decode($result, 1);
      //print_r($result);exit;
  }
}

