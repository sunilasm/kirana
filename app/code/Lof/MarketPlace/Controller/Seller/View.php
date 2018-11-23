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
namespace Lof\MarketPlace\Controller\Seller;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Lof\MarketPlace\Model\Layer\Resolver;

class View extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @param Context                                             $context              [description]
     * @param \Magento\Store\Model\StoreManager                   $storeManager         [description]
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    [description]
     * @param \Lof\MarketPlace\Model\Seller                              $sellerModel           [description]
     * @param \Magento\Framework\Registry                         $coreRegistry         [description]
     * @param Resolver                                            $layerResolver        [description]
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory [description]
     * @param \Lof\MarketPlace\Helper\Data                              $sellerHelper          [description]
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\Seller $sellerModel,
        \Magento\Framework\Registry $coreRegistry,
        Resolver $layerResolver,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Lof\MarketPlace\Helper\Data $sellerHelper
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_sellerModel = $sellerModel;
        $this->layerResolver = $layerResolver;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_sellerHelper = $sellerHelper;
    }

    public function _initSeller()
    {
        $sellerId = (int)$this->getRequest()->getParam('seller_id', false);
        if (!$sellerId) {
            return false;
        }
        try{
            $seller = $this->_sellerModel->load($sellerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_seller', $seller);
        return $seller;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_sellerHelper->getConfig('general_settings/enable')){
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $seller = $this->_initSeller();
        if ($seller) {
            $this->layerResolver->create('seller');
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $page = $this->resultPageFactory->create();
            // apply custom layout (page) template once the blocks are generated
            if ($seller->getPageLayout()) {
                $page->getConfig()->setPageLayout($seller->getPageLayout());
            }
            $page->addHandle(['type' => 'VES_BRAND_'.$seller->getId()]);
            if (($layoutUpdate = $seller->getLayoutUpdateXml()) && trim($layoutUpdate)!='') {
                $page->addUpdate($layoutUpdate);
            }

            /*$collectionSize = $seller->getProductCollection()->getSize();
            if($collectionSize){
                $page->addHandle(['type' => 'lofmarketplace_seller_layered']);
            }*/
            $page->getConfig()->addBodyClass('page-products')
            ->addBodyClass('seller-' . $seller->getUrlKey());
            return $page;
        }elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}