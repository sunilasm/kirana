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

    public function orgreailer() 
    {
        //print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $quote = $this->quoteFactory->create()->load($post['quote_id']);
        $flag = 0;
        $sellerData = '';
        if($post['latitude'] != '' && $post['longitude'] != '')
        {
            $ranageSeller = $this->getInRangeSeller($post['latitude'], $post['longitude']);
            $items = $quote->getAllItems();
            $response = array();
            $i = 0;
            foreach($ranageSeller as $orgretailer)
            {
                $tempSellerProductArray = array();
                $tempSellerProductIdArray = array();
                $presentProducts = array();
                $seller_products = array();
                $seller_productsNew = array();
                $productPresentCollArray = array();
                $productNotPresentCollArray = array();
                $presentSubTotalArray = array();
                // Seller Product Collection
                $sellerCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $orgretailer));
                foreach($sellerCollection as $sellcoll):
                    $tempSellerProductArray[$sellcoll['product_id']][] = $sellcoll['seller_id'];
                    $tempSellerProductIdArray[] = $sellcoll['product_id'];
                    $seller_products[$sellcoll->getProduct_id()] = $sellcoll->getData();
                endforeach;

                // Get Orgnized Retailer deatils
                $sellerCollectionDetails = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $orgretailer));
                $sellerData = array();
                foreach($sellerCollectionDetails as $sellcoll):
                    $sellerData = $sellcoll->getData();
                endforeach;

                // Quote Data
                $cartSubTotal = 0;
		        $cartPresentProducts = 0;
                $cartNotPresentProducts = 0;               
	            foreach ($items as $item) 
                {
                    $collection = $this->_productCollectionFactory->create();
                    $collection->addAttributeToSelect('*');
                    $collection->addAttributeToSort('price', 'asc');
                    $produt_found = 0;
                    // print_r($item->getSku());exit;
                    // If seller have products.
                    if(count($tempSellerProductArray))
                    {
                        $collection->addFieldToFilter('entity_id', array('in' => $tempSellerProductIdArray));

                        if($item->getName() != null){
                            $collection->addFieldToFilter([['attribute' => 'sku', 'like' => '%'.$item->getSku().'%']]);
                        }
                        
                        $products = $collection->getData();
                        
                        foreach ($collection as $product)
                        {
                            if(!$produt_found){
                                $productCollectionData = $product->getData();

                                if(array_key_exists($product->getId(), $seller_products)){
                                    $productCollectionData['pickup_from_store'] = $seller_products[$product->getId()]['pickup_from_store'];
                                }
                                $productCollectionData['quote_qty'] = $item->getQty();
                                $collectionNew['seller_id'] = $item->getSeller_id();
                                $productPresentCollArray[] = $productCollectionData;
				                //print_r($seseller_products); exit;
                                if(isset($seller_products[$product->getId()]['pickup_from_store']))
                                {
                                    $cartSubTotal += ($seller_products[$product->getId()]['pickup_from_store'] * $item->getQty());
                                }
                                $cartPresentProducts += $item->getQty();
                                $produt_found = 1;
                            }
                            
                        }
                    }
                    
                    // If product not present with store.
                    if($produt_found == 0)
                    {
                        // print_r($item->getProduct_id());exit;
                        $sellerCollectionNew = $this->_sellerProductCollection->getCollection()->addFieldToFilter('product_id', array('in' => $item->getProduct_id()));
                        // print_r($sellerCollectionNew->getData());exit;
                        foreach($sellerCollectionNew as $sellcoll):
                            $seller_productsNew[$sellcoll->getProduct_id()] = $sellcoll->getData();
                        endforeach;

                        $collectionNew = $this->_productCollectionFactory->create();
                        $collectionNew->addAttributeToSelect('*');
                        $collectionNew->addFieldToFilter('entity_id', ['in' => $item->getProduct_id()]);

                        foreach($collectionNew as $product):
                            // print_r($product->getId());
                            // print_r($seller_products);
                            $collectionNew = $product->getData();
                            $collectionNew['quote_qty'] = $item->getQty();
                            $collectionNew['seller_id'] = $item->getSeller_id();
                            if(array_key_exists($product->getId(), $seller_productsNew)){
                                $collectionNew['pickup_from_store'] = $seller_productsNew[$product->getId()]['pickup_from_store'];
                            }
			    $cartNotPresentProducts += $item->getQty();
                            $productNotPresentCollArray[] = $collectionNew;
                        endforeach;
                    }           
                }

                $cartSummeryArray = array('total_item_count' => ($cartPresentProducts + $cartNotPresentProducts), 'present_item_count' => $cartPresentProducts, 'not_present_item_count' => $cartNotPresentProducts, 'sub_total' => number_format((float)$cartSubTotal, 2, '.', ''));

                $response[$i]['store'] = $sellerData;
                $response[$i]['present_data'] = $productPresentCollArray;
                $response[$i]['not_present_data'] = $productNotPresentCollArray;
                $response[$i]['cart_summary'] = $cartSummeryArray;
                $i++;
            }
        }
        if(count($response))
        {
            $response = $this->sort_by_present_item_count($response);
            $final_response = array();
            $org_return_count = 3;
            for($i=0; $i<$org_return_count; $i++)
            {
                $final_response[$i] = $response[$i];
            }
            $response = $final_response;
        }
        $data = $response;
        return $data;
    }

    private function sort_by_present_item_count($array) 
    {
        $sorter = array();
        $ret = array();
        reset($array);
        $count_array = array();
        foreach($array as $key => $store)
        {
            $count_array[$key] = $store['cart_summary']['present_item_count'];
        }
        arsort($count_array);
        $response = array();
        foreach($count_array as $key => $value)
        {
            $response[] = $array[$key];
        }

        for($i=0; $i<count($response); $i++)
        {
            $temp = $i+1;
            if($temp < count($response))
            { 
                if($response[$i]['cart_summary']['present_item_count'] > 0)
                {
                    if($response[$i]['cart_summary']['present_item_count'] == $response[$temp]['cart_summary']['present_item_count'])
                    {
                        if($response[$i]['cart_summary']['sub_total'] > $response[$temp]['cart_summary']['sub_total'])
                        {
                            $temp_array = $response[$i];
                            $response[$i] = $response[$temp];
                            $response[$temp] = $temp_array;
                        }
                    }
                }
            }
        }
        return $response;
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
        //->setPageSize(3);
        // get Seller id's
        $sellerData = $sellerCollection->getData();


        foreach($sellerData as $seldata):
            $selerIdArray[] = $seldata['seller_id'];
            
        endforeach;
	//print_r($selerIdArray);exit;
        return  $selerIdArray;
    }
   
}
