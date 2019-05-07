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
            $tempOrgnizedNameArray = array();
            $tempSellerIdArray = array();
            $itemsArray = array();
            $response = array();
            $response1 = array();
            $final = array();
            $j = 0;$k = 0;
            $sellerIdPresentArray = array();
            $kiranaArray = array();
            $orgnizedRetailrArray = array();
            $orgnizedRetailrProductArray = array();
            $kiranaProductArray = array();
            $kiranaNamesArray = array();
            $sellers = array();
            foreach ($items as $item) 
            {
            $organizedQtyCount = 0;
            $kiranaQtyCount = 0;
                // Pickup from store
                // if($item->getPrice_type() == 1)
                // {
                    $i = 0;
                    if(!in_array($item->getSeller_id(), $tempOrgnizedSellerIdArray))
                    {
                        $tempOrgnizedSellerIdArray[] = $item->getSeller_id();
                        // Get Seller Data
                        $sellerCollectionDetails = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()));

                        foreach($sellerCollectionDetails as $sellcoll):
                            $tempOrgnizedNameArray[$item->getSeller_id()]['name'] = $sellcoll->getName();
                            $selllers[$item->getSeller_id()]['store'] = $sellcoll->getData();
                            $selllers[$item->getSeller_id()]['cart_summary']['total_item_count'] = 0;
                            $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] = 0;
                            if($item->getPrice_type() == 1)
                            {
                                $selllers[$item->getSeller_id()]['type'] = 'org';
                            }
                            else
                            {
                                $selllers[$item->getSeller_id()]['type'] = 'kirana';
                            }
   
                        endforeach;
                    }
                    // Get Product doorsetp price and pick price.
                    $sellerProductCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()))->addFieldToFilter('product_id', array('in' => $item->getProduct_id()));
                    $sellerProductData = $sellerProductCollection->getData();
                    // print_r($sellerProductData);exit;

                    // Get Product image url
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $hotPrd = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct_id());
                    $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
                    $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $hotPrd->getThumbnail();

                    // Get item data
                    $selllers[$item->getSeller_id()]['products'][] = array(
                        'item_id' => $item->getItem_id(),
                        'sku' => $item->getSku(),
                        'qty' => $item->getQty(),
                        'product_type' => $item->getProduct_type(),
                        'quote_id' => $item->getQuote_id(),
                        'extension_attributes' => array(
                            'image' => $hotPrd->getThumbnail(),
                            'seller_name' => $tempOrgnizedNameArray[$item->getSeller_id()]['name'],
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
                    $subTotal = 0;
                    $selllers[$item->getSeller_id()]['cart_summary']['total_item_count'] += $item->getQty();
                    if($item->getPrice_type() == 1)
                    {
                        $subTotal = ($sellerProductData[0]['pickup_from_store'] * $item->getQty());
                    }
                    else
                    {
                        $subTotal = ($sellerProductData[0]['doorstep_price'] * $item->getQty());
                    }
                    
                    $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] += $subTotal;
                    $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] = number_format((float)$selllers[$item->getSeller_id()]['cart_summary']['sub_total'], 2, '.', '');
               
            }
            $response = array();
            $i=0;
            $j=0;
            foreach ($selllers as $seller) {
                if($seller['type'] == 'org')
                {
                    $response['pick_up_from_store'][$i] = $seller;
                    $i++;
                }
                else
                {
                    $response['deliver_by_kirana'][$j] = $seller;
                    $j++;   
                }
                
            }
            $data = array($response);
        }
        return $data;
    }

    
   
}
