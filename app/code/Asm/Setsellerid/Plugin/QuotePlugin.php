<?php

/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Asm\Setsellerid\Plugin;

use Magento\Quote\Api\Data\CartInterface;

class QuotePlugin {

    /**
     * @param \Magento\Quote\Api\Data\CartItemExtensionFactory $cartItemExtension
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    protected $helperData;
    public function __construct(
        \Magento\Quote\Api\Data\CartItemExtensionFactory $cartItemExtension, 
        \Lof\MarketPlace\Helper\Data $helperData,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository        ) {
        $this->cartItemExtension = $cartItemExtension;
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
    }

    /**
     * Add attribute values
     *
     * @param   \Magento\Quote\Api\CartRepositoryInterface $subject,
     * @param   $quote
     * @return  $quoteData
     */
    public function afterGet(
    \Magento\Quote\Api\CartRepositoryInterface $subject, $quote
    ) {
        $quoteData = $this->setAttributeValue($quote);
        return $quoteData;
    }

    /**
     * Add attribute values
     *
     * @param   \Magento\Quote\Api\CartRepositoryInterface $subject,
     * @param   $quote
     * @return  $quoteData
     */
    public function afterGetActiveForCustomer(
    \Magento\Quote\Api\CartRepositoryInterface $subject, $quote
    ) {
        $quoteData = $this->setAttributeValue($quote);
        return $quoteData;
    }

    /**
     * set value of attributes
     *
     * @param   $product,
     * @return  $extensionAttributes
     */
    private function setAttributeValue($quote) {
        $data = [];
        if ($quote->getItemsCount()) {
            foreach ($quote->getItems() as $item) { 
                $data = [];
                $extensionAttributes = $item->getExtensionAttributes();
                if ($extensionAttributes === null) {

                    $extensionAttributes = $this->cartItemExtension->create();
                }
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $productData = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getId());

               // $productData = $this->productRepository->create()->get($item->getId());                
                $extensionAttributes->setImage($productData->getThumbnail());
                $sellerName = $this->helperData->getSellernameId($item->getSellerId());
                $extensionAttributes->setSellerName($sellerName);
                $extensionAttributes->setSellerId($item->getSellerId());
                $extensionAttributes->setProductId($item->getProductId());
                $item->setExtensionAttributes($extensionAttributes);
            }
        } 
    return $quote;
    }  
}
