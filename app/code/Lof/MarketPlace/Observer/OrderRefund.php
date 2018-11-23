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
class OrderRefund implements ObserverInterface {

    protected $helper;

    protected $sellerProduct;

    protected $calculate;
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
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct
    ) {
        $this->calculate = $calculate;
        $this->helper      = $helper;
        $this->sellerProduct = $sellerProduct; 
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
        $creditmemo = $observer->getEvent ()->getCreditmemo ();

        $order = $creditmemo->getOrder ();
        $customerId = $order->getCustomerId();
        $sellerNotRefund = $allRefundId = $sellerRefund = array ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $orderRefund = $objectManager->create('Lof\MarketPlace\Model\Refund');
        $_order = $objectManager->create('Lof\MarketPlace\Model\Order');
        $helper = $objectManager->get('Lof\MarketPlace\Helper\Data');
        $orderitems= $objectManager->create('Lof\MarketPlace\Model\Orderitems');
        $seller = $objectManager->create('Lof\MarketPlace\Model\Seller');
        
        $commission = 100;
        foreach ( $creditmemo->getItems () as $item ) {
        	 /**
             * Get Product Data
             * 
             * @var int(Product Id)
             */
            $productId = $item->getProductId ();
            /**
             * Create object instance
             */
            
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
             * Assign commissiom
             */
            $productComission = $product->getCommission();

            /**
             * Checking for seller id exist or not
             */
            if (! empty ( $sellerId )) {
                /**
                 * Send email notification to buyer
                 */
                //$this->sendRefundEmailNotification ( $order, $productId, $sellerId, 'buyer' );
                /**
                 * Set email notification to seller
                 */
                //$this->sendRefundEmailNotification ( $order, $productId, $sellerId, 'Seller' );
            }
            /**
             * Checking for seller id exist or not
             */
            if (! empty ( $sellerId )) {
                if(!isset($sellerRefund[$sellerId])) $sellerRefund[$sellerId]=[];
                    $sellerRefund[$sellerId][] = $item;
            }
            $currentTimestamp = (new \DateTime())->getTimestamp();

        }
       
        foreach($sellerRefund as $sellerId => $items){
            
        	$orderSeller= $objectManager->create('Lof\MarketPlace\Model\Order')->load($sellerId,'seller_id')->load($order->getId(),'order_id');
           
            $sellerOrderId = $order->getId();
            $refundData = [
            'seller_id' => $sellerId,
            'order_id' => $sellerOrderId,
            'creditmemo_id' => $creditmemo->getId(),
            'state' => $creditmemo->getState(),
            'status' => __('Refunded'),
            'subtotal' => 0,
            'base_subtotal' => 0,
            'tax_amount' => 0,
            'base_tax_amount' => 0,
            'shipping_tax_amount' => 0,
            'base_shipping_tax_amount' => 0,
            'discount_amount'  => 0,
            'base_discount_amount' => 0,
            'shipping_amount' => 0,
            'base_shipping_amount' => 0,
            'subtotal_incl_tax' => 0,
            'base_subtotal_incl_tax' => 0,
            'total_qty' => 0,
            'updated_at' => $currentTimestamp,
            'shipping_incl_tax' => 0,
            'base_shipping_incl_tax' => 0,
            'grand_total' => 0,
            'base_grand_total' => 0,
            'base_total_refunded' => 0,
            'refunded' => 0
            ];

        	foreach ($items as $key => $item) {
                    
                if(abs($item->getData('price')) > 0) {   
    	            $refundData['grand_total'] += $refundData['subtotal'] +
    	                $refundData['shipping_amount'] +
    	                $refundData['tax_amount'] -
    	                $refundData['discount_amount'];
    	            $refundData['base_grand_total'] += $refundData['base_subtotal'] +
    	                $refundData['base_shipping_amount'] +
    	                $refundData['base_tax_amount'] -
    	                $refundData['base_discount_amount'];
                    $refundData['total_qty'] += $item->getData('qty');
                    $commission = $this->helper->getCommission($sellerId,$item->getProductId());
                    $priceCommission = $this->calculate->calculate($commission,$item);    
                    $order_items = $orderitems->getCollection()->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('seller_id',$sellerId)->addFieldToFilter('product_sku',$item->getSku());
                
                    foreach ($order_items as $key => $order_item) {
                        $seller_commission = $priceCommission + $order_item->getData('seller_commission_refund');
                        $seller_amount = $seller_commission;
                        $admin_commission = $item->getData('row_total') + $item->getData('tax_amount') - $item->getData('discount_amount') - $priceCommission  + $order_item->getData('admin_commission_refund');
                        $qtyRefunded = $order_item->getData('qty_refunded')+$item->getData('qty');
                        $order_item->setQtyRefunded($qtyRefunded)->setAdminCommissionRefund($admin_commission)->setSellerCommissionRefund($seller_commission);
                        if((int)$order_item->getProductQty() >= $qtyRefunded) {
                            $order_item->save();
                        }
                        $productId = $order_item->getProductId ();
                    }    
    	        	$refundData['refunded'] += $priceCommission;
                }	
	        }
            
	         $refundDataObj = new \Magento\Framework\DataObject($refundData);

            $refundData = $refundDataObj->getData();
            
	      	$orderRefund->setData($refundData)->save();
            $sellerData = $seller->load($sellerId,'seller_id');
	        $description = __('Refund from order').' #'.$sellerOrderId;
	        $refund = - $refundData['refunded'];
            $count_refund = $orderSeller->getIsRefunded() + $refundData['total_qty'];
            $sale = $sellerData->getSale() - $refundData['total_qty'];
            $sellerAmount = $orderSeller->getSellerAmount() - $refundData['refunded'];
            if($count_refund == $orderSeller->getIsInvoiced()) {
                $orderSeller->setStatus('closed')->setSellerAmount($sellerAmount)->setIsRefunded($count_refund)->save();
            } else {
                $orderSeller->setSellerAmount($sellerAmount)->setIsRefunded($count_refund)->save();
            }
	        $this->updateSellerAmount ( $sellerId,$refund,$description);
            
	        $total_sold = $sellerData->getData('total_sold') +  $refund;
            $sellerData->setSale($sale)->setTotalSold($total_sold)->save();
        }
      

    }
     /**
     * Get commission value
     * 
     * @param float $commission
     * @param float $productPrice
     * @param int $productQty
     * 
     * @return float
     */
    public function getCommissionValue($commissionProduct,$productPrice){
        if ($commissionProduct != 0) {
            $commissionPerProduct = $productPrice * ($commissionProduct / 100);
            $commission = $commissionPerProduct;
        } else {
            $commission = 0;
        }
        return $commission;
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
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load seller by seller id
         */
        $sellerModel = $objectManager->get ( 'Lof\MarketPlace\Model\Amount' );

        $amount_transaction = $objectManager->get('Lof\MarketPlace\Model\Amounttransaction');

        $date = $objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime');

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
        $sellerDetails->save ();

        $amount_transaction->save();
    }

}