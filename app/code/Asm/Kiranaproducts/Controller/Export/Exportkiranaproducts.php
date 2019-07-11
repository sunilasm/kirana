<?php
namespace Asm\Kiranaproducts\Controller\Export;

class Exportkiranaproducts extends \Magento\Framework\App\Action\Action
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
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection
    )
    {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->_customerSession = $customerSession;
        $this->product = $product;
        $this->_sellerProductCollection = $sellerProductCollection;
        $this->_sellerCollection = $sellerCollection;
        parent::__construct($context, $customerSession);
    }

    public function execute()
    {
        //print_r("okkk");exit;
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
    }

    protected function getProductData()
    {
        $result = [];
        // $obj = $bootstrap->getObjectManager();
        // $obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
        // $appState = $obj->get('\Magento\Framework\App\State');
        $kirnaProducts = $this->_sellerProductCollection->getCollection();
        // print_r($kirnaProducts->getData());exit;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $products = $objectManager->create('\Magento\Catalog\Model\Product')->getCollection();
        $products->addAttributeToSelect(array('name'))
        ->addFieldTofilter('type_id','simple')
        ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->load();
        //$customerData = $customer->getData();
        $result[] = [
            'entity_id',
            'product_id',
            'seller_id',
            'product_name',
            'name',
            'doorstep_price',
            'pickup_from_store',
            'mrp'
        ];
        foreach ($kirnaProducts as $product) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productData = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getProduct_id());
            $kiranaCollection = $this->_sellerCollection->getCollection()->addFieldTofilter('seller_id',$product->getSeller_id());
            $kiranaData = $kiranaCollection->getData();
            if($productData->getPrice() <= 5 || $productData->getPrice() <= 5.00){
                $doorsetp = $productData->getPrice();
                $pickup = $productData->getPrice();
            }elseif($product->getSku() == 'SKU590000592'){
                $doorsetp = ($productData->getPrice()*0.8-4);
                $pickup = $productData->getPrice();
            }elseif($product->getSku() == 'SKU590000692'){
                $doorsetp = $productData->getPrice();
                $pickup = ($productData->getPrice()*0.9);
            }else{
                $doorsetp = ($productData->getPrice()*0.8-4);
                $pickup = ($productData->getPrice()*0.9);
            }

            $result[] = [
                $product->getEntity_id(),
                $product->getProduct_id(),
                $product->getSeller_id(),
                $productData->getName(),
                $kiranaData[0]['name'],
                $doorsetp,
                $pickup,
                $productData->getPrice(),
            ];
            // print_r($result);exit;
        }

        return $result;
    }
}

