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
         //print_r($sellerId['orgretail']); exit();
         
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
