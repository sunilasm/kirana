<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\AddresschangeInterface;
 
class Addresschangeview implements AddresschangeInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $_wishlistRepository;
    protected $_productRepository;
    protected $_sellerCollection;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Quote\Model\QuoteFactory $quoteFactory,
       \Asm\AdvanceSearch\Model\Searchview $inRange,
       \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
       \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Lof\MarketPlace\Model\Seller $sellerCollection
    ) {
       $this->request = $request;
       $this->quoteFactory = $quoteFactory;
       $this->inRange = $inRange;
       $this->_wishlistRepository= $wishlistRepository;
       $this->_productRepository = $productRepository;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->_sellerCollection = $sellerCollection;
    }

    public function addresschange() {
        // print_r("Api execute successfully");exit;
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        // print_r($post);exit;
        $quote = $this->quoteFactory->create()->load($post['quote_id']);
        $items = $quote->getAllItems();
        $sellerId = $this->inRange->getInRangeSeller($post['lat'], $post['long']);
        // print_r($sellerId);exit;
        if(isset($post['customer_id'])){
            $customerId = $post['customer_id'];
        }else{
            $customerId = '';
        }
        $wishlistProductsArray = array();
        $removeProductsArray = array();
        $currentProductsArray = array();
        foreach ($items as $item) 
        {
            // print_r($item->getData());exit;
            if(!in_array($item->getSeller_id(), $sellerId)){

                $sellerCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('product_id', array('in' => $item->getProduct_id()));
                $tempSellerProductArray = array();
                foreach($sellerCollection as $seller):
                    if(in_array($seller['seller_id'], $sellerId)){
                        $tempSellerProductArray[] = $seller['seller_id'];
                    }
                    //$i++;
                endforeach;
                if(count($tempSellerProductArray)){
                    $sellerCollection = $this->_sellerCollection->getCollection()
                                    ->setOrder('position','ASC')
                                    ->addFieldToFilter('seller_id',array('in'=>$tempSellerProductArray[0]));
                    $sellerData = $sellerCollection->getData();
                    if($sellerData[0]['group_id'] == 2){
                        $priceType = 1;
                    }else{
                        $priceType = 0;
                    }
                    // Call remove item function
                    $this->removeItem($post['quote_id'], $item->getItemId());
                    // Call add item function
                    $this->addItem($post['quote_id'], $item->getProduct_id(), $priceType,$tempSellerProductArray[0],$item->getQty(),$item->getSku());

                }else{
                    if($customerId){
                    // Add to wishlist
                        $wishlistProductsArray[] = $item->getProduct_id();
                        $product = $this->_productRepository->getById($item->getProduct_id());
                        $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);
                        $wishlist->addNewItem($product);
                        $wishlist->save();
                        $this->removeItem($post['quote_id'], $item->getItemId());
                    }else{
                        // Remove from cart
                        $removeProductsArray[] = $item->getProduct_id();
                        $this->removeItem($post['quote_id'], $item->getItemId());    
                    }
                }
                // print_r($tempSellerProductArray);exit;
            }
        }
        $currentCartItems = count($items) - count($wishlistProductsArray) - count($removeProductsArray);
        $data = array("total_count" => count($items),"wishlist_count" => count($wishlistProductsArray), "removed_count" => count($removeProductsArray),"current_cart_count" => $currentCartItems);
        $response = array($data);
        return $response;
    }

    // Remove item form cart
    public function removeItem($quoteId, $itemId){
        // print_r($quoteId.'--'.$itemId);exit;
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
        $ch = curl_init("$baseUrl".''."rest/V1/carts/".$quoteId."/items/".$itemId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        $result = curl_exec($ch);
        $result = json_decode($result, 1);
        // print_r($result);exit;
    }

    // add item in cart
    public function addItem($quoteId, $productId, $priceType, $sellerId, $qty, $sku){
        // print_r($quoteId.'|--|'.$productId.'|--|'.$priceType.'|--|'.$sellerId.'|--|'.$qty.'|--|'.$sku);exit;
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
                                'quote_id' => $quoteId,
                                'sku' => $sku,
                                'qty' => $qty
                            ],
                            'product_id' => $productId,
                            'seller_id' => $sellerId,
                            'price_type' => $priceType

                        ];
            $ch = curl_init("$baseUrl".''."rest//V1/carts/mine/items");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

            $result = curl_exec($ch);

            $resultArray[] = json_decode($result, 1);
    }

   
}
