<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\FlushcartInterface;
 
class Flushcartview implements FlushcartInterface
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

    public function flushcart() {
      //print_r("Herrerer");exit;
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $quote = $this->quoteFactory->create()->load($post['quote_id']);
        $customer = $this->_customerFactory->create()->load($post['customer_id']);
        $shippingAddressId = $customer->getDefaultShipping();
        $billingAddressId = $customer->getDefaultBilling();
        $address = $objectManager->create('Magento\Customer\Model\Address')->load($billingAddressId);
        //$shippingAddress = $this->_addressFactory->create()->load($shippingAddressId);
        //echo "<pre>";print_r($shippingAddress->getData());exit;
        //$latitude = $post['latitude'];
        //$longitude = $post['longitude'];
        $latitude = $address->getLatitude();
        $longitude = $address->getLongitude();

        // print_r($address->getData());exit;
        $productCollectionArray = array();
        $kiranaArray = array();
        $result = array();
        $flag = 0;
        if($latitude && $longitude){
            $ranageSeller = $this->searchRange->getInRangeSeller($latitude, $longitude);
            // print_r($ranageSeller);exit;
            $items = $quote->getAllItems(); 
            $i = 0;
            foreach ($items as $item) {
               if (!in_array($item->getSellerId(), $ranageSeller))
                {
                  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                  $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');
                  $itemId = $item->getItemId();
                  $quoteItem = $itemModel->load($itemId);
                  $quoteItem->delete();
                  $flag = 1;
                }
            }
            if($flag){
              $result = array("status" => "Success","message" => "Items are successfully removed from cart.");
            }else{
              $result = array("status" => "Success","message" => "Cart does not have any items out of current geolocation.");
            }
        }else{
          $result = array("status" => "Error","message" => "Address geolocation details are not found.");
        }
       $data = array($result);
        // print_r(json_encode($data));exit;
       return $data;
    }

}
