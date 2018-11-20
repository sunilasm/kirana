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
namespace Lof\MarketPlace\Model;
use Lof\MarketPlace\Model\Commission as CommissionRule;
class CalculateCommission
{

	public function calculate($commission,$price) {
 
		if(is_array($commission)) {
            switch($commission['commission_by']){
                case CommissionRule::COMMISSION_BY_FIXED_AMOUNT:
                    $_commission = $commission['commission_amount'];
                    break;
                case CommissionRule::COMMISSION_BY_PERCENT_PRODUCT_PRICE:
                    if(!$price->getData('base_row_total')){
                        $baseRowTotal = ($price->getData('price_incl_tax') * $price->getData('qty')) - $price->getData('base_tax_amount');
                        $price->setData('base_row_total',$baseRowTotal);
                    }
                    switch($commission['commission_action']){
                        case CommissionRule::COMMISSION_BASED_PRICE_INCL_TAX:
                            $amount = $price->getData('base_row_total') + $price->getData('base_tax_amount');
                            break;
                        case CommissionRule::COMMISSION_BASED_PRICE_EXCL_TAX:
                            $amount = $price->getData('base_row_total');
                            break;
                        case CommissionRule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_INCL_TAX:
                            $amount = $price->getData('base_row_total') - $price->getData('base_discount_amount') + $price->getData('base_tax_amount');
                            break;
                        case CommissionRule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_EXCL_TAX:
                            $amount = $price->getData('base_row_total')  - $price->getData('base_discount_amount');
                            break;
                        default:
                            $amount = $price->getData('base_row_total')  - $price->getData('base_discount_amount');
                    }
                    $_commission = ($commission['commission_amount'] * $amount)/100;
                    break;
            }
            return 	$_commission;
        } else {    
            if ($commission != 0) {
                $commissionPerProduct = ($price->getData('row_total')- $price->getData('discount_amount')) * ($commission / 100);
                $_commission = $commissionPerProduct;
            } else {
                $_commission = 0;
            }
            return $_commission;
        } 
	}
}