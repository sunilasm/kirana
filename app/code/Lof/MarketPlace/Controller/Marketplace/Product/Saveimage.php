<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class Saveimage extends \Magento\Framework\App\Action\Action 
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper
     */
    protected $initializationHelper;

    /**
     * @var \Magento\Catalog\Model\Product\Copier
     */
    protected $productCopier;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    protected $productTypeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Lof\VendorsProduct\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $_session;

     /**
     *
     * @var Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    protected $productBuilder;

    protected $uploadimage;
    /**
     *
     * @param \Magento\Backend\App\Action\Context$context
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
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Model\Product\Copier $productCopier,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\Seller $seller,
        \Magento\Catalog\Model\Product\Gallery\UpdateHandler $updateHandler,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Uploadimage $uploadimage
    ) {
        parent::__construct($context);
        $this->uploadimage = $uploadimage;
        $this->updateHandler = $updateHandler;
        $this->storeManager         = $storeManager;
        $this->initializationHelper = $initializationHelper;
        $this->productCopier        = $productCopier;
        $this->productTypeManager   = $productTypeManager;
        $this->productRepository    = $productRepository;
        $this->helper               = $helper;
        $this->_session             = $customerSession;
        $this->productBuilder       = $productBuilder;
        $this->seller               = $seller;
    }
   

    /**
     * Save product action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
       
        $data = $this->getRequest()->getPostValue();
       
        if ($data) {
            try {
                foreach ($data['product']['media_gallery']['images'] as $key => $file) {
                    $this->uploadimage->moveImageFromTmp($file['file']);
                }
                  $this->messageManager->addSuccess('Import Image Product Success');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $result = $this->_redirect('catalog/product/index', ['store' => $storeId]);
            $this->messageManager->addError('No data to save');
        }
        $this->_redirect( 'catalog/product/uploadimage' );
    }


}
