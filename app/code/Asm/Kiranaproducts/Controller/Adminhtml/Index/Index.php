<?php

namespace Asm\Kiranaproducts\Controller\Adminhtml\Index;


class Index extends \Magento\Backend\App\Action
{
    protected $fileFactory;
    protected $csvProcessor;
    protected $directoryList;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Catalog\Model\Product $product
    )
    {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->_customerSession = $customerSession;
        $this->product = $product;
        parent::__construct($context, $customerSession);
    }


    public function execute()
    {
        $fileName = 'product_deatils.csv';
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            . "/" . $fileName;

        //$customer = $this->_customerSession->getCustomer();
        $personalData = $this->getProductData();

        $this->csvProcessor
            ->setDelimiter(';')
            ->setEnclosure('"')
            ->saveData(
                $filePath,
                $personalData
            );

        return $this->fileFactory->create(
            $fileName,
            [
                'type' => "filename",
                'value' => $fileName,
                'rm' => true,
            ],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/octet-stream'
        );

        // $this->_view->loadLayout();
        // $this->_view->getLayout()->initMessages();
        // $this->_view->renderLayout();
	}

	protected function getProductData()
    {
        $result = [];
        // $obj = $bootstrap->getObjectManager();
        // $obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
        // $appState = $obj->get('\Magento\Framework\App\State');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $products = $objectManager->create('\Magento\Catalog\Model\Product')->getCollection();
        $products->addAttributeToSelect(array('name'))
        ->addFieldTofilter('type_id','simple')
        ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->load();
        //$customerData = $customer->getData();
        $result[] = [
            'id',
            'name',
            'sku',
            'price'
        ];
        foreach ($products as $product) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productData = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
            $result[] = [
                $productData->getId(),
                $productData->getName(),
                $productData->getSku(),
                $productData->getPrice(),
            ];
        }

        return $result;
    }
    
}

?>