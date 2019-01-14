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

namespace Lof\MarketPlace\Block\Account;

class Navigation extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
    */
    protected $sellerFactory;
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
     * @var \Lof\MarketPlace\Model\Message
    */
    protected $message;
      /**
     * @var \Lof\MarketPlace\Model\MessageDetail
    */
    protected $detail;
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
       \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\Message $message,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        \Lof\MarketPlace\Model\Seller $seller,
        array $data = []
    ) {
        $this->message = $message;
        $this->detail = $detail;
        $this->sellerFactory = $sellerFactory;
        $this->seller = $seller;
        $this->session           = $customerSession;
        parent::__construct($context, $defaultPath);
    }
    /**
     *  get Seller Colection
     *
     * @return Object
     */
     public function getSellerCollection(){
        $sellerCollection = $this->seller->getCollection();
        return $sellerCollection;
    }
    public function isSeller() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
            return $status;
        }
    }

    public function getSellerId() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $sellerId = $this->sellerFactory->create()->load($customerId,'customer_id')->getSellerId();
            return $sellerId;
        }
    }

    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl(); 
    }

    public function getDetail() {
        return $this->detail->getCollection()->addFieldToFilter('sender_id',$this->getSellerId());
    }

    public function getMessage() {
        return $this->message->getCollection()->addFieldToFilter('owner_id',$this->getSellerId());
    }
    public function getDetailUnRead() {
        return $this->getDetail()->addFieldToFilter('is_read',0)->addFieldToFilter('seller_send',0);

    }
    public function getMessageUnRead() {

     
         $data = $this->message->getCollection()->addFieldToFilter('owner_id',$this->session->getCustomerId())->addFieldToFilter('is_read',0);
       
        return $data->getData();
    }
     /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $html        = '';
        $customer    = $this->isSeller();
          $highlight   = '';
        if (!$customer) {

            $html = '<li class="nav item' . $highlight . ' lrw-nav-item"><a href="' . $this->getUrl('lofmarketplace/seller/becomeseller') . '"';
            $html .= $this->getTitle()
            ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
            : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= '<span>'.__("Become A Kirana").'</span>';

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }
            $html .= '</a></li>';


            return $html;
        }

      

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current lrw-nav-item">';
            $html .= '<strong>'
            . '<span>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . '</span>';
            $html .= '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . ' lrw-nav-item"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
            ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
            : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= '<span>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . '</span>';

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }
            $html .= '</a></li>';
        }

        return $html;
    }

}