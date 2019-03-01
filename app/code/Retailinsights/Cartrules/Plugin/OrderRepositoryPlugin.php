<?php
/* File: app/code/Atwix/OrderFeedback/Plugin/OrderRepositoryPlugin.php */

namespace Retailinsights\Cartrules\Plugin;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
           /**
         * @var ProductRepository
         */
        protected $productRepository;
    /**
     * Order Comment field name
     */
    const FIELD_NAME = 'uom';

    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(
        OrderExtensionFactory $extensionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
        )
    {
        $this->extensionFactory = $extensionFactory;
        $this->_productRepository = $productRepository;
    }

    /**
     * Add "order_comment" extension attribute to order data object to make it accessible in API data of order record
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $addAtt = array();
        $info = array();       
        foreach ($order->getAllItems() as $orderItems) {   
            $itemExtAttr = $order->getExtensionAttributes();
                
            $product = $this->_productRepository->getById($orderItems->getProductId());
            $uom = $product->getUom();
            $sku = $orderItems->getSku();
            $addAtt[$sku] = $uom;
            
   
        } 
        $info[] = $addAtt; 
        $data = json_encode($info);

       $orderComment = $order->getData(self::FIELD_NAME);
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
       
       
        $extensionAttributes->setUom($data);
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

  
}