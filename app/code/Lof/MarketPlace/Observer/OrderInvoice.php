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
class OrderInvoice implements ObserverInterface {

    protected $helper;

    protected $sellerProduct;

    protected $calculate;

    protected $sender;

    protected $amount;
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
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\AmountFactory $amount
    ) {
        $this->amount = $amount;
        $this->sender = $sender;
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
        $invoice = $observer->getEvent ()->getInvoice ();
        /**
         * Create object instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $order = $invoice->getOrder ();
        $sellerNotInvoice = $allSellerId = $sellerInvoice = array ();
        $orderInvoice = $objectManager->create('Lof\MarketPlace\Model\Invoice');
        $orderitems= $objectManager->create('Lof\MarketPlace\Model\Orderitems');
        $customerId = $order->getCustomerId();
        foreach ( $invoice->getAllItems () as $item ) {
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
                if(!isset($sellerInvoice[$sellerId])) $sellerInvoice[$sellerId]=[];
                    $sellerInvoice[$sellerId][] = $item;
            }
            $currentTimestamp = (new \DateTime())->getTimestamp();
        }    
        foreach($sellerInvoice as $sellerId => $items){
            $ordersellerCollection = $objectManager->create('Lof\MarketPlace\Model\Order')->getCollection();
            $ordersellerCollection->addFieldToFilter("seller_id", $sellerId)
                                  ->addFieldToFilter("order_id", $order->getId());
            $orderSellerData = $ordersellerCollection->getFirstItem();

            if($orderSellerData) {
                $orderSeller= $objectManager->create('Lof\MarketPlace\Model\Order')->load((int)$orderSellerData->getId());
            } else {
                $orderSeller= $objectManager->create('Lof\MarketPlace\Model\Order')->load($sellerId,'seller_id')->load($order->getId(),'order_id');
                if(!$orderSeller->getId()){
                    $orderSeller->setSellerId($sellerId);
                    $orderSeller->setOrderId($order->getId());
                }
            }
           $sellerDatas = $objectManager->get ( 'Lof\MarketPlace\Model\Seller' )->load ( $sellerId, 'seller_id' );
            $sellerOrderId = $order->getId();
         
            $invoiceData = [
            'seller_id' => $sellerId,
            'seller_order_id' => $sellerOrderId,
            'invoice_id' => $invoice->getId(),
            'state' => $invoice->getState(),
            'order_id' => $invoice->getOrderId(),
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
            'total_qty' => sizeof($items),
            'updated_at' => $currentTimestamp,
            'shipping_incl_tax' => 0,
            'base_shipping_incl_tax' => 0,
            'grand_total' => 0,
            'base_grand_total' => 0,
            'base_total_refunded' => 0,
            ];
            $seller_amount = 0;
            $seller_commission = 0;
            foreach($items as $item){
                $commission = $this->helper->getCommission($sellerId,$item->getProductId());
                $priceCommission = $this->calculate->calculate($commission,$item);

                $invoiceData['subtotal'] += $item->getData('row_total');
                $invoiceData['base_subtotal'] += $item->getData('base_row_total');
                $invoiceData['tax_amount'] += $item->getData('tax_amount');
                $invoiceData['base_tax_amount'] += $item->getData('base_tax_amount');
                $invoiceData['discount_amount'] += $item->getData('discount_amount');
                $invoiceData['base_discount_amount'] += $item->getData('base_discount_amount');
                $invoiceData['subtotal_incl_tax'] += $item->getData('row_total_incl_tax');
                $invoiceData['base_subtotal_incl_tax'] += $item->getData('base_row_total_incl_tax');
                
                
                $order_items = $orderitems->getCollection()->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('seller_id',$sellerId)->addFieldToFilter('product_sku',$item->getSku());
               
               
                foreach ($order_items as $key => $order_item) {
                    $seller_commission = $priceCommission + $order_item->getData('seller_commission');
                    $seller_amount += $seller_commission;
                    $admin_commission = $item->getData('row_total') + $item->getData('tax_amount') - $item->getData('discount_amount') - $priceCommission  + $order_item->getData('admin_commission');
                    $qtyInvoiced = $order_item->getData('qty_invoiced')+$item->getData('qty');
                    $order_item->setQtyInvoiced($qtyInvoiced)->setAdminCommission($admin_commission)->setSellerCommission($seller_commission);
                    if((int)$order_item->getProductQty() >= $qtyInvoiced) {
                        $order_item->save();
                    }
                    
                }

            }

            $invoiceData['grand_total'] = $invoiceData['subtotal'] +
                $invoiceData['shipping_amount'] +
                $invoiceData['tax_amount'] -
                $invoiceData['discount_amount'];
            $invoiceData['base_grand_total'] = $invoiceData['base_subtotal'] +
                $invoiceData['base_shipping_amount'] +
                $invoiceData['base_tax_amount'] -
                $invoiceData['base_discount_amount'];
            $invoiceData['seller_amount'] = $seller_commission;    
          
                
            $invoiceDataObj = new \Magento\Framework\DataObject($invoiceData);

            $invoiceData = $invoiceDataObj->getData();
        
            $orderInvoice->setData($invoiceData)->save();
            
            $seller =  $objectManager->create('Lof\MarketPlace\Model\Seller')->load($sellerId,'seller_id');
            $total_sold = $seller->getTotalSold();
            if(count($seller->getData()) > 0) {
                if(!$orderSeller->canShip()){
                   
                    $orderSeller->setStatus(Order::STATE_COMPLETE);
                    $count_sale = 0;
                    foreach ($orderitems->getCollection()->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('seller_id',$sellerId) as $key => $value) {
                          
                        $orderitem = $objectManager->create('Lof\MarketPlace\Model\Orderitems')->load($sellerId,'seller_id')->load($value->getId(),'id');
                        $orderitem->setStatus('complete');
                        $orderitem->save(); 
                          $count_sale = $count_sale + $orderitem->getData('qty_invoiced');  
                         $total_sold = $total_sold + $orderitem->getData('seller_commission_order');             
                     }
                     $seller->setSale($count_sale)->setTotalSold($total_sold);
                     $seller->save();
                    
                }else{
                    $orderSeller->setStatus(Order::STATE_PROCESSING);
                    $count_sale = 0;
                     foreach ($orderitems->getCollection()->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('seller_id',$sellerId) as $key => $value) {

                        $orderitem = $objectManager->create('Lof\MarketPlace\Model\Orderitems')->load($sellerId,'seller_id')->load($value->getId(),'id');
                        $orderitem->setStatus('processing');
                        $orderitem->save();  
                        $count_sale = $count_sale +  $orderitem->getData('qty_invoiced');  
                        $total_sold = $total_sold + $orderitem->getData('seller_commission_order');                 
                     }
                    $seller->setSale($count_sale)->setTotalSold($total_sold);
                    $seller->save();
     
                }
                  

                if($orderSeller->getStatus() == Order::STATE_COMPLETE){
                    $description = __('Amount from order').' #'.$sellerOrderId.','.__('invoice').' #'.$invoice->getId();
                    
                    $this->updateSellerAmount ( $sellerId,$seller_amount,$description);
                }

                $orderSeller->setIsInvoiced($count_sale);
                $orderSeller->save();

                if($this->helper->getConfig('email_settings/enable_send_email')) {
                    $invoiceRepositoryInterface = $objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface');
                    $invoice_status = $invoiceRepositoryInterface->create()->getStates()[$invoice->getState()]->getText();
                    $data = [];
                    $data['email']= $sellerDatas->getData('email');
                    $data['name'] = $sellerDatas->getData('name');
                    $data['order_id'] = $invoice->getOrderId();
                    $data['invoice_id'] = $invoice->getId();
                    $data['invoice_status'] = $invoice_status;
                    $this->sender->newInvoice($data);
                }
            }
        }
        
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

        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load seller by seller id
         */
    

        $amount_transaction = $objectManager->get('Lof\MarketPlace\Model\Amounttransaction');

        $date = $objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime');

        $sellerDetails = $this->amount->create()->load ( $updateSellerId, 'seller_id' );
       
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
        $amount_transaction->setSellerId($updateSellerId)->setBalance($totalRemainingAmount)->setDescription($description)->setUpdatedAt($date->gmtDate());

        $sellerDetails->setSellerId($updateSellerId)->setAmount($totalRemainingAmount);

        
        /**
         * Save remaining amount
         */
        $sellerDetails->save ();
        $amount_transaction->save();
    }

}