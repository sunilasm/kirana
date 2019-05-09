<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\OutofrangeInterface;
 
class Outofrangeview implements OutofrangeInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $quoteFactory;
    protected $_productCollectionFactory;
     protected $_customerFactory;
    protected $_addressFactory;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Asm\Customapi\Model\LocalityFactory $localityCollection,
       \Magento\Quote\Model\QuoteFactory $quoteFactory,
       \Asm\Search\Model\Searchview $searchRange,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Magento\Customer\Model\CustomerFactory $customerFactory,
       \Magento\Customer\Model\AddressFactory $addressFactory
    ) {
       $this->request = $request;
       $this->_locality = $localityCollection;
       $this->quoteFactory = $quoteFactory;
       $this->searchRange = $searchRange;
       $this->_productCollectionFactory = $productCollectionFactory;
       $this->_customerFactory = $customerFactory;
       $this->_addressFactory = $addressFactory;
    }

    public function outofrange() {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $quote = $this->quoteFactory->create()->load($post['quote_id']);
        // Pass Customer id get lat and long
        /*
        $customer = $this->_customerFactory->create()->load($post['customer_id']);
        $shippingAddressId = $customer->getDefaultShipping();
        $address = $objectManager->create('Magento\Customer\Model\Address')->load($shippingAddressId);
        $latitude = $address->getLatitude();
        $longitude = $address->getLongitude();
        */
        if(array_key_exists('latitude', $post)){
          $latitude = $post['latitude'];
        }else{
          $latitude = '';
        }

        if(array_key_exists('longitude', $post)){
          $longitude = $post['longitude'];
        }else{
          $longitude = '';
        }
        // $longitude = $post['longitude'];
        //print_r($shippingAddress->getData());exit;
        $productCollectionArray = array();
        $kiranaArray = array();
        $flag = 0;
        if($latitude && $longitude){
            $ranageSeller = $this->searchRange->getInRangeSeller($latitude, $longitude);
            $items = $quote->getAllItems(); 
            $i = 0;
            foreach ($items as $item) {
                $collection = $this->_productCollectionFactory->create();
                $collection->addAttributeToSelect('*');
                $collection->addFieldToFilter('entity_id', ['in' => $item->getProductId()]);
                if (!in_array($item->getSellerId(), $ranageSeller))
                {
                  foreach ($collection as $product){
                        $productCollectionArray[] = $product->getData();
                        $flag = 1;
                   }
                }
                array_push($kiranaArray,$item->getSellerId());
            }
            if($flag){
              $dataNew = array("product_count" => count($productCollectionArray),"kirana_count" => count(array_unique($kiranaArray)),"products" => $productCollectionArray);
              $data = array($dataNew);
            }else{
               $data = array("status" => "Success","message" => "Cart does not have any items out of current geolocation..");
            }
        }else{
          $data = array("status" => "Error","message" => "Address geolocation details are not found.");
        }
        //$summry = array("product_count" => count($productCollectionArray),"kirana_count" => count(array_unique($kiranaArray)));
        
        return $data;
    }

}


