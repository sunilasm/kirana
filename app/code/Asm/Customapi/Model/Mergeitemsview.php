<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\MergeItemInterface;
use Retailinsights\Promotion\Model\PromoTableFactory;

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
    private $eventManager;
    protected $_promoFactory;
    
    public function __construct(
        PromoTableFactory $promoFactory,
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Quote\Model\QuoteFactory $quoteFactory,
       \Magento\Framework\Event\Manager $eventManager,
       \Asm\Customapi\Model\Addresschangeview $cartFunction
    ) {
       $this->request = $request;
       $this->quoteFactory = $quoteFactory;
       $this->_cart = $cartFunction;
       $this->eventManager = $eventManager;
       $this->_promoFactory = $promoFactory;
    }

    public function mergecartitems() {

        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/quoteman.log'); 
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('@@@@  Guest Quote Id'.$post['guest_quote_id']);
        $logger->info('@@@@   Quote Id'.$post['quote_id']);

        $quoteId = $post['guest_quote_id'];
        $promotions = $this->_promoFactory->create()->getCollection()
                ->addFieldToFilter('cart_id', $quoteId);
        $itemInfo = '';
        foreach($promotions->getData() as $k => $val){     
            if($val['cart_id']==$quoteId){
                $itemInfo = json_decode($val['item_qty'],true);        
            }
        }
        
        $guestquote = $this->quoteFactory->create()->load($post['guest_quote_id']);
        $items = $guestquote->getAllItems();
        $totalItems = count($items);
        $movedProductsArray = array();
        $currentProductsArray = array();
        $bxgy = 0;
        foreach ($items as $item) 
        {
            $qty = $item->getQty();
            $bxgy = 0;
            if(isset($itemInfo) && !empty($itemInfo)){
                foreach($itemInfo as $k => $itemArray){
                    foreach($itemArray as $key => $value){
                        $itemData = json_decode($value);
                        if(isset($itemData->qty) && !empty($itemData->qty)) {
                            if($itemData->type == "BXGX" && $itemData->parent == $item->getItemId()){
                                $qty = ($item->getQty()-$itemData->qty);
                            }
                            if(($itemData->type == "BXGY" || $itemData->type == "BWGY") && $itemData->id == $item->getItemId()){
                                $bxgy = 1;
                            }  
                        }
                    }
                }
            }
            // Add in cart
            if(isset($post['quote_id']) && ($bxgy == 0)){
                $this->_cart->addItem($post['quote_id'], $item->getProduct_id(), $item->getPriceType(),$item->getSellerId(),$qty,$item->getSku());
            }
            // Remove in cart
            if(isset($post['guest_quote_id'])){
                $movedProductsArray[] = $item->getProduct_id();
                $this->_cart->removeItem($post['guest_quote_id'], $item->getItemId());
            }
        }
        $this->eventManager->dispatch('promotion_after_add_cart', ['quoteid' => $post['quote_id'] ]); 
        $currentCartItems = $totalItems - count($movedProductsArray);
        $data = array("total_count" => $totalItems, "moved_count" => count($movedProductsArray));
        $response = array($data);
        return $response; 
    } 
}

