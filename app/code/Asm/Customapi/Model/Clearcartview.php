<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\ClearcartInterface;
 
class Clearcartview implements ClearcartInterface
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

    public function clearcart() {
      // print_r("Herrerer");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $quote = $this->quoteFactory->create()->load($post['quote_id']);
        
        $result = array();
            $items = $quote->getAllItems(); 
            $i = 0;
            if(count($items)){
              foreach ($items as $item) {
                  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                  $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');
                  $itemId = $item->getItemId();
      //print_r($itemId);exit;
                  $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                  $baseUrl = $storeManager->getStore()->getBaseUrl();
                  $userData = array("username" => "adminapi", "password" => "Admin@123");
                  $ch = curl_init("$baseUrl".''."rest/V1/integration/admin/token");
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

                  $token = curl_exec($ch);
                  $ch = curl_init("$baseUrl".''."rest/V1/carts/".$post['quote_id']."/items/".$itemId);
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

                  $result = curl_exec($ch);

                  $result = json_decode($result, 1);
                  //print_r($result);exit;
                }
              $result = array("status" => "Success","message" => "Items are successfully removed from cart.");
            }else{
              $result = array("status" => "Success","message" => "Cart does not have any items out of current geolocation.");
            }
        $data = $result;
        // print_r(json_encode($data));exit;
       return $data;
    }

}


