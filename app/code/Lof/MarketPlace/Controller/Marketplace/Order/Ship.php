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


class Ship extends \Lof\MarketPlace\Controller\Marketplace\Order {

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
                $trackingid = '';
                $carrier = '';
                $trackingData = [];
                $paramData = $this->getRequest()->getParams();
                if (!empty($paramData['tracking_id'])) {
                    $trackingid = $paramData['tracking_id'];
                    $trackingData[1]['number'] = $trackingid;
                    $trackingData[1]['carrier_code'] = 'custom';
                }
                if (!empty($paramData['carrier'])) {
                    $carrier = $paramData['carrier'];
                    $trackingData[1]['title'] = $carrier;
                }

                if (!empty($paramData['api_shipment'])) {
                    $this->_eventManager->dispatch(
                        'generate_api_shipment',
                        [
                            'api_shipment' => $paramData['api_shipment'],
                            'order_id' => $orderId,
                        ]
                    );
                    $shipmentData = $this->_customerSession->getData('shipment_data');
                    $apiName = $shipmentData['api_name'];
                    $trackingid = $shipmentData['tracking_number'];
                    $trackingData[1]['number'] = $trackingid;
                    $trackingData[1]['carrier_code'] = 'custom';
                    $this->_customerSession->unsetData('shipment_data');
                }

                if (empty($paramData['api_shipment']) || $trackingid != '') {
                    if ($order->canUnhold()) {
                        $this->messageManager->addError(
                            __('Can not create shipment as order is in HOLD state')
                        );
                    } else {
                        $items = [];
                        $shippingAmount = 0;

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
                                    array_push($items, $saleproduct->getData('order_item_id'));
                                }
                            } 
                        }

                        $itemsarray = $this->_getShippingItemQtys($order, $items);

                        if (count($itemsarray) > 0) {
                            $shipment = false;
                            $shipmentId = 0;
                            if (!empty($paramData['shipment_id'])) {
                                $shipmentId = $paramData['shipment_id'];
                            }
                            if ($shipmentId) {
                                $shipment = $this->_objectManager->create(
                                    'Magento\Sales\Model\Order\Shipment'
                                )->load($shipmentId);
                            } elseif ($orderId) {
                                if ($order->getForcedDoShipmentWithInvoice()) {
                                    $this->messageManager
                                    ->addError(
                                        __('Cannot do shipment for the order separately from invoice.')
                                    );
                                }
                                if (!$order->canShip()) {
                                    $this->messageManager->addError(
                                        __('Cannot do shipment for the order.')
                                    );
                                }

                                $shipment = $this->_prepareShipment(
                                    $order,
                                    $itemsarray['data'],
                                    $trackingData
                                );
                            }
                            if ($shipment) {
                                $comment = '';
                                $shipment->getOrder()->setCustomerNoteNotify(
                                    !empty($data['send_email'])
                                );
                                $shippingLabel = '';
                                if (!empty($data['create_shipping_label'])) {
                                    $shippingLabel = $data['create_shipping_label'];
                                }
                                $isNeedCreateLabel=!empty($shippingLabel) && $shippingLabel;
                                $shipment->getOrder()->setIsInProcess(true);

                                $transactionSave = $this->_objectManager->create(
                                    'Magento\Framework\DB\Transaction'
                                )->addObject(
                                    $shipment
                                )->addObject(
                                    $shipment->getOrder()
                                );
                                $transactionSave->save();

                                $shipmentId = $shipment->getId();

                                $courrier = 'custom';
                               

                                $this->_shipmentSender->send($shipment);

                                $shipmentCreatedMessage = __('The shipment has been created.');
                                $labelMessage = __('The shipping label has been created.');
                                $this->messageManager->addSuccess(
                                    $isNeedCreateLabel ? $shipmentCreatedMessage.' '.$labelMessage
                                    : $shipmentCreatedMessage
                                );
                            }
                        }
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t ship order right now.')
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

     /**
     * Prepare shipment.
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return \Magento\Sales\Model\Order\Shipment|false
     */
    protected function _prepareShipment($order, $items, $trackingData)
    {
        $shipment = $this->_shipmentFactory->create(
            $order,
            $items,
            $trackingData
        );

        if (!$shipment->getTotalQty()) {
            return false;
        }

        return $shipment->register();
    }
}
