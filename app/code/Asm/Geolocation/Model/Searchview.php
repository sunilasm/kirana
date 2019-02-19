<?php
namespace Asm\Geolocation\Model;
use Asm\Geolocation\Api\SearchInterface;
 
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
// print_r("Exit");
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $request->getBodyParams();
        $post = $request->getBodyParams();
        //print_r($post);exit;
        $quoteId = $post['quote_id'];
        $sku = $post['sku'];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteModel = $objectManager->create('Magento\Quote\Model\Quote');
        $quoteItems = $quoteModel->load($quoteId)->getAllVisibleItems();
        $quoteItemArray = array();
        $i = 1;
        foreach($quoteItems as $item):
            // print_r($item);
            $quoteItemArray[$i] = $item->getSku();
            $quoteItemIndexArray[$i] = $item->getItemid();
            $i++;
        endforeach;
        // print_r($quoteItemIndexArray);exit;
        $data = '';
        $message = 'You have no items in your shopping cart.';
        if(count($quoteItemArray)){
            $ArrayIndex = array_search($sku, $quoteItemArray);
           // print_r($ArrayIndex);exit;
            if($ArrayIndex){

                // Get base Url
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $baseUrl = $storeManager->getStore()->getBaseUrl();
		// $baseUrl = "http://13.233.41.0/";
                $userData = array("username" => "adminapi", "password" => "Admin@123");
                $ch = curl_init("$baseUrl".''."rest/V1/integration/admin/token");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

                $token = curl_exec($ch);

                $ch = curl_init("$baseUrl".''."rest/V1/carts/".$quoteId."/items/".$quoteItemIndexArray[$ArrayIndex]);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

                $result = curl_exec($ch);

                $result = json_decode($result, 1);
                $message = 'Sku is successfully removed from cart.';
            }
        }
        $data = array('status'=>'Sucess','message' => $message);
        //print_r($data);exit;
        return $data;
    }
}
