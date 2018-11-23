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

namespace Lof\MarketPlace\Block\Group;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_seller;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Lof\MarketPlace\Helper\Data                           $sellerHelper  
     * @param \Lof\MarketPlace\Model\Seller                           $seller        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param \Lof\MarketPlace\Helper\Data                           $sellerHelper  
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        array $data = []
        ) {
        $this->_seller = $seller;
        $this->_coreRegistry = $registry;
        $this->_sellerHelper = $sellerHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $seller = $this->_seller;
        $group = $this->getCurrentGroup();
        $sellerCollection = $seller->getCollection()
        ->addFieldToFilter('group_id',$group->getId())
        ->addFieldToFilter('status',1)
        ->setOrder('position','ASC');
        $this->setCollection($sellerCollection);
        $template = 'group/view.phtml';
        if(!$this->hasData('template')){
            $this->setTemplate($template);
        }
    }

    public function getCurrentGroup()
    {
        $group = $this->_coreRegistry->registry('current_group_seller');
        if ($group) {
            $this->setData('current_group_seller', $group);
        }
        return $group;
    }

	/**
     * Prepare breadcrumbs
     *
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $group = $this->getCurrentGroup();
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $sellerRoute = $this->_sellerHelper->getConfig('general_settings/route');
        $page_title = $this->_sellerHelper->getConfig('seller_list_page/page_title');

        if($breadcrumbsBlock){
        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $baseUrl
            ]
            );

        $breadcrumbsBlock->addCrumb(
            'lofmarketplace',
            [
                'label' => $page_title,
                'title' => $page_title,
                'link' => $baseUrl.$sellerRoute
            ]
            );

        $breadcrumbsBlock->addCrumb(
            'seller',
            [
                'label' => $group->getName(),
                'title' => $group->getName(),
                'link' => ''
            ]
            );
        }
    }

    /**
     * Set seller collection
     * @param \Lof\MarketPlace\Model\Seller
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * Retrive seller collection
     * @param \Lof\MarketPlace\Model\Seller
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_sellerHelper->getConfig($key);
        if(!$result){
            return $default;
        }
        return $result;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('lof-sellerlist');
        $group = $this->getCurrentGroup();
        $page_title = $group->getName();
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);
            $this->pageConfig->setKeywords($page_title);
            $this->pageConfig->setDescription($page_title); 
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('lofmarketplace_toolbar');
        if ($block) {
            $block->setDefaultOrder("position");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $itemsperpage = (int)$this->getConfig('group_page/item_per_page',12);
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($collection);
            $this->setChild('group-toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}