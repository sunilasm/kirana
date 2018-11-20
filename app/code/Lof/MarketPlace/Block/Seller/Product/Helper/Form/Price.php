<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\MarketPlace\Block\Seller\Product\Helper\Form;

/**
 * Product form price field helper
 */
class Price extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price
{
    
//     /**
//      * Get after element Html
//      * @see \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price::getAfterElementHtml()
//      * @return string
//      */
//     public function getAfterElementHtml(){
//         return "";
//     }
    
//     /**
//      * @return mixed
//      */
//     public function getBeforeElementHtml()
//     {
//         $html = parent::getBeforeElementHtml();

//         $addJsObserver = false;
//         if ($attribute = $this->getEntityAttribute()) {
//             $store = $this->getStore($attribute);
//             if ($this->getType() !== 'hidden') {
//                 $html .= '<strong>'
//                     . $this->_localeCurrency->getCurrency($store->getBaseCurrencyCode())->getSymbol()
//                     . '</strong>';
//             }
//             if ($this->_taxData->priceIncludesTax($store)) {
//                 if ($attribute->getAttributeCode() !== 'cost') {
//                     $addJsObserver = true;
//                     $html .= ' <strong>[' . __(
//                         'Inc. Tax'
//                     ) . '<span id="dynamic-tax-' . $attribute->getAttributeCode() . '"></span>]</strong>';
//                 }
//             }
//         }
//         if ($addJsObserver) {
//             $html .= $this->_getTaxObservingCode($attribute);
//         }

//         return $html;
//     }
    
//     /**
//      * Get the Html for the element.
//      *
//      * @return string
//      */
//     public function getElementHtml()
//     {
//         $html = '<div class="input-group"><div class="addon">';
//         $htmlId = $this->getHtmlId();
    
//         if (($beforeElementHtml = $this->getBeforeElementHtml())) {
//             $html .= '<label class="input-group-addon addbefore" for="' .
//                 $htmlId .
//                 '">' .
//                 $beforeElementHtml .
//                 '</label>';
//         }
    
//         $html .= '<input id="' .
//             $htmlId .
//             '" name="' .
//             $this->getName() .
//             '" ' .
//             $this->_getUiId() .
//             ' value="' .
//             $this->getEscapedValue() .
//             '" ' .
//             $this->serialize(
//                 $this->getHtmlAttributes()
//             ) . '/>';
    
//         if (($afterElementJs = $this->getAfterElementJs())) {
//             $html .= $afterElementJs;
//         }
    
//         if (($afterElementHtml = $this->getAfterElementHtml())) {
//             $html .= '<label class="addafter" for="' .
//                 $htmlId .
//                 '">' .
//                 $afterElementHtml .
//                 '</label>';
//         }
//         $html .= "</div></div>";
//         return $html;
//     }
}
