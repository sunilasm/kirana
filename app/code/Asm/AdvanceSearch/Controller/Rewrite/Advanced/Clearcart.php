<?php
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
class Clearcart extends Action
{
	protected  $_modelCart;
	protected $checkoutSession;
	public function __construct(CheckoutSession $checkoutSession,Cart $modelCart)
        {
        	$this->checkoutSession = $checkoutSession;
        	$this->_modelCart = $modelCart;
        }
		public function execute()
        {
			$cart = $this->_modelCart;
			$quoteItems = $this->checkoutSession->getQuote()->getItemsCollection();
			foreach($quoteItems as $item)
			{
				$cart->removeItem($item->getId())->save(); 
			}
		}
}