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
namespace Lof\MarketPlace\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Seller Model
 */
class Seller extends \Magento\Framework\Model\AbstractModel
{	
	/**
	 * Seller's Statuses
	 */
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_sellerHelper;

    protected $customer;
    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller|null                      $resource                 
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Lof\MarketPlace\Helper\Data                                    $sellerHelper              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\ResourceModel\Seller $resource = null,
        \Lof\MarketPlace\Model\ResourceModel\Seller\Collection $resourceCollection = null,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Magento\Customer\Model\Session $customer,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_sellerHelper = $sellerHelper;
        $this->customer = $customer;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

	/**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Lof\MarketPlace\Model\ResourceModel\Seller');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Approved'), self::STATUS_DISABLED => __('Disapproved')];
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Get category products collection
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getProductCollection()
    { 
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');

        $data = array(); 
        foreach ($collection as $key => $_collection) {
            if($_collection->getSellerId() == $this->getSellerId()) {
                $data[] = $_collection->getData()['entity_id'];
            }
        } 
        return $data;
    }

    public function getSellerId() {
        $collection = $this->getCollection()->addFieldToFilter('seller_id',$this->customer->getCustomerId());
        $seller_id = -1;
       
        foreach ($collection as $key => $_collection) {
           $seller_id = $_collection->getData('seller_id');
        } 
        return $seller_id;
    }


    public function loadByCustomer(\Magento\Customer\Model\Customer $customer){
        $this->_getResource()->loadByCustomer($this,$customer);
        return $this;
    }
    
    public function getUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $route = $this->_sellerHelper->getConfig('general_settings/route');
        $url_prefix = $this->_sellerHelper->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        $url_suffix = $this->_sellerHelper->getConfig('general_settings/url_suffix');
        return $url.$urlPrefix.$this->getUrlKey().$url_suffix;
    }

    /**
     * Retrive image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $image;
        };
        return $url;
    }

    /**
     * Retrive thumbnail URL
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        $url = false;
        $thumbnail = $this->getThumbnail();
        if ($thumbnail) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $thumbnail;
        } else {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) .'images/user.png';
        }
        return $url;
    }
}