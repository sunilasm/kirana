<?php
namespace Asm\Cart\Model;
use Asm\Cart\Api\CartInterface;

class Cart implements CartInterface{

	public function quotetotal($customerId) {
		if(empty($customerId) || !isset($customerId) || $customerId == ""){
			throw new InputException(__('Id required'));
		}else{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$quote = $objectManager->create('Magento\Checkout\Model\Cart')->getCollection()->addFieldToFilter('customer_id',$customerId);
			
			$cartItems = $quote->getQuote()->getAllItems();
			
			$cartData = array();
			if(count($cartItems)){
				foreach($cartItems as $cartRes){
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
}