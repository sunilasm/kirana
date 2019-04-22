<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\AddorgnizedretailerInterface;
 
class Addorgreailerview implements AddorgnizedretailerInterface
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
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Asm\Geolocation\Helper\Data $helperData,
       \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
       $this->request = $request;
       $this->_sellerCollection = $sellerCollection;
       $this->helperData = $helperData;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->quoteFactory = $quoteFactory;
       $this->_productCollectionFactory = $productCollectionFactory;
    }

    public function addorgreailer() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        // Remove cart items
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
        }
        // End cart items
        // Add product in cart
        $productsArray = $post['products'];
        $resultArray = array();
        foreach ($productsArray as $product)
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $baseUrl = $storeManager->getStore()->getBaseUrl();
            $userData = array("username" => "adminapi", "password" => "Admin@123");
            $ch = curl_init("$baseUrl".''."rest/V1/integration/admin/token");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

            $token = curl_exec($ch);
            // print_r($token);exit;

            $productData = [
                            'cart_item' => [
                                'quote_id' => $post['quote_id'],
                                'sku' => $product['sku'],
                                'qty' => $product['quote_qty']
                            ],
                            'product_id' => $product['product_id'],
                            'seller_id' => $post['store_id'],
                            'price_type' => 1

                        ];
            $ch = curl_init("$baseUrl".''."rest//V1/carts/mine/items");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

            $result = curl_exec($ch);

            $resultArray[] = json_decode($result, 1);
        }
        $data = $resultArray;
        return $data;
    }
}
