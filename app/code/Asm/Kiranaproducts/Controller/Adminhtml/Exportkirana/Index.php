<?php

namespace Asm\Kiranaproducts\Controller\Adminhtml\Exportkirana;


class Index extends \Magento\Backend\App\Action
{
    protected $fileFactory;
    protected $csvProcessor;
    protected $directoryList;
    protected $_sellerCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Lof\MarketPlace\Model\Seller $sellerCollection
    )
    {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->_customerSession = $customerSession;
        $this->_sellerCollection = $sellerCollection;
        parent::__construct($context, $customerSession);
    }


    public function execute()
    {
        //print_r("hererer");exit;
        $fileName = 'kiranas.csv';
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            . "/" . $fileName;

        //$customer = $this->_customerSession->getCustomer();
        $sellerData = $this->getSellerData();

        $this->csvProcessor
            ->setDelimiter(';')
            ->setEnclosure('"')
            ->saveData(
                $filePath,
                $sellerData
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

	protected function getSellerData()
    {
        $result = [];
        $sellerCollection = $this->_sellerCollection->getCollection()
        ->addFieldToFilter('status',1);
        $sellerData = $sellerCollection->getData();
        $result[] = [
            'id',
            'name'
        ];
        foreach ($sellerData as $seller) {
            $result[] = [
                $seller['seller_id'],
                $seller['name'],
            ];
        }
        return $result;
    }
    
}

?>