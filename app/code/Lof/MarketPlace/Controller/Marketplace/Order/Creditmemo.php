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


class Creditmemo extends \Lof\MarketPlace\Controller\Marketplace\Order {

    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
      

        if ($order = $this->_initOrder()) { 
            //try {
                 $creditmemo = $this->_initOrderCreditmemo($order);
               
                if ($creditmemo) {
                    if (!$creditmemo->isValidGrandTotal()) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('The credit memo\'s total must be positive.')
                        );
                    }
                    $data = $this->getRequest()->getParam('creditmemo');
              
                    if (!empty($data['comment_text'])) {
                        $creditmemo->addComment(
                            $data['comment_text'],
                            isset($data['comment_customer_notify']),
                            isset($data['is_visible_on_front'])
                        );
                        $creditmemo->setCustomerNote($data['comment_text']);
                        $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
                    }

                    if (isset($data['do_offline'])) {
                        //do not allow online refund for Refund to Store Credit
                        if (!$data['do_offline'] && !empty($data['refund_customerbalance_return_enable'])) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('Cannot create online refund for Refund to Store Credit.')
                            );
                        }
                    }
                    $creditmemoManagement = $this->_objectManager->create(
                        'Magento\Sales\Api\CreditmemoManagementInterface'
                    );
                    $creditmemo = $creditmemoManagement
                    ->refund($creditmemo, (bool) $data['do_offline'], !empty($data['send_email']));

                    if (!empty($data['send_email'])) {
                        $this->_creditmemoSender->send($creditmemo);
                    }

                    if (!empty($data['send_email'])) {
                        $this->_creditmemoSender->send($creditmemo);
                    }

                    $this->messageManager->addSuccess(__('You created the credit memo.'));
                }
           /* } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t create credit memo for order right now.')
                );
            }*/

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
    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return $this|bool
     */
    protected function _initCreditmemoInvoice($order)
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $invoiceId = $this->_objectManager->create('Lof\MarketPlace\Model\Invoice')->load($order->getId(),'seller_order_id')->getInvoiceId();
       
        if ($invoiceId) {
            $invoice = $this->_invoiceRepository->get($invoiceId);
            $invoice->setOrder($order);
            if ($invoice->getId()) {
                return $invoice;
            }
        }

        return false;
    }

    /**
     * Initialize creditmemo model instance.
     *
     * @return \Magento\Sales\Model\Order\Creditmemo|false
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _initOrderCreditmemo($order)
    {
        $data = $this->getRequest()->getPost('creditmemo');
        $refundData = $this->getRequest()->getParams();

        $creditmemo = false;

        $sellerId = $this->_customerSession->getCustomerId();
        $orderId = $order->getId();

        $invoice = $this->_initCreditmemoInvoice($order);
       
        $items = [];
        $itemsarray = [];
        $shippingAmount = 0;
        $codcharges = 0;
        $paymentCode = '';
        $paymentMethod = '';
        if ($order->getPayment()) {
            $paymentCode = $order->getPayment()->getMethod();
        }
        $trackingsdata = $this->_objectManager->create(
            'Lof\MarketPlace\Model\Order'
        )->getCollection()
        ->addFieldToFilter(
            'order_id',
            ['eq' => $orderId]
        )
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $sellerId]
        );

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
                    array_push($items, $saleproduct->getData('order_item_id'));
                }
            } 
        }

        $savedData = $this->_getItemData($order, $items);
       
        $qtys = [];
        foreach ($savedData as $orderItemId => $itemData) {
            if (isset($itemData['qty'])) {
                $qtys[$orderItemId] = $itemData['qty'];
            }
            if (isset($refundData['creditmemo']['items'][$orderItemId]['back_to_stock'])) {
                $backToStock[$orderItemId] = true;
            }
        }

        if (empty($refundData['creditmemo']['shipping_amount'])) {
            $refundData['creditmemo']['shipping_amount'] = 0;
        }
        if (empty($refundData['creditmemo']['adjustment_positive'])) {
            $refundData['creditmemo']['adjustment_positive'] = 0;
        }
        if (empty($refundData['creditmemo']['adjustment_negative'])) {
            $refundData['creditmemo']['adjustment_negative'] = 0;
        }
        if (!$shippingAmount >= $refundData['creditmemo']['shipping_amount']) {
            $refundData['creditmemo']['shipping_amount'] = 0;
        }
        $refundData['creditmemo']['qtys'] = $qtys;
       
        if ($invoice) {
            $creditmemo = $this->_creditmemoFactory->createByInvoice(
                $invoice,
                $refundData['creditmemo']
            );
        } else {
            $creditmemo = $this->_creditmemoFactory->createByOrder(
                $order,
                $refundData['creditmemo']
            );
        }
        
        /*
         * Process back to stock flags
         */
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            $parentId = $orderItem->getParentItemId();
            if (isset($backToStock[$orderItem->getId()])) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } elseif (empty($savedData)) {
                $creditmemoItem->setBackToStock(
                    $this->_stockConfiguration->isAutoReturnEnabled()
                );
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }
       
        $this->_coreRegistry->register('current_creditmemo', $creditmemo);
       
        return $creditmemo;
    }
     /**
     * Get requested items qtys.
     */
    protected function _getItemData($order, $items)
    {
        $refundData = $this->getRequest()->getParams();
        
        $data['items'] = [];
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getItemId(), $items)
                && isset($refundData['creditmemo']['items'][$item->getItemId()]['qty'])) {
                $data['items'][$item->getItemId()]['qty'] = intval(
                    $refundData['creditmemo']['items'][$item->getItemId()]['qty']
                );

                $_item = $item;
                // for bundle product
                $bundleitems = array_merge([$_item], $_item->getChildrenItems());
                if ($_item->getParentItem()) {
                    continue;
                }

                if ($_item->getProductType() == 'bundle') {
                    foreach ($bundleitems as $_bundleitem) {
                        if ($_bundleitem->getParentItem()) {
                            $data['items'][$_bundleitem->getItemId()]['qty'] = intval(
                                $refundData['creditmemo']['items'][$_bundleitem->getItemId()]['qty']
                            );
                        }
                    }
                }
            } else {
                if (!$item->getParentItemId()) {
                    $data['items'][$item->getItemId()]['qty'] = 0;
                }
            }
        }
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = [];
        }

        return $qtys;
    }
}
