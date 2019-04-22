<?php
namespace Asm\AdvanceSearch\Model;
use Asm\AdvanceSearch\Api\SearchInterface;
use Lof\MarketPlace\Model\SellerProductFactory as SellerProduct;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Framework\Event\ObserverInterface;
 
class Searchview implements SearchInterface
{
    /**
     * @var ProductRepository
     */
    /**
     * Returns greeting message to user
     *@param ProductRepository $productRepository
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
   
    protected $sellerProduct;
    protected $request;
    protected $_productCollectionFactory;
    protected $_sellerCollection;
    public function __construct(
        ProductRepository $productRepository,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        SellerProduct $sellerProduct,
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Asm\Geolocation\Helper\Data $helperData
    ) {
        $this->productRepository = $productRepository;
        $this->_productRepository = $productRepository;
        $this->itemFactory = $itemFactory;
       $this->sellerProduct = $sellerProduct; 
       $this->request = $request;
       $this->_productCollectionFactory = $productCollectionFactory; 
       $this->_sellerCollection = $sellerCollection;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->helperData = $helperData;
    }
    public function name() {


        $title = $this->request->getParam('title');
        $lat = $this->request->getParam('latitude');
        $lon = $this->request->getParam('longitude');
        $searchtermpara = $this->request->getParam('searchterm');
        $quoteId = $this->request->getParam('quote_id');
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteModel = $objectManager->create('Magento\Quote\Model\Quote');
        $quoteItems = $quoteModel->load($quoteId)->getAllVisibleItems();
        $quoteItemArray = array();
        $i = 1;
        $quoteItemSellerArray = array();
        foreach($quoteItems as $item):
            $quoteItemSellerArray[$item->getSellerId()] = $item->getItemid();
            $quoteItemArray[$item->getSku()]['qty'] = $item->getQty();

             $quoteItemArray[$item->getSku()]['price_type'] = $item->getPriceType();
            //$quoteItemIndexArray[$i] = $item->getItemid();
            $quoteItemIndexArray[$i] = $item->getItemid();
            $i++;

        endforeach;
        $data = array();
        $flag = 0;
        $pages = 0;
        if($searchtermpara){ $searchterm = 0; }else{ $searchterm = 1; }
        if($searchterm){
            if($title){
                $productCollectionResponse = $this->getSearchTermData($title, $lat, $lon);
                $pages = (isset($productCollectionResponse['pages'])) ? $productCollectionResponse['pages'] : 0;
                $productCollectionArray = (isset($productCollectionResponse['items'])) ? $productCollectionResponse['items'] : '';
                //$productCollectionArray = $this->getSearchTermData($title, $lat, $lon);
                 if($productCollectionArray){
                    $data = $productCollectionArray;
                }else{
                    $data = $productCollectionArray;
                }
                $flag = 0;
            }else{
                $flag = 1;
                $data = array('message' => 'Please specify at least one search term');
            }
        }else{
            $productCollectionResponse = $this->getSearchTermData($title = null, $lat, $lon);
                $pages = (isset($productCollectionResponse['pages'])) ? $productCollectionResponse['pages'] : 0;
                $productCollectionArray = (isset($productCollectionResponse['items'])) ? $productCollectionResponse['items'] : '';
                
            //$productCollectionArray = $this->getSearchTermData($title = null,$lat, $lon);
             if($productCollectionArray){
                $data = $productCollectionArray;
            }else{
                $data = $productCollectionArray;
            }
            $flag = 2;
        }
	//print_r($quoteItemArray);//exit;
        if($flag != 1){
            if(count($data)){
        
                foreach($data as $key => $proData):
                     
                    if(array_key_exists($proData['sku'], $quoteItemArray) ){
			//print_r("herre".$key.'--SKU->'.$proData['sku']."<br/>");
                        $data[$key] += ['quote_qty' => $quoteItemArray[$proData['sku']]['qty']];
                        $data[$key]['price_type'] = $quoteItemArray[$proData['sku']]['price_type']; 

                    }else{
			//print_r("okk".$key."<br>");
                        $data[$key] += ['quote_qty' => 0];
                        $data[$key]['price_type'] = NULL;                      
                    }
                endforeach;
            }
        }
        $response = array('pages' => $pages, 'items' => $data);
        return $response = array($response);
    }
    /*
    Get seller id's based on lat & lon.
    */
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
        //print_r($this->_sellerCollection->getCollection()->getData()); exit;
        // filter collection in range of lat and long
        $sellerCollection = $this->_sellerCollection->getCollection()
        ->setOrder('position','ASC')
        ->addFieldToFilter('geo_lat',array('gteq'=>$minLat))
        ->addFieldToFilter('geo_lng',array('gteq'=>$minLon))
        ->addFieldToFilter('geo_lat',array('lteq'=>$maxLat))
        ->addFieldToFilter('geo_lng',array('lteq'=>$maxLon))
        ->addFieldToFilter('status',1);
        //->addFieldToFilter('group_id',2);
        // get Seller id's
        $sellerData = $sellerCollection->getData();

