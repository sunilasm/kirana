<?php
 
namespace Retailinsights\Slider\Plugin;

use \Magento\Quote\Api\Data\CartItemInterface;
use Lof\MarketPlace\Model\SellerProductFactory as SellerProduct;

class PriceFix
{
   /**
     * Quote repository.
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    /**
     * @var SellerProduct
     */
    protected $sellerProduct;
    protected $quoteRepository;
    protected $CartItem;
    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param   $quote
     */
     /**
    * @var EventManager
    */
    private $eventManager;
    protected $request;
     
    public function __construct(\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
    \Magento\Quote\Api\Data\CartItemInterface $CartItem,
    \Magento\Framework\Event\Manager $eventManager,
    \Magento\Framework\Webapi\Rest\Request $request,
    SellerProduct $sellerProduct)
    {
        $this->quoteRepository = $quoteRepository;
        $this->cartItem = $CartItem;
        $this->request = $request;
        $this->sellerProduct = $sellerProduct;
        $this->eventManager = $eventManager;
    }
    
    public function afterSave(\Magento\Quote\Model\Quote\Item\Repository $subject, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
        $logger = new \Zend\Log\Logger(); $logger->addWriter($writer); 
        $logger->info('in PriceFix');
        
        //$post = $this->request->getBodyParams();
        
        $cartId = $cartItem->getQuoteId();
        $sku = $cartItem->getSku();
       // $logger->info($cartId);
       // $logger->info($sku);
        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItems = $quote->getItems();
        
        foreach($quoteItems as $key => $value) {
            if($sku == $quoteItems[$key]->getSku()) {
            

            if($quoteItems[$key]->getPriceType() == 0){
                $price = $this->getStorePrice($quoteItems[$key]->getSellerId(), $quoteItems[$key]->getProductId(), "0");
            } else  {
                $price = $this->getStorePrice($quoteItems[$key]->getSellerId(), $quoteItems[$key]->getProductId(), "1");
            } 
        $quoteItems[$key]->setCustomPrice($price);
        $quoteItems[$key]->setOriginalCustomPrice($price);
        $quoteItems[$key]->save();
        }
        }
       

        $this->quoteRepository->save($quote->collectTotals());
        if($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_NAME']) {
            if($_SERVER['REQUEST_METHOD']!= 'PUT'){
                $this->eventManager->dispatch('promotion_after_add_cart', ['quoteid' => $cartId ]); 
            }
        }

         return $quote->getLastAddedItem();
    }
    
    public function getStorePrice($sellerId, $productId, $storeType) 
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
        $logger = new \Zend\Log\Logger(); $logger->addWriter($writer); 
        $logger->info($sellerId."--".$productId."--".$storeType);

        $storePrice = 0;
        $SellerProd = $this->sellerProduct->create()->getCollection();
        $fltColl = $SellerProd->addFieldToFilter('seller_id', $sellerId)
        ->addFieldToFilter('product_id',$productId);
        $idInfo = $fltColl->getData();
        if(!empty($idInfo)) {
        foreach($idInfo as $info){
        $id = $info['entity_id'];
        }
        $data = $this->sellerProduct->create()->load($id);
        if($storeType == 0){
            $storePrice = $data->getDoorstepPrice(); 
        }else{
            $storePrice = $data->getPickupFromStore(); 
        }
        }
        $logger->info($storePrice);
        
        return $storePrice;
    }
        
}