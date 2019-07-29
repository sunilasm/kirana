<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\ReorderInterface;
 
class Reorderview implements ReorderInterface
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
       \Asm\Customapi\Model\Addresschangeview $addproduct
    ) {
       $this->request = $request;
       $this->addproduct = $addproduct;
    }

    public function reorder() {

        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        if(isset($post['order_id'])){
            $orderId = $post['order_id'];
            $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            $orderItems = $order->getAllItems();
            if(isset($post['quote_id'])){
                $quoteId = $post['quote_id'];
                foreach($orderItems as $item)
                {
                  $this->addproduct->addItem($quoteId, $item->getProductId(), $item->getPriceType(), $item->getSellerId(), $item->getQtyOrdered(), $item->getSku());
                }
            }else{
                $data = array("Please create cart");
            }
            $data = array("Success, Please check cart");
        }else{
            $data = array("Required order id");
        }
        // $response = array($data);
        return $data;
    } 
}

