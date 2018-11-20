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


namespace Lof\MarketPlace\Controller\Adminhtml\SellerProduct;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory;

/**
 * Class Deny.
 */
class Deny extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime          $dateTime
     * @param CollectionFactory                           $collectionFactory
     * @param Processor                                   $productPriceIndexerProcessor
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $collection = $this->_objectManager->create(
            'Lof\MarketPlace\Model\SellerProduct'
        )->getCollection()
        ->addFieldToFilter('product_id', $data['product_id'])
        ->addFieldToFilter('seller_id', $data['seller_id']);
        
        if ($collection->getSize()) {
            $productIds = [$data['product_id']];
            $allStores = $this->_storeManager->getStores();
            $status = \Magento\Catalog\Model\SellerProduct\Attribute\Source\Status::STATUS_DISABLED;

            $sellerSellerProduct = $this->_objectManager->create(
                'Lof\MarketPlace\Model\SellerProduct'
            )->getCollection();

            $coditionData = "`product_id`=".$data['product_id'];

            $sellerSellerProduct->setSellerProductData(
                $coditionData,
                ['status' => $status]
            );
            foreach ($allStores as $eachStoreId => $storeId) {
                $this->_objectManager->get(
                    'Magento\Catalog\Model\SellerProduct\Action'
                )->updateAttributes($productIds, ['status' => $status], $storeId);
            }

            $this->_objectManager->get(
                'Magento\Catalog\Model\SellerProduct\Action'
            )->updateAttributes($productIds, ['status' => $status], 0);


            $catagoryModel = $this->_objectManager->get('Magento\Catalog\Model\Category');

            $helper = $this->_objectManager->get('Lof\MarketPlace\Helper\Data');

            $id = 0;

            foreach ($collection as $item) {
                $id = $item->getId();
            }
            
            $model = $this->_objectManager->get(
                'Magento\Catalog\Model\SellerProduct'
            )->load($data['product_id']);

            $catarray = $model->getCategoryIds();
            $categoryname = '';
            foreach ($catarray as $keycat) {
                $categoriesy = $catagoryModel->load($keycat);
                if ($categoryname == '') {
                    $categoryname = $categoriesy->getName();
                } else {
                    $categoryname = $categoryname.','.$categoriesy->getName();
                }
            }
            $allStores = $this->_storeManager->getStores();

            $pro = $this->_objectManager->create(
                'Lof\MarketPlace\Model\SellerProduct'
            )->load($id);

            $helper = $this->_objectManager->get('Lof\MarketPlace\Helper\Data');
            $adminStoreEmail = $helper->getAdminEmailId();
            $adminEmail = $adminStoreEmail ? $adminStoreEmail : $helper->getDefaultTransEmailId();
            $adminUsername = 'Admin';


            $this->messageManager->addSuccess(__('Product has been set commission.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::product');
    }
}
