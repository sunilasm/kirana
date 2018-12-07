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

namespace Lof\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
/**
 * Product validate
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Validate extends \Magento\Framework\App\Action\Action 
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var \Magento\Catalog\Model\Product\Validator
     */
    protected $productValidator;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /** @var \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;


    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
        /**
     * @var Initialization\Helper
     */
    protected $initializationHelper;

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
        Context $context,
        \Magento\Catalog\Model\Product\Validator $productValidator,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->productValidator = $productValidator;
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->productFactory = $productFactory;
    }

   
    /**
     * Validate product
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);

        try {
            $productData = $this->getRequest()->getPost('product', []);

            if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
                $productData['stock_data']['use_config_manage_stock'] = 0;
            }
            $storeId = $this->getRequest()->getParam('store', 0);
            $store = $this->getStoreManager()->getStore($storeId);
            $this->getStoreManager()->setCurrentStore($store->getCode());
            /* @var $product \Magento\Catalog\Model\Product */
            $product = $this->productFactory->create();
            $product->setData('_edit_mode', true);
            if ($storeId) {
                $product->setStoreId($storeId);
            }
            $setId = $this->getRequest()->getPost('set') ?: $this->getRequest()->getParam('set');
            if ($setId) {
                $product->setAttributeSetId($setId);
            }
            $typeId = $this->getRequest()->getParam('type');
            if ($typeId) {
                $product->setTypeId($typeId);
            }
            $productId = $this->getRequest()->getParam('id');
            if ($productId) {
                $product->load($productId);
            }
            $product = $this->getInitializationHelper()->initializeFromData($product, $productData);

            /* set restrictions for date ranges */
            $resource = $product->getResource();
            $resource->getAttribute('special_from_date')->setMaxValue($product->getSpecialToDate());
            $resource->getAttribute('news_from_date')->setMaxValue($product->getNewsToDate());
            $resource->getAttribute('custom_design_from')->setMaxValue($product->getCustomDesignTo());

            $this->productValidator->validate($product, $this->getRequest(), $response);
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessages([$e->getMessage()]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response->setError(true);
            $response->setMessages([$e->getMessage()]);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $layout = $this->layoutFactory->create();
            $layout->initMessages();
            $response->setError(true);
            $response->setHtmlMessage($layout->getMessagesBlock()->getGroupedHtml());
        }

        return $this->resultJsonFactory->create()->setData($response);
    }

    /**
     * @return StoreManagerInterface
     * @deprecated 101.0.0
     */
    private function getStoreManager()
    {
        if (null === $this->storeManager) {
            $this->storeManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Store\Model\StoreManagerInterface::class);
        }
        return $this->storeManager;
    }

    /**
     * @return Initialization\Helper
     * @deprecated 101.0.0
     */
    protected function getInitializationHelper()
    {
        if (null === $this->initializationHelper) {
            $this->initializationHelper = ObjectManager::getInstance()->get(Initialization\Helper::class);
        }
        return $this->initializationHelper;
    }
}
