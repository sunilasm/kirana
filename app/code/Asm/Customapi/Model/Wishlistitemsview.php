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

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Wishlist\Model\WishlistFactory $wishlistRepository
    ) {
       $this->request = $request;
       $this->_wishlistRepository= $wishlistRepository;
    }

    public function wishlistitems() {

        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();

        // $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($post['customer_id'], true);
        // $wishlistCollection = $wishlist->getItemCollection();
        // print_r($post['customer_id']);exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $wishlist = $objectManager->get('\Magento\Wishlist\Model\Wishlist');
        $wishlist_collection = $wishlist->loadByCustomerId($post['customer_id'], true)->getItemCollection();
        $data = $wishlist_collection->getData();
        if(count($data)){
            $result = $data;
        }else{
            $result = array("Success" => "No products in wishlist");
        }
        //$response = array($result);
        return $result;
    } 
}

