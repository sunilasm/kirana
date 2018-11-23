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


class Email extends \Lof\MarketPlace\Controller\Marketplace\Order {

    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
      

        if ($order = $this->_initOrder()) { 
            try {
                $this->_orderManagement->notify($order->getEntityId());
                $this->messageManager->addSuccess(
                    __('You sent the order email.')
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t send the email order right now.')
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
