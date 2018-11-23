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
namespace Lof\MarketPlace\Block;

class Sellerpage extends \Magento\Framework\View\Element\Template
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
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $seller,
        array $data = []
        ) {
        $this->_seller = $seller;
        $this->_coreRegistry = $registry;
        $this->_sellerHelper = $sellerHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        parent::_construct();
        $itemsperpage = (int)$this->getConfig('seller_list_page/item_per_page',12);
        $seller = $this->_seller;
        $sellerCollection = $seller->getCollection()
        ->addFieldToFilter('status',1)
        ->setOrder('position','ASC');
        $this->setCollection($sellerCollection);

        $template = '';
        $layout = $this->getConfig('seller_list_page/layout');
        if($layout == 'grid'){
            $template = 'sellerlistpage_grid.phtml';
        }else{
            $template = 'sellerlistpage_list.phtml';
        }
        if(!$this->hasData('template')){
            $this->setTemplate($template);
        }
    }

	/**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $seller
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
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
        $page_title = $this->getConfig('seller_list_page/page_title');
        $meta_description = $this->getConfig('seller_list_page/meta_description');
        $meta_keywords = $this->getConfig('seller_list_page/meta_keywords');
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('lof-sellerlist');
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);   
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
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
            $itemsperpage = (int)$this->getConfig('seller_list_page/item_per_page',12);
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}