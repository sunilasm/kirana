<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\AddwishlistInterface;
 
class Addwishlistview implements AddwishlistInterface
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
    protected $_productRepository;
    protected $_sellerCollection;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Lof\MarketPlace\Model\Seller $sellerCollection
    ) {
       $this->request = $request;
       $this->_wishlistRepository= $wishlistRepository;
       $this->_productCollectionFactory = $productCollectionFactory;
       $this->_productRepository = $productRepository;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->_sellerCollection = $sellerCollection;
    }

    public function addwishlistitems() {

        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
       
        $result = array();
        if(isset($post['product_id'])){
            $product = $this->_productRepository->getById($post['product_id']);
        }

        if(isset($post['customer_id'])){
            $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($post['customer_id'], true);
            $wishlist->addNewItem($product);
            $wishlist->save();
            $wishlist_collection = $wishlist->getItemCollection();
            $wishlistItemData = $wishlist_collection->getData();
            if(count($wishlistItemData)){
                foreach($wishlistItemData as $wishItem):
                        // print_r($wishItem);exit;
                    if(!$wishItem['seller_id']){
                         // Get seller name
                        $sellerCollectionNew = $this->_sellerCollection->getCollection()
                        ->setOrder('position','ASC')
                        ->addFieldToFilter('seller_id',array('in'=>$post['seller_id']));
                        foreach ($sellerCollectionNew as $sellNew) {
                            $sellerName = $sellNew->getName();
                        }
                        // Get seller product price
                        $sellerProductCollectionNew = $this->_sellerProductCollection->getCollection()->addFieldToFilter('product_id', array('in' => $post['product_id']))->addFieldToFilter('seller_id', array('in' => $post['seller_id']));
                        $sellerProductPrice = $sellerProductCollectionNew->getData();
                        // Get doorsetp delivery
                        if($post['price_type'] == 0){
                            $sellerprice = $sellerProductPrice[0]['doorstep_price']; 
                        }
                        // Get pick from store
                        if($post['price_type'] == 1){
                            $sellerprice = $sellerProductPrice[0]['pickup_from_store'];
                        }
                        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
                        $connection = $resource->getConnection();
                        $tableName = $resource->getTableName('wishlist_item');
                        $sql = "UPDATE " . $tableName . " SET seller_id = '" . $post['seller_id'] . "', seller_name = '" . $sellerName . "', seller_price = '" . $sellerprice . "', price_type = '" . $post['price_type'] . "' WHERE wishlist_item_id = " . $wishItem['wishlist_item_id']." AND product_id = " . $wishItem['product_id'];
                        // print_r($sql);exit;
                        $connection->query($sql);
                        $wishlist_collection = $wishlist->getItemCollection();
                        $wishlistItemData = $wishlist_collection->getData();
                        $result['wishlist_item_id'] = $wishItem['wishlist_item_id'];
                        $result['wishlist_id'] = $wishItem['wishlist_id'];
                    }
                endforeach;
            }
        }
        if(count($result)){
            $response = array($result);
        }else{
            $response = array("Success" => "Product already present in wishlist");
        }
        return $response;
    } 
}

