<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Block\Seller\Widget;

class Button extends \Magento\Backend\Block\Widget\Button
{
    /**
     * Define block template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Lof_MarketPlace::widget/button.phtml');
    }
    
    /**
     * Retrieve attributes html
     *
     * @return string
     */
    public function getAttributesHtml()
    {
        $disabled = $this->getDisabled() ? 'disabled' : '';
        $title = $this->getTitle();
        if (!$title) {
            $title = $this->getLabel();
        }
        $classes = [];
        $classes[] = 'btn';
        $classes[] = 'btn-default';

        if ($this->getClass()) {
            $classes[] = $this->getClass();
        }
        if ($disabled) {
            $classes[] = $disabled;
        }
    
        return $this->_attributesToHtml($this->_prepareAttributes($title, $classes, $disabled));
    }
}
