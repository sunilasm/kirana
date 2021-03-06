<?php
namespace Asm\Setsellerid\Plugin;
use Lof\MarketPlace\Model\SellerProductFactory as SellerProduct;
use Retailinsights\Promotion\Model\PromoTableFactory;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;


class Item
{
    /**
     * @var SellerProduct
     */
    protected $sellerProduct;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Hexcrypto\WishlistAPI\Helper\Data $wishlistHelper
      * @param SellerProduct $sellerProduct
     */
    /**
         *@var \Magento\Catalog\Helper\ImageFactory
         */
    protected $productImageHelper;
    private $quoteItemFactory;
    protected $_promoFactory;

    public function __construct(
         SellerProduct $sellerProduct,
         ProductImageHelper $productImageHelper,
         PromoTableFactory $promoFactory,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalItemExtensionFactory,
         \Magento\Quote\Api\Data\TotalsExtensionFactory $totalExtensionFactory,
        ProductRepository $productRepository
      //  \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
    
    ) {
        $this->_promoFactory = $promoFactory;
        $this->productImageHelper = $productImageHelper;
        $this->sellerProduct = $sellerProduct;
             
        $this->itemFactory = $itemFactory;
        $this->totalItemExtension = $totalItemExtensionFactory;
        $this->totalExtension = $totalExtensionFactory;
       // $this->quoteItemFactory = $quoteItemFactory;
        $this->productRepository = $productRepository;
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
        $door=0;
        $PickupFromStore=0;
        $discount_amount = 0;
        $isEditable = 1;
        $free_price = '';
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/appromo.log'); 
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        foreach($totals->getItems() as $item)
        {
            $freeQty = 0; $freeProduct = 0;
            $productName = $productImg = $productUom = $productPriceType = '';

            $quoteItem = $this->itemFactory->create()->load($item->getItemId());
         
            $product = $this->productRepository->create()->getById($quoteItem->getProductId());

            $productName = $product->getName();
            $productImg = $this->productImageHelper->create()->init($product, 'product_thumbnail_image')->setImageFile($product->getThumbnail())->getUrl();
            $optionId = $product->getUnitm();
                $weight = round($product->getWeight(), 0);
                $attribute = $product->getResource()->getAttribute('unitm');
                if ($attribute->usesSource()) {
                    $productUom = $weight." ".$attribute->getSource()->getOptionText($optionId);
                }
            $productPriceType = $quoteItem->getPriceType();

            $discountData = $this->_promoFactory->create()->getCollection()
            ->addFieldToFilter('cart_id', $quoteItem->getQuoteId());
            if(isset($discountData)){
                foreach($discountData->getData() as $k => $val){ 
                        $discount_amount = $val['total_discount'];
                        $itemInfo = json_decode($val['item_qty'],true);
                        foreach($itemInfo as $k => $itemArray){
                          foreach($itemArray as $key => $value){
                            $itemData = json_decode($value);
                            if(isset($itemData->id)){
                                if($itemData->id == $quoteItem->getItemId()) {
                                    $freeQty = $itemData->qty;
                                }
                            }
                            if(isset($itemData->type) && (($itemData->type == 'BXGY') || ($itemData->type == 'BWGY')) && ($itemData->id == $quoteItem->getItemId())){
                                $isEditable = 0;
                                $free_price = "00.00";
                             }
                            if(isset($itemData->parent)){
                                if($itemData->parent == $quoteItem->getItemId()) {
                                    $freeProduct = $itemData->id;
                                }
                            }
                                
                          }
                        }
                    }
            }
            

            // $SellerProd = $this->sellerProduct->create()->getCollection();
            // $fltColl = $SellerProd->addFieldToFilter('seller_id', $quoteItem->getSellerId())
            //             ->addFieldToFilter('product_id', $quoteItem->getProductId());
            // $idInfo = $fltColl->getData();
           
            // if(!empty($idInfo)){
            //             foreach($idInfo as $info){
            //                 $id = $info['entity_id'];
            //             }
            //         $data = $this->sellerProduct->create()->load($id);
            //         $door = $data->getDoorstepPrice();
            //         $PickupFromStore= $data->getPickupFromStore();
            //         $PickupFromNearbyStore= $data->getPickupFromNearbyStore();
            //     }
            
            $door = $quoteItem->getPrice();
            $PickupFromStore = $quoteItem->getPrice();
        
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
            $extensionAttributes->setIsEditable($isEditable);
            $extensionAttributes->setExtnRowTotal($rowTotal);
            $extensionAttributes->setExtFreeQty($freeQty);
            $extensionAttributes->setExtFreeProduct($freeProduct);
            $extensionAttributes->setSku($product->getSku());
            $extensionAttributes->setFreePrice($free_price);

            $extensionAttributes->setProductName($productName);
            $extensionAttributes->setProductImg($productImg);
            $extensionAttributes->setProductUom($productUom);
            $extensionAttributes->setProductPriceType($productPriceType);

            

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
            $extensionAttributes->setSpSubtotal($pickupFrmStorePrice-$discount_amount);
           // $extensionAttributes->setExtnGrandTotal($grandTotal);
            $totals->setExtensionAttributes($extensionAttributes);
        return $totals;
    }
}
