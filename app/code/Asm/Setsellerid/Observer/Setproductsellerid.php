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

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $seller_id = array();
        $price_type = array();
        if($request->getBodyParams())
        {
            $post = $request->getBodyParams();
          
            if(isset($post['product_id'])){
                $seller_id["product_id"] = $post['product_id'];
                $seller_id["seller_id"] = $post['seller_id'];
                $seller_id["price_type"] = $post['price_type'];
                /*$seller_id["seller_kirana_id"] = $post["seller_kirana_id"];
                $seller_id["seller_org_store_id"] = $post["seller_org_store_id"];
                $seller_id["org_store_qty"] = $post["org_store_qty"];
                $seller_id["kirana_qty"] = $post["kirana_qty"];*/

                $quote = $observer->getQuote();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerSession = $objectManager->create('Magento\Customer\Model\Session');
                foreach ($quote->getAllItems() as $quoteItem) {
                    if($seller_id["product_id"]){
                        if($seller_id["product_id"] == $quoteItem->getProductId()){
                            $quoteItem->setSellerId($seller_id["seller_id"]);
                            $quoteItem->setPriceType($seller_id["price_type"]);
                           /* $quoteItem->setSellerKiranaId($seller_id["seller_kirana_id"]);
                            $quoteItem->setSellerOrgStoreId($seller_id["seller_org_store_id"]);
                            $quoteItem->setOrgStoreQty($quoteItem->getOrgStoreQty()+$seller_id["org_store_qty"]);
                            $quoteItem->setKiranaQty($quoteItem->getKiranaQty()+$seller_id["kirana_qty"]);*/
                            $quoteItem->save();

                            $itemExtAttr = $quoteItem->getExtensionAttributes();
                            if ($itemExtAttr === null) {
                                $itemExtAttr = $this->extensionFactory->create();
                            }
                            
                            $itemExtAttr->setSellerId($seller_id["seller_id"]);
                            $itemExtAttr->setPriceType($seller_id["price_type"]);
                            // $itemExtAttr->setSellerKiranaId($seller_id["seller_kirana_id"]);
                            // $itemExtAttr->setSellerOrgStoreId($seller_id["seller_org_store_id"]);
                            // $itemExtAttr->setOrgStoreQty($quoteItem->getOrgStoreQty()+$seller_id["org_store_qty"]);
                            // $itemExtAttr->setKiranaQty($quoteItem->getKiranaQty()+$seller_id["kirana_qty"]);

                            $quoteItem->setExtensionAttributes($itemExtAttr);
                        }
                    }
                }
                $quote->save();
            }
        }
        return $this;
    }
}
