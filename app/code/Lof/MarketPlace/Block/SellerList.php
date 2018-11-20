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
namespace Lof\MarketPlace\Block;
use Magento\Customer\Model\Context as CustomerContext;

class SellerList extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

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
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
        ) {
        $this->_sellerCollection = $sellerCollection;
        $this->_sellerHelper = $sellerHelper;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);


    }

    public function _construct(){
        if(!$this->getConfig('general_settings/enable') || !$this->getConfig('seller_block/enable')) return;
        parent::_construct();
        $carousel_layout = $this->getConfig('seller_block/carousel_layout');
        $template = '';
        if($carousel_layout == 'owl_carousel'){
            $template = 'block/seller_list_owl.phtml';
        }else{
            $template = 'block/seller_list_bootstrap.phtml';
        }
        if(!$this->getTemplate() && $template!=''){
            $this->setTemplate($template);
        }
    }

    public function getConfig($key, $default = '')
    {   
        $widget_key = explode('/', $key);
        if( (count($widget_key)==2) && ($resultData = $this->hasData($widget_key[1])) )
        {
            return $this->getData($widget_key[1]);
        }
        $result = $this->_sellerHelper->getConfig($key);
        if($result == ""){
            return $default;
        }
        return $result;
    }

    public function getSellerCollection()
    {
        $number_item = $this->getConfig('seller_block/number_item');
        $sellerGroups = $this->getConfig('seller_block/seller_groups');
        $collection = $this->_sellerCollection->getCollection()
        ->setOrder('position','ASC')
        ->addFieldToFilter('status',1);
        $sellerGroups = explode(',', $sellerGroups);
        if(is_array($sellerGroups) && count($sellerGroups)>0)
        {
            $collection->addFieldToFilter('group_id',array('in' => $sellerGroups));
        }
        $collection->setPageSize($number_item)
        ->setCurPage(1)
        ->setOrder('position','ASC');
        return $collection;
    }


    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
        'VES_BRAND_LIST',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
        'template' => $this->getTemplate(),
        $this->getProductsCount()
        ];
    }

    public function _toHtml()
    {
       
        return parent::_toHtml();
    }
}