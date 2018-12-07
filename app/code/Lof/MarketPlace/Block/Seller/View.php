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
namespace Lof\MarketPlace\Block\Seller;

class View extends \Magento\Framework\View\Element\Template
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_sellerHelper;

    protected $_groupModel;


    protected $_vacation;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context       
     * @param \Magento\Catalog\Model\Layer\Resolver            $layerResolver 
     * @param \Magento\Framework\Registry                      $registry      
     * @param \Lof\MarketPlace\Helper\Data                           $sellerHelper   
     * @param \Lof\MarketPlace\Model\Group                           $groupModel    
     * @param array                                            $data          
     */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Catalog\Model\Layer\Resolver $layerResolver,
    	\Magento\Framework\Registry $registry,
    	\Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Group $groupModel,
        \Lof\MarketPlace\Model\Vacation $vacation,
        array $data = []
        ) {
        $this->vacation = $vacation;
    	$this->_sellerHelper = $sellerHelper;
    	$this->_catalogLayer = $layerResolver->get();
    	$this->_coreRegistry = $registry;
        $this->_groupModel = $groupModel;
        parent::__construct($context, $data);
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
        $sellerRoute = $sellerRoute?$sellerRoute:"lofmarketplace/index/index";
        $page_title = $this->_sellerHelper->getConfig('seller_list_page/page_title');
        $seller = $this->getCurrentSeller();

        $group = false;
        if($groupId = $seller->getGroupId()){
            $group = $this->_groupModel->load($groupId);
        }
        if($breadcrumbsBlock)
        {
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
        
        if($group && $group->getStatus()){
            $breadcrumbsBlock->addCrumb(
                'group',
                [
                'label' => $group->getName(),
                'title' => $group->getName(),
                'link' => $group->getUrl()
                ]
                );
        }

        $breadcrumbsBlock->addCrumb(
            'seller',
            [
                'label' => $seller->getName(),
                'title' => $seller->getName(),
                'link' => ''
            ]
            );
        }
    }

    public function getCurrentSeller()
    {
        $seller = $this->_coreRegistry->registry('current_seller');
        if ($seller) {
            $this->setData('current_seller', $seller);
        }
        return $seller;
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
    	return $this->getChildHtml('product_list');
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */

    public function getVacation() {

        $seller = $this->getCurrentSeller();
        $vacation = $this->vacation->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('seller_id',$seller->getData('seller_id'))->getFirstItem();

         return $vacation;
    }

    protected function _prepareLayout()
    {
        $seller = $this->getCurrentSeller();
        $page_title = $seller->getName();
        $meta_description = $seller->getMetaDescription();
        $meta_keywords = $seller->getMetaKeywords();
        $this->_addBreadcrumbs();
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
    public function _toHtml()
    {

        if ($this->getCurrentSeller()->getData('status') == 0) {
            return;
        }
        return parent::_toHtml();
    }
}