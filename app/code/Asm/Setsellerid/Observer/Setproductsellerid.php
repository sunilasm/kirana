<?php
namespace Asm\Setsellerid\Observer;
set_time_limit(0);
ini_set('memory_limit', '1G');
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Quote\Api\Data\CartItemExtensionFactory;
use Magento\Customer\Model\Session;

class Setproductsellerid implements \Magento\Framework\Event\ObserverInterface
{
	protected $_request;

	 /**
         * @var ObjectManagerInterface
         */
        protected $_objectManager;

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

        protected $session;

        /**
         * @param \Magento\Framework\ObjectManagerInterface $objectManager
         * @param ProductRepository $productRepository
         * @param \Magento\Catalog\Helper\ImageFactory
         * @param \Magento\Store\Model\StoreManagerInterface
         * @param \Magento\Store\Model\App\Emulation
         * @param CartItemExtensionFactory $extensionFactory
         */
        public function __construct(
            \Magento\Framework\ObjectManagerInterface $objectManager,
            ProductRepository $productRepository,
            ProductImageHelper $productImageHelper,
            StoreManager $storeManager,
            AppEmulation $appEmulation,
            CartItemExtensionFactory $extensionFactory,
            Session $session
        ) {
            $this->_objectManager = $objectManager;
            $this->productRepository = $productRepository;
            $this->productImageHelper = $productImageHelper;
            $this->storeManager = $storeManager;
            $this->appEmulation = $appEmulation;
            $this->extensionFactory = $extensionFactory;
            $this->session = $session;
        }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Setproductsellerrrrrrrrrrr xxxxxx");

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $seller_id = array();
        if($request->getBodyParams())
        {
            $post = $request->getBodyParams();
            if(isset($post['product']) && isset($post['price'])){
                $seller_id["product"] = $post['product'];
                $seller_id["seller_id"] = $post['seller_id'];
                $seller_id["price"] = $post['price'];
            
                $logger->info("seller_id");
                $logger->info($seller_id);
                $quote = $observer->getQuote();
                $quote->setIsMultiShipping(false);
                $quote->collectTotals();
                $quote->setTotalsCollectedFlag(false);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerSession = $objectManager->create('Magento\Customer\Model\Session');

                $item = $observer->getQuote();
                $cartItems = [];
                if($item->getQuote()->getItems()){
                    foreach ($item->getQuote()->getItems() as $key => $value) {
                        $cartItems[$value->getSku()] = $value->getQty();
                    }
                }
                $logger->info("Setproductsellerrrrrrrrrrr Setproductseller222");
                $logger->info($cartItems);

                $price = $seller_id["price"];
                $item->setSellerId($seller_id["seller_id"]);
                $item->setOriginalCustomPrice($price);
                $item->setCustomPrice($price);
                foreach ($quote->getAllVisibleItems() as $quoteItem) {
                    if(isset($seller_id["product"]) && isset($seller_id["seller_id"]) && isset($seller_id["price"])){
                        //if($seller_id["product"] == $quoteItem->getProductId()){
                            $logger->info("quoteItem");
                            $logger->info($quoteItem->getProductId());
                           /* $quoteItem->setPrice($seller_id["price"]);
                            $quoteItem->setCustomRowTotalPrice($seller_id["seller_id"]);
                           */ 
                            //$quoteItem->setSellerId($seller_id["seller_id"]);
                           /* $quoteItem->setCustomPrice($seller_id["price"]);
                            $quoteItem->setOriginalCustomPrice($seller_id["price"]);
                            $quoteItem->setBaseRowTotal($seller_id["price"]);
                            $quoteItem->setBasePrice($seller_id["price"]);
                            $quoteItem->setRawTotal($seller_id["price"]);
                            $quoteItem->setBaseRawTotal($seller_id["price"]);
                            $quoteItem->setOriginalPrice($seller_id["price"]);
                            $quoteItem->save();
                            $quoteItem->getProduct()->setIsSuperMode(true);
                            $quoteItem->save();*/
                            /* $quoteItem->setCustomPrice($seller_id["price"]);
                            //$quoteItem->setPrice($seller_id["price"]);
                            $quoteItem->setOriginalCustomPrice($seller_id["price"]);
                            $quoteItem->setPrice($seller_id["price"]);
                            $quoteItem->setBasePrice($seller_id["price"]);
                            $quoteItem->setRawTotal($seller_id["price"]);
                            $quoteItem->setBaseRawTotal($seller_id["price"]);
                            $quoteItem->setOriginalPrice($seller_id["price"]);
                            $quoteItem->setOriginalBasePrice($seller_id["price"]);
                            $quoteItem->getProduct()->setIsSuperMode(true);*/

                            $itemExtAttr = $quoteItem->getExtensionAttributes();
                            if ($itemExtAttr === null) {
                                $itemExtAttr = $this->extensionFactory->create();
                            }
                            $itemExtAttr->setSellerId($seller_id["seller_id"]);
                            $quoteItem->setExtensionAttributes($itemExtAttr);
                            //$quoteItem->save();
                            $logger->info("Setproductsellerrrrrrrrrrr API");
                            //$logger->info($quoteItem->getData());
                        //}
                    }
                }
                //$quote->save();
                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->setTotalsCollectedFlag(true)->collectTotals();
                //$quote->calcRowTotal();
                $quote->collectTotals();
            }
        }
        return $this;
    }
}