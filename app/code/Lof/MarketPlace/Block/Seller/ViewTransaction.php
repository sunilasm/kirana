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

class ViewTransaction extends \Magento\Framework\View\Element\Template {


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
     * @var \Lof\MarketPlace\Model\Payment
     */
    protected $payment;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;


    protected $request;

    protected $_priceCurrency;
    /**
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;

    /**
     * @var \Lof\MarketPlace\Model\Withdrawal
     */
    protected $withdrawal;
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

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
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\PaymentFactory $payment,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Lof\MarketPlace\Model\Amount $amount,
        \Lof\MarketPlace\Model\Withdrawal $withdrawal,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
        ) {
        parent::__construct($context);
        
        $this->withdrawal = $withdrawal;
        $this->request = $context->getRequest();
		$this->_helper        = $helper;
		$this->_coreRegistry  = $registry;
		$this->_sellerFactory = $sellerFactory;
		$this->_resource      = $resource;
        $this->_priceCurrency = $priceCurrency;
        $this->session           = $customerSession;
        $this->amount = $amount;
        $this->payment = $payment;
         
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

    public function getPayment() {
        return $this->payment->create()->load($this->getWithdrawal()->getData('payment_id'));
    }
     /**
     * Get current currency code
     *
     * @return string
     */ 
    public function getCurrentCurrencyCode()
    {
      return $this->_priceCurrency->getCurrency()->getCurrencyCode();
    }

    public function getWithdrawal() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $withdrawal = $objectManager->get('Lof\MarketPlace\Model\Withdrawal')->load($this->getWithdrawalId());
        return $withdrawal;
    }

    public function getWithdrawalId() {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }
     /**
     *  get amount data
     *
     * @return payment data
     */
     public function getAmount(){
        $balance = 0;
        $amount = $this->amount->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->getData();
        foreach ($amount as $key => $_amount) {
            $balance = $_amount['amount'];
        }
        return $balance;
    }
   
	/**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Payment'));
        return parent::_prepareLayout ();
    }
}