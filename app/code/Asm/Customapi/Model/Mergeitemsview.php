<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\MergeItemInterface;
 
class Mergeitemsview implements MergeItemInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Quote\Model\QuoteFactory $quoteFactory,
       \Asm\Customapi\Model\Addresschangeview $cartFunction
    ) {
       $this->request = $request;
       $this->quoteFactory = $quoteFactory;
       $this->_cart = $cartFunction;
    }

    public function mergecartitems() {

        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();

        $guestquote = $this->quoteFactory->create()->load($post['guest_quote_id']);
        $items = $guestquote->getAllItems();
        foreach ($items as $item) 
        {
            // Add in cart
            if(isset($post['quote_id'])){
                $this->_cart->addItem($post['quote_id'], $item->getProduct_id(), $item->getPriceType(),$item->getSellerId(),$item->getQty(),$item->getSku());
            }
            // Remove in cart
            if(isset($post['guest_quote_id'])){
                $this->_cart->removeItem($post['guest_quote_id'], $item->getItemId());
            }
        }

        // if(count($productCollectionArray)){
        //     $result = $productCollectionArray;
        // }else{
        //     $result = array("Success" => "No products in wishlist");
        // }
        // return $result;
    } 
}

