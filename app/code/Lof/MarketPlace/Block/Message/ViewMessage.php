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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Block\Message;

class ViewMessage extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     * @var \Lof\MarketPlace\Model\MessageFactory
     */
    protected $message;
    
    /**
     * @var \Lof\MarketPlace\Model\MessageDetail
     */
    protected $detail;
     /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Message\Collection
     */
    protected $_unreadMessageCollection;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Message $message,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->seller = $seller;
        $this->_customerSession = $customerSession;
        $this->message = $message;
        $this->detail = $detail;
        $this->request =  $context->getRequest();
    }
     
    public function getSeller() {
        $seller = $this->seller->getCollection()->addFieldToFilter('seller_id',$this->getMessage()->getData('owner_id'))->getFirstItem();
        return $seller;
    }

    /**
     * Get Unread Message Collection
     *
     * @return \Lof\MarketPlace\Model\ResourceModel\Message\Collection
     */
    public function getMessage(){
       $message = $this->message->getCollection()->addFieldToFilter('message_id',$this->getId())->getFirstItem();
        return $message;
    }

    public function getDetail() {
        $detail = $this->detail->getCollection()->addFieldToFilter('message_id',$this->getId());
        return $detail;
    }

    public function getId()
    {
        $id =  explode('/',trim($this->request->getPathInfo(), '/'))[4];
        return $id;
    }
    
    public function isRead() {
        foreach ($this->getDetail()->addFieldToFilter('receiver_id',$this->_customerSession->getCustomerId()) as $key => $detail) {
            $detail->setData('is_read',1)->save();
        }
         return;
    }
    /**
     * @return string
     */
    public function getMessageUrl()
    {
        return $this->getUrl('customer/message');
    }

    public function toHtml(){
        if(!$this->_customerSession->isLoggedIn()) return '';
        return parent::toHtml();
    }
     /**
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $ticket = $this->getMessage();
        $this->pageConfig->getTitle()->set(__($ticket->getSubject()));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__($ticket->getSubject()));
        }
    }

}
