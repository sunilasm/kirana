<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Controller\Marketplace\Order;


use Magento\Framework\App\Action\Context;


class Invoice extends \Lof\MarketPlace\Controller\Marketplace\Order {

    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
      

        if ($order = $this->_initOrder()) { 
            try {
                $helper =  $this->_objectManager->create('Lof\MarketPlace\Helper\Data');
                $sellerId = $helper->getSellerId();
                $orderId = $order->getId();
                
                if ($order->canUnhold()) {
                    $this->messageManager->addError(
                        __('Can not create invoice as order is in HOLD state')
                    );
                } else {
                    $data = [];
                    $data['send_email'] = 1;
                    $helper = $this->_objectManager->create(
                        'Lof\MarketPlace\Helper\Data'
                    );
                 
                    $items = [];
                    $itemsarray = [];
                    $shippingAmount = 0;
                    $couponAmount = 0;
                    $codcharges = 0;
                    $paymentCode = '';
                    $paymentMethod = '';
                    $codCharges = 0;
                    $tax = 0;
                    if ($order->getPayment()) {
                        $paymentCode = $order->getPayment()->getMethod();
                    }
                    $trackingsdata = $this->_objectManager->create(
                        'Lof\MarketPlace\Model\Order'
                    )
                    ->getCollection()
                    ->addFieldToFilter(
                        'order_id',
                        $orderId
                    )
                    ->addFieldToFilter(
                        'seller_id',
                        $sellerId
                    );
                   
                    foreach ($trackingsdata as $tracking) {
                        $shippingAmount = $tracking->getShippingAmount();
                        $couponAmount = $tracking->getCouponAmount();
                    }
                    
                    
                    $collection = $this->_objectManager->create(
                        'Lof\MarketPlace\Model\Orderitems'
                    )
                    ->getCollection()
                    ->addFieldToFilter(
                        'order_id',
                        ['eq' => $orderId]
                    )
                    ->addFieldToFilter(
                        'seller_id',
                        ['eq' => $sellerId]
                    );
                    foreach ($collection as $saleproduct) {
                        $orderData = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
                        $orderItems = $orderData->getAllItems();
                        foreach($orderItems as $item) {
                            if($item->getData('item_id') == $saleproduct->getData('order_item_id')) {
                                $tax = $tax + $item->getData('tax_amount');
                                array_push($items, $saleproduct->getData('order_item_id'));
                            }
                        } 
                    }

                    $itemsarray = $this->_getItemQtys($order, $items);

                    if (count($itemsarray) > 0 && $order->canInvoice()) {
                        $invoice = $this->_objectManager->create(
                            'Magento\Sales\Model\Service\InvoiceService'
                        )->prepareInvoice($order, $itemsarray['data']);
                        if (!$invoice) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('We can\'t save the invoice right now.')
                            );
                        }
                        if (!$invoice->getTotalQty()) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('You can\'t create an invoice without products.')
                            );
                        }
                        $this->_coreRegistry->register(
                            'current_invoice',
                            $invoice
                        );

                        if (!empty($data['capture_case'])) {
                            $invoice->setRequestedCaptureCase(
                                $data['capture_case']
                            );
                        }

                        if (!empty($data['comment_text'])) {
                            $invoice->addComment(
                                $data['comment_text'],
                                isset($data['comment_customer_notify']),
                                isset($data['is_visible_on_front'])
                            );

                            $invoice->setCustomerNote($data['comment_text']);
                            $invoice->setCustomerNoteNotify(
                                isset($data['comment_customer_notify'])
                            );
                        }

                        $invoice->setBaseDiscountAmount($couponAmount);
                        $invoice->setDiscountAmount($couponAmount);
                        $invoice->setShippingAmount($shippingAmount);
                        $invoice->setBaseShippingInclTax($shippingAmount);
                        $invoice->setBaseShippingAmount($shippingAmount);
                        $invoice->setSubtotal($itemsarray['subtotal']);
                        $invoice->setBaseSubtotal($itemsarray['baseSubtotal']);
                        
                        $invoice->setGrandTotal(
                            $itemsarray['subtotal'] +
                            $shippingAmount +
                            $codcharges +
                            $tax -
                            $couponAmount
                        );
                        $invoice->setBaseGrandTotal(
                            $itemsarray['subtotal'] +
                            $shippingAmount +
                            $codcharges +
                            $tax -
                            $couponAmount
                        );

                        $invoice->register();

                        $invoice->getOrder()->setCustomerNoteNotify(
                            !empty($data['send_email'])
                        );
                        $invoice->getOrder()->setIsInProcess(true);

                        $transactionSave = $this->_objectManager->create(
                            'Magento\Framework\DB\Transaction'
                        )->addObject(
                            $invoice
                        )->addObject(
                            $invoice->getOrder()
                        );
                        $transactionSave->save();

                        $invoiceId = $invoice->getId();

                        $this->_invoiceSender->send($invoice);

                        $this->messageManager->addSuccess(
                            __('Invoice has been created for this order.')
                        );
                        
                       
                    } else {
                        $this->messageManager->addError(
                            __('Cannot create Invoice for this order.')
                        );
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t create invoice for order right now.')
                );
            }

            return $this->resultRedirectFactory->create()->setPath(
                'catalog/sales/orderview/view',
                [
                    'id' => $order->getEntityId(),
                    '_secure' => $this->getRequest()->isSecure(),
                ]
            );
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'catalog/sales/order',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
        
    }
}
