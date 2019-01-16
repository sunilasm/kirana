<?php
namespace Asm\Setsellerid\Observer;

class Setproductseller implements \Magento\Framework\Event\ObserverInterface
{
	protected $_request;

	/*public function __construct(
	    \Magento\Framework\App\RequestInterface $request,
	    \Psr\Log\LoggerInterface $logger,
	    \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
	    \Magento\Sales\Model\OrderFactory $orderFactory
	) { 
	    $this->_request = $request;
	    $this->_logger = $logger;
	    $this->_historyFactory = $historyFactory;
	    $this->_orderFactory = $orderFactory;
	}*/

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
  	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$request = $objectManager->get('\Magento\Framework\App\RequestInterface');
 	$seller_id = array();
 	$variable = $request->getPost();
 	foreach ($variable as $key => $value) {
 		if($key=="seller_id"){
 			//print_r($value);
 			$seller_id["value"] = $value;
 		}
 		if($key=="product"){
 			$seller_id["product"] = $value;
 		}
 	}
 	//print_r($seller_id);exit;
 	$cart = $observer->getEvent()->getData('cart');
    $cartItems = $cart->getItems();
    foreach($cartItems as $item){

    	if(isset($seller_id["value"])){
	    	if($item->getProductId() == $seller_id["product"]){
	    		//print_r($seller_id["value"]);
	    		$item->setSellerId($seller_id["value"]);
	    		//print_r($item->getProductId());
	    		//print_r($seller_id["value"]);exit;
	    	}
    	}
   	}
   	/*if($seller_id){
   		$quoteItem = $observer->getQuoteItem();
    	$quoteItem->setSellerId($seller_id["value"]);
   	}*/
	
    return $this;
  }
}