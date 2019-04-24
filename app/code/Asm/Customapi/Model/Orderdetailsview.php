<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\OrderdetailsInterface;
 
class Orderdetailsview implements OrderdetailsInterface
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
       \Magento\Quote\Model\QuoteFactory $quoteFactory,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
       $this->request = $request;
       $this->quoteFactory = $quoteFactory;
       $this->_sellerCollection = $sellerCollection;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->_productCollectionFactory = $productCollectionFactory;
    }

    public function orderdetails() {
        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        if($post['quote_id']){
            $quote = $this->quoteFactory->create()->load($post['quote_id']);
            $items = $quote->getAllItems();
            $sellerData = array();
            $deliverdeatils = array();
            $pickupdeatils = array();
            $tempOrgnizedSellerIdArray = array();
            $tempSellerIdArray = array();
            $itemsArray = array();
            $response = array();
            $response1 = array();
            $final = array();
            $j = 0;$k = 0;
            foreach ($items as $item) 
            {
            $orgnizedRetailrArray = array();
            $kiranaArray = array();
            $orgnizedRetailrProductArray = array();
            $kiranaProductArray = array();
                // Pickup from store
                if($item->getPrice_type() == 1)
                {
                    $i = 0;
                    if(!in_array($item->getSeller_id(), $tempOrgnizedSellerIdArray))
                    {
                        $tempOrgnizedSellerIdArray[] = $item->getSeller_id();
                        // Get Seller Data
                        $sellerCollectionDetails = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()));
                        foreach($sellerCollectionDetails as $sellcoll):
                            $orgnizedRetailrArray = $sellcoll->getData();
                        endforeach;
                    }
                    // Get Product doorsetp price and pick price.
                    $sellerProductCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()))->addFieldToFilter('product_id', array('in' => $item->getProduct_id()));
                    $sellerProductData = $sellerProductCollection->getData();

                    // Get Product image url
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $hotPrd = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct_id());
                    $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
                    $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $hotPrd->getThumbnail();

                    // Get item data
                    $orgnizedRetailrProductArray[] = array(
                        'item_id' => $item->getItem_id(),
                        'sku' => $item->getSku(),
                        'qty' => $item->getQty(),
                        'product_type' => $item->getProduct_type(),
                        'quote_id' => $item->getQuote_id(),
                        'extension_attributes' => array(
                            'image' => $hotPrd->getThumbnail(),
                            'seller_name' => $orgnizedRetailrArray['name'],
                            'product_id' => $item->getProduct_id(),
                            'image_url' => $imageUrl,
                            'doorstep_price' => $sellerProductData[0]['doorstep_price'],
                            'pickup_from_store' => $sellerProductData[0]['pickup_from_store'],
                            'price_type' => $item->getPrice_type(),
                            'volume' => $item->getExtension_attributes()->getVolume(),
                            'seller_id' => $item->getSeller_id(),
                            'unitm' => $item->getExtension_attributes()->getUnitm()
                        )

                    );
                   // 
                    // $itemsArray[''] = $item->getExtension_attributes()->getUnitm();

                     $subTotal = ($sellerProductData[0]['pickup_from_store'] * $item->getQty());
                    $cartSummeryArray = array('total_item_count' => count($orgnizedRetailrProductArray), 'sub_total' => number_format($subTotal, 2));

                    $response[$j]['store'] = $orgnizedRetailrArray;
                    $response[$j]['products'] = $orgnizedRetailrProductArray;
                    $response[$j]['cart_summary'] = $cartSummeryArray;
                    $j++;
                    

                }
                 else
                { // Delivered by kirana
                    if(!in_array($item->getSeller_id(), $tempSellerIdArray))
                    {
                        $tempSellerIdArray[] = $item->getSeller_id();
                        // Get Seller Data
                        $sellerCollectionDetails = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()));
                        foreach($sellerCollectionDetails as $sellcoll):
                            $kiranaArray = $sellcoll->getData();
                        endforeach;
                    }

                    // Get Product doorsetp price and pick price.
                    $sellerProductCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()))->addFieldToFilter('product_id', array('in' => $item->getProduct_id()));
                    $sellerProductData = $sellerProductCollection->getData();

                    // Get Product image url
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $hotPrd = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct_id());
                    $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
                    $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $hotPrd->getThumbnail();

                    // print_r($hotPrd->getThumbnail());exit;
                    // Get item data
                    $kiranaProductArray[] = array(
                        'item_id' => $item->getItem_id(),
                        'sku' => $item->getSku(),
                        'qty' => $item->getQty(),
                        'product_type' => $item->getProduct_type(),
                        'quote_id' => $item->getQuote_id(),
                        'extension_attributes' => array(
                            'image' => $hotPrd->getThumbnail(),
                            'seller_name' => $kiranaArray['name'],
                            'product_id' => $item->getProduct_id(),
                            'image_url' => $imageUrl,
                            'doorstep_price' => $sellerProductData[0]['doorstep_price'],
                            'pickup_from_store' => $sellerProductData[0]['pickup_from_store'],
                            'price_type' => $item->getPrice_type(),
                            'volume' => $item->getExtension_attributes()->getVolume(),
                            'seller_id' => $item->getSeller_id(),
                            'unitm' => $item->getExtension_attributes()->getUnitm()
                        )

                    );
                    $subTotal = ($sellerProductData[0]['doorstep_price'] * $item->getQty());
                    $cartSummeryArray = array('total_item_count' => count($kiranaProductArray), 'sub_total' => number_format($subTotal, 2));
                    // print_r($kiranaProductArray);exit;
                    $response1[$k]['store'] = $kiranaArray;
                    $response1[$k]['products'] = $kiranaProductArray;
                    $response1[$k]['cart_summary'] = $cartSummeryArray;
                     $k++;

                }
               
            }
            $final[0]['pick_up_from_store'] = $response;
            $final[1]['deliver_by_kirana'] = $response1;
            // print_r($response);
            // exit;
            $data = $final;
        }
        // exit;
        //print_r($data);exit;
        return $data;
    }
   
}
