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

use Lof\MarketPlace\Controller\Marketplace\Product\ImportResult as ImportResultController;
use Magento\Framework\Controller\ResultFactory;

class Processimport extends ImportResultController
{
    /**
     * @var \Magento\ImportExport\Model\Import
     */
    protected $importModel;

    protected $csvProcessor;

    protected $_productRepository;

    protected $helper;
     /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    protected $sellerproduct;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\ImportExport\Model\Report\ReportProcessorInterface $reportProcessor
     * @param \Magento\ImportExport\Model\History $historyModel
     * @param \Magento\ImportExport\Helper\Report $reportHelper
     * @param \Magento\ImportExport\Model\Import $importModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\ImportExport\Model\Report\ReportProcessorInterface $reportProcessor,
        \Magento\ImportExport\Model\History $historyModel,
        \Magento\ImportExport\Helper\Report $reportHelper,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Catalog\Model\ProductFactory $productRepository,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\SellerProduct $sellerproduct,
        \Magento\ImportExport\Model\Import $importModel
    ) {
        parent::__construct($context, $reportProcessor, $historyModel, $reportHelper);
        $this->importModel = $importModel;
        $this->csvProcessor = $csvProcessor;
        $this->_productRepository = $productRepository;
        $this->helper = $helper;
        $this->_resource = $resource;
        $this->sellerproduct = $sellerproduct;
    }

    /**
     * Start import process action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
     
        if ($data) {
            /** @var \Magento\Framework\View\Result\Layout $resultLayout */
            $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
            /** @var $resultBlock \Magento\ImportExport\Block\Adminhtml\Import\Frame\Result */
            $resultBlock = $resultLayout->getLayout()->getBlock('import.frame.result');
            $resultBlock
                ->addAction('show', 'import_validation_container')
                ->addAction('innerHTML', 'import_validation_container_header', __('Status'))
                ->addAction('hide', ['edit_form', 'upload_button', 'messages']);
           
            $this->importModel->setData($data);
          
            $this->importModel->importSource();
            $errorAggregator = $this->importModel->getErrorAggregator();
             
            if ($this->importModel->getErrorAggregator()->hasToBeTerminated()) {
               $resultBlock->addError(__('Maximum error count has been reached or system error is occurred!'));
                $this->addErrorMessages($resultBlock, $errorAggregator);
            } else {
                $this->importModel->invalidateIndex();
             
                $file = $this->importModel->invalidateIndex()->uploadSource();
                $connection = $this->_resource->getConnection();
                $table_name = $this->_resource->getTableName('lof_marketplace_product');
                $product_table = $this->_resource->getTableName('catalog_product_entity');
                 $approval = $this->helper->getConfig('seller_settings/approval');

                if (file_exists($file)) {
                    $data = $this->csvProcessor->getData($file);
                    
                    $file=[];
                    foreach($data as $key => $_data) {
                        if($key) {
                            $file[] = $_data[0];
                           
                            $product= $this->_productRepository->create()->loadByAttribute('sku',$_data[0]);
                            $productId = $product->getId();

                            if($productId) {
                                $sellerproduct = $this->sellerproduct->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();
                                // $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId)->setData('seller_id',$this->helper->getSellerId())->save();
                                if($approval){
                                    $status = 1;
                                }else{
                                    $status = 2;
                                } 
                                $connection->query('UPDATE ' . $product_table . ' SET seller_id = '.$this->helper->getSellerId().',approval = '.$status.' WHERE  entity_id = '.(int)$productId);
                                if(count($sellerproduct->getData()) >0) {
                                  
                                    $connection->query('UPDATE ' . $table_name . ' SET seller_id = '.$this->helper->getSellerId().',status = 3 WHERE  product_id = '.(int)$productId);
                                    
                                } else {
                                $connection->query('INSERT INTO ' . $table_name . ' (seller_id,product_id,status,product_name) VALUES ( ' . $this->helper->getSellerId() . ', ' . (int)$productId . ', 0,'.$product->getName().')');  
                                } 
                            } 
                        }
                    }
                   
                }
               
                $this->addErrorMessages($resultBlock, $errorAggregator);
                $resultBlock->addSuccess(__('Import successfully done'));
            }
            return $resultLayout;
           
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/import');

        return $resultRedirect;
    }
}
