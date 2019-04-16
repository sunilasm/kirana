<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Asm\Setsellerid\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository extends \Magento\Quote\Model\Quote\Item\Repository
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
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory
     * @param CartItemProcessorInterface[] $cartItemProcessors
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory,
        array $cartItemProcessors = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->itemDataFactory = $itemDataFactory;
        $this->cartItemProcessors = $cartItemProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {

        $output = [];
        $custom_array = [];
        $custom_array1 = [];
        $total_output = [];
        $custom_options = [];

        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        /** @var  \Magento\Quote\Model\Quote\Item  $item */
        foreach ($quote->getAllVisibleItems() as $item) {
        	        $i=0;
	            $item = $this->getCartItemOptionsProcessor()->addProductOptions($item->getProductType(), $item);
    	        $custom_options = $this->getCartItemOptionsProcessor()->applyCustomOptions($item);
    	        $output[] = $custom_options;

        	if($item['kirana_qty']!=null && $item['org_store_qty']!=null ){
        		for ($j=0; $j <2 ; $j++) {
		        	if($j==0){
			        	$custom_array[$i]['item_id'] = $item['item_id'];
			        	$custom_array[$i]['sku'] = $item['sku'];
			        	$custom_array[$i]['qty'] = $item['qty'];
			        	$custom_array[$i]['name'] = $item['name'];
			        	$custom_array[$i]['product_type'] = $item['product_type'];
			        	$custom_array[$i]['quote_id'] = $item['quote_id'];			        	
			        	$custom_array[$i]['extension_attributes']['product_id'] = $item['product_id'];
			        	$custom_array[$i]['extension_attributes']['image_url'] = $this->getImageUrl($item['product_id']);
			        	$custom_array[$i]['extension_attributes']['unitm'] = $this->getUnitm($item['product_id']);
			        	$custom_array[$i]['extension_attributes']['seller_kirana_name'] = $this->getSellerName($item['seller_kirana_id']);
			        	$custom_array[$i]['extension_attributes']['seller_kirana_id'] = $item['seller_kirana_id'];
			        	$custom_array[$i]['extension_attributes']['kirana_qty'] = $item['kirana_qty'];
			        	$total_output[]= $custom_array;
		        	}
		        	elseif($j==1){
			        	$custom_array1[$i]['item_id'] = $item['item_id'];
			        	$custom_array1[$i]['sku'] = $item['sku'];
			        	$custom_array1[$i]['qty'] = $item['qty'];
			        	$custom_array1[$i]['name'] = $item['name'];
			        	$custom_array1[$i]['product_type'] = $item['product_type'];
			        	$custom_array1[$i]['quote_id'] = $item['quote_id'];
			        	$custom_array1[$i]['extension_attributes']['product_id'] = $item['product_id'];
			        	$custom_array1[$i]['extension_attributes']['image_url'] = $this->getImageUrl($item['product_id']);
			        	$custom_array1[$i]['extension_attributes']['unitm'] = $this->getUnitm($item['product_id']);
			        	$custom_array1[$i]['extension_attributes']['seller_org_store_name'] = $this->getSellerName($item['seller_org_store_id']);
			        	$custom_array1[$i]['extension_attributes']['seller_org_store_id'] = $item['seller_org_store_id'];
			        	$custom_array1[$i]['extension_attributes']['org_store_qty'] = $item['org_store_qty'];
			        	$total_output[] = $custom_array1;
		        	}
        		}
        	
        	}
        	elseif($item['kirana_qty']==null){
	        	$custom_array[$i]['item_id'] = $item['item_id'];
	        	$custom_array[$i]['sku'] = $item['sku'];
	        	$custom_array[$i]['qty'] = $item['qty'];
	        	$custom_array[$i]['name'] = $item['name'];
	        	$custom_array[$i]['product_type'] = $item['product_type'];
	        	$custom_array[$i]['quote_id'] = $item['quote_id'];
	        	$custom_array[$i]['extension_attributes']['product_id'] = $item['product_id'];
	        	$custom_array[$i]['extension_attributes']['image_url'] = $this->getImageUrl($item['product_id']);
	        	$custom_array[$i]['extension_attributes']['unitm'] = $this->getUnitm($item['product_id']);
	        	$custom_array[$i]['extension_attributes']['seller_org_store_name'] = $this->getSellerName($item['seller_org_store_id']);
	        	$custom_array[$i]['extension_attributes']['seller_org_store_id'] = $item['seller_org_store_id'];
	        	$custom_array[$i]['extension_attributes']['org_store_qty'] = $item['org_store_qty'];
        		$total_output[] = $custom_array;
        	}
        	elseif($item['org_store_qty']==null){
	        	$custom_array[$i]['item_id'] = $item['item_id'];
	        	$custom_array[$i]['sku'] = $item['sku'];
	        	$custom_array[$i]['qty'] = $item['qty'];
	        	$custom_array[$i]['name'] = $item['name'];
	        	$custom_array[$i]['product_type'] = $item['product_type'];
	        	$custom_array[$i]['quote_id'] = $item['quote_id'];	        	
	        	$custom_array[$i]['extension_attributes']['product_id'] = $item['product_id'];
	        	$custom_array[$i]['extension_attributes']['image_url'] = $this->getImageUrl($item['product_id']);
	        	$custom_array[$i]['extension_attributes']['unitm'] = $this->getUnitm($item['product_id']);
	        	$custom_array[$i]['extension_attributes']['seller_kirana_name'] = $this->getSellerName($item['seller_kirana_id']);
	        	$custom_array[$i]['extension_attributes']['seller_kirana_id'] = $item['seller_kirana_id'];
	        	$custom_array[$i]['extension_attributes']['kirana_qty'] = $item['kirana_qty'];
	        	$total_output[] = $custom_array;
        	}
        	else{
        		$total_output = $output;
        	}
            $i++;
        }
        return $total_output;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $cartId = $cartItem->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);

        $quoteItems = $quote->getItems();
        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote);
        $quote->collectTotals();
        return $quote->getLastAddedItem();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cartId, $itemId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $this->quoteRepository->save($quote);
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

    private function getSellerName($sellerid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $seller = $objectManager->get ( 'Lof\MarketPlace\Model\Seller' )->load ( $sellerid, 'seller_id' );
        return $seller->getData('name');
    }

    private function getImageUrl($product_id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ('Magento\Catalog\Api\ProductRepositoryInterfaceFactory' )->create()->getById($product_id);
        $imageurl =$objectManager->get ('Magento\Catalog\Helper\ImageFactory' )->create()->init($product, 'product_thumbnail_image')->setImageFile($product->getThumbnail())->getUrl();
        return $imageurl;
    }

    private function getUnitm($product_id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ('Magento\Catalog\Api\ProductRepositoryInterfaceFactory' )->create()->getById($product_id);
        return (float)$product->getWeight()." ".$product->getUomLabel();
    }
}
