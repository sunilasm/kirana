<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class Edit extends \Magento\Framework\App\Action\Action 
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $productBuilder;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';
    
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;
      /**
     *
     * @var Lof\MarketPlace\Helper\Data
     */
    protected $helper;
    /**
     * 
     * @param \Lof\Vendors\App\Action\Context $context
     * @param \Lof\Vendors\App\ConfigInterface $config
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->stockFilter = $stockFilter;
        parent::__construct($context);
        $this->productBuilder = $productBuilder;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->session           = $customerSession;
        $this->helper = $helper;
    }
    public function getFrontendUrl($route = '', $params = []){
        return $this->_frontendUrl->getUrl($route,$params);
    }
    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url){
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }
    /**
     * Product edit form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerSession = $this->session;
        if(!$customerSession->isLoggedIn()) {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }

        $productId = (int) $this->getRequest()->getParam('id');
        if($productId) {
            //$sellerId = $this->helper->getSellerIdByProduct($productId);
            $sellerId = $this->helper->getSellerId();
            if($this->helper->getSellerId() !=  $sellerId) {
                $this->messageManager->addNotice(__( 'That product is not yours' ) );
                return $this->_redirect(
                'catalog/product'
                );
            }
        }
        $product = $this->productBuilder->build($this->getRequest());
       
        if ($productId && !$product->getId()) {
            $this->messageManager->addError(__('This product no longer exists.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/*/');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->addHandle('catalog_product_' . $product->getTypeId());
        
        $title = $resultPage->getConfig()->getTitle();
            $title->prepend(__("Catalog"));
            $title->prepend(__("Manage Products"));
            $title->prepend($product->getName());
            // $breadCrumbBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
            // $breadCrumbBlock->addLink(__("Catalog"), __("Catalog"))
            //     ->addLink(__("Manage Products"), __("Manage Products"),$this->getUrl('catalog/product'))
            //     ->addLink($product->getName(), $product->getName());

        if (!$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->isSingleStoreMode()
            &&
            ($switchBlock = $resultPage->getLayout()->getBlock('store_switcher'))
        ) {
            $switchBlock->setDefaultStoreName(__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl(
                        'catalog/product/*',
                        ['_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null]
                    )
                );
        }

        $block = $resultPage->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        return $resultPage;
    }
}
