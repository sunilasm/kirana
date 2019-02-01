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

   // public function clear() {

     //   $quoteId = $this->request->getParam('quoteId');
       // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       // $quoteModel = $objectManager->create('Magento\Quote\Model\Quote');
       // $quoteItem = $quoteModel->load($quoteId);
       // $quoteItem->delete();
       // $data = array('message' => 'You have no items in your shopping cart.');
       // return $data;
   // }

    // public function deletesku() {

    //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //     $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
    //     $request->getBodyParams();
    //     $post = $request->getBodyParams();
    //     //print_r($post);exit;
    //     $quoteId = $post['quote_id'];
    //     $sku = $post['sku'];

    //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //     $quoteModel = $objectManager->create('Magento\Quote\Model\Quote');
    //     $quoteItems = $quoteModel->load($quoteId)->getAllVisibleItems();
    //     $quoteItemArray = array();
    //     $i = 1;
    //     foreach($quoteItems as $item):
    //         // print_r($item);
    //         $quoteItemArray[$i] = $item->getSku();
    //         $quoteItemIndexArray[$i] = $item->getItemid();
    //         $i++;
    //     endforeach;
    //     //print_r($quoteItemIndexArray);exit;
    //     $data = '';
    //     $message = 'You have no items in your shopping cart.';
    //     if(count($quoteItemArray)){
    //         $ArrayIndex = array_search($sku, $quoteItemArray);
    //         //print_r($ArrayIndex);exit;
    //         if($ArrayIndex){

    //             // Get base Url
    //             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //             $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    //             //$baseUrl = $storeManager->getStore()->getBaseUrl();
	// 	$baseUrl = "http://13.233.41.0/";
	// 	//print_r($baseUrl); exit;
    //             $userData = array("username" => "sunil.n", "password" => "Admin@123");
    //             $ch = curl_init("$baseUrl".''."rest/V1/integration/admin/token");
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

    //             $token = curl_exec($ch);
	// 	//echo $token; exit;
    //             $ch = curl_init("$baseUrl".''."rest/V1/carts/".$quoteId."/items/".$quoteItemIndexArray[$ArrayIndex]);
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

    //             $result = curl_exec($ch);
	// 	//print_r($result); exit;
    //             $result = json_decode($result, 1);
    //             $message = 'Sku is successfully removed from cart.';
    //         }
    //     }
    //     $data = array('status'=>'Sucess','message' => $message);
    //     print_r($data);exit;
    //     return $data;
    // }

    // public function checkcart() {

    //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //     $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
    //     $request->getBodyParams();
    //     $post = $request->getBodyParams();
    //     // print_r($post);exit;
    //     $product_id = $post['product_id'];
    //     $seller_id = $post['seller_id'];
    //     $quoteId = $post['cartItem']['quote_id'];
    //     $sku = $post['cartItem']['sku'];
    //     $qty = $post['cartItem']['qty'];
    //     // Get base Url
    //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //     $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    //     $baseUrl = $storeManager->getStore()->getBaseUrl();
    //     // Get Quote
    //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //     $quoteModel = $objectManager->create('Magento\Quote\Model\Quote');
    //     $quoteItems = $quoteModel->load($quoteId)->getAllVisibleItems();
    //     $quoteItemArray = array();
    //     foreach($quoteItems as $item):
    //         $quoteItemArray[] = $item->getSku();
    //     endforeach;
    //     $data = '';
    //     $ArrayIndex = array_search($sku, array_values($quoteItemArray));
    //     print_r($quoteItems[$ArrayIndex]);exit;
    //     if($ArrayIndex){
    //         if($qty > 0){
    //             $quoteItems[$ArrayIndex]->setQty($qty);
    //             $quoteItems[$ArrayIndex]->save();
    //             $data = array('message' => 'True1');
    //         }else{
    //             if($qty == 0){
    //                 $quoteItems[$ArrayIndex]->delete();
    //                 $data = array('message' => 'True2');
    //             }else{
    //                 $data = array('message' => 'Qty can not be nigavtive.');
    //             }
    //         }  
    //     }else{
    //          if($qty > 0){
    //             $userData = array("username" => "admin", "password" => "Admin@123");
    //             $ch = curl_init("$baseUrl".''."rest/V1/integration/admin/token");
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

    //             $token = curl_exec($ch);
    //             $productData['cartItem'] = array( 
    //                                         "quote_id" => $quoteId,
    //                                         "sku" => $sku,
    //                                         "qty" => $qty
    //                                     );
    //            $productData['product_id'] = $product_id; 
    //            $productData['seller_id'] = $seller_id; 
    //            // array('product_id' => $product_id, 'seller_id' => $seller_id);
    //           // print_r($productData);exit;

    //             $ch = curl_init("$baseUrl".''."rest//V1/carts/mine/items");
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

    //             $result = curl_exec($ch);

    //             $result = json_decode($result, 1);
    //             print_r($result);exit;
    //             $data = array('message' => 'True3');  
    //         }else{
    //             $data = array('message' => 'No Product');  
    //         }   
    //     }
    //     print_r($data);exit;
    //     return $data;
    // }

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
