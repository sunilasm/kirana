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
    private $productsRepository;
    public function __construct(
        ProductRepository $productRepository,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        SellerProduct $sellerProduct,
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Asm\Geolocation\Helper\Data $helperData,
       \Magento\Catalog\Api\ProductRepositoryInterface $productsRepository
    ) {
        $this->_productRepository = $productRepository;
        $this->itemFactory = $itemFactory;
       $this->sellerProduct = $sellerProduct; 
       $this->request = $request;
       $this->_productCollectionFactory = $productCollectionFactory; 
       $this->_sellerCollection = $sellerCollection;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->helperData = $helperData;
       $this->_productsRepository = $productsRepository;
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
        if($searchtermpara){ $searchterm = 0; }else{ $searchterm = 1; }
        if($searchterm){
            if($title){
                $productCollectionArray = $this->getSearchTermData($title, $lat, $lon);
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
            $productCollectionArray = $this->getSearchTermData($title = null,$lat, $lon);
             if($productCollectionArray){
                $data = $productCollectionArray;
            }else{
                $data = $productCollectionArray;
            }
            $flag = 2;
        }
      //  print_r($data); exit();
        if($flag != 1){
            if(count($data[1]["items"]) != 0){
        
                foreach($data[1]["items"] as $key => $proData):
                    if(array_key_exists($proData['sku'], $quoteItemArray) ){
                        $data[1]["items"][$key] += ['quote_qty' => $quoteItemArray[$proData['sku']]['qty']];
                        $data[1]["items"][$key]['price_type'] = $quoteItemArray[$proData['sku']]['price_type']; 
                    }else{
                        $data[1]["items"][$key] += ['quote_qty' => 0];
                        $data[1]["items"][$key]['price_type'] = NULL;                      
                    }
                endforeach;
            }
        }
    
        return $data;
    }
    /*
    Get seller id's based on lat & lon.
    */
    public function getInRangeSeller($lat, $lon, $prodId){
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
        ->addFieldToFilter('geo_lat',array('gteq'=>$minLat))
        ->addFieldToFilter('geo_lng',array('gteq'=>$minLon))
        ->addFieldToFilter('geo_lat',array('lteq'=>$maxLat))
        ->addFieldToFilter('geo_lng',array('lteq'=>$maxLon))
        ->addFieldToFilter('status',1);
        // get Seller id's
        $sellerData = $sellerCollection->getData();
        //print_r($sellerData); exit();
        $kirana = array();
        $orgRetail = array();
        $selId = array();
        $orgRetailColl = array();
        $kiranaColl = array();
        $_sellerProdk = $this->sellerProduct->create()->getCollection();
        foreach($sellerData as $seldata):
            $sellerProdk = clone $_sellerProdk;
            $fltColl = $sellerProdk->addFieldToFilter('seller_id', $seldata['seller_id'])
                ->addFieldToFilter('product_id', $prodId);
                $fltData = $fltColl->getData();
             //print_r($fltData); exit();
            if(count($fltData) != 0){
                $selProd = $fltData;
               
                if($seldata['group_id'] == 1){
                      $kirana['seller_id'] = $seldata['seller_id'];
                      $kirana['price'] = $selProd[0]['doorstep_price'];
                      $kirana['grp_id'] = $seldata['group_id'];
                      $kiranaColl[$selProd[0] ['doorstep_price']] = $kirana;       
                } else {
                     $orgRetail['seller_id'] = $seldata['seller_id'];
                     $orgRetail['price'] = $selProd[0]['pickup_from_store'];
                     $orgRetail['grp_id'] = $seldata['group_id'];
                     $orgRetailColl[$selProd[0] ['pickup_from_store']] = $orgRetail;
                      
                }
            }
            
        endforeach;
       ksort($orgRetailColl);
       $chsnOrgRetail = array_shift($orgRetailColl);
       ksort($kiranaColl);
       $chsnKirana = array_shift($kiranaColl);
        
        if(!empty($chsnKirana)){
        $selerIdArray[] = $chsnKirana;
        }
        if(!empty($chsnOrgRetail)){
            
        $selerIdArray[] = $chsnOrgRetail;
        }
        return  $selerIdArray;
        //print_r($selerIdArray); exit();

    }
    public function getSearchTermData($title, $lat, $lon){
            $FnlProductCollection = array();
            
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToSort('entity_id', 'asc');
            $title = $this->request->getParam('title');
            $page = $this->request->getParam('current_page');
            $size = $this->request->getParam('page_size');
            $itemCount = $page * $size;
        if($title != null){
               $collection->addFieldToFilter([['attribute' => 'name', 'like' => '%'.$title.'%']]);
        }
        $totalItems = count($collection->getData());
        $totpages = intval($totalItems/$size) ; 
        
        $sellerProd = $this->sellerProduct->create()->getCollection();
        $newfinalarr = array();
        foreach($collection->getData() as $productDtls){
            $prodId = $productDtls['entity_id'];
            $sellerId = $this->getInRangeSeller($lat, $lon, $prodId);
                if(count($sellerId) != 0){ 
                $productCollectionArray = array();
                $productCollectionArray = $productDtls;
                $product = $this->_productsRepository->getById($prodId);
                $productCollectionArray['name'] = $product->getData('name');
                $productCollectionArray['image'] = $product->getData('image');
                $productCollectionArray['small_image'] = $product->getData('small_image');
                $productCollectionArray['thumbnail'] = $product->getData('thumbnail');       
                $productCollectionArray['unitm'] = (round($product->getData('weight'),0)).' '.($product->getData('uom_label'));
                foreach($sellerId as $seller){
                    $_sellerProd = clone $sellerProd;
                    $fltColl = $_sellerProd->addFieldToFilter('seller_id', $seller['seller_id'])
                            ->addFieldToFilter('product_id', $prodId);
                    $fltData = $fltColl->getData();
             
                if(count($fltData) != 0){
                
                if($seller['grp_id'] == 1){
                
                    $productCollectionArray['kirana'] = $seller['seller_id'];
                    $productCollectionArray['doorstep_delivery'] =  $fltData[0]['doorstep_price'];
                } else {
                    $productCollectionArray['org_retail'] = $seller['seller_id'];
                    $productCollectionArray['pickup_from_store'] =  $fltData[0]['pickup_from_store'];
                }
                }
                }
            
            $FnlProductCollection[] = $productCollectionArray;
           
            
            //$FnlProductCollection["pages"] = $totpages;
            if(count($FnlProductCollection) == $itemCount){
                break;

            }
            } 
        }
        $newfinalarr[]["pages"] = $totpages;

        $newfinalarr[]["items"] = $FnlProductCollection;
        /*$page = $this->request->getParam('current_page');
        $total = count( $FnlProductCollection );     
        $limit = $this->request->getParam('page_size');
        $totalPages = ceil( $total/ $limit ); 
        $page = max($page, 1); 
        $page = min($page, $totalPages); 
        $offset = ($page - 1) * $limit;
        if( $offset < 0 ) $offset = 0;
        $FnlProductCollectionar = array_slice( $FnlProductCollection, $offset, $limit );*/
        
        return $newfinalarr;
    }
   
}
