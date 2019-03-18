<?php

namespace Asm\Setsellerid\Plugin;

class Item
{

    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Hexcrypto\WishlistAPI\Helper\Data $wishlistHelper
     */
            private $quoteItemFactory;

    public function __construct(
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalItemExtensionFactory,
         \Magento\Quote\Api\Data\TotalsExtensionFactory $totalExtensionFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
    
    ) {
        $this->itemFactory = $itemFactory;
        $this->totalItemExtension = $totalItemExtensionFactory;
         $this->totalExtension = $totalExtensionFactory;
        $this->quoteItemFactory = $quoteItemFactory;


    }

    /**
     * add sku in total cart items

     * @param  \Magento\Quote\Api\CartTotalRepositoryInterface $subject
     * @param  \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return \Magento\Quote\Api\Data\TotalsInterface $totals

     */
    public function afterGet(
        \Magento\Quote\Api\CartTotalRepositoryInterface $subject,
        \Magento\Quote\Api\Data\TotalsInterface $totals
    ) {
        $grandTotal=0;

        foreach($totals->getItems() as $item)
        {

            $quoteItem = $this->itemFactory->create()->load($item->getItemId());
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/itemsss.log'); 
            $logger = new \Zend\Log\Logger(); 
            $logger->addWriter($writer);
            $item->getQty(); 
            
            $quoteItem->getPriceType();
            $quoteItem->getSellerId();
             $logger->info($quoteItem->getPriceType());
            $logger->info($item->getQty());
            $logger->info($quoteItem->getSellerId());
             $logger->info($quoteItem->getPrice());

             $rowTotal = $item->getQty() * $quoteItem->getPrice();

            $extensionAttributes = $item->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->totalItemExtension->create();
            }

            $extensionAttributes->setExtnRowTotal($rowTotal);
            $item->setExtensionAttributes($extensionAttributes);
            $grandTotal += $rowTotal;

        }



          $extensionAttributes = $totals->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->totalExtension->create();
            }
            $data = 20;
            $extensionAttributes->setExtnGrandTotal($grandTotal);
            $totals->setExtensionAttributes($extensionAttributes);

        return $totals;
    }

}