<?php
namespace Asm\Search\Model;
use Asm\Search\Api\SearchInterface;
 
class Searchview implements SearchInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $_productCollectionFactory;
    protected $_sellerCollection;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection
    ) {
       $this->request = $request;
       $this->_productCollectionFactory = $productCollectionFactory; 
       $this->_sellerCollection = $sellerCollection;
       $this->_sellerProductCollection = $sellerProductCollection;

    }

    public function name() {
        $title = $this->request->getParam('title');
        $lat = $this->request->getParam('latitude');
        $lon = $this->request->getParam('longitude');
        $searchtermpara = $this->request->getParam('searchterm');
        if($searchtermpara){ $searchterm = 0; }else{ $searchterm = 1; }
        if($searchterm){
            if($title){
                $productCollectionArray = $this->getSearchTermData($title, $lat, $lon);
                 if($productCollectionArray){
                    $data = $productCollectionArray;
                }else{
                    $data = $productCollectionArray;
                }
            }else{
                 $data = array('message' => 'Please specify at least one search term');
            }
        }else{
            $productCollectionArray = $this->getSearchTermData($title = null,$lat, $lon);
             if($productCollectionArray){
                $data = $productCollectionArray;
            }else{
                $data = $productCollectionArray;
            }
        }
        return $data;
    }
    /*
    Get seller id's based on lat & lon.
    */
    public function getInRangeSeller($lat, $lon){
        $selerIdArray = array();
        $distance = 1; //your distance in KM
        $R = 6371; //constant earth radius. You can add precision here if you wish

        $maxLat = $lat + rad2deg($distance/$R);
        $minLat = $lat - rad2deg($distance/$R);
        $maxLon = $lon + rad2deg(asin($distance/$R) / cos(deg2rad($lat)));
        $minLon = $lon - rad2deg(asin($distance/$R) / cos(deg2rad($lat)));

        // filter collection in range of lat and long
        $sellerCollection = $this->_sellerCollection->getCollection()
        ->setOrder('position','ASC')
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
	//print_r($selerIdArray);exit;
        return  $selerIdArray;
    }

    public function getSearchTermData($title, $lat, $lon){
        $productCollectionArray = array();
            $sellerProductsArray = array();
            $arratAttributes = array();
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            // Check lat and lng is set or not
            if($lat != '' && $lon != ''){
                $productCollectionArray = array();
                $ranageSeller = $this->getInRangeSeller($lat, $lon);
                $collection->addFieldToFilter('seller_id', array('in' => $ranageSeller));
            }
            $collection->addAttributeToSort('price', 'asc');
            if($title != null){
                 // check current page
                $current_page = $this->request->getParam('current_page');
                if($current_page == ''){
                    $current_page = 1;
                }else{
                    $current_page = $this->request->getParam('current_page');
                }
                // Check page size
                $page_size = $this->request->getParam('page_size');
                if($page_size == ''){
                    $page_size = 10;
                }else{
                    $page_size = $this->request->getParam('page_size');
                }
                $collection->addFieldToFilter([['attribute' => 'name', 'like' => '%'.$title.'%']]);
                $collection->setCurPage($current_page)->setPageSize($page_size);
            }
            foreach ($collection as $product){
                $productCollectionArray[] = $product->getData();
            }
        return $productCollectionArray;
    }
}
