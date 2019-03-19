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
        $customer = $this->_customerFactory->create()->load($post['customer_id']);
        $shippingAddressId = $customer->getDefaultShipping();
        $address = $objectManager->create('Magento\Customer\Model\Address')->load($shippingAddressId);
        //$latitude = $post['latitude'];
        //$longitude = $post['longitude'];
        $latitude = $address->getLatitude();
        $longitude = $address->getLongitude();

        //print_r($shippingAddress->getData());exit;
        $productCollectionArray = array();
        $kiranaArray = array();
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
                   }
                }
                array_push($kiranaArray,$item->getSellerId());
            }
        }
        $summry = array("product_count" => count($productCollectionArray),"kirana_count" => count(array_unique($kiranaArray)));
      //$summry->product_count = count($productCollectionArray);
      //$summry->kirana_count = count($kiranaArray);
        $dataNew = array("product_count" => count($productCollectionArray),"kirana_count" => count(array_unique($kiranaArray)),"products" => $productCollectionArray);
        $data = array($dataNew);
        return $data;
    }

}
