<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\WishlistInterface;
 
class Wishlistitemsview implements WishlistInterface
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
    protected $_productCollectionFactory;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
       $this->request = $request;
       $this->_wishlistRepository= $wishlistRepository;
       $this->_productCollectionFactory = $productCollectionFactory;
    }

    public function wishlistitems() {

        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        // Get Wish List items
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $wishlist = $objectManager->get('\Magento\Wishlist\Model\Wishlist');
        $wishlist_collection = $wishlist->loadByCustomerId($post['customer_id'], true)->getItemCollection();
        $data = $wishlist_collection->getData();
        // print_r($data);exit;
        $productCollectionArray = array();
        foreach ($data as $item) {
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addFieldToFilter('entity_id', ['in' => $item['product_id']]);
            foreach ($collection as $product){
                $productData = $product->getData();
                $productData['wishlist_item_id'] = $item['wishlist_item_id'];
                $productData['wishlist_id'] = $item['wishlist_id'];
                $productData['wishlist_product_id'] = $item['product_id'];
                $productData['wishlist_seller_id'] = $item['seller_id'];
                $productData['wishlist_seller_name'] = $item['seller_name'];
                $productData['wishlist_qty'] = $item['qty'];
                $productData['wishlist_product_price'] = $item['seller_price'];
                $productData['wishlist_price_type'] = $item['price_type'];
                $productData['wishlist_added_at'] = $item['added_at'];
                $productCollectionArray[] = $productData;
            }
        }
        if(count($productCollectionArray)){
            $result = $productCollectionArray;
        }else{
            $result = array("Success" => "No products in wishlist");
        }
        return $result;
    } 
}

