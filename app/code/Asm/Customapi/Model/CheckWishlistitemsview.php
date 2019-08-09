<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\CheckWishlistInterface;
 
class CheckWishlistitemsview implements CheckWishlistInterface
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
    
    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Wishlist\Model\WishlistFactory $wishlistRepository
    ) {
       $this->request = $request;
       $this->_wishlistRepository= $wishlistRepository;
    }

    public function checkwishlistitems() {

        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $customer_id = (isset($post['customer_id'])) ? $post['customer_id'] : 0;
        $product_id = (isset($post['product_id'])) ? $post['product_id'] : 0;
        if($customer_id && $product_id)
        {
            // Get Wish List items
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $wishlist = $objectManager->get('\Magento\Wishlist\Model\Wishlist');
            $wishlist_collection = $wishlist->loadByCustomerId($post['customer_id'], true)->getItemCollection();
            $data = $wishlist_collection->getData();
            $flag = false;
            foreach ($data as $item) {
                if($product_id == $item['product_id'])
                {
                    $flag = true;
                }
            }
            $result = array("Success" => $flag);
        }
        else
        {
            $result = array("Success" => "Customer Id and product id required");
        }
        return $result;
    } 
}

