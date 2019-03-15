<?php
namespace Asm\Setsellerid\Observer;
set_time_limit(0);
ini_set('memory_limit', '1G');
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Quote\Api\Data\CartItemExtensionFactory;

class Setorderproductseller implements \Magento\Framework\Event\ObserverInterface
{
	protected $_request;

	
        /**
         *@var \Magento\Store\Model\App\Emulation
         */
        protected $appEmulation;

        /**
         * @var CartItemExtensionFactory
         */
       
        private $quoteItemFactory;

        public function __construct(
            \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
            AppEmulation $appEmulation
        ) {
            
            $this->appEmulation = $appEmulation;
            $this->quoteItemFactory = $quoteItemFactory;
        }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();

        $quoteItems = [];
        // Map Quote Item with Quote Item Id
        foreach($order->getAllItems() as $orderItems){
            
            $quoteItem = $this->quoteItemFactory->create();
            $quoteItemdtls = $quoteItem->load($orderItems->getQuoteItemId());
            $orderItems->setPriceType($quoteItemdtls->getPriceType());  
            $orderItems->setSellerId($quoteItemdtls->getSellerId()); 
            $orderItems->save();
        }
        $orderItems->save();
        $order->save();
       
        return;
    }
}
