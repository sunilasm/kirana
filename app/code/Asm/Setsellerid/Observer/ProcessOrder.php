<?php
namespace Asm\Setsellerid\Observer;
/**
 * Create an order in Companyname when order status match the statuses in the backend
 *
 * @param EventObserver $observer
 * @return void
 */
public function execute(EventObserver $observer){
  
    if($request->getBodyParams())
        {
            $post = $request->getBodyParams();
          
            if(isset($post['product_id'])){
                $seller_id["product_id"] = $post['product_id'];
                $seller_id["seller_id"] = $post['seller_id'];
                $seller_id["price_type"] = $post['price_type'];
            }
        } 

         $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/orderPlaceafter.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer); 
                $logger->info($seller_id["seller_id"]);      
    $quoteItem = $observer->getEvent()->getItem();
    $orderItem = $observer->getOrderItem();
    if ($quoteItem->getSellerId()) {
    $orderItem->setSellerId($quoteItem->getSellerId());
    }
    return $this;
}

?>