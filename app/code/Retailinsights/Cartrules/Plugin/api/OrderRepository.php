<?php

namespace Retailinsights\Cartrules\Plugin\api;

use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;


class OrderRepository
{

    const FIELD_NAME = 'unitm';

/**

         *@var \Magento\Catalog\Helper\ImageFactory

         */

        protected $productImageHelper;

     /**

         * @var ProductRepository

         */

        protected $productRepository;

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
        ProductImageHelper $productImageHelper,
        OrderExtensionFactory $extensionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
 )
    {
        $this->extensionFactory = $extensionFactory;
         $this->_productRepository = $productRepository;
         $this->productImageHelper = $productImageHelper;
    }

    
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
   
             $orders = $searchResult->getItems();



        foreach ($orders as &$order) {
                $addAtt = array();
                $info = array(); 
                $addImage = array();
                $infoImage = array();   
            
            foreach($order->getItems() as $items){
             //$items->getProductId()
            $product = $this->_productRepository->getById($items->getProductId());

              $uom = $product->getUnitm();
               $weight = round($product->getWeight(), 0);
                $optionId = $product->getUnitm();
                $attribute = $product->getResource()->getAttribute('unitm');
                if ($attribute->usesSource()) {
                    $optionText = $attribute->getSource()->getOptionText($optionId);
                }
             $sku = $items->getSku();
             $imageurl =$this->productImageHelper->create()->init($product, 'product_thumbnail_image')->setImageFile($product->getThumbnail())->getUrl();
            $addAtt[$sku] = $weight." ".$optionText;
            $addImage[$sku] = $imageurl;

             $addAtt[$sku] = $weight." ".$optionText;

              
        
            
            }
            $info[] = $addAtt; 
            $infoImage[] = $addImage;
            $data = json_encode($info);
            $dataImage = json_encode($infoImage);
            $extensionAttributes = $items->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
                        $extensionAttributes->setUnitm($data);
                        $extensionAttributes->setImageUrl($dataImage);
            $order->setExtensionAttributes($extensionAttributes);
           // $deliveryType = $order->getData(self::DELIVERY_TYPE);
            
        }

        return $searchResult;
    }
}