<?php

namespace Asm\Setsellerid\Plugin;

class RepositoryPlugin
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeSave(
        \Magento\Quote\Model\Quote\Item\Repository $subject,
        \Magento\Quote\Api\Data\CartItemInterface $cartItem
    )
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Setproductsellerrrrrrrrrrr RepositoryPlugin");
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $seller_id = array();
        if($request->getBodyParams())
        {
            $post = $request->getBodyParams();
            if(isset($post['product']) && isset($post['price'])){
                $logger->info($post['product']);
                $logger->info($post['price']);
            }
        }
        $cartId = $cartItem->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->getShippingAddress();
    }
}

/*

namespace Asm\Setsellerid\Plugin;

class RepositoryPlugin
{
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    public function afterSave(
        \Magento\Quote\Model\Quote\Item\Repository $subject,
        \Magento\Quote\Api\Data\CartItemInterface $cartItem
    )
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Setproductsellerrrrrrrrrrr RepositoryPlugin");
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $seller_id = array();
        if($request->getBodyParams())
        {
            $post = $request->getBodyParams();
            if(isset($post['product']) && isset($post['price'])){
                $logger->info($post['product']);
                $logger->info($post['price']);
                $seller_id["product"] = $post['product'];
                $seller_id["seller_id"] = $post['seller_id'];
                $seller_id["price"] = $post['price'];
            }
        }
        $cartId = $cartItem->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);
        $logger->info($quote->getProductId());
        $logger->info($quote->getId());
           $logger->info("Setproductsellerrrrrrrrrrrwwwwwwww");
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
               $logger->info("Setproductsellerrrrrrrrrrr 222222222 ".$quoteItem->getProductId()."  ".$seller_id["product"]);
                    //if(isset($seller_id["product"]) && isset($seller_id["seller_id"]) && isset($seller_id["price"])){
                       // if($seller_id["product"] == $quoteItem->getProductId()){
                            $logger->info("quoteItem");
                            $logger->info($quoteItem->getProductId());
                            $logger->info($quoteItem->getId());
                            $quoteItem->setCustomRowTotalPrice($seller_id["seller_id"]);
                            $quoteItem->setSellerId($seller_id["seller_id"]);
                            $quoteItem->setCustomPrice($seller_id["price"]);
                            $quoteItem->setOriginalCustomPrice($seller_id["price"]);
                            //$quoteItem->setRawTotal($seller_id["price"]);
                            $quoteItem->setPrice($seller_id["price"]);
                            $quoteItem->setBaseRowTotal($seller_id["price"]);
                            $quoteItem->setBasePrice($seller_id["price"]);
                            
                            $quoteItem->setBaseRawTotal($seller_id["price"]);
                            $quoteItem->setOriginalPrice($seller_id["price"]);
                            $quoteItem->save();
                            $quoteItem->getProduct()->setIsSuperMode(true);
                            $logger->info("Setproductsellerrrrrrrrrrr APIeeeeeeeeeee");
                            $logger->info($quoteItem->getRawTotal());
                            $logger->info("Setproductsellerrrrrrrrrrr APIeeeeeeeeeee111");
                            //$logger->info($quoteItem->getData());
                       // }
                    //}
                }
        $quote->getShippingAddress();
    }
}*/