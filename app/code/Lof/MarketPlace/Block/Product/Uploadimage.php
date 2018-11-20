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

namespace Lof\MarketPlace\Block\Product;

use Magento\Customer\Model\Context as CustomerContext;

class Uploadimage extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_sellerCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
     /**
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    /**
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection
     */
    protected $attributeSet;
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context         
     * @param \Magento\Framework\Registry                      $registry        
     * @param \Lof\MarketPlace\Helper\Data                           $sellerHelper     
     * @param \Lof\MarketPlace\Model\Seller                           $sellerCollection 
     * @param array                                            $data            
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attributeSet,
        \Magento\Catalog\Model\Product $product,
        array $data = []
        ) {
         parent::__construct($context, $data); 

        $this->_sellerCollection = $sellerCollection;
        $this->_sellerHelper = $sellerHelper;
        $this->_coreRegistry = $registry;
        $this->_resource = $resource;
        $this->storeManager =  $context->getStoreManager();
        $this->attributeSet = $attributeSet;
        $this->product = $product;
       
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getSellerCollection(){
        $product = $this->getProduct();
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('lof_marketplace_product');
        $sellerIds = $connection->fetchCol(" SELECT seller_id FROM ".$table_name." WHERE product_id = ".$product->getId());
        if($sellerIds || count($sellerIds) > 0) {
            $collection = $this->_sellerCollection->getCollection()
                ->setOrder('position','ASC')
                ->addFieldToFilter('status',1);
            $collection->getSelect()->where('seller_id IN (?)', $sellerIds);
            return $collection;
        }
        return false;
    }
    /**
     * Get base currency symbol
     *
     * @return string
     */
    public function getBaseCurrency() {
        return $this->storeManager->getStore ()->getBaseCurrencyCode ();
    }
    /**
     * Get Attribute set datas
     *
     * @return array
     */
    public function getAttributeSet() {
        return $this->attributeSet->toOptionArray ();
    }
    /**
     * Getting product data
     *
     * @param int $productId            
     *
     * @return object $productData
     */
    public function getProductData($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
    }

    public function _toHtml(){
        if(!$this->_sellerHelper->getConfig('product_view_page/enable_seller_info')) return;

        return parent::_toHtml();
    }
    /**
     * Get Default Attribute Set Id
     *
     * @return int
     */
    public function getDefaultAttributeSetId() {
        return $this->product->getDefaultAttributeSetId ();
    }

     /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        
        $this->pageConfig->getTitle ()->set(__('Import Product'));
        return parent::_prepareLayout ();
    }

}