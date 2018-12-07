<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Catalog\Model\ProductFactory;
class Save extends \Lof\MarketPlace\Controller\Marketplace\Product
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
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;


    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;

    
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
    /**
    * @var SearchCriteriaBuilder
    */
    protected $searchCriteriaBuilder;
        /**
     * @var LinkManagementInterface
     */
    protected $linkManagement;

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
        ProductFactory $productFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement,
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        parent::__construct($context,$productBuilder);
        $this->storeManager         = $storeManager;
        $this->initializationHelper = $initializationHelper;
        $this->productCopier        = $productCopier;
        $this->productTypeManager   = $productTypeManager;
        $this->productRepository    = $productRepository;
        $this->helper               = $helper;
        $this->_session             = $customerSession;
        $this->seller               = $seller;
        $this->_productFactory = $productFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->linkManagement = $linkManagement;
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
        $storeId = $this->getRequest()->getParam('store');
        $redirectBack = $this->getRequest()->getParam('back', false);
        
        $productId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
       
        $data = $this->getRequest()->getPostValue();
   
        $productAttributeSetId = $this->getRequest()->getParam('set');
        $productTypeId = $this->getRequest()->getParam('type');
        $seller_id = $this->helper->getSellerId();

        if ($data) {
            try {

                $params = $this->getRequest()->getParams();
               
                $product = $this->productBuilder->build($this->getRequest());

                $approval = $this->helper->getConfig('seller_settings/approval');
       
                if($approval){
                    $product->setApproval(1);
                }else{
                    if(!$product->getId()){                     
                        $product->setApproval(2);
                    }
                }
 
                $product = $this->initializationHelper->initialize($product);
               
                $this->productTypeManager->processProduct($product);
                /*Set vendor ID and save*/
                $product->setWebsiteIds([$this->storeManager->getWebsite()->getId() => $this->storeManager->getWebsite()->getId()]);


                if (isset($data['product'][$product->getIdFieldName()])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to save product'));
                }

                $originalSku = $product->getSku();
                $product->setSellerId($seller_id);

                $product->save();
                $this->getCategoryLinkManagement()->assignProductToCategories(
                    $product->getSku(),
                    $product->getCategoryIds()
                );

               /* $this->handleImageRemoveError($data, $product->getId());*/
                $productId = $product->getId();
               
                $productAttributeSetId = $product->getAttributeSetId();
               
                $productTypeId = $product->getTypeId();

                /**
                 * Do copying data to marketplace
                 */
                if($productId) {
                    $sellerProduct = $this->_objectManager->create('Lof\MarketPlace\Model\SellerProduct');
                    $model = $this->_objectManager->create('Lof\MarketPlace\Model\SellerProduct');
                    foreach ($sellerProduct->getCollection()->getData() as $key => $_seller) {
                        if($productId == $_seller['product_id']) {
                            $model->load($_seller['entity_id']);
                        }
                    }
                    
                    $model->setProductId($productId)
                    ->setStoreId($this->helper->getCurrentStoreId())
                    ->setSellerId($product->getSellerId())
                    ->save();

                  
                }
                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo => $storeFrom) {
                        $this->_objectManager->create('Magento\Catalog\Model\Product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

               
                $this->messageManager->addSuccess(__('You saved the product.'));
                if ($product->getSku() != $originalSku) {
                    $this->messageManager->addNotice(
                        __(
                            'SKU for product %1 has been changed to %2.',
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($product->getName()),
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($product->getSku())
                        )
                    );
                }

                $this->_eventManager->dispatch(
                    'controller_action_catalog_product_save_entity_after',
                    ['controller' => $this]
                );

                if ($redirectBack === 'duplicate') {
                    if($productId){
                        $oldProduct = $this->_objectManager->create('Magento\Catalog\Model\Product');
                        $newProduct = $this->productCopier->copy($oldProduct->load($productId));
                    }else{
                        $newProduct = $this->productCopier->copy($product);
                    }
                    $model = $this->_objectManager->create('Lof\MarketPlace\Model\SellerProduct');
                    $model->setProductId($newProduct->getEntityId())
                    ->setStoreId($this->helper->getCurrentStoreId())
                    ->setSellerId($product->getSellerId())
                    ->save();

                    $this->messageManager->addSuccess(__('You duplicated the product.'));
                }
                 
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setProductData($data);
                $redirectBack = $productId ? true : 'new';
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
                $this->_session->setProductData($data);
                $redirectBack = $productId ? true : 'new';
            }
        } else {
            $result = $this->_redirect('catalog/product/index', ['store' => $storeId]);
            $this->messageManager->addError('No data to save');
            return $resultRedirect;
        }
        
        if ($redirectBack === 'new') {
            $result = $this->_redirect(
                'catalog/product/new',
                ['set' => $productAttributeSetId, 'type' => $productTypeId]
            );
        } elseif ($redirectBack === 'duplicate' && isset($newProduct)) {
            $result = $this->_redirect(
                'catalog/product/edit',
                ['id' => $newProduct->getId(), 'back' => null, '_current' => true]
            );
        } elseif ($redirectBack) {
            $result = $this->_redirect(
                'catalog/product/edit',
                ['id' => $productId, '_current' => true, 'set' => $productAttributeSetId]
            );
        } else {
            $result = $this->_redirect('catalog/product', ['store' => $storeId]);
        }

        return $result;
    }

    /**
     * Notify customer when image was not deleted in specific case.
     * TODO: temporary workaround must be eliminated in MAGETWO-45306
     *
     * @param array $postData
     * @param int $productId
     * @return void
     */
    private function handleImageRemoveError($postData, $productId)
    {
        if (isset($postData['product']['media_gallery']['images'])) {
            $removedImagesAmount = 0;
            foreach ($postData['product']['media_gallery']['images'] as $image) {
                if (!empty($image['removed'])) {
                    $removedImagesAmount++;
                }
            }
            if ($removedImagesAmount) {
                $expectedImagesAmount = count($postData['product']['media_gallery']['images']) - $removedImagesAmount;
                $product = $this->productRepository->getById($productId);
                if ($expectedImagesAmount != count($product->getMediaGallery('images'))) {
                    $this->messageManager->addNotice(
                        __('The image cannot be removed as it has been assigned to the other image role')
                    );
                }
            }
        }
    }

    private function getCategoryLinkManagement()
    {
        if (null === $this->categoryLinkManagement) {
            $this->categoryLinkManagement = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Catalog\Api\CategoryLinkManagementInterface::class);
        }
        return $this->categoryLinkManagement;
    }
}
