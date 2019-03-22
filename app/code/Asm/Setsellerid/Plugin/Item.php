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
        $doorStepPId = 0;
        $pickupFrmStorePId = 0;
        foreach($totals->getItems() as $item)
        {

            $quoteItem = $this->itemFactory->create()->load($item->getItemId());
            
            $SellerProd = $this->sellerProduct->create()->getCollection();
            $fltColl = $SellerProd->addFieldToFilter('seller_id', $quoteItem->getSellerId())
                        ->addFieldToFilter('product_id', $quoteItem->getProductId());
            $idInfo = $fltColl->getData();
           
            if(!empty($idInfo)){
                        foreach($idInfo as $info){
                            $id = $info['entity_id'];
                        }
                    $data = $this->sellerProduct->create()->load($id);
                    $door = $data->getDoorstepPrice();
                    $PickupFromStore= $data->getPickupFromStore();
                    $PickupFromNearbyStore= $data->getPickupFromNearbyStore();
                }
        
                if($quoteItem->getPriceType() == 0){
                    $doorStepPId += $quoteItem->getQty();
                    $dsPrice = $door * $quoteItem->getQty();
                     $doorStepPrice += $dsPrice;
                     $rowTotal = $dsPrice;
    
                  } else {
                    $pickupFrmStorePId += $quoteItem->getQty();
                    $spPrice = $PickupFromStore * $quoteItem->getQty();
                    $pickupFrmStorePrice += $spPrice;
                    $rowTotal = $spPrice;
                  } 
            
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
         
            $extensionAttributes->setDsCount($doorStepPId);
            $extensionAttributes->setDsSubtotal($doorStepPrice);
            $extensionAttributes->setSpCount($pickupFrmStorePId);
            $extensionAttributes->setSpSubtotal($pickupFrmStorePrice);
            $extensionAttributes->setExtnGrandTotal($grandTotal);
            $totals->setExtensionAttributes($extensionAttributes);
        return $totals;
    }
}