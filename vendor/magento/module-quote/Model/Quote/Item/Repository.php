<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Model\Quote\Item;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements \Magento\Quote\Api\CartItemRepositoryInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Product repository.
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $itemDataFactory;

    /**
     * @var CartItemProcessorInterface[]
     */
    protected $cartItemProcessors;

    /**
     * @var CartItemOptionsProcessor
     */
    private $cartItemOptionsProcessor;
    
    /**
    * @var EventManager
    */
      private $eventManager;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory
     * @param CartItemProcessorInterface[] $cartItemProcessors
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory,
        \Magento\Framework\Event\Manager $eventManager,
        array $cartItemProcessors = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->itemDataFactory = $itemDataFactory;
        $this->cartItemProcessors = $cartItemProcessors;
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        //$this->eventManager->dispatch('retailinsights_hyloshapi_observer_cartobserver',['quote'=>$this->quoteRepository->getActive($cartId)]);
        $output = [];
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        /** @var  \Magento\Quote\Model\Quote\Item  $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $item = $this->getCartItemOptionsProcessor()->addProductOptions($item->getProductType(), $item);
            $output[] = $this->getCartItemOptionsProcessor()->applyCustomOptions($item);
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $cartId = $cartItem->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
        $logger = new \Zend\Log\Logger(); $logger->addWriter($writer); 
        $logger->info('Cart Save');

        
        $quoteItems = $quote->getItems();
        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote);
        $quote->collectTotals();
        
        // $quote = $this->quoteRepository->getActive($cartId);
        // $quoteItems = $quote->getItems();
        // $price =0;
        // foreach($quoteItems as $key => $value) {
        //     if ($key == 0) {
        //         $price =11;
        //     } else {
        //         $price =12;
        //     }
        // $quoteItems[$key]->setCustomPrice($price);
        // $quoteItems[$key]->setOriginalCustomPrice($price);
        // $quoteItems[$key]->save();
        // $logger->info($quoteItems[$key]->getPrice()."-----".$quoteItems[$key]->getQty()); 
        // }
        // $this->quoteRepository->save($quote->collectTotals());
        $logger->info('Cart after totals');
        return $quote->getLastAddedItem();
    }

     
    public function deleteById($cartId, $itemId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pvn.log'); 
        $logger = new \Zend\Log\Logger(); $logger->addWriter($writer); 
        $logger->info('Cart DElete');
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $this->quoteRepository->save($quote);
            $this->eventManager->dispatch('promotion_after_add_cart', ['quoteid' => $cartId ]); 
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not remove item from quote'));
        }

        return true;
    }

    /**
     * @return CartItemOptionsProcessor
     * @deprecated 100.1.0
     */
    private function getCartItemOptionsProcessor()
    {
        if (!$this->cartItemOptionsProcessor instanceof CartItemOptionsProcessor) {
            $this->cartItemOptionsProcessor = ObjectManager::getInstance()->get(CartItemOptionsProcessor::class);
        }

        return $this->cartItemOptionsProcessor;
    }
}
