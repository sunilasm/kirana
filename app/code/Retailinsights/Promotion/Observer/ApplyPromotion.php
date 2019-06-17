<?php
namespace Retailinsights\Promotion\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Retailinsights\Promotion\Model\PostTableFactory;
use Retailinsights\Promotion\Model\PromoTableFactory;

class ApplyPromotion implements ObserverInterface
{
  protected $_productRepository;
  protected $_cart;
  protected $quoteRepository;
  protected $_promoFactory;
  protected $_quoteAddressFactory;
  protected $_connection;

  public function __construct(
   \Magento\Catalog\Model\ProductRepository $productRepository,
   \Magento\Checkout\Model\Cart $cart,
   PostTableFactory $PostTableFactory ,
   PromoTableFactory $promoFactory,
   \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
   \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
   \Magento\Framework\App\ResourceConnection $_connection
   )
  {
      $this->_productRepository = $productRepository;
      $this->_cart = $cart;
      $this->_PostTableFactory = $PostTableFactory;
      $this->_promoFactory = $promoFactory;
      $this->quoteRepository = $quoteRepository;
      $this->_quoteAddressFactory = $quoteAddressFactory;
      $this->_connection = $_connection;
  }
  public function execute(\Magento\Framework\Event\Observer $observer)
  {  
    $connection = $this->_connection->getConnection();
    $quoteId = $observer->getData('quoteid');
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/appromo.log'); 
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('ApplyPromo Observer');
        //Deleting promotion data in custom table 
       
        $delData = $this->_promoFactory->create()->getCollection()
        ->addFieldToFilter('cart_id', $quoteId);
        foreach($delData->getData() as $k => $val){           
          if($val['cart_id']==$quoteId){
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
        $bnxafCount = 0;
        $bnxgoCount = 0;
        $itemPriceTotal = 0;
        foreach($quoteItems as $key => $value) {
          $sellerId = $quoteItems[$key]->getSellerId();
          $sku = $quoteItems[$key]->getSku();
          $quantity = $quoteItems[$key]->getQty();
          if(isset($mappedRulesArray[$sellerId])){ 
            foreach($mappedRulesArray[$sellerId] as $k => $promo) {
              $description = json_decode($promo['description'],true);
              $ruleCode = $description['code'];
              $logger->info($ruleCode);
              if($ruleCode == "BXGOFF"){  
                $logger->info('in bxgoff'); 

                $checkPromo = $this->checkPromoBxgoff($promo['p_id'], $sellerId, $sku, $quantity, 0, $quoteItems[$key]->getPrice());
                array_push($promoFinalEntry , $checkPromo);
              }
              if($ruleCode == "BXGPOFF"){
                $logger->info('in bxgoff'); 

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
                  $logger->info($ruleSku);
                  foreach($ruleSku as $rule_sku =>$sku_qty){
                    if(($rule_sku == $sku) && ($sku_qty <= $quantity)){
                      $bnxafCount ++;
                      $itemPriceTotal = $quoteItems[$key]->getPrice();
                    }
                  }
                  $logger->info('skucount'.$bnxafCount.'      skulength'.$ruleSkuLen); 

                  if($ruleSkuLen == $bnxafCount){ 
                    $sku = $ruleSku;
                    $fixedPrice = $promo['discount_amount'];
                    $discountBnxaf = ($itemPriceTotal - $fixedPrice);
                    $checkPromo = $this->checkPromoBnxafBnxgo($discountBnxaf,$sellerId);
                    array_push($promoFinalEntry , $checkPromo);
                  }
                  // $logger->info('efsf');
                  // $logger->info($promoFinalEntry);
              }
              if($ruleCode == "BNXG1O"){ 
                //$logger->info('in BNXG1O');
                $actionArr = json_decode($promo['actions_serialized'], true);
                $ruleSku = array();
                
                foreach($actionArr['buy_product'] as $k => $v){
                  $ruleSku[$v['sku']] = $v['qty'];
                }
                $ruleSkuLen = sizeof($ruleSku);
                foreach($ruleSku as $rule_sku =>$sku_qty){
                  if(($rule_sku == $sku) && ($sku_qty <= $quantity)){
                    $bnxgoCount ++;
                  }
                }
                $discount_bnxgo = 0;
                if($ruleSkuLen == $bnxgoCount){
                  foreach($actionArr['discount_product'] as $k=>$v){
                    if($v['sku'] == $sku){
                      $discount_bnxgo = ($quoteItems[$key]->getPrice()*$v['discount_product'])/100;
                    }
                    // $logger->info($k);
                    // $logger->info($v);
                  } 
                  $checkPromo = $this->checkPromoBnxafBnxgo($discount_bnxgo,$sellerId);
                  array_push($promoFinalEntry , $checkPromo);
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
        // $logger->info('>>>>>>>>>>>>>>=======<<<<<<<<<<<<<<');
        // $logger->info($checkPromo);
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
        $subTotal = $quote->getBaseSubtotal();
        $newSubTotal = ($subTotal - $total_disc);
        $total_disc = '-'.$total_disc;
  
          // $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
          // $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
          //$connection = $this->_connection->getConnection();
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
 
  public function checkPromoBnxafBnxgo($discountpromo,$sellerId){ 
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
   
}
