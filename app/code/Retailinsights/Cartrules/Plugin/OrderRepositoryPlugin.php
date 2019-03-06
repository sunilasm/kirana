<?php

/* File: app/code/Atwix/OrderFeedback/Plugin/OrderRepositoryPlugin.php */



namespace Retailinsights\Cartrules\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;

use Magento\Sales\Api\Data\OrderExtensionFactory;

use Magento\Sales\Api\Data\OrderExtensionInterface;

use Magento\Sales\Api\Data\OrderInterface;

use Magento\Sales\Api\Data\OrderSearchResultInterface;



use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;

use Magento\Sales\Api\OrderRepositoryInterface;



/**

 * Class OrderRepositoryPlugin

 */

class OrderRepositoryPlugin

{

         /**

         *@var \Magento\Catalog\Helper\ImageFactory

         */

        protected $productImageHelper;

           /**

         * @var ProductRepository

         */

        protected $productRepository;

    /**

     * Order Comment field name

     */

    const FIELD_NAME = 'unitm';



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

        $this->productImageHelper = $productImageHelper;

        $this->extensionFactory = $extensionFactory;

        $this->_productRepository = $productRepository;

    }
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)

    {

        $addAtt = array();

        $info = array();   
        $addImage = array();

        $infoImage = array(); 

        foreach ($order->getAllItems() as $orderItems) {   

            $itemExtAttr = $order->getExtensionAttributes();
            $product = $this->_productRepository->getById($orderItems->getProductId());
            $uom = $product->getUnitm();
            $weight = round($product->getWeight(), 0);
            $optionId = $product->getUnitm();
                $attribute = $product->getResource()->getAttribute('unitm');
                if ($attribute->usesSource()) {
                    $optionText = $attribute->getSource()->getOptionText($optionId);
                }

            $sku = $orderItems->getSku();
           
            $imageurl =$this->productImageHelper->create()->init($product, 'product_thumbnail_image')->setImageFile($product->getThumbnail())->getUrl();
            $addAtt[$sku] = $weight." ".$optionText;
            $addImage[$sku] = $imageurl;
        } 

        $info[] = $addAtt; 

        $infoImage[] = $addImage;

        $data = json_encode($info);

        $dataImage = json_encode($infoImage);
        
       $orderComment = $order->getData(self::FIELD_NAME);

        $extensionAttributes = $order->getExtensionAttributes();

        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes->setUnitm($data);

        $extensionAttributes->setImageUrl($dataImage);

        $order->setExtensionAttributes($extensionAttributes);
        return $order;

    }


}