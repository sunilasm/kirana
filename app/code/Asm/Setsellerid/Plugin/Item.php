<?php

namespace Asm\Setsellerid\Plugin;
use Lof\MarketPlace\Model\SellerProductFactory as SellerProduct;

class Item
{

        /**
         * @var SellerProduct
         */
        protected $sellerProduct;

    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Hexcrypto\WishlistAPI\Helper\Data $wishlistHelper
      * @param SellerProduct $sellerProduct
     */
            private $quoteItemFactory;

    public function __construct(
         SellerProduct $sellerProduct,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalItemExtensionFactory,
         \Magento\Quote\Api\Data\TotalsExtensionFactory $totalExtensionFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
    
    ) {
        $this->sellerProduct = $sellerProduct;
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
        $doorStepPrice=0;
        $pickupFrmStorePrice=0;

        $doorStepPId = array();
        $pickupFrmStorePId = array();
        foreach($totals->getItems() as $item)
        {

            $quoteItem = $this->itemFactory->create()->load($item->getItemId());
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/item2.log'); 
            $logger = new \Zend\Log\Logger(); 
            $logger->addWriter($writer);
          
            

            $SellerProd = $this->sellerProduct->create()->getCollection();
            $fltColl = $SellerProd->addFieldToFilter('seller_id', $quoteItem['seller_id'])
                        ->addFieldToFilter('product_id', $quoteItem->getProductId());
            $idInfo = $fltColl->getData();
           // $logger->info($fltColl->getData());
            //$logger->info($quoteItem['seller_id']);
           
            if(!empty($idInfo)){
                        foreach($idInfo as $info){
                            $id = $info['entity_id'];

                        }

                    $data = $this->sellerProduct->create()->load($id);
                    $door = $data->getDoorstepPrice();
                    $PickupFromStore= $data->getPickupFromStore();
                    $PickupFromNearbyStore= $data->getPickupFromNearbyStore();
                }
         $logger->info('PId ' . $quoteItem->getProductId()); 
         $logger->info('PickupFromNearbyStore ' . $PickupFromNearbyStore); 
         $logger->info('PickupFromStore ' .$PickupFromStore);      
         $logger->info('door' .$door);
         $logger->info('pt ' .$quoteItem->getPriceType());
         $logger->info('qty '.$quoteItem->getQty());

                if($quoteItem->getPriceType() == 0){
                    $doorStepPId[] = $quoteItem->getProductId();
                    $dsPrice = $door * $quoteItem->getQty();
                     $doorStepPrice += $dsPrice;
    
                  } else if($quoteItem->getPriceType() == 1) {
                    $pickupFrmStorePId[] = $quoteItem->getProductId();
                    $spPrice = $PickupFromStore * $quoteItem->getQty();
                    $pickupFrmStorePrice += $spPrice;

                  } 

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
         
            $extensionAttributes->setDsCount(count($doorStepPId));
            $extensionAttributes->setDsSubtotal($doorStepPrice);
            $extensionAttributes->setSpCount(count($pickupFrmStorePId));
            $extensionAttributes->setSpSubtotal($pickupFrmStorePrice);
            $extensionAttributes->setExtnGrandTotal($grandTotal);
            $totals->setExtensionAttributes($extensionAttributes);

        return $totals;
    }

}