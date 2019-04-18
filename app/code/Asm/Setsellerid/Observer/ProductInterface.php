<?php
/**
 * Copyright © 2018-2019 Hopescode, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Asm\Setsellerid\Observer;

use Magento\Framework\Event\ObserverInterface;
    use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;

    use Lof\MarketPlace\Model\SellerProductFactory as SellerProduct;

    use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
    use Magento\Store\Model\StoreManagerInterface as StoreManager;
    use Magento\Store\Model\App\Emulation as AppEmulation;
    use Magento\Quote\Api\Data\CartItemExtensionFactory;
    use Magento\Quote\Api\Data\CartExtensionFactory;


    class ProductInterface implements ObserverInterface
    {   
         /**
         * @var SellerProduct
         */
        protected $sellerProduct;

        /**
         * @var ObjectManagerInterface
         */
        protected $_objectManager;

        /**
         * @var ProductRepository
         */
        protected $productRepository;

        /**
         *@var \Magento\Catalog\Helper\ImageFactory
         */
        protected $productImageHelper;

        /**
         *@var \Magento\Store\Model\StoreManagerInterface
         */
        protected $storeManager;

        /**
         *@var \Magento\Store\Model\App\Emulation
         */
        protected $appEmulation;

        /**
         * @var CartItemExtensionFactory
         */
        protected $extensionFactory;

        protected $cartExtFactory;

        /**
         * @param \Magento\Framework\ObjectManagerInterface $objectManager
         * @param ProductRepository $productRepository
          * @param SellerProduct $sellerProduct

         * @param \Magento\Catalog\Helper\ImageFactory
         * @param \Magento\Store\Model\StoreManagerInterface
         * @param \Magento\Store\Model\App\Emulation
         * @param CartItemExtensionFactory $extensionFactory
         */
        public function __construct(
            \Magento\Framework\ObjectManagerInterface $objectManager,

            SellerProduct $sellerProduct,
            ProductRepository $productRepository,
            ProductImageHelper $productImageHelper,
            StoreManager $storeManager,
            AppEmulation $appEmulation,
            CartItemExtensionFactory $extensionFactory,
            CartExtensionFactory $cartExtFactory
        ) {
            $this->_objectManager = $objectManager;
            $this->productRepository = $productRepository;
            $this->productImageHelper = $productImageHelper;
            $this->storeManager = $storeManager;
            $this->appEmulation = $appEmulation;
            $this->extensionFactory = $extensionFactory;
            $this->cartExtFactory = $cartExtFactory;
            $this->sellerProduct = $sellerProduct;
        }

        public function execute(\Magento\Framework\Event\Observer $observer, string $imageType = NULL)
            {

            $doorStepPrice=0;
            $pickupFrmStorePrice=0;
            $PickupFromStore=0;
            $PickupFromNearbyStore=0;

            $door=0;

            $doorStepPId = 0;
            $pickupFrmStorePId = 0;
       
            $quote = $observer->getQuote();
            
            $subTotal = 0;
            
            foreach ($quote->getAllItems() as $quoteItem) {
               
                $product = $this->productRepository->create()->getById($quoteItem->getProductId());

                $SellerProd = $this->sellerProduct->create()->getCollection();
                $fltColl = $SellerProd->addFieldToFilter('seller_id', $quoteItem['seller_id'])
                        ->addFieldToFilter('product_id', $quoteItem->getProductId());
                $idInfo = $fltColl->getData();
                if(!empty($idInfo)){
                        foreach($idInfo as $info){
                            $id = $info['entity_id'];
                             $data = $this->sellerProduct->create()->load($id);
                    $door = $data->getDoorstepPrice();
                    $PickupFromStore= $data->getPickupFromStore();
                    $PickupFromNearbyStore= $data->getPickupFromNearbyStore();

                        }
                }
                if($quoteItem->getPriceType() == 0){
                    $doorStepPId += $quoteItem->getQty();
                    $dsPrice = $door * $quoteItem->getQty();
                     $doorStepPrice += $dsPrice;
    
                } else if($quoteItem->getPriceType() == 1) {
                    $pickupFrmStorePId += $quoteItem->getQty();
                    $spPrice = $PickupFromStore * $quoteItem->getQty();
                    $pickupFrmStorePrice += $spPrice;

                }


                $uom = $product->getUnitm();
                $optionId = $product->getUnitm();
                $weight = round($product->getWeight(), 0);
                $attribute = $product->getResource()->getAttribute('unitm');
                if ($attribute->usesSource()) {
                    $optionText = $weight." ".$attribute->getSource()->getOptionText($optionId);
                }

                $itemExtAttr = $quoteItem->getExtensionAttributes();

                if ($itemExtAttr === null) {
                    $itemExtAttr = $this->extensionFactory->create();
                } 
                
                $imageurl =$this->productImageHelper->create()->init($product, 'product_thumbnail_image')->setImageFile($product->getThumbnail())->getUrl();

                $itemExtAttr->setUnitm($optionText);
                $itemExtAttr->setVolume($product->getVolume());
                if(!empty($idInfo)){
                $itemExtAttr->setDoorstepPrice($door);
                $itemExtAttr->setPickupFromStore($PickupFromStore);
                $itemExtAttr->setPriceType($quoteItem['price_type']);
                $itemExtAttr->setPickupFromNearbyStore($PickupFromNearbyStore);
                
                $itemExtAttr->setImageUrl($imageurl);
                $quoteItem->setExtensionAttributes($itemExtAttr);
    
                }
             
            }
            $itemExtAttrquote = $quote->getExtensionAttributes();
            if ($itemExtAttrquote === null) {
                    $itemExtAttrquote = $this->cartExtFactory->create();
                } 
                $itemExtAttrquote->setDsCount($doorStepPId);
            $itemExtAttrquote->setDsSubtotal($doorStepPrice);
            $itemExtAttrquote->setSpCount($pickupFrmStorePId);
            $itemExtAttrquote->setSpSubtotal($pickupFrmStorePrice);
                $quote->setExtensionAttributes($itemExtAttrquote);

         return;

        }

        /**
         * Helper function that provides full cache image url
         * @param \Magento\Catalog\Model\Product
         * @return string
         */
        protected function getImageUrl($product, string $imageType = NULL)
        {
            $storeId = $this->storeManager->getStore()->getId();

            $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
            $imageUrl = $this->productImageHelper->create()->init($product, $imageType)->getUrl();

            $this->appEmulation->stopEnvironmentEmulation();

            return $imageUrl;
        }

    }