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
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Withdrawal extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $payment;

    protected $_priceCurrency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var Lof\MarketPlace\Model\Payment
     */
    protected $seller;
    /**
     * @var Lof\MarketPlace\Model\Amount 
     */
    protected $amount;
    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        \Lof\MarketPlace\Model\Payment $payment,
        \Lof\MarketPlace\Model\Amount $amount,
        \Lof\MarketPlace\Model\Seller $seller,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PriceCurrencyInterface $priceFormatter,
        array $data = []
    ) {
        $this->amount = $amount;
        $this->urlBuilder = $urlBuilder;
        $this->payment = $payment;
        $this->seller = $seller;
        $this->_priceCurrency = $priceCurrency;
        $this->priceFormatter = $priceFormatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $payment = $this->payment->getCollection()->addFieldToFilter('payment_id',$item['payment_id']);
                
                $payment_name = $seller_name = $balance = '';
                foreach ($payment as $key => $_payment) {
                    $payment_name = $_payment->getData('name');
                    
                }  
               
                // $payment_amount = $this->getPriceFomat($item['amount'],$this->getCurrentCurrencyCode());
                // $payment_fee = $this->getPriceFomat($item['fee'],$this->getCurrentCurrencyCode());
                // $payment_netamount = $this->getPriceFomat($item['net_amount'],$this->getCurrentCurrencyCode()); 

                $item[$fieldName.'_html'] = "<button class='button'><span>View Transaction</span></button>";
                $item[$fieldName.'_title'] = __('Withdrawal Information');
                $item[$fieldName.'_submitlabel'] = __('Complete Withdrawal');
                $item[$fieldName.'_cancellabel'] = __('Cancel');
                $item[$fieldName.'_withdrawalid'] = $item['withdrawal_id'];
                $item[$fieldName.'_status'] = $item['status'];
                $item[$fieldName.'_sellername'] = $item['seller_id'];
                $item[$fieldName.'_sellerid'] = $item['sellerid'];
                $item[$fieldName.'_balance'] = $item['seller_amount'];
                $item[$fieldName.'_paymentname'] = $payment_name;
                $item[$fieldName.'_email'] = $item['email'];
                $item[$fieldName.'_amount'] = $item['amount'];
                $item[$fieldName.'_fee'] = $item['fee'];
                $item[$fieldName.'_netamount'] = $item['net_amount'];
                $item[$fieldName.'_createdat'] = $item['created_at'];
                $item[$fieldName.'_formaction'] = $this->urlBuilder->getUrl('lofmarketplace/withdrawal/submit');
            }
        }

        return $dataSource;
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
}
