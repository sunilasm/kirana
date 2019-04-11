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
        //$this->productRepository = $productRepository;
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
	//print_r($data);exit;
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
     
        $kirana = array();
        $orgRetail = array();
        $selId = array();
        $orgRetailColl = array();
        $kiranaColl = array();
        //print_r($sellerData); exit();
        foreach($sellerData as $seldata):
            
            $SellerProd = $this->sellerProduct->create()->getCollection();
            $fltColl = $SellerProd->addFieldToFilter('seller_id', $seldata['seller_id'])
                ->addFieldToFilter('product_id', $prodId);
                $fltData = $fltColl->getData();
                
            if(count($fltData) != 0){
                $selProd = $fltData;
               
                if($seldata['group_id'] == 4){

                        $orgRetail['seller_id'] = $seldata['seller_id'];
                        $orgRetail['price'] = $selProd[0]['pickup_from_store'];
                        $orgRetail['grp_id'] = $seldata['group_id'];
                        $orgRetailColl[$selProd[0] ['pickup_from_store']] = $orgRetail;
                } else {
                        $kirana['seller_id'] = $seldata['seller_id'];
                        $kirana['price'] = $selProd[0]['doorstep_price'];
                        $kirana['grp_id'] = $seldata['group_id'];
                        $kiranaColl[$selProd[0] ['doorstep_price']] = $kirana;
                }

            }
            
        endforeach;
       ksort($orgRetailColl);
       $chsnOrgRetail = array_shift($orgRetailColl);
       ksort($kiranaColl);
       $chsnKirana = array_shift($kiranaColl);
        //print_r($kiranaColl); exit();
        
        if(!empty($chsnKirana)){
        $selerIdArray[] = $chsnKirana;
        }
        if(!empty($chsnOrgRetail)){
            
        $selerIdArray[] = $chsnOrgRetail;
        }
       // print_r($selerIdArray); exit();
        return  $selerIdArray;
        
    }
    public function getSearchTermData($title, $lat, $lon){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/search_test.log'); 
        $logger = new \Zend\Log\Logger(); 
        $logger->addWriter($writer); 

            $productCollectionArray = array();
            $sellerProductsArray = array();
            $arratAttributes = array();
            $collection = $this->_productCollectionFactory->create();
           
           

            $collection->addAttributeToSelect('*');
          
            $collection->addAttributeToSort('price', 'desc');
       // if($title != null){
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
                    $page_size = 100;
                }else{
                    $page_size = $this->request->getParam('page_size');
                    $page_size = 100;
                }
               
                $collection->addFieldToFilter([['attribute' => 'name', 'like' => '%'.$title.'%']]);
                $collection->setCurPage($current_page)->setPageSize($page_size);
           // }
            //print_r($collection->getData()); exit();
        $FnlProductCollection = array();
        foreach($collection->getData() as $productDtls){
            $prodId = $productDtls['entity_id'];// exit();
           // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            //$product = $objectManager->get('Magento\Catalog\Model\Product')->load($prodId);
            ///echo $product->getAttributeText('your_attribut');

            $sellerId = $this->getInRangeSeller($lat, $lon, $prodId);
            //print_r($productDtls);exit();
            if(empty($sellerId)){
                continue;
            } else {
                $productCollectionArray = $productDtls;
               
                foreach($sellerId as $seller){
                    $SellerProd = $this->sellerProduct->create()->getCollection();
                    $fltColl = $SellerProd->addFieldToFilter('seller_id', $seller['seller_id'])
                            ->addFieldToFilter('product_id', $prodId);
               // $productCollectionArray['unitm'] = (round($product->getWeight(),0)).' '.($product->getUomLabel());
                if(count($fltColl->getData()) != 0){
                if($seller['grp_id'] == 1){
                    $productCollectionArray['kirana'] = $seller['seller_id'];
                    $productCollectionArray['doorstep_delivery'] =  $fltColl->getData()[0]['doorstep_price'];
                } else {
                    $productCollectionArray['org_retail'] = $seller['seller_id'];
                    $productCollectionArray['pickup_from_store'] =  $fltColl->getData()[0]['pickup_from_store'];

                }

                }

                }
            }

            $FnlProductCollection[] = $productCollectionArray;
        }
           $FnlProductCollectionar = array_slice($FnlProductCollection, 0, 10);
        //print_r($FnlProductCollection); exit();
        return $FnlProductCollectionar;
    }
   
}
