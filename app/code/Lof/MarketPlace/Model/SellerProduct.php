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
namespace Lof\MarketPlace\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Seller Model
 */
class SellerProduct extends \Magento\Framework\Model\AbstractModel
{   
    /**
     * Seller's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const STATUS_PENDING = 2;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    //protected $_productCollectionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_scopeConfig;


    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Lof\MarketPlace\Model\ResourceModel\Product|null                      $resource                 
     * @param \Lof\MarketPlace\Model\ResourceModel\Product\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Magento\Framework\App\Config\ScopeConfigInterface        $scopeConfig              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\ResourceModel\SellerProduct $resource = null,
        \Lof\MarketPlace\Model\ResourceModel\SellerProduct\Collection $resourceCollection = null,
        //\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        //$this->_productCollectionFactory = $productCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }



    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Lof\MarketPlace\Model\ResourceModel\SellerProduct');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [0 => __('Not Submited'),1 => __('Pending'), 2 => __('Approved'),3 => __('Unapproved'),];
    }
}