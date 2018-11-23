<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Block\Seller\Widget\Form;

/**
 * Adminhtml footer block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Generic extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var string
     */
    protected $_template = 'Lof_MarketPlace::widget/form.phtml';
    
    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Lof\MarketPlace\Block\Seller\Widget\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Lof\MarketPlace\Block\Seller\Widget\Form\Renderer\Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Lof\MarketPlace\Block\Seller\Widget\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    
        return $this;
    }
    
}
