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

namespace Lof\MarketPlace\Block\Seller;

class Message extends \Magento\Framework\View\Element\Template {


	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;
    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $_groupFactory;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    protected $message;
        /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    protected $detail;
    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
    */
	public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Model\Group $groupFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Message $message,
        \Lof\MarketPlace\Model\MessageDetail $detail,
         \Magento\Customer\Model\Session $customerSession, 
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        $this->detail = $detail;
        $this->message        = $message;
		$this->_helper        = $helper;
		$this->_coreRegistry  = $registry;
		$this->_sellerFactory = $sellerFactory;
        $this->_groupFactory  = $groupFactory;  
		$this->_resource      = $resource;
        $this->session           = $customerSession;
        parent::__construct($context);
    }
    /**
     *  get Seller Colection
     *
     * @return Object
     */
     public function getSellerCollection(){
        $store = $this->_storeManager->getStore();
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }
     /**
     *  get Group Colection
     *
     * @return Object
     */
     public function getGroupCollection(){
        $store = $this->_storeManager->getStore();
        $groupCollection = $this->_groupFactory->getCollection();
        return $groupCollection;
    }
    public function getMessage() {
        $message = $this->message->getCollection()->addFieldToFilter('owner_id',$this->getSellerId());
        return $message;
    }
     /**
     *  get Seller Id
     *
     * @return Seller Id
     */
     public function getSeller(){
        $seller = $this->_sellerFactory->getCollection()->addFieldToFilter('customer_id',$this->session->getId())->getFirstItem();
        
        return $seller;
    }
     public function getUnreadMessage($message_id){ 
       
        $unread = $this->message->getCollection()->addFieldToFilter('message_id',$message_id)->addFieldToFilter('owner_id',$this->session->getCustomerId())->addFieldToFilter('is_read',0);
        if(count($unread) > 0) {
            $count = 'Unread';
        } else {
            $count = 'Read';
        }
        return $count;
    }
  
   /**
     *  get Seller Id
     *
     * @return Seller Id
     */
     public function getSellerId(){
        $seller_id = '';
        $seller = $this->_sellerFactory->getCollection()->addFieldToFilter('customer_id',$this->session->getId())->getData();
         foreach ($seller as $key => $_seller) {
              $seller_id = $_seller['seller_id'];
          } 
        return $seller_id;
    }
	/**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Message'));
        return parent::_prepareLayout ();
    }
}