<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product form weight field helper
 */
namespace Lof\MarketPlace\Block\Seller\Product\Helper\Form;

use Magento\Catalog\Model\Product\Edit\WeightResolver;

class Weight extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $productHelper;


    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Locale\Format $localeFormat
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Lof\MarketPlace\Helper\Data $productHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Locale\Format $localeFormat,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Lof\MarketPlace\Helper\Data $productHelper,
        array $data = []
    ) {
    
        parent::__construct($factoryElement,
            $factoryCollection,
            $escaper,
            $localeFormat,
            $directoryHelper
        );
        $this->productHelper = $productHelper;
    }
    
    /**
     * Add Weight Switcher radio-button element html to weight field
     *
     * @return string
     */
    public function getElementHtml()
    {
//         return \Magento\Framework\Data\Form\Element\Text::getElementHtml();
        if (!$this->getForm()->getDataObject()->getTypeInstance()->hasWeight()) {
            $this->weightSwitcher->setValue(WeightResolver::HAS_NO_WEIGHT);
        }
        if ($this->getDisabled()) {
            $this->weightSwitcher->setDisabled($this->getDisabled());
        }
        $disableVirtualType = in_array(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL, $this->productHelper->getProductTypeRestriction());
        
        return '<div class="admin__field-control weight-switcher">' .
            '<div class="admin__control-switcher'.($disableVirtualType?' no-display':'').'" data-role="weight-switcher">' .
            $this->weightSwitcher->getLabelHtml() .
                '<div class="admin__field-control-group">' .
                $this->weightSwitcher->getElementHtml() .
                '</div>' .
            '</div>' .
            '<div class="admin__control-addon">' .
            \Magento\Framework\Data\Form\Element\Text::getElementHtml() .
                '<label class="admin__addon-suffix" for="' .
                $this->getHtmlId() .
                '"><span>' .
                $this->directoryHelper->getWeightUnit() .
                '</span></label>' .
            '</div>' .
        '</div>';
    }
    
    public function getLabel(){
        return __("Weight");
    }
    
    public function getName(){
        return 'product[weight]';
    }
}
