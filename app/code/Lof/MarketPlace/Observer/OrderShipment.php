<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_FollowUpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
/**
 * This class contains order refund functions
 */
class OrderShipment implements ObserverInterface {

    protected $helper;

    protected $sellerProduct;

    protected $calculate;

    protected $sender;
    /**
     * Constructor
     *
     * @param \Magento\GoogleAdwords\Helper\Data $image
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $collection
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct
    ) {
        $this->calculate = $calculate;
        $this->helper      = $helper;
        $this->sellerProduct = $sellerProduct; 
        $this->sender = $sender;
    }
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
         /**
         * Get Order Details
         * 
         * @var unknown
         */
        $shipment = $observer->getEvent ()->getShipment ();
        $invoice = $observer->getEvent ()->getInvoice ();
        $order = $shipment->getOrder ();
         /**
         * Create object instance
         */
         $customerId = $order->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerNotInvoice = $allSellerId = $sellerShipment = array ();

        foreach ( $shipment->getAllItems () as $item ) {
              $orderItem = $item->getOrderItem();
            /**
             * Get Product Data
             * 
             * @var int(Product Id)
             */
            $productId = $item->getProductId ();
             
            /**
             * Load product data by product id
             */
            $product = $objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            /**
             * Assign seller id
             */
           $priceComparison = $this->helper->isEnableModule('Lofmp_PriceComparison');
            if($priceComparison) {
                $assignHelper = $objectManager->create('Lofmp\PriceComparison\Helper\Data');
                $quote = $assignHelper->getQuoteCollection()->addFieldToFilter('product_id',$productId)->addFieldToFilter('customer_id',$customerId)->getLastItem();
                if(count($quote->getData())>0) {
                    $sellerId = $quote->getSellerId();
                } else {
                    $sellerId = $product->getSellerId();
                }
            } else {
                $sellerId = $product->getSellerId();
            }
           /**
             * Checking for seller id exist or not
             */
            if (! empty ( $sellerId )) {
                if(!isset($sellerShipment[$sellerId])) $sellerShipment[$sellerId]=[];
                    $sellerShipment[$sellerId][] = $item;
            }
            $currentTimestamp = (new \DateTime())->getTimestamp();
        }
        
        foreach($sellerShipment as $sellerId => $items){
           
            $sellerOrder = $objectManager->create('Lof\MarketPlace\Model\Order')->load($sellerId,'seller_id')->load($order->getId(),'order_id');
            $seller =  $objectManager->create('Lof\MarketPlace\Model\Seller')->load($sellerId,'seller_id');
            $sellerInvoice = $objectManager->create('Lof\MarketPlace\Model\Invoice')->load($sellerId,'seller_id')->load($order->getId(),'seller_order_id');
            
            $orderitems= $objectManager->create('Lof\MarketPlace\Model\Orderitems');
            $count_sale = 0;
             foreach ($orderitems->getCollection()->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('seller_id',$sellerId) as $key => $value) {
                    $orderitem = $objectManager->create('Lof\MarketPlace\Model\Orderitems')->load($sellerId,'seller_id')->load($value->getId(),'id');
                    $count_sale = $count_sale + $orderitem->getData('qty_invoiced');
                    $orderitem->setStatus('complete');
                    $orderitem->save();  
            }
           
            foreach ($sellerOrder->getCollection()->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('seller_id',$sellerId) as $key => $_sellerOrder) {
                $_sellerOrder->setIsShiped($count_sale);
                $_sellerOrder->setStatus(Order::STATE_COMPLETE);
                $_sellerOrder->save();
                $description = __('Amount from order').' #'.$_sellerOrder->getOrderId().','.__('invoice').' #'.$sellerInvoice->getInvoiceId();
                
                     
                if($sellerId != 9) {
                    $this->updateSellerAmount ( $sellerId, $_sellerOrder->getSellerAmount(),$description);
                }

                if($this->helper->getConfig('email_settings/enable_send_email')) {
                    //$invoiceRepositoryInterface = $objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface');
                    //$invoice_status = $invoiceRepositoryInterface->create()->getStates()[$invoice->getState()]->getText();
                    
                    $data = [];
                    $data['email']= $seller->getData('email');
                    $data['name'] = $seller->getData('name');
                    $data['order_id'] = $sellerOrder->getOrderId();
                    $data['order_status'] = Order::STATE_COMPLETE;

                    $this->sender->newShipment($data);
                }
            }

        }
            
        //send email to vendors
        //$this->_shipmentSender->send($shipment,true);
        return $this;
    }

    /**
     * Update seller amount
     *
     * @param int $updateSellerId            
     * @param double $totalAmount            
     *
     * @return void
     */
    public function updateSellerAmount($updateSellerId, $totalAmount,$description) {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerModel = $objectManager->create ( 'Lof\MarketPlace\Model\Amount' );

        $amount_transaction = $objectManager->create('Lof\MarketPlace\Model\Amounttransaction');

        $date = $objectManager->create('\Magento\Framework\Stdlib\DateTime\DateTime');
        

        $sellerDetails = $sellerModel->load ( $updateSellerId, 'seller_id' );

        /**
         * Get remaining amount
         */
        $remainingAmount = $sellerDetails->getAmount ();
        /**
         * Total remaining amount
         */
        $totalRemainingAmount = $remainingAmount + $totalAmount;
        /**
         * Set total remaining amount
         */
        $amount_transaction->setSellerId($updateSellerId)->setAmount($totalAmount)->setBalance($totalRemainingAmount)->setDescription($description)->setUpdatedAt($date->gmtDate());

        $sellerDetails->setSellerId($updateSellerId)->setAmount($totalRemainingAmount);

       
        /**
         * Save remaining amount
         */
        $amount_transaction->save();
        $sellerDetails->save ();
    }

}