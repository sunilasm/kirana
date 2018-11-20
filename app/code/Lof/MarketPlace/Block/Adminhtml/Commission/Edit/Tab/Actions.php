<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
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
 * 
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Block\Adminhtml\Commission\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Actions extends Generic implements TabInterface
{
      /**
     * @return Form
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lof_marketplace_commission');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset(
            'action_fieldset',
            ['legend' => __('Calculate Commission Using The Following Information')]
        );

        $fieldset->addField(
            'commission_by',
            'select',
            [
                'label' => __('Commission By'),
                'onchange'  => 'hideShowCommissionAction()',
                'name' => 'commission_by',
                'options' => [
                    'by_fixed' => __('Fixed Amount'),
                    'by_percent' => __('Percent Of Product Price'),
                ]
            ]
        );
        
        $fieldset->addField(
            'commission_action',
            'select',
            [
                'label' => __('Calculate Commission Based On'),
                'name' => 'commission_action',
                'options' => [
                    'by_price_incl_tax' => __('Product Price (Incl. Tax)'),
                    'by_price_excl_tax' => __('Product Price (Excl. Tax)'),
                    'by_price_after_discount_incl_tax' => __('Product Price After Discount (Incl. Tax)'),
                    'by_price_after_discount_excl_tax' => __('Product Price After Discount (Excl. Tax)'),
                ]
            ]
        );

        $fieldset->addField(
            'commission_amount',
            'text',
            [
                'name' => 'commission_amount',
                'required' => true,
                'class' => 'validate-not-negative-number',
                'label' => __('Commission')
            ]
        );


        $fieldset->addField(
            'stop_rules_processing',
            'select',
            [
                'label' => __('Discard subsequent rules'),
                'title' => __('Discard subsequent rules'),
                'name' => 'stop_rules_processing',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );

        $form->setValues($model->getData());


        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * Prepare content for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

  
}
