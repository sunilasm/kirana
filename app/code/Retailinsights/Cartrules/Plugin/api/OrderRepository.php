<?php
namespace Retailinsights\Cartrules\Plugin\api;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Lof\MarketPlace\Model\SellerProductFactory as SellerProduct;

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
    protected $sellerProduct;

    protected $itemextensionFactory;
    public function __construct(
        SellerProduct $sellerProduct,
        ProductImageHelper $productImageHelper,
        OrderExtensionFactory $extensionFactory,
        OrderItemExtensionFactory $itemextensionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository

    )
    {
        $this->itemextensionFactory = $itemextensionFactory;
        $this->extensionFactory = $extensionFactory;
        $this->_productRepository = $productRepository;
        $this->productImageHelper = $productImageHelper;
        $this->sellerProduct = $sellerProduct;

    }
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
       
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
                $addAtt = array();
                $info = array(); 
                $addImage = array();
                $infoImage = array(); 
                $orderItems = $order->getItems();
                $grandTotal = 0;
                $chosenprice = 0;
            foreach($orderItems as $items){
            $priceType = $items->getPriceType();
          
            $product = $this->_productRepository->getById($items->getProductId());
            $SellerProd = $this->sellerProduct->create()->getCollection();
             $fltColl = $SellerProd->addFieldToFilter('seller_id', $items->getSellerId())
                        ->addFieldToFilter('product_id', $items->getProductId());
                        $idInfo = $fltColl->getData();
                       
                if(!empty($idInfo)){
                        foreach($idInfo as $info){
                            $id = $info['entity_id'];
                        }
                         $data = $this->sellerProduct->create()->load($id);
                         if($priceType == '0'){  
                          $chosenprice = $data->getDoorstepPrice();
                        } else if ($priceType == '1'){
                          $chosenprice  = $data->getPickupFromStore();
                        } else {
                          $chosenprice = $data->getPickupFromNearbyStore();
                        }
                  } 
              $uom = $product->getUnitm();
               $weight = round($product->getWeight(), 0);
                $optionId = $product->getUnitm();
                $attribute = $product->getResource()->getAttribute('unitm');
                if ($attribute->usesSource()) {
                    $optionText = $attribute->getSource()->getOptionText($optionId);
                }
             $sku = $items->getSku();
                    
           $data = $items->getProductOptions();
           $qty = $data['info_buyRequest']['qty'];

           $rowTotal = $qty * $chosenprice;

             $imageurl =$this->productImageHelper->create()->init($product, 'product_thumbnail_image')->setImageFile($product->getThumbnail())->getUrl();
             $addAtt[$sku] = $weight." ".$optionText;
             $addImage[$sku] = $imageurl;
            $extensionAttributes = $items->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->itemextensionFactory->create();
            $extensionAttributes->setUnitm($optionText);
            $extensionAttributes->setImageUrl($imageurl);
            $extensionAttributes->setPriceType($priceType);
            $extensionAttributes->setExtnRowTotal($rowTotal);
            $extensionAttributes->setChosenPrice($chosenprice);
            $items->setExtensionAttributes($extensionAttributes);

            $grandTotal += $rowTotal;
             
            }
            $orderextensionAttributes = $order->getExtensionAttributes();
            $orderextensionAttributes = $orderextensionAttributes ? $orderextensionAttributes : $this->extensionFactory->create();
            $orderextensionAttributes->setExtnGrandTotal($grandTotal);
            $order->setExtensionAttributes($orderextensionAttributes);

            $info[] = $addAtt; 
            $infoImage[] = $addImage;
            
        }
        return $searchResult;
    }
}