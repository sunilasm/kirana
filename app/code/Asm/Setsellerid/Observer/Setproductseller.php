<?php
namespace Asm\Setsellerid\Observer;
class Setproductseller implements \Magento\Framework\Event\ObserverInterface
{
	protected $_request;
	public function __construct(
	    \Magento\Framework\App\RequestInterface $request,
	    \Psr\Log\LoggerInterface $logger,
	    \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
	    \Magento\Sales\Model\OrderFactory $orderFactory
	) { 
	    $this->_request = $request;
	    $this->_logger = $logger;
	    $this->_historyFactory = $historyFactory;
	    $this->_orderFactory = $orderFactory;
	}

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
	
	/*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$this->_request = $objectManager->get('\Magento\Framework\App\RequestInterface');*/
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        $seller_id = array();
        $variable = $request->getPost();
         $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Setproductsellerrrrrrrrrrr Setproductseller");
	$seller_id = array();
	$variable = $this->_request->getPost();
 	foreach ($variable as $key => $value) {
 		 $logger->info($value);
 		if($key=="seller_id"){
 			//print_r($value);
 			$seller_id["value"] = $value;
 		}
 		if($key=="product"){
 			$seller_id["product"] = $value;
 		}
 	}
 	$cart = $observer->getEvent()->getData('cart');
    $cartItems = $cart->getItems();
    foreach($cartItems as $item){
    	if(isset($seller_id["value"])){
	    	if($item->getProductId() == $seller_id["product"]){
	    		$item->setSellerId($seller_id["value"]);
	    	}
    	}
   	}
    return $this;
  }
}
