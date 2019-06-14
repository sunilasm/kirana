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
  protected $_cartTotal;

  public function __construct(
   \Magento\Catalog\Model\ProductRepository $productRepository,
   \Magento\Checkout\Model\Cart $cart,
   PostTableFactory $PostTableFactory ,
   PromoTableFactory $promoFactory,
   \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
   \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
   \Magento\Quote\Model\Quote\Address\Total $total,
   Magento\Quote\Model\Cart $cartTotal) 
  {
      $this->_productRepository = $productRepository;
      $this->_cart = $cart;
      $this->_PostTableFactory = $PostTableFactory;
      $this->_promoFactory = $promoFactory;
      $this->quoteRepository = $quoteRepository;
      $this->_quoteAddressFactory = $quoteAddressFactory;
      $this->_total = $total;
      $this->_cartTotal = $cartTotal;
  }
  public function execute(\Magento\Framework\Event\Observer $observer)
  {  
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
              $deletePrev = $this->_promoFactory->create();
              $deletePrev->load($val['ap_id']);
              $deletePrev->delete();
          }
        }
      

        // $delData->load($quoteId);
        // $delData->delete();

        $quote = $this->quoteRepository->getActive($quoteId);
        

        //$quoteAdd = $this->_quoteAddressFactory->create()->getCollection()
       // ->addFieldToFilter('quote_id',$quoteId); 
        $grandTotal = $this->_cartTotal->setDiscountAmount(100);
        
        
         $logger->info($this->_cartTotal->getDiscountAmount());         
        $logger->info('>>--------<<');        
          // $quoteAdd->setDiscountAmount(100);
          // $quoteAdd->setGrandTotal(3000);	
          // $quoteAdd->save();
     
         

        


        $quoteItems = $quote->getItems();
        $mappedRulesArray = $this->getCustomTableRules();
        $promoFinalEntry = [];
        $promoFinalEntry['discount'] = array();
        $promoFinalEntry['item'] = array();
        $checkPromo = array();
        $total_discount = 0;
        foreach($quoteItems as $key => $value) {
          $quoteItems[0]->setDiscountAmount(100);
          $quoteItems[0]->save();
          $sellerId = $quoteItems[$key]->getSellerId();
          $sku = $quoteItems[$key]->getSku();
          $quantity = $quoteItems[$key]->getQty();
          if(isset($mappedRulesArray[$sellerId])){ 
            foreach($mappedRulesArray[$sellerId] as $k => $promo) {
              $description = json_decode($promo['description'],true);
              $ruleCode = $description['code'];
              if($ruleCode == "BXGOFF"){  
                $checkPromo = $this->checkPromoBxgoff($promo['p_id'], $sellerId, $sku, $quantity, 0, $quoteItems[$key]->getPrice());
                array_push($promoFinalEntry , $checkPromo);
              }
              if($ruleCode == "BXGPOFF"){
                $checkPromo = $this->checkPromoBxgoff($promo['p_id'], $sellerId, $sku, $quantity, 1, $quoteItems[$key]->getPrice());
                array_push($promoFinalEntry , $checkPromo);
              }
              if($ruleCode == "BNXAF"){
               // $checkPromo = $this->checkPromoBnxaf($promo['p_id'], $sellerId, $sku, $quantity);
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
        $this->_promoFactory->create()->setData(
          array(
          'item_qty' => json_encode($promoFinalEntry['item']),
          'cart_id' => $quoteId,
          'promo_code_id' => '',
          'promo_discount'=> json_encode($promoFinalEntry['discount']),
          'total_discount'=> 1000   
          )        
        )->save();
        // foreach($promoFinalEntry['discount'] as $k => $v){
        //   foreach($v as $a => $amt){
        // //$logger->info(json_decode($amt, true));         
        //   }
        // }      
        $logger->info('>>>>>>>>>>><<<<<<<<<<<<');         
        $logger->info(json_encode($promoFinalEntry['discount']));
        $logger->info(json_encode($promoFinalEntry['item']));
        $logger->info('>>>>>>>>>>><<<<<<<<<<<<');         
        //$logger->info($promoFinalEntry['discount']);
     

     
    
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
              $totalPrice = 0;
              $percentDisc = $promotionData[0]['discount_amount']; 
              $totalPrice = $discountFactor * $price;
              $prDiscount = ($percentDisc * $totalPrice)/100 ;
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
 
  public function checkPromoBnxaf($customPromoId, $sellerId, $sku, $quantity){
      $rule_data = $this->_PostTableFactory->create()->getCollection()
      ->addFieldToFilter('p_id',$customPromoId);
      $promotionData = $rule_data->getData();
      // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/appromo.log'); 
      // $logger = new \Zend\Log\Logger();
      // $logger->addWriter($writer);
      // $logger->info($promotionData);         

  }
   
}
