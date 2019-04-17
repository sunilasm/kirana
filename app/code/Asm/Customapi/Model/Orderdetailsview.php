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
            $orgnizedRetailrArray = array();
            $orgnizedRetailrProductArray = array();
            $itemsArray = array();
            $response = array();
            $j = 0;
            foreach ($items as $item) 
            {
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
                            $orgnizedRetailrArray[$item->getSeller_id()] = $sellcoll->getData();
                        endforeach;
                    }
                    print_r($orgnizedRetailrArray);exit;
                    // Get item data
                    $orgnizedRetailrProductArray[$item->getSeller_id()][] = array(
                        'item_id' => $item->getItem_id(),
                        'sku' => $item->getSku(),
                        'qty' => $item->getQty(),
                        'product_type' => $item->getProduct_type(),
                        'quote_id' => $item->getQuote_id(),
                        'extension_attributes' => array(
                            'image' => $item->getExtension_attributes()->getImage(),
                            'seller_name' => $item->getSeller_name(),
                            'product_id' => $item->getProduct_id(),
                            'image_url' => $item->getImage_url(),
                            'doorstep_price' => $item->getDoorstep_price(),
                            'pickup_from_store' => $item->getPickup_from_store(),
                            'price_type' => $item->getPrice_type(),
                            'volume' => $item->getExtension_attributes()->getVolume(),
                            'seller_id' => $item->getSeller_id(),
                            'unitm' => $item->getExtension_attributes()->getUnitm()
                        )

                    );
                   
                    // $itemsArray[''] = $item->getExtension_attributes()->getUnitm();

                }
                else
                { // Delivered by kirana

                }

            }
            print_r($orgnizedRetailrProductArray);
            exit;
            $data = $response;
        }
        exit;
        //print_r($data);exit;
        return $data;
    }
   
}
