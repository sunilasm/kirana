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
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Withdrawals extends \Magento\Framework\View\Element\Template {


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
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;
     /**
     * @var \Lof\MarketPlace\Model\Withdrawal
     */
    protected $withdrawal;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    protected $_priceCurrency;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;
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
        \Lof\MarketPlace\Model\Payment $payment,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Withdrawal $withdrawal,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\Amount $amount,
        \Magento\Customer\Model\Session $customerSession, 
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PriceCurrencyInterface $priceFormatter,
        array $data = []
        ) {
        parent::__construct($context);

        $this->withdrawal = $withdrawal;
        $this->amount = $amount;
        $this->payment = $payment;
		$this->_helper        = $helper;
		$this->_coreRegistry  = $registry;
		$this->_sellerFactory = $sellerFactory;
		$this->_resource      = $resource;
        $this->session           = $customerSession;
        $this->_storeManager = $context->getStoreManager();
        $this->_priceCurrency = $priceCurrency;
        $this->priceFormatter = $priceFormatter;
        
    }
    public function getPriceFomat($price,$base_currency_code) {
        $currencyCode = isset($base_currency_code) ? $base_currency_code : null;
        return $this->priceFormatter->format(
                    $price,
                    false,
                    null,
                    null,
                    $currencyCode
                );
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
     * Get current currency code
     *
     * @return string
     */ 
    public function getCurrentCurrencyCode()
    {
      return $this->_priceCurrency->getCurrency()->getCurrencyCode();
    }

    public function getCurrentCurrency() {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
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
     *  get payment data
     *
     * @return payment data
     */
     public function getPayment(){
        $payment = $this->payment->getCollection();
        return $payment;
    }
    /**
     *  get total amount data
     *
     * @return total amount data
     */
     public function getTotalAmount(){
        $balance = 0;
        $withdrawal = $this->withdrawal->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->addFieldToFilter('status',1)->getData();
        foreach ($withdrawal as $key => $_withdrawal) {
            $balance += $_withdrawal['amount'];
        }
        return $balance;
    }
    /**
     *  get withdrawal data
     *
     * @return withdrawal data
     */
     public function getWithdrawal(){
        $withdrawal = $this->withdrawal->getCollection()->addFieldToFilter('seller_id',$this->getSellerId());
        return $withdrawal;
    }
    /**
     *  get status data
     *
     * @return status data
     */
     public function getStatus($status){
        $data = '';
        if($status == 0) {
            $data = '<span class="btn btn-warning">'.__('Pending').'</span>';
        } elseif($status == 1) {
            $data = '<span class="btn btn-success">'.__('Completed').'</span>';
        } elseif ($status == 2) {
            $data = '<span class="btn btn-danger">'.__('Cancel').'</span>';
        }
        return $data;
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
        $this->pageConfig->getTitle ()->set(__('Withdrawals'));
        return parent::_prepareLayout ();
    }
}