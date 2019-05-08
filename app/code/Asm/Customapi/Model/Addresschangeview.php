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

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Quote\Model\QuoteFactory $quoteFactory,
       \Asm\AdvanceSearch\Model\Searchview $inRange,
       \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
       \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
       $this->request = $request;
       $this->quoteFactory = $quoteFactory;
       $this->inRange = $inRange;
       $this->_wishlistRepository= $wishlistRepository;
       $this->_productRepository = $productRepository;
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
        $customerId = $post['customer_id'];
        foreach ($items as $item) 
        {
            if(in_array($item->getSeller_id(), $sellerId)){
                if($customerId){
                    $product = $this->_productRepository->getById($item->getProduct_id());
                    $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);
                    $wishlist->addNewItem($product);
                    $wishlist->save();
                }else{
                    // Remove from cart
                }
            }
        }
        return $data;
    }
   
}
