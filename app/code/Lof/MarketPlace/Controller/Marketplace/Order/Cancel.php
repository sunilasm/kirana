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


class Cancel extends \Lof\MarketPlace\Controller\Marketplace\Order {

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
                $flag =$helper->cancelorder($order, $sellerId);
                
                if ($flag) {
                    $paymentCode = '';
                    $paymentMethod = '';
                    if ($order->getPayment()) {
                        $paymentCode = $order->getPayment()->getMethod();
                    }
                    $orderId = $this->getRequest()->getParam('id');
                   
                    $trackingcoll = $this->_objectManager->create(
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
                    foreach ($trackingcoll as $tracking) {
                        $tracking->setTrackingNumber('canceled');
                        $tracking->setCarrierName('canceled');
                        $tracking->setIsCanceled(1);
                        $tracking->save();
                    }
                    $this->messageManager->addSuccess(
                        __('The order has been cancelled.')
                    );
                } else {
                    $this->messageManager->addError(
                        __('You are not permitted to cancel this order.')
                    );

                    return $this->resultRedirectFactory->create()->setPath(
                        'catalog/sales/order',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t cancel order right now.')
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
