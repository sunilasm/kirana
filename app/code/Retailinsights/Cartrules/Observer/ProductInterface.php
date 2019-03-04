<?php
namespace Retailinsights\Cartrules\Observer;
use Magento\Framework\Event\ObserverInterface;
    use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
    use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
    use Magento\Store\Model\StoreManagerInterface as StoreManager;
    use Magento\Store\Model\App\Emulation as AppEmulation;
    use Magento\Quote\Api\Data\CartItemExtensionFactory;
    class ProductInterface implements ObserverInterface
    {   
        protected $_productRepository;
       
        /**
         * @var ProductRepository
         */
        protected $productRepository;
        /**
         *@var \Magento\Catalog\Helper\ImageFactory
         */
        protected $productImageHelper;
        /**
         *@var \Magento\Store\Model\StoreManagerInterface
         */
        protected $storeManager;
        /**
         *@var \Magento\Store\Model\App\Emulation
         */
        protected $appEmulation;
        /**
         * @var CartItemExtensionFactory
         */
        protected $extensionFactory;
        protected $collection;
        /**
         * @param ProductRepository $productRepository
         * @param \Magento\Catalog\Helper\ImageFactory
         * @param \Magento\Store\Model\StoreManagerInterface
         * @param \Magento\Store\Model\App\Emulation
         * @param CartItemExtensionFactory $extensionFactory
         */
        public function __construct(
            ProductRepository $productRepository,
            ProductImageHelper $productImageHelper,
            StoreManager $storeManager,
            AppEmulation $appEmulation,
            CartItemExtensionFactory $extensionFactory,
            \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $collection
        ) {
            $this->productRepository = $productRepository;
            $this->productImageHelper = $productImageHelper;
            $this->storeManager = $storeManager;
            $this->appEmulation = $appEmulation;
            $this->extensionFactory = $extensionFactory;
            
            $this->_productRepository = $productRepository;
            $this->collection = $collection;
          
        }
    public function execute(\Magento\Framework\Event\Observer $observer, string $imageType = NULL)
        {
            $quote = $observer->getQuote();
            
           /**
             * Code to add the items attribute to extension_attributes
             */
            foreach ($quote->getAllItems() as $quoteItem) {
                $itemExtAttr = $quoteItem->getExtensionAttributes();
                
                $product = $this->_productRepository->getById($quoteItem->getProductId());
                 $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testnew.log'); 
                $logger = new \Zend\Log\Logger(); 
                $logger->addWriter($writer); 
                $logger->info($product->getUnitm());
                $optionId = $product->getUnitm();
                $attribute = $product->getResource()->getAttribute('unitm');
                if ($attribute->usesSource()) {
                    $optionText = $attribute->getSource()->getOptionText($optionId);
                }


                $data = $optionText;
               
               // if ($itemExtAttr === null) {
                    $itemExtAttr = $this->extensionFactory->create();
                //}
                 $itemExtAttr->setUnitm($data);
                $quoteItem->setExtensionAttributes($itemExtAttr);   
                
            }  
            return;
        }
    }