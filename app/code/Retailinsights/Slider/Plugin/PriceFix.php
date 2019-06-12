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
     
    public function __construct(\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
    \Magento\Quote\Api\Data\CartItemInterface $CartItem,
    SellerProduct $sellerProduct)
    {
        $this->quoteRepository = $quoteRepository;
        $this->cartItem = $CartItem;
        $this->sellerProduct = $sellerProduct;
    }
    
    public function afterSave(\Magento\Quote\Model\Quote\Item\Repository $subject, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
        $logger = new \Zend\Log\Logger(); $logger->addWriter($writer); 
        $logger->info('PriceFix');
        
        $cartId = $cartItem->getQuoteId();
        $sku = $cartItem->getSku();
        $logger->info($cartId);
        $logger->info($sku);
        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItems = $quote->getItems();
        
        foreach($quoteItems as $key => $value) {
            if($sku == $quoteItems[$key]->getSku()) {
            
$logger->info($quoteItems[$key]->getPrice()."-----".$quoteItems[$key]->getQty()."-----".$quoteItems[$key]->getSku()."--".$quoteItems[$key]->getSellerId()."-----".$quoteItems[$key]->getProductId()); 

            if($quoteItems[$key]->getPriceType() == 0){
                $price = $this->getStorePrice($quoteItems[$key]->getSellerId(), $quoteItems[$key]->getProductId(), "0");
            } else  {
                $price = $this->getStorePrice($quoteItems[$key]->getSellerId(), $quoteItems[$key]->getProductId(), "1");
            } 
        $quoteItems[$key]->setCustomPrice($price);
        $quoteItems[$key]->setOriginalCustomPrice($price);
        $quoteItems[$key]->save();
$logger->info($quoteItems[$key]->getPrice()."-----".$quoteItems[$key]->getQty()."-----".$quoteItems[$key]->getSellerId()."-----".$quoteItems[$key]->getProductId()); 
        }
        }
        
        $this->quoteRepository->save($quote->collectTotals());
        
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