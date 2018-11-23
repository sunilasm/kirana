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
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
class Invoice extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection
    */
    protected $invoice;
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
     * @var string[]
     */
    protected $states;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

     /**
     * @var \Lof\MarketPlace\Helper\Data
    */
    protected $helper;
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection $invoice,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Helper\Data $helper,
        InvoiceRepositoryInterface $invoiceRepository,
        PriceCurrencyInterface $priceFormatter,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->states = $invoiceRepository->create()->getStates();
        $this->priceFormatter = $priceFormatter;
        $this->invoice = $invoice;
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
        $invoiceCollection = $this->invoice->addFieldToFilter('seller_id',$this->helper->getSellerId());
        return $invoiceCollection;
    }
    public function getStatus($status) {
        return isset($this->states[$status])? $this->states[$status]->getText():$status;
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
    public function isSeller() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
            return $status;
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
        $this->pageConfig->getTitle ()->set(__('Invoice'));
        return parent::_prepareLayout ();
    }
}