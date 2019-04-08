<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\OrgnizedretailerInterface;
 
class Orgreailerview implements OrgnizedretailerInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $_sellerCollection;
    protected $_productCollectionFactory;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Asm\Geolocation\Helper\Data $helperData,
       \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
       $this->request = $request;
       $this->_sellerCollection = $sellerCollection;
       $this->helperData = $helperData;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->quoteFactory = $quoteFactory;
       $this->_productCollectionFactory = $productCollectionFactory;
    }

    public function orgreailer() {
        //print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $quote = $this->quoteFactory->create()->load($post['quote_id']);
        $flag = 0;
        $sellerData = array();
        if($post['latitude'] != '' && $post['longitude'] != ''){
            $productPresentCollArray = array();
            $productNotPresentCollArray = array();
            $ranageSeller = $this->getInRangeSeller($post['latitude'], $post['longitude']);
             // print_r($ranageSeller);exit;
            $items = $quote->getAllItems();

            //foreach($ranageSeller as $rangesellerData):
            $quoteSellerId = array();
            foreach ($items as $item) {
                $quoteSellerId[] = $item->getSellerId();
            }
            foreach ($ranageSeller as $sellerData) {
                if(in_array($sellerData, $quoteSellerId)){
                    $sellerCollection = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id',$sellerData);
                    $sellerDataNew[] = $sellerCollection->getData();
                    foreach ($collection as $product){
                        $productPresentCollArray[] = $product->getData();
                        $flag = 1;
                   }
                }else{
                    foreach ($collection as $product){
                        $productNotPresentCollArray[] = $product->getData();
                        $flag = 1;
                     }
                }
            }
            // print_r($sellerDataNew);exit;
            // foreach ($items as $item) {
            //     $collection = $this->_productCollectionFactory->create();
            //     $collection->addAttributeToSelect('*');
            //     $collection->addFieldToFilter('entity_id', ['in' => $item->getProductId()]);
            //     if (in_array($item->getSellerId(), $ranageSeller))
            //     {
            //       foreach ($collection as $product){
            //             $productPresentCollArray[] = $product->getData();
            //             $flag = 1;
            //        }
            //     }else{
            //         foreach ($collection as $product){
            //             $productNotPresentCollArray[] = $product->getData();
            //             $flag = 1;
            //          }
            //     }
            // }

            //endforeach;
            // print_r($sellerData);exit;
            $cartSummeryArray = array('total_item_count' => $quote->getItemsCount(), 'present_item_count' => count($productPresentCollArray), 'not_present_item_count' => count($productNotPresentCollArray), 'sub_total' => $quote->getSubtotal());
        }

        $dataNew = array("orgnized_details" => $sellerData,"present_data" => $productPresentCollArray,"not_present_data" => $productNotPresentCollArray,"cart_summry" => $cartSummeryArray);
         $data = array($dataNew);
        return $data;
    }

    public function getInRangeSeller($lat, $lon){
        $selerIdArray = array();
        $rangeSetting = $this->helperData->getGeneralConfig('enable');
        $rangeInKm = $this->helperData->getGeneralConfig('range_in_km');
        if($rangeSetting == 1){
            if($rangeInKm){
                $distance = $rangeInKm; //your distance in KM
            }else{
                $distance = 1; //your distance in KM
            }
        }else{
            $distance = 1; //your distance in KM
        }
        
        $R = 6371; //constant earth radius. You can add precision here if you wish
        $maxLat = $lat + rad2deg($distance/$R);
        $minLat = $lat - rad2deg($distance/$R);
        $maxLon = $lon + rad2deg(asin($distance/$R) / cos(deg2rad($lat)));
        $minLon = $lon - rad2deg(asin($distance/$R) / cos(deg2rad($lat)));
        // filter collection in range of lat and long
        $sellerCollection = $this->_sellerCollection->getCollection()
        ->setOrder('position','ASC')
        ->addFieldToFilter('group_id',2)
        ->addFieldToFilter('geo_lat',array('gteq'=>$minLat))
        ->addFieldToFilter('geo_lng',array('gteq'=>$minLon))
        ->addFieldToFilter('geo_lat',array('lteq'=>$maxLat))
        ->addFieldToFilter('geo_lng',array('lteq'=>$maxLon))
        ->addFieldToFilter('status',1);
        // get Seller id's
        $sellerData = $sellerCollection->getData();


        foreach($sellerData as $seldata):
            $selerIdArray[] = $seldata['seller_id'];
            
        endforeach;
        return  $selerIdArray;
    }
   
}
