<?php
namespace Asm\Cart\Model;
use Asm\Cart\Api\CartInterface;

class Cart implements CartInterface{

	public function quotetotal() {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $RequestData = $request->getRequestData();
       
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteModel = $objectManager->create('Magento\Quote\Model\Quote')->getCollection();
        $quoteItems = $quoteModel->getAllVisibleItems();
		
		//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		//$quote = $objectManager->create('Magento\Checkout\Model\Cart')->getCollection()->addFieldToFilter('customer_id',$customerId);
		//$quoteItems = $quote->getQuote()->getAllItems();
		$cartData = array();
		if(count($quoteItems)){
			foreach($quoteItems as $cartRes){
				/*$data = array(
					"quote_id"   => $cartRes->getQuoteId(),
				);*/
				$cartData[] = $cartRes;
			}
			return $cartData;
		}else{
			return $cartData;
		}
	}
}