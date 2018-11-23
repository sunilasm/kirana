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
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource;

class Seller extends \Magento\Catalog\Model\Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
    	$seller = $this->getCurrentSeller();
    	if(isset($this->_productCollections[$seller->getId()])){
    		$collection = $this->_productCollections;
    	}else{
    		$collection = $seller->getProductCollection();
    		$this->prepareProductCollection($collection);
            $this->_productCollections[$seller->getId()] = $collection;
    	} 
    	return $collection;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentSeller()
    {
    	$seller = $this->getData('current_seller');
    	if ($seller === null) {
    		$seller = $this->registry->registry('current_seller');
    		if ($seller) {
    			$this->setData('current_seller', $seller);
    		}
    	}
    	return $seller;
    }
}