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

namespace Lof\MarketPlace\Block\Seller\Message;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
class View extends \Magento\Framework\View\Element\Template
{
	
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

    protected $request;
     /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    public $helper;
     /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\Message $message,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = []
    ) {
         parent::__construct($context, $data);
         
        $this->detail = $detail;
    	$this->message = $message;
    	$this->helper = $helper;
    	$this->groupRepository = $groupRepository;
    	$this->request =  $context->getRequest();
        $this->seller = $seller;
        $this->session           = $customerSession;
        $this->_paymentData = $paymentData;
       
    }

    public function getMessage() {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
    	$message = $objectManager->get('Lof\MarketPlace\Model\Message')->load($this->getMessageId());
    	return $message;
    }

    public function getMessageId() {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return $params[5];
    }
     /**
     *  get Seller Id
     *
     * @return Seller Id
     */
     public function getSeller(){
        $seller = $this->seller->getCollection()->addFieldToFilter('customer_id',$this->session->getId())->getFirstItem();
        
        return $seller;
    }

    public function getDetail() {
        $detail = $this->detail->getCollection()->addFieldToFilter('message_id',$this->getMessageId())->setOrder('detail_id','desc');
        return $detail;
    }
    public function isRead() {
        foreach ($this->getDetail()->addFieldToFilter('receiver_id',$this->session->getCustomerId()) as $key => $detail) {
            $detail->setData('is_read',1)->save();
        }
        $message = $this->getMessage()->setData('is_read',1);
        $message->save();
         return;
    }
    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set($this->getMessage()->getSubject());
        return parent::_prepareLayout ();
    }
}