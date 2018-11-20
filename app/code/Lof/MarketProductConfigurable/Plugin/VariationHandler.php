<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketProductConfigurable\Plugin;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class PriceBackend
 *
 *  Make price validation optional for configurable product
 */
class VariationHandler extends \Magento\ConfigurableProduct\Model\Product\VariationHandler
{

    /**
     * @param \Magento\Catalog\Model\Product\Attribute\Backend\Price $subject
     * @param \Closure $proceed
     * @param @param \Magento\Catalog\Model\Product $parentProduct
     * @param array $productsData
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGenerateSimpleProducts(
        \Magento\ConfigurableProduct\Model\Product\VariationHandler $subject,
        \Closure $proceed,
        $parentProduct,
        $productsData
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $helper = $objectManager->create('Lof\MarketPlace\Helper\Data');
        $generatedProductIds = [];
        $productsData = $this->duplicateImagesForVariations($productsData);
        foreach ($productsData as $simpleProductData) {
            $newSimpleProduct = $this->productFactory->create();
            if (isset($simpleProductData['configurable_attribute'])) {
                $configurableAttribute = json_decode($simpleProductData['configurable_attribute'], true);
                unset($simpleProductData['configurable_attribute']);
            } else {
                throw new LocalizedException(__('Configuration must have specified attributes'));
            }
        
            $this->fillSimpleProductData(
                $newSimpleProduct,
                $parentProduct,
                array_merge($simpleProductData, $configurableAttribute)
            );
          
            $newSimpleProduct->setSellerId($helper->getSellerId());
            $newSimpleProduct->setApproval($parentProduct->getApproval());
            $newSimpleProduct->save();
        
            $generatedProductIds[] = $newSimpleProduct->getId();
        }
        return $generatedProductIds;
    }
}
