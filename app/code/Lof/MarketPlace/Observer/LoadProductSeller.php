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
namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadProductSeller implements ObserverInterface
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogData
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
        )
    {
        $this->_resource = $resource;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('lof_marketplace_product');
        if($product->getId()) {
            $productIds = $connection->fetchCol(" SELECT seller_id FROM ".$table_name." WHERE product_id = ".$product->getId());
            $product->setData('product_seller', implode($productIds, ','));
        }
        
    }
}