        foreach($sellerData as $seldata):
            $selerIdArray[] = $seldata['seller_id'];
        endforeach;
        //print_r($selerIdArray); exit;
        return  $selerIdArray;
    }
    public function getSearchTermData($title, $lat, $lon)
    {
        $productCollectionArray = array();
        $sellerProductsArray = array();
        $arratAttributes = array();
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        if($lat != '' && $lon != '')
        {
            $productCollectionArray = array();
            $ranageSeller = $this->getInRangeSeller($lat, $lon);
            $sellerCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $ranageSeller));
        }
        $tempSellerProductArray = array();
        $i=0;
        foreach($sellerCollection as $seller)
        {
            $tempSellerProductArray[$seller['product_id']][] = $seller['seller_id'];
            $tempSellerProductIdArray[] = $seller['product_id'];
        }
        if(count($tempSellerProductArray))
        {
            $collection->addFieldToFilter('entity_id', array('in' => $tempSellerProductIdArray));
        }

        $collection->addAttributeToSort('price', 'asc');
        // check current page
        $current_page = $this->request->getParam('current_page');
        if($current_page == '')
        {
            $current_page = 1;
        }else{
            $current_page = $this->request->getParam('current_page');
        }
        // Check page size
        $page_size = $this->request->getParam('page_size');
        if($page_size == '')
        {
            $page_size = 10;
        }
        else
        {
            $page_size = $this->request->getParam('page_size');
        }
        $product_count = count($sellerCollection);
        $total_pages = 0;
        if($product_count > 0)
        {
            $total_pages = round($product_count/$page_size);
        }    
        
        if($title != null)
        {
            $collection->addFieldToFilter([['attribute' => 'name', 'like' => '%'.$title.'%']]);
        }
        $product_count = count($collection->getData()); 
        $collection->setCurPage($current_page)->setPageSize($page_size);
        $max_product_list = ($current_page * $page_size);
        $min_product_list = (($current_page - 1) * $page_size);
        $count_flag = 0;
        if($max_product_list <= $product_count)
        {
            $count_flag = 1;
        }
        elseif($min_product_list < $product_count && $max_product_list > $product_count)
        {
            $count_flag = 1;
        }
        $sellerNameArray = array();
        $sellerCollection = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $ranageSeller));
        foreach($sellerCollection as $seller):
            $sellerNameArray[$seller->getId()] = $seller->getName();
        endforeach;
        if($count_flag)
        {
            foreach ($collection as $product){
                $productCollectionTemp = array();  
                $productCollectionTemp = $product->getData();
                foreach ($tempSellerProductArray as $key => $value) 
                {
                    if($productCollectionTemp['entity_id'] == $key)
                    {
                        foreach($value as $seller_index => $seller_id)
                        {
                            $productCollectionTemp['seller_name'] = $sellerNameArray[$seller_id];
                        $productCollectionTemp['seller_id'] = $seller_id;
                        $SellerProd = $this->sellerProduct->create()->getCollection();
                        $fltColl = $SellerProd->addFieldToFilter('seller_id', $seller_id)
                                ->addFieldToFilter('product_id', $productCollectionTemp['entity_id']);
                        $data = $this->sellerProduct->create()->load($fltColl->getData()[0]['entity_id']);
                    
                        $productCollectionTemp['unitm'] = (round($product->getWeight(),0)).' '.($product->getUomLabel());
                        $productCollectionTemp['price_type'] =  $data->getPriceType();
                        $productCollectionTemp['doorstep_price'] =  $data->getDoorstepPrice();
                        $productCollectionTemp['pickup_from_store'] =  $data->getPickupFromStore();
                        $productCollectionTemp['pickup_from_nearby_store'] =  $data->getPickupFromNearbyStore();
                        $productCollectionArray[] = $productCollectionTemp;
                        }
                    }
                }
            }
        }
        $response = array('pages' => $total_pages, 'items' => $productCollectionArray);
        return $response;
    }
}