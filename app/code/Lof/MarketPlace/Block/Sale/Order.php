<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
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

namespace Lof\MarketPlace\Block\Sale;

class Order extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Lof\MarketPlace\Model\OrderFactory
    */
    protected $order;
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
    */
    protected $seller;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\Order $order,
        \Lof\MarketPlace\Model\SellerFactory $seller,
        array $data = []
    ) {
        $this->order = $order;
        $this->seller = $seller;
        $this->session           = $customerSession;
        parent::__construct($context, $data);
    }
    /**
     *  get Seller Colection
     *
     * @return Object
     */
     public function getInvoiceCollection(){
        $invoiceCollection = $this->invoice;
        return $invoiceCollection;
    }
    public function isSeller() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load('customer_id',$customerId)->getStatus();
            return $status;
        }
    }

    public function getOrder() {
        $order = $this->order->getCollection()->addFieldToFilter('seller_id',$this->getSellerId());
        return $order;
    }
     public function getOrderDate($date) {
        return $this->formatDate(
            $this->getOrderAdminDate($date),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }
     public function getOrderAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }
     public function getOrderData($order_id) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($order_id);
        return $order;
    }
    public function getSellerId() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $seller = $this->seller->create()->load($customerId,'customer_id');
            return $seller->getData('seller_id');
        }
    }
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl(); 
    }
    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Order'));
        return parent::_prepareLayout ();
    }
}