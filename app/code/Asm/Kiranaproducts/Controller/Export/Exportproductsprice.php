<?php
namespace Asm\Kiranaproducts\Controller\Export;

class Exportproductsprice extends \Magento\Framework\App\Action\Action
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
        \Magento\Catalog\Model\Product $product,
        \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection
    )
    {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->_customerSession = $customerSession;
        $this->_sellerProductCollection = $sellerProductCollection;
        $this->product = $product;
        parent::__construct($context, $customerSession);
    }

    public function execute()
    {
        //print_r("okkk");exit;
        $fileName = 'product_deatils.csv';
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            . "/" . $fileName;

        //$customer = $this->_customerSession->getCustomer();

       
        //print_r($sellerProductArray);exit;

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
    }

    protected function getProductData()
    {
        $result = [];
        // $obj = $bootstrap->getObjectManager();
        // $obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
        // $appState = $obj->get('\Magento\Framework\App\State');

        $sellerCollection = $this->_sellerProductCollection->getCollection();
        $sellerProductArray = array();
        foreach($sellerCollection as $sproduct):
            $sellerProductArray[] = $sproduct->getProductId();
        endforeach;
        //print_r(count($sellerProductArray));exit;


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $products = $objectManager->create('\Magento\Catalog\Model\Product')->getCollection();
        $products->addAttributeToSelect(array('name'))
        ->addFieldToFilter('entity_id', ['in' => $sellerProductArray])
        ->addFieldTofilter('type_id','simple')
        ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->load();
        //$customerData = $customer->getData();
        $result[] = [
            'id',
            'doorstep_price',
            'pickup_from_store',
            'price'
        ];
        foreach ($products as $product) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productData = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
            if($productData->getPrice() <= 4 || $productData->getPrice() <= 4.00){
                $doorsetp = $productData->getPrice();
                $pickup = $productData->getPrice();
                // print_r($doorsetp.'---'.$pickup);exit;
            }else{
                $doorsetp = (($productData->getPrice()*0.8)-4);
                $pickup = ($productData->getPrice()*0.9);
            }
            // print_r($productData->getPrice()."--");
            $result[] = [
                $productData->getId(),
                $doorsetp,
                $pickup,
                $productData->getPrice(),
            ];
        }
        //print_r($result);exit;
        return $result;
    }
}
