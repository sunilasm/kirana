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

class Link extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     * @var \Lof\MarketPlace\Model\MessageFactory
     */
    protected $_messageFactory;
    
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Message\Collection
     */
    protected $_unreadMessageCollection;

     /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\MessageDetailFactory $messageFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->_messageFactory = $messageFactory;
        $this->httpContext = $httpContext;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 0,
            'cache_tags' => ['lof_marketplace_message_link']]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $conditions = "";
        return [
        'LOF_MARKETPLACE_MESSAGE_LINK',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
        $conditions
        ];
    }
    
    public function getCacheLifetime()
    {
        return null;
    }

    /**
     * Get Unread Message Collection
     *
     * @return \Lof\MarketPlace\Model\ResourceModel\Message\Collection
     */
    public function getUnreadMessageCollection(){
        if(!$this->_unreadMessageCollection){
            $this->_unreadMessageCollection = $this->_messageFactory->create()->getCollection();
            $this->_unreadMessageCollection->addFieldToFilter('receiver_id',$this->_customerSession->getCustomerId())
                ->addFieldToFilter('is_read',0)
                ->setOrder('message_id','DESC')
                ->setPageSize(5);
        }
    
        return $this->_unreadMessageCollection;
    }
    
    
    /**
     * Get Unread Message Count
     *
     * @return int
     */
    public function getUnreadMessageCount(){
        return $this->getUnreadMessageCollection()->getSize();
    }
    
    /**
     * @return string
     */
    public function getMessageUrl()
    {
        return $this->getUrl('lofmarketplace/customer/message');
    }

    public function toHtml(){
        if(!$this->_customerSession->isLoggedIn()) return '';
        return parent::toHtml();
    }
}
