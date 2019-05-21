<?php
namespace Asm\AdvanceSearch\Model;
use Asm\AdvanceSearch\Api\SearchInterface;
use Lof\MarketPlace\Model\SellerProductFactory as SellerProduct;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Framework\Event\ObserverInterface;
use Retailinsights\Promotion\Model\PostTableFactory;
 
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
        PostTableFactory $PostTableFactory ,
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
       $this->_PostTableFactory = $PostTableFactory;
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
            $quoteItemIndexArray[$i] = $item->getItemid();
            $i++;
        endforeach;
        $data = array();
        $flag = 0;
        $pages = 0;
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
    public function getInRangeSeller($lat, $lon){
        $selerIdArray = array();
        $orgRetail = array();
        $retail = array();
        $rangeSetting = $this->helperData->getGeneralConfig('enable');
        $rangeInKm = $this->helperData->getGeneralConfig('range_in_km');
        //$rangeInKm = 10;
        if($rangeSetting == 1){
            if($rangeInKm){
                $distance = (is_numeric($rangeInKm)) ? $rangeInKm : 1; //your distance in KM
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
        foreach($sellerData as $seldata):
            //print_r($seldata);exit();
            if($seldata['group_id'] == 1){
                
                $retail[] = $seldata['seller_id'];

            } else {
                $orgRetail[] = $seldata['seller_id'];

            }
             

        endforeach;
                

        $selerIdArray['retail'] = $retail;
        $selerIdArray['orgretail'] = $orgRetail;
        
        //print_r($selerIdArray); exit();
        return  $selerIdArray;
    }
    public function getSearchTermData($title, $lat, $lon){
         $sellerId = $this->getInRangeSeller($lat, $lon);
         //print_r($sellerId); exit();
         
         $pickRetail = array();
         $pickOrgRetail = array();
         
         
         $proIds = array();
         foreach($sellerId as $key => $seller){
            $_sellerProdk = $this->sellerProduct->create()->getCollection()->setOrder('product_id', 'asc');
            $sellerProdCol = $_sellerProdk->addFieldToFilter('seller_id', array('in'=>$seller));
            //print_r($sellerProdCol->getData()); exit();
            $chsnPrice = 0;
            foreach($sellerProdCol as $sellerData){
                $proIds[] = $sellerData['product_id'];

                if($key == 'orgretail'){
                    if(!empty($sellerData['pickup_from_store']) || ($sellerData['pickup_from_store'] != NULL) || ($sellerData['pickup_from_store'] != 0) ){
                        $pickOrgRetail[$sellerData['product_id']][$sellerData['seller_id']] = $sellerData['pickup_from_store'];
                    }
                } else {
                    if(!empty($sellerData['doorstep_price']) || ($sellerData['doorstep_price'] != NULL) || ($sellerData['doorstep_price'] != 0) ){
                        $pickRetail[$sellerData['product_id']][$sellerData['seller_id']] = $sellerData['doorstep_price'];
                        
                    }    
                }
                
            }
            
         }

         $Productcollection = $this->_productCollectionFactory->create();
         $Productcollection->addFieldToFilter('entity_id', array('in'=>array_unique($proIds)));
         $Productcollection->addFieldToFilter('status', 1);
         if($title != null){
            $Productcollection->addFieldToFilter([['attribute' => 'name', 'like' => '%'.$title.'%']]);
         }
         $count = count($Productcollection->getData());
         $Productcollection = $this->_productCollectionFactory->create();
         $Productcollection->addFieldToFilter('entity_id', array('in'=>array_unique($proIds)));
         $Productcollection->addFieldToFilter('status', 1);
         $Productcollection->addAttributeToSort('price', 'asc');
         $Productcollection->addAttributeToSelect('*');


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
                if($title != null){
                    $Productcollection->addFieldToFilter([['attribute' => 'name', 'like' => '%'.$title.'%']]);
                }
                $Productcollection->setCurPage($current_page)->setPageSize($page_size);
         $result = array();
         
         foreach($Productcollection->getData() as $product){
            $chsnOrgId = $chsnOrgPrice = $chsnRetailId = $chsnRetailPrice = "";
            $entColl = array();
            $entColl = $product;
            $product = $this->_productsRepository->getById($product['entity_id']);
            //print_r($pickOrgRetail); exit();
            if(array_key_exists($product['entity_id'], $pickOrgRetail)){
                $orgsellers = $pickOrgRetail[$product['entity_id']];
                asort($orgsellers);
                reset($orgsellers);
                $chsnOrgId = key($orgsellers); 
                $chsnOrgPrice = $orgsellers[$chsnOrgId]; 
            }
            if(array_key_exists($product['entity_id'], $pickRetail)){
                $retsellers = $pickRetail[$product['entity_id']];
                asort($retsellers);
                reset($retsellers);
                $chsnRetailId = key($retsellers);
                $chsnRetailPrice = $retsellers[$chsnRetailId];
            }
            
           
            $entColl['name'] = $product->getData('name');
            $entColl['image'] = $product->getData('image');
            $entColl['small_image'] = $product->getData('small_image');
            $entColl['thumbnail'] = $product->getData('thumbnail'); 
            $entColl['volume'] = $product->getData('volume');         
            $entColl['unitm'] = (round($product->getData('weight'),0)).' '.($product->getData('uom_label'));
            if(!empty($chsnOrgId && $chsnOrgPrice)){
                $entColl['org_retail'] = $chsnOrgId;
                $entColl['pickup_from_store'] = $chsnOrgPrice;
            }
            if(!empty($chsnRetailId && $chsnRetailPrice)){
                $entColl['kirana'] = $chsnRetailId;
                $entColl['doorstep_delivery'] = $chsnRetailPrice;                
            }
            
            //=====adding promotions
            $mapped_data = $this->_PostTableFactory->create()->getCollection();
             $orgret_arr = array();
            $kirana_arr = array();
            foreach ($mapped_data->getData() as $k => $promo) {    //store-promo-mapp data array  
                $skus = array(); 
                $disc_amt = 0;
                $disc_per = 0;
                $add_kiranapromo = $add_orgpromo = 0;
                $p_action = $promo['simple_action'];    //by_percent or by_fixed
                $con_arr = json_decode($promo['conditions_serialized'] , true); 
                if(!empty($con_arr['conditions'])){
                    $conditionsarr = $con_arr['conditions'];
                    foreach($conditionsarr as $ck => $con){  // promo rule conditions array
                        if($con['attribute']=='sku'){
                            $skus[] = $con['value'];
                        }
                        if(!empty($con['conditions'])){
                            foreach($con['conditions'] as $c_inn => $c_inn_val){
                                if($c_inn_val['attribute']=='sku'){
                                    $skus[] = $c_inn_val['value'];
                                }
                            }
                        }
                    }
                }    
                if(!empty($entColl['kirana'])){
                    if(($promo['store_id']== $entColl['kirana']) && ($promo['status']==1)){
                        if(!empty($skus)){
                            if(in_array($product['sku'], $skus)){
                                $add_kiranapromo = 1;
                            }
                        }else{
                            $add_kiranapromo = 1;
                        }
                    }
                }
                if(!empty($entColl['org_retail'])){ 
                    if(($promo['store_id']== $entColl['org_retail'])  && ($promo['status']==1)){
                        if(!empty($skus)){
                            if(in_array($product['sku'], $skus)){
                                $add_orgpromo = 1;
                            }
                        }else{
                                $add_orgpromo = 1;
                        }
                    }  
                    
                }       
                if($add_kiranapromo == 1){
                    if($p_action == 'by_fixed'){
                        $disc_amt = $promo['discount_amount'];
                        $disc_per = ($promo['discount_amount']*100)/$chsnRetailPrice ;
                    }else{
                        $disc_amt = ($chsnRetailPrice * $promo['discount_amount'])/100 ;
                        $disc_per = $promo['discount_amount'];
                    }
                    $kirana_temp['discount_percent'] = ceil($disc_per);
                    $kirana_temp['final_amt'] = ceil($chsnRetailPrice - $disc_amt); 
                    array_push($kirana_arr,$kirana_temp);
                } 
                if($add_orgpromo == 1){
                    if($p_action == 'by_fixed'){
                         $disc_amt = $promo['discount_amount'];
                         $disc_per = ($promo['discount_amount']*100)/$chsnOrgPrice ;
                    }else{
                         $disc_amt = ($chsnOrgPrice * $promo['discount_amount'])/100 ;
                         $disc_per = $promo['discount_amount'];
                    } 
                    $orgret_temp['message'] = $promo['description'];
                    $orgret_temp['discount_percent'] = ceil($disc_per);
                    $orgret_temp['final_amt'] = ceil($chsnOrgPrice - $disc_amt);                    
                    array_push($orgret_arr,$orgret_temp);
                }
            }//store-promo-mapp data array end 
            $entColl['promotion']['kirana'] = $kirana_arr;
            $entColl['promotion']['org_retail'] = $orgret_arr;
            $result[] = $entColl;

         }
         //$totalItems = count($proIds);

         $noOfPages = ceil($count/$page_size);
         $fnlRslt[]['pages'] = $noOfPages;
         $fnlRslt[]['items'] = $result;
         //print_r($fnlRslt); exit();
        return $fnlRslt;       
    }
}
